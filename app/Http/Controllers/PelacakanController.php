<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentTracking;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PelacakanController extends Controller
{
    /** Status event yang dianggap EXCEPTION (dibedakan dari event normal di timeline). */
    private const EXCEPTION_STATUSES = [
        'DELAYED', 'EXCEPTION', 'ON_HOLD', 'FAILED',
        'CUSTOMS_HOLD', 'WEATHER_DELAY', 'PORT_CONGESTION',
        'DAMAGED', 'MISROUTED', 'DOCUMENTATION_ISSUE',
    ];

    /** Batas event yang dimuat per shipment (yang diambil = 300 event TERBARU). */
    private const EVENT_LIMIT = 300;

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        // 1. Daftar hasil / picker: TANPA eager load event.
        $results = $this->scoped()
            ->select(['id', 'shipment_number', 'customer_name', 'origin', 'destination', 'status'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('shipment_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('shipment_name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->limit(12)
            ->get();

        // 2. Shipment yang sedang dilacak (hanya ini yang memuat event).
        $current = null;

        if ($request->filled('id')) {
            $id = $request->input('id');
            $current = $this->scoped()->find($id);

            // Otorisasi server-side: customer tidak boleh membuka shipment milik orang lain.
            if (!$current && $this->isCustomer() && Shipment::whereKey($id)->exists()) {
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

        $selected = $current ? $this->buildSelected($current) : null;

        $picker = $results->map(fn ($s) => [
            'id' => $s->id,
            'number' => $s->shipment_number,
            'customer' => $s->customer_name,
            'origin' => $s->origin,
            'destination' => $s->destination,
            'status' => $s->status,
        ])->values();

        $stats = $this->stats();

        return view('pelacakan.index', compact('search', 'selected', 'picker', 'stats'));
    }

    private function buildSelected(Shipment $current): array
    {
        $iso = fn ($v) => $v ? Carbon::parse($v)->toIso8601String() : null;
        $txt = fn ($v) => $v ? Carbon::parse($v)->format('d M Y H:i') : '-';

        $totalEvents = $current->trackingEvents()->count();

        // Ambil event TERBARU (desc + limit), lalu balik jadi kronologis (lama -> baru).
        // Dulu: orderBy asc + limit 300 sehingga event terbaru bisa terpotong.
        $rows = $current->trackingEvents()
            ->select(['id', 'status', 'location', 'description', 'event_time', 'latitude', 'longitude', 'updated_by'])
            ->orderByDesc('event_time')
            ->orderByDesc('id')
            ->limit(self::EVENT_LIMIT)
            ->get()
            ->reverse()
            ->values();

        $creators = $this->userNames(
            $rows->pluck('updated_by')->filter(fn ($v) => is_numeric($v))->unique()->values()->all()
        );

        $events = $rows->map(function ($e) use ($iso, $txt, $creators) {
            $time = $e->event_time ? Carbon::parse($e->event_time) : null;

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
            $status = strtoupper((string) $e->status);

            // Event valid: punya waktu, bukan waktu di masa depan, dan punya lokasi/koordinat.
            $valid = $time !== null && !$time->isFuture() && ($coordOk || $location !== '');

            $by = $e->updated_by;
            if (is_numeric($by) && isset($creators[(int) $by])) {
                $by = $creators[(int) $by];
            }

            return [
                'id' => $e->id,
                'status' => $e->status,
                'exception' => in_array($status, self::EXCEPTION_STATUSES, true),
                'valid' => $valid,
                'is_current' => false,
                'location' => $e->location,
                'desc' => $e->description,
                'time' => $iso($e->event_time),
                'time_text' => $txt($e->event_time),
                'lat' => $lat,
                'lng' => $lng,
                'by' => $by,
            ];
        })->all();

        // Current location = event TERBARU yang VALID.
        $currentEvent = null;
        for ($i = count($events) - 1; $i >= 0; $i--) {
            if ($events[$i]['valid']) {
                $events[$i]['is_current'] = true;
                $currentEvent = $events[$i];
                break;
            }
        }

        $eventsCol = collect($events);
        $facilities = $this->facilities();

        return [
            'id' => $current->id,
            'number' => $current->shipment_number,
            'name' => $current->shipment_name,
            'customer' => $current->customer_name,
            'carrier' => $current->carrier,
            'mode' => $current->transport_type,
            'vnum' => $current->vehicle_number ?? $current->vessel_name ?? $current->flight_number ?? $current->plate_number ?? null,
            'cargo' => $current->cargo_type,
            'weight' => $current->weight,
            'volume' => $current->volume,
            'notes' => $current->notes,
            'origin' => $current->origin,
            'destination' => $current->destination,
            'origin_pos' => $this->facilityCoord($facilities, $current->origin),
            'dest_pos' => $this->facilityCoord($facilities, $current->destination),
            'status' => $current->status,
            'etd' => $iso($current->estimated_departure),
            'eta' => $iso($current->estimated_arrival),
            'etd_text' => $txt($current->estimated_departure),
            'eta_text' => $txt($current->estimated_arrival),
            'delay_cause' => optional($rows->where('status', 'DELAYED')->last())->description,
            'current' => $currentEvent,
            'events_total' => $totalEvents,
            'events_exception' => $eventsCol->where('exception', true)->count(),
            'events_invalid' => $eventsCol->where('valid', false)->count(),
            'events' => $events,
        ];
    }

    // ------------------------------------------------------------------
    // Otorisasi: customer hanya boleh melihat shipment miliknya.
    // ------------------------------------------------------------------

    private function isCustomer(): bool
    {
        $role = auth()->user()->role ?? null;
        if ($role instanceof \BackedEnum) {
            $role = $role->value;
        }

        return strtolower((string) $role) === 'customer';
    }

    /** Query shipment yang sudah dibatasi sesuai hak akses user yang login. */
    private function scoped(): Builder
    {
        $query = Shipment::query();

        if (!$this->isCustomer()) {
            return $query; // admin & operator: semua shipment
        }

        $user = auth()->user();

        if (Schema::hasColumn('shipments', 'user_id')) {
            return $query->where('user_id', $user->getAuthIdentifier());
        }
        if (Schema::hasColumn('shipments', 'customer_id')) {
            return $query->where('customer_id', $user->getAuthIdentifier());
        }

        // Fallback: cocokkan berdasarkan nama customer.
        return $query->where('customer_name', $user->name);
    }

    /** Peta id user -> nama, untuk menampilkan pembuat event. */
    private function userNames(array $ids): array
    {
        if (!$ids) {
            return [];
        }

        try {
            if (!Schema::hasTable('users')) {
                return [];
            }

            return DB::table('users')->whereIn('id', $ids)->pluck('name', 'id')->all();
        } catch (\Throwable $e) {
            return [];
        }
    }

    // Fasilitas jarang berubah: di-cache 10 menit, sudah dalam huruf kecil & hanya yang punya koordinat.
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

    // 1 query agregat, di-cache 60 detik. Cache dipisah per user untuk customer agar angka tidak bocor.
    private function stats(): array
    {
        $customer = $this->isCustomer();
        $key = 'pelacakan.stats.' . ($customer ? 'u' . auth()->id() : 'all');

        return Cache::remember($key, 60, function () use ($customer) {
            $row = $this->scoped()
                ->selectRaw("COUNT(CASE WHEN status = 'IN_TRANSIT' THEN 1 END) AS in_transit")
                ->selectRaw("COUNT(CASE WHEN status = 'DELIVERED' THEN 1 END) AS delivered")
                ->selectRaw("COUNT(CASE WHEN status != 'DELIVERED' AND estimated_arrival IS NOT NULL AND estimated_arrival < ? THEN 1 END) AS late_count", [now()])
                ->first();

            $events = $customer
                ? ShipmentTracking::whereIn('shipment_id', $this->scoped()->select('id'))->count()
                : ShipmentTracking::count();

            return [
                'events' => (int) $events,
                'in_transit' => (int) $row->in_transit,
                'delayed' => (int) $row->late_count,
                'delivered' => (int) $row->delivered,
            ];
        });
    }

    // Link lama /pelacakan/{shipment} tetap berfungsi (dengan cek otorisasi).
    public function show(Shipment $shipment)
    {
        if (!$this->scoped()->whereKey($shipment->id)->exists()) {
            abort(403, 'Anda tidak berhak mengakses pengiriman ini.');
        }

        return redirect()->route('pelacakan.index', ['id' => $shipment->id]);
    }
}
