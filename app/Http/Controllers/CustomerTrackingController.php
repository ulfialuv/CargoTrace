<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentTracking;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CustomerTrackingController extends Controller
{
    /** Status event yang dianggap EXCEPTION (dibedakan dari event normal di timeline). */
    private const EXCEPTION_STATUSES = [
        'DELAYED', 'EXCEPTION', 'ON_HOLD', 'FAILED',
        'CUSTOMS_HOLD', 'WEATHER_DELAY', 'PORT_CONGESTION',
        'DAMAGED', 'MISROUTED', 'DOCUMENTATION_ISSUE',
    ];

    /** Batas event yang dimuat per shipment (yang diambil = 300 event TERBARU). */
    private const EVENT_LIMIT = 300;

    /** Jumlah pengiriman milik customer yang ditampilkan di picker. */
    private const PICKER_LIMIT = 12;

    public function index(Request $request)
    {
        // "tracking" tetap diterima agar link lama dari halaman lain tidak rusak.
        $search = trim((string) $request->query('search', $request->query('tracking', '')));

        // 1. Daftar hasil / picker: hanya shipment milik customer, tanpa eager load event.
        $results = $this->scoped()
            ->select(['id', 'shipment_number', 'customer_name', 'origin', 'destination', 'status'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('shipment_number', 'like', "%{$search}%")
                        ->orWhere('shipment_name', 'like', "%{$search}%");
                })
                // Nomor resi yang persis sama diprioritaskan.
                ->orderByRaw('CASE WHEN shipment_number = ? THEN 0 ELSE 1 END', [$search]);
            })
            ->latest()
            ->limit(self::PICKER_LIMIT)
            ->get();

        // 2. Shipment yang sedang dilacak.
        $current = null;

        if ($request->filled('id')) {
            $id = $request->query('id');
            $current = $this->scoped()->find($id);

            // Otorisasi server-side: customer tidak boleh membuka shipment milik orang lain.
            if (!$current && Shipment::whereKey($id)->exists()) {
                abort(403, 'Anda tidak berhak mengakses pengiriman ini.');
            }
        }

        if (!$current) {
            $pickId = $search === ''
                ? ($results->firstWhere('status', 'IN_TRANSIT')->id ?? $results->first()->id ?? null)
                : ($results->first()->id ?? null);

            $current = $pickId ? $this->scoped()->find($pickId) : null;
        }

        if ($current && !$results->contains('id', $current->id)) {
            $results->prepend($current);
        }

        $exceptions = $current ? $this->exceptionsFor($current) : collect();
        $selected = $current ? $this->buildSelected($current, $exceptions) : null;

        $picker = $results->map(fn ($s) => [
            'id' => $s->id,
            'number' => $s->shipment_number,
            'customer' => $s->customer_name,
            'origin' => $s->origin,
            'destination' => $s->destination,
            'status' => strtoupper((string) $s->status),
        ])->values();

        $stats = $this->stats();

        return view('customer.pelacakan.index', compact(
            'search', 'selected', 'picker', 'stats', 'exceptions'
        ));
    }

    // ------------------------------------------------------------------
    // Data pengiriman yang dilacak
    // ------------------------------------------------------------------

    private function buildSelected(Shipment $current, Collection $exceptions): array
    {
        $iso = fn ($v) => $v ? Carbon::parse($v)->toIso8601String() : null;
        $txt = fn ($v) => $v ? Carbon::parse($v)->format('d M Y H:i') : '-';

        $totalEvents = $current->trackingEvents()->count();

        // Ambil event TERBARU (desc + limit), lalu balik jadi kronologis (lama -> baru).
        $rows = $current->trackingEvents()
            ->select(['id', 'status', 'location', 'description', 'event_time', 'latitude', 'longitude'])
            ->orderByDesc('event_time')
            ->orderByDesc('id')
            ->limit(self::EVENT_LIMIT)
            ->get()
            ->reverse()
            ->values();

        $events = [];

        foreach ($rows as $e) {
            $time = $e->event_time ? Carbon::parse($e->event_time) : null;

            // Event tanpa waktu atau bertanggal di masa depan tidak ditampilkan ke customer.
            if ($time === null || $time->isFuture()) {
                continue;
            }

            $lat = is_numeric($e->latitude) ? (float) $e->latitude : null;
            $lng = is_numeric($e->longitude) ? (float) $e->longitude : null;

            // Koordinat hanya dipakai jika lengkap, dalam rentang valid, dan bukan (0,0).
            $coordOk = $lat !== null && $lng !== null
                && $lat >= -90 && $lat <= 90
                && $lng >= -180 && $lng <= 180
                && !($lat == 0 && $lng == 0);
            if (!$coordOk) {
                $lat = $lng = null;
            }

            $location = trim((string) $e->location);

            $events[] = [
                'id' => $e->id,
                'status' => $e->status,
                'exception' => in_array(strtoupper((string) $e->status), self::EXCEPTION_STATUSES, true),
                // Valid untuk menentukan current location: punya lokasi atau koordinat.
                'valid' => $coordOk || $location !== '',
                'is_current' => false,
                'location' => $e->location,
                'desc' => $e->description,
                'time' => $iso($e->event_time),
                'time_text' => $txt($e->event_time),
                'lat' => $lat,
                'lng' => $lng,
            ];
        }

        // Current location = event TERBARU yang VALID.
        $currentEvent = null;
        for ($i = count($events) - 1; $i >= 0; $i--) {
            if ($events[$i]['valid']) {
                $events[$i]['is_current'] = true;
                $currentEvent = $events[$i];
                break;
            }
        }

        $facilities = $this->facilities();
        $delay = $this->delayInfo($current);

        // Penyebab keterlambatan: event DELAYED terakhir, atau exception yang masih terbuka.
        $delayCause = optional($rows->filter(fn ($r) => strtoupper((string) $r->status) === 'DELAYED')->last())->description;
        if (!$delayCause) {
            $open = $exceptions->first(fn ($x) => strtoupper((string) $x->status) !== 'RESOLVED');
            $delayCause = $open ? ($open->cause ?: $open->description) : null;
        }

        return [
            'id' => $current->id,
            'number' => $current->shipment_number,
            'name' => $current->shipment_name,
            'customer' => $current->customer_name,
            'carrier' => $current->carrier,
            'mode' => $this->mode($current->transport_type),
            'vnum' => $current->vehicle_number ?? $current->vessel_name ?? $current->flight_number ?? $current->plate_number ?? null,
            'cargo' => $current->cargo_type,
            'weight' => $current->weight,
            'origin' => $current->origin,
            'destination' => $current->destination,
            'origin_pos' => $this->facilityCoord($facilities, $current->origin),
            'dest_pos' => $this->facilityCoord($facilities, $current->destination),
            'status' => strtoupper((string) $current->status),
            'etd' => $iso($current->estimated_departure),
            'eta' => $iso($current->estimated_arrival),
            'etd_text' => $txt($current->estimated_departure),
            'eta_text' => $txt($current->estimated_arrival),
            'delay' => $delay,
            'delay_cause' => $delayCause,
            'current' => $currentEvent,
            'events_total' => $totalEvents,
            'events_exception' => collect($events)->where('exception', true)->count(),
            'events' => $events,
        ];
    }

    /**
     * Aturan keterlambatan deterministik (PPT slide 8):
     * ETA terlewati dan belum DELIVERED => delayed.
     * 0-6 jam: LOW, >6-24 jam: MEDIUM, >24 jam: HIGH (threshold bisa diubah lewat config).
     */
    private function delayInfo(Shipment $s): array
    {
        $low = (int) config('cargotrace.delay.low_max_hours', 6);
        $medium = (int) config('cargotrace.delay.medium_max_hours', 24);

        $status = strtoupper((string) $s->status);
        $flagged = $status === 'DELAYED';

        $info = [
            'is_delayed' => false,
            'flagged' => $flagged,
            'late_minutes' => 0,
            'remaining_minutes' => null,
            'severity' => null,
            'low_max' => $low,
            'medium_max' => $medium,
        ];

        if ($status === 'DELIVERED') {
            $info['flagged'] = false;

            return $info;
        }

        if ($s->estimated_arrival) {
            $eta = Carbon::parse($s->estimated_arrival);
            $now = now();

            if ($now->greaterThan($eta)) {
                $minutes = (int) floor(abs($eta->diffInMinutes($now)));
                $hours = $minutes / 60;

                $info['is_delayed'] = true;
                $info['late_minutes'] = $minutes;
                $info['severity'] = $hours <= $low ? 'LOW' : ($hours <= $medium ? 'MEDIUM' : 'HIGH');

                return $info;
            }

            $info['remaining_minutes'] = (int) ceil(abs($now->diffInMinutes($eta)));
        }

        // Status tertulis DELAYED walau ETA belum terlewati.
        if ($flagged) {
            $info['is_delayed'] = true;
            $info['severity'] = 'LOW';
        }

        return $info;
    }

    /** Exception milik shipment ini (hanya yang relevan untuk customer). */
    private function exceptionsFor(Shipment $shipment): Collection
    {
        return $shipment->exceptions()
            ->get()
            ->sortByDesc(fn ($e) => $e->occurred_at ?? $e->created_at)
            ->values();
    }

    private function mode(?string $type): string
    {
        return match (strtoupper(trim((string) $type))) {
            'AIR', 'UDARA' => 'udara',
            'SEA', 'LAUT' => 'laut',
            default => 'darat',
        };
    }

    // ------------------------------------------------------------------
    // Otorisasi: customer hanya boleh melihat shipment miliknya.
    // ------------------------------------------------------------------

    /** Query shipment yang dibatasi ke customer yang sedang login. */
    private function scoped(): Builder
    {
        return Shipment::query()->where('user_id', auth()->id());
    }

    // ------------------------------------------------------------------
    // Koordinat fasilitas (hanya dipakai untuk menggambar asal/tujuan di peta)
    // ------------------------------------------------------------------

    // Fasilitas jarang berubah: di-cache 10 menit, huruf kecil & hanya yang punya koordinat.
    private function facilities(): array
    {
        return Cache::remember('pelacakan.facilities', 600, function () {
            if (!Schema::hasTable('facilities')) {
                return [];
            }

            return DB::table('facilities')
                ->whereIn(DB::raw('UPPER(status)'), ['ACTIVE', 'AKTIF'])
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->get(['name', 'city', 'facility_code', 'address', 'latitude', 'longitude'])
                ->filter(fn ($f) => is_numeric($f->latitude) && is_numeric($f->longitude))
                ->map(fn ($f) => [
                    'lat' => (float) $f->latitude,
                    'lng' => (float) $f->longitude,
                    'keys' => array_values(array_filter([
                        mb_strtolower(trim((string) $f->name)),
                        mb_strtolower(trim((string) $f->city)),
                        mb_strtolower(trim((string) $f->facility_code)),
                        mb_strtolower(trim((string) $f->address)),
                    ])),
                ])
                ->values()
                ->all();
        });
    }

    private function facilityCoord(array $facilities, ?string $place): ?array
    {
        $place = mb_strtolower(trim((string) $place));
        if ($place === '') {
            return null;
        }

        foreach ($facilities as $f) {
            foreach ($f['keys'] as $v) {
                if (str_contains($place, $v) || str_contains($v, $place)) {
                    return [$f['lat'], $f['lng']];
                }
            }
        }

        return null;
    }

    // ------------------------------------------------------------------
    // Ringkasan angka (hanya milik customer ini), di-cache 60 detik per user.
    // ------------------------------------------------------------------

    private function stats(): array
    {
        return Cache::remember('customer.pelacakan.stats.u' . auth()->id(), 60, function () {
            $row = $this->scoped()
                ->selectRaw("COUNT(CASE WHEN status = 'IN_TRANSIT' THEN 1 END) AS in_transit")
                ->selectRaw("COUNT(CASE WHEN status = 'DELIVERED' THEN 1 END) AS delivered")
                ->selectRaw("COUNT(CASE WHEN status != 'DELIVERED' AND estimated_arrival IS NOT NULL AND estimated_arrival < ? THEN 1 END) AS late_count", [now()])
                ->first();

            $events = ShipmentTracking::whereIn('shipment_id', $this->scoped()->select('id'))->count();

            return [
                'events' => (int) $events,
                'in_transit' => (int) $row->in_transit,
                'delayed' => (int) $row->late_count,
                'delivered' => (int) $row->delivered,
            ];
        });
    }
}
