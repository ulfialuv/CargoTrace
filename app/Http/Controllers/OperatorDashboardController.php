<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentTracking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Dashboard untuk role Logistics Operator.
 *
 * Sama seperti dashboard admin (statistik, grafik, peta, daftar shipment, exception),
 * tetapi TANPA fitur khusus admin: pemasukan, berita, kelola user/carrier/fasilitas, audit log.
 */
class OperatorDashboardController extends Controller
{
    /** Kemungkinan nama tabel exception (dicek berurutan). */
    private const EXCEPTION_TABLES = ['shipment_exceptions', 'exceptions'];

    /** Status exception yang dianggap sudah selesai. */
    private const EXCEPTION_CLOSED = ['RESOLVED'];

    public function index(Request $request)
    {
        $now = now();

        $statuses = ['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED'];

        /* ===== Statistik: satu query, cache 30 detik ===== */
        $agg = $this->remember('op.dash.agg', 30, function () use ($now) {
            $row = Shipment::query()->selectRaw(
                "COUNT(*) AS cnt_all,
                 SUM(CASE WHEN status = 'DELIVERED' THEN 1 ELSE 0 END) AS cnt_done,
                 SUM(CASE WHEN status = 'IN_TRANSIT' THEN 1 ELSE 0 END) AS cnt_transit,
                 SUM(CASE WHEN status IN ('CREATED','BOOKED') THEN 1 ELSE 0 END) AS cnt_need,
                 SUM(CASE WHEN status NOT IN ('DELIVERED','CANCELLED')
                           AND estimated_arrival IS NOT NULL
                           AND estimated_arrival < ? THEN 1 ELSE 0 END) AS cnt_late",
                [$now->toDateTimeString()]
            )->first();

            return [
                'total'      => (int) ($row->cnt_all ?? 0),
                'delivered'  => (int) ($row->cnt_done ?? 0),
                'in_transit' => (int) ($row->cnt_transit ?? 0),
                'need'       => (int) ($row->cnt_need ?? 0),
                'delayed'    => (int) ($row->cnt_late ?? 0),
            ];
        });

        $total   = $agg['total'];
        $delayed = $agg['delayed'];

        /* ===== Exception (tidak di-cache, operator yang mengelolanya) ===== */
        [$exceptionList, $exceptionShipmentNumbers, $openExceptions] = $this->loadExceptions();

        $delayRate     = $total > 0 ? round(($delayed / $total) * 100, 1) : 0;
        $exceptionRate = $total > 0 ? round(($openExceptions / $total) * 100, 1) : 0;

        $stats = [
            'total'           => $total,
            'delivered'       => $agg['delivered'],
            'in_transit'      => $agg['in_transit'],
            'delayed'         => $delayed,
            'open_exceptions' => $openExceptions,
            'delay_rate'      => $delayRate,
        ];

        $needActionShipments = $agg['need']; // CREATED + BOOKED (belum dijemput)

        $delayStats = [
            'delayed_pct'   => $delayRate,
            'exception_pct' => $exceptionRate,
            'normal_pct'    => max(0, round(100 - $delayRate - $exceptionRate, 1)),
        ];

        /* ===== Data grafik (cache 60 detik) ===== */
        $charts = $this->remember('op.dash.charts', 60, function () use ($now) {
            $byStatus = Shipment::query()
                ->select('status', DB::raw('COUNT(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->all();

            $byCarrier = Shipment::query()
                ->select('carrier', DB::raw('COUNT(*) as total'))
                ->whereNotNull('carrier')
                ->where('carrier', '!=', '')
                ->groupBy('carrier')
                ->orderByDesc('total')
                ->limit(12)
                ->pluck('total', 'carrier')
                ->all();

            $from   = $now->copy()->subDays(13)->startOfDay();
            $perDay = Shipment::query()
                ->where('created_at', '>=', $from)
                ->selectRaw('DATE(created_at) AS d, COUNT(*) AS c')
                ->groupBy('d')
                ->pluck('c', 'd')
                ->all();

            $trend = [];
            for ($i = 13; $i >= 0; $i--) {
                $d       = $now->copy()->subDays($i)->startOfDay();
                $trend[] = ['label' => $d->format('d M'), 'value' => (int) ($perDay[$d->format('Y-m-d')] ?? 0)];
            }

            $carriers = Shipment::query()
                ->whereNotNull('carrier')
                ->where('carrier', '!=', '')
                ->distinct()
                ->orderBy('carrier')
                ->pluck('carrier')
                ->all();

            return compact('byStatus', 'byCarrier', 'trend', 'carriers');
        });

        $byStatus  = collect($charts['byStatus']);
        $byCarrier = collect($charts['byCarrier']);
        $trend     = collect($charts['trend']);
        $carriers  = collect($charts['carriers']);

        /* ===== Daftar shipment (search, filter, pagination server-side) ===== */
        $shipmentQuery = Shipment::query();

        if ($request->filled('q')) {
            $term = trim($request->input('q'));
            $shipmentQuery->where(function ($q) use ($term) {
                $q->where('shipment_number', 'like', "%{$term}%")
                    ->orWhere('shipment_name', 'like', "%{$term}%")
                    ->orWhere('customer_name', 'like', "%{$term}%")
                    ->orWhere('origin', 'like', "%{$term}%")
                    ->orWhere('destination', 'like', "%{$term}%");
            });
        }
        if ($request->filled('status')) {
            $shipmentQuery->where('status', $request->input('status'));
        }
        if ($request->filled('carrier')) {
            $shipmentQuery->where('carrier', $request->input('carrier'));
        }
        if ($request->filled('date_from')) {
            $shipmentQuery->whereDate('estimated_departure', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $shipmentQuery->whereDate('estimated_departure', '<=', $request->input('date_to'));
        }
        if ($request->input('delay') === 'late') {
            $shipmentQuery->whereNotIn('status', ['DELIVERED', 'CANCELLED'])
                ->whereNotNull('estimated_arrival')
                ->where('estimated_arrival', '<', $now);
        } elseif ($request->input('delay') === 'ontime') {
            $shipmentQuery->where(function ($q) use ($now) {
                $q->where('status', 'DELIVERED')
                    ->orWhereNull('estimated_arrival')
                    ->orWhere('estimated_arrival', '>=', $now);
            });
        }

        $shipments = $shipmentQuery->orderByDesc('created_at')->paginate(10)->withQueryString();

        /* ===== Shipment terlambat ===== */
        $delayedList = Shipment::query()
            ->whereNotIn('status', ['DELIVERED', 'CANCELLED'])
            ->whereNotNull('estimated_arrival')
            ->where('estimated_arrival', '<', $now)
            ->orderBy('estimated_arrival')
            ->limit(5)
            ->get();

        /* ===== Aktivitas tracking terbaru ===== */
        try {
            $recentActivities = ShipmentTracking::with('shipment')
                ->latest('event_time')
                ->take(8)
                ->get();
        } catch (\Throwable $e) {
            $recentActivities = collect();
        }

        /* ===== Fasilitas (hanya dibaca untuk peta, cache 5 menit) ===== */
        $facilities = $this->remember('op.dash.facilities', 300, function () {
            if (!Schema::hasTable('facilities')) {
                return [];
            }

            return DB::table('facilities')
                ->whereIn(DB::raw('UPPER(status)'), ['ACTIVE', 'AKTIF'])
                ->orderBy('name')
                ->get(['id', 'facility_code', 'name', 'type', 'address', 'city', 'province', 'country', 'latitude', 'longitude'])
                ->map(fn ($f) => (array) $f)
                ->values()
                ->all();
        });
        $facilities = collect($facilities);

        /* ===== Data peta (cache 45 detik) ===== */
        $mapData = collect($this->remember('op.dash.map', 45, fn () => $this->buildMapData($facilities->all())));

        /* ===== Notifikasi (lonceng) ===== */
        $notifications = $delayedList
            ->map(fn ($s) => [
                'type' => 'delay',
                'text' => $s->shipment_number . ' melewati ETA',
                'time' => $s->estimated_arrival,
            ])
            ->concat($exceptionList->map(fn ($e) => [
                'type' => 'exception',
                'text' => ($exceptionShipmentNumbers[$e->shipment_id ?? 0] ?? 'Shipment') . ' · ' . ($e->type ?? $e->exception_type ?? 'Exception'),
                'time' => $e->created_at ?? null,
            ]))
            ->take(8)
            ->values();

        $etaCol = 'estimated_arrival';

        return view('operator.dashboard', compact(
            'etaCol', 'stats', 'needActionShipments', 'byStatus', 'byCarrier', 'delayStats', 'trend',
            'statuses', 'carriers', 'shipments', 'delayedList', 'exceptionList', 'exceptionShipmentNumbers',
            'recentActivities', 'facilities', 'mapData', 'notifications'
        ));
    }

    /**
     * Cache yang tidak boleh bikin halaman error:
     * kalau cache driver bermasalah, langsung jalankan query-nya.
     */
    private function remember(string $key, int $ttl, \Closure $fn)
    {
        try {
            return Cache::remember($key, $ttl, $fn);
        } catch (\Throwable $e) {
            return $fn();
        }
    }

    /**
     * Exception yang masih terbuka (status bukan RESOLVED).
     * Aman dipakai walau tabel exception belum ada: hasilnya kosong.
     *
     * @return array{0: \Illuminate\Support\Collection, 1: array, 2: int}
     */
    private function loadExceptions(): array
    {
        $empty = [collect(), [], 0];

        try {
            $table = collect(self::EXCEPTION_TABLES)->first(fn ($t) => Schema::hasTable($t));
            if (!$table) {
                return $empty;
            }

            $q = DB::table($table);
            if (Schema::hasColumn($table, 'status')) {
                $marks = implode(',', array_fill(0, count(self::EXCEPTION_CLOSED), '?'));
                $q->whereRaw("COALESCE(UPPER(status), '') NOT IN ($marks)", self::EXCEPTION_CLOSED);
            }

            $open  = (clone $q)->count();
            $order = Schema::hasColumn($table, 'created_at') ? 'created_at' : 'id';
            $list  = $q->orderByDesc($order)->limit(5)->get();

            $ids     = $list->pluck('shipment_id')->filter()->unique()->all();
            $numbers = $ids ? Shipment::whereIn('id', $ids)->pluck('shipment_number', 'id')->all() : [];

            return [$list, $numbers, (int) $open];
        } catch (\Throwable $e) {
            return $empty;
        }
    }

    /**
     * Data shipment untuk peta (maks 120 terbaru, tanpa yang dibatalkan).
     * Koordinat asal/tujuan dicari dari tabel fasilitas.
     */
    private function buildMapData(array $facilities): array
    {
        $index = [];
        foreach ($facilities as $f) {
            if (!is_numeric($f['latitude'] ?? null) || !is_numeric($f['longitude'] ?? null)) {
                continue;
            }
            $keys = [];
            foreach (['name', 'city', 'facility_code', 'address'] as $k) {
                $v = mb_strtolower(trim((string) ($f[$k] ?? '')));
                if ($v !== '') {
                    $keys[] = $v;
                }
            }
            $index[] = ['pos' => [(float) $f['latitude'], (float) $f['longitude']], 'keys' => $keys];
        }

        $coordCache    = [];
        $facilityCoord = function (?string $place) use ($index, &$coordCache) {
            $place = mb_strtolower(trim((string) $place));
            if ($place === '') {
                return null;
            }
            if (array_key_exists($place, $coordCache)) {
                return $coordCache[$place];
            }

            // 1) cocok persis, 2) saling mengandung (minimal 3 huruf)
            foreach ($index as $f) {
                if (in_array($place, $f['keys'], true)) {
                    return $coordCache[$place] = $f['pos'];
                }
            }
            foreach ($index as $f) {
                foreach ($f['keys'] as $v) {
                    if (mb_strlen($v) >= 3 && (str_contains($place, $v) || str_contains($v, $place))) {
                        return $coordCache[$place] = $f['pos'];
                    }
                }
            }

            return $coordCache[$place] = null;
        };

        // Nama relasi tracking mengikuti model Shipment.
        $rel = method_exists(Shipment::class, 'trackingEvents') ? 'trackingEvents' : 'tracking';

        return Shipment::query()
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', '!=', 'CANCELLED');
            })
            ->with([$rel => fn ($q) => $q->whereNotNull('latitude')->whereNotNull('longitude')->orderBy('event_time')])
            ->latest()
            ->limit(120)
            ->get()
            ->map(function ($s) use ($rel, $facilityCoord) {
                $attrs = $s->getAttributes();

                $points = $s->{$rel}
                    ->filter(fn ($p) => is_numeric($p->latitude) && is_numeric($p->longitude))
                    ->map(fn ($p) => [
                        'lat'      => (float) $p->latitude,
                        'lng'      => (float) $p->longitude,
                        'location' => $p->location,
                        'status'   => $p->status,
                        'time'     => $p->event_time ? Carbon::parse($p->event_time)->format('d M Y H:i') : null,
                    ])->values()->all();

                $eta = $attrs['estimated_arrival'] ?? null;

                return [
                    'id'             => $s->id,
                    'number'         => $s->shipment_number,
                    'customer'       => $s->customer_name,
                    'carrier'        => $s->carrier,
                    'origin'         => $s->origin,
                    'destination'    => $s->destination,
                    'origin_pos'     => $facilityCoord($s->origin),
                    'dest_pos'       => $facilityCoord($s->destination),
                    'status'         => $s->status,
                    'transport_type' => $s->transport_type,
                    'vehicle_number' => $attrs['vehicle_number'] ?? $attrs['plate_number'] ?? $attrs['vessel_name'] ?? $attrs['flight_number'] ?? null,
                    'eta'            => $eta ? Carbon::parse($eta)->format('d M Y H:i') : null,
                    'points'         => $points,
                ];
            })
            ->values()
            ->all();
    }
}
