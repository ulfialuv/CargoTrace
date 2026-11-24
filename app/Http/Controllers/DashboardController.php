<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * Kolom biaya di tabel shipments yang dianggap sebagai "pemasukan".
     * Dicek berurutan, yang pertama ada akan dipakai.
     * Kalau nama kolom biayamu beda, tambahkan di sini.
     */
    private const PRICE_COLUMNS = [
        'total_price', 'total_cost', 'shipping_cost', 'freight_cost', 'price', 'cost',
        'amount', 'total_amount', 'revenue', 'total_biaya', 'biaya', 'harga', 'tarif',
    ];

    /** Kolom tanggal yang dipakai untuk mengelompokkan pemasukan. */
    private const INCOME_DATE_COLUMN = 'created_at';

    /** Status yang TIDAK dihitung sebagai pemasukan. */
    private const INCOME_EXCLUDE_STATUS = ['CANCELLED'];

    public function index(Request $request)
    {
        $now = now();

        $statuses = ['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED'];

        /* ===== Statistik: satu query saja, di-cache 30 detik ===== */
        $agg = $this->remember('dash.agg', 30, function () use ($now) {
            $row = Shipment::query()->selectRaw(
                "COUNT(*) AS cnt_all,
                 SUM(CASE WHEN status = 'DELIVERED' THEN 1 ELSE 0 END) AS cnt_done,
                 SUM(CASE WHEN status = 'IN_TRANSIT' THEN 1 ELSE 0 END) AS cnt_transit,
                 SUM(CASE WHEN status NOT IN ('DELIVERED','CANCELLED')
                           AND estimated_arrival IS NOT NULL
                           AND estimated_arrival < ? THEN 1 ELSE 0 END) AS cnt_late",
                [$now->toDateTimeString()]
            )->first();

            return [
                'total'      => (int) ($row->cnt_all ?? 0),
                'delivered'  => (int) ($row->cnt_done ?? 0),
                'in_transit' => (int) ($row->cnt_transit ?? 0),
                'delayed'    => (int) ($row->cnt_late ?? 0),
            ];
        });

        $total     = $agg['total'];
        $delayed   = $agg['delayed'];
        $delayRate = $total > 0 ? round(($delayed / $total) * 100, 1) : 0;

        // Tabel exception belum dibuat.
        $exceptionList = collect();
        $exceptionShipmentNumbers = [];
        $openExceptions = 0;
        $exceptionRate = 0;

        $stats = [
            'total'           => $total,
            'delivered'       => $agg['delivered'],
            'in_transit'      => $agg['in_transit'],
            'delayed'         => $delayed,
            'open_exceptions' => $openExceptions,
            'delay_rate'      => $delayRate,
        ];

        $delayStats = [
            'delayed_pct'   => $delayRate,
            'exception_pct' => $exceptionRate,
            'normal_pct'    => max(0, round(100 - $delayRate - $exceptionRate, 1)),
        ];

        /* ===== Data grafik (cache 60 detik) ===== */
        $charts = $this->remember('dash.charts', 60, function () use ($now) {
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

            $from = $now->copy()->subDays(13)->startOfDay();
            $perDay = Shipment::query()
                ->where('created_at', '>=', $from)
                ->selectRaw('DATE(created_at) AS d, COUNT(*) AS c')
                ->groupBy('d')
                ->pluck('c', 'd')
                ->all();

            $trend = [];
            for ($i = 13; $i >= 0; $i--) {
                $d = $now->copy()->subDays($i)->startOfDay();
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

        /* ===== Pemasukan harian / bulanan / tahunan ===== */
        $income = $this->remember('dash.income', 120, fn () => $this->buildIncome($now));

        /* ===== Daftar shipment (filter) ===== */
        $shipmentQuery = Shipment::query();

        if ($request->filled('q')) {
            $term = trim($request->input('q'));
            $shipmentQuery->where(function ($q) use ($term) {
                $q->where('shipment_number', 'like', "%{$term}%")
                    ->orWhere('customer_name', 'like', "%{$term}%")
                    ->orWhere('origin', 'like', "%{$term}%")
                    ->orWhere('destination', 'like', "%{$term}%")
                    ->orWhere('shipment_name', 'like', "%{$term}%");
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

        $delayedList = Shipment::query()
            ->whereNotIn('status', ['DELIVERED', 'CANCELLED'])
            ->whereNotNull('estimated_arrival')
            ->where('estimated_arrival', '<', $now)
            ->orderBy('estimated_arrival')
            ->limit(6)
            ->get();

        /* ===== Fasilitas aktif (cache 5 menit) ===== */
        $facilities = $this->remember('dash.facilities', 300, function () {
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
        $mapData = collect($this->remember('dash.map', 45, fn () => $this->buildMapData($facilities->all())));

        $notifications = collect();
        $etaCol = 'estimated_arrival';

        return view('dashboard.index', compact(
            'etaCol', 'stats', 'byStatus', 'byCarrier', 'delayStats', 'trend', 'statuses', 'carriers',
            'shipments', 'delayedList', 'exceptionList', 'exceptionShipmentNumbers',
            'facilities', 'mapData', 'notifications', 'income'
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
     * Pemasukan dikelompokkan per hari (800 hari terakhir), per bulan dan per tahun (5 tahun).
     * Cukup satu query GROUP BY tanggal, sisanya dijumlahkan di PHP.
     */
    private function buildIncome(Carbon $now): array
    {
        $out = ['available' => false, 'column' => null, 'daily' => [], 'months' => [], 'years' => []];

        try {
            $cols = array_map('strtolower', Schema::getColumnListing('shipments'));
        } catch (\Throwable $e) {
            return $out;
        }

        $dateCol = self::INCOME_DATE_COLUMN;
        if (!in_array($dateCol, $cols, true)) {
            return $out;
        }

        $price = null;
        foreach (self::PRICE_COLUMNS as $c) {
            if (in_array($c, $cols, true)) {
                $price = $c;
                break;
            }
        }

        $g = DB::connection()->getQueryGrammar();
        $dateSql  = $g->wrap($dateCol);
        $priceSql = $price ? 'COALESCE(SUM(' . $g->wrap($price) . '), 0)' : '0';

        $rows = Shipment::query()
            ->where(function ($q) {
                $q->whereNull('status')->orWhereNotIn('status', self::INCOME_EXCLUDE_STATUS);
            })
            ->where($dateCol, '>=', $now->copy()->subYears(5)->startOfYear())
            ->selectRaw("DATE($dateSql) AS d, COUNT(*) AS c, $priceSql AS t")
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        $dailyFrom = $now->copy()->subDays(800)->format('Y-m-d');
        $daily = [];
        $months = [];
        $years = [];

        foreach ($rows as $r) {
            $d = (string) $r->d;
            $c = (int) $r->c;
            $t = (float) $r->t;

            if ($d >= $dailyFrom) {
                $daily[] = ['k' => $d, 'c' => $c, 't' => $t];
            }

            $m = substr($d, 0, 7);
            $y = substr($d, 0, 4);
            $months[$m] = ['k' => $m, 'c' => ($months[$m]['c'] ?? 0) + $c, 't' => ($months[$m]['t'] ?? 0) + $t];
            $years[$y]  = ['k' => $y, 'c' => ($years[$y]['c'] ?? 0) + $c, 't' => ($years[$y]['t'] ?? 0) + $t];
        }

        return [
            'available' => $price !== null,
            'column'    => $price,
            'daily'     => $daily,
            'months'    => array_values($months),
            'years'     => array_values($years),
        ];
    }

    /**
     * Data shipment untuk peta (maks 120 terbaru, tanpa yang dibatalkan).
     * Koordinat asal/tujuan dicari dari tabel fasilitas.
     */
    private function buildMapData(array $facilities): array
    {
        // Siapkan indeks fasilitas sekali saja (bukan di dalam loop shipment).
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

        $coordCache = [];
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

        // Nama relasi tracking mengikuti model Shipment kamu.
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
