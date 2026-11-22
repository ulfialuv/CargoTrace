<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $now = now();

        $statuses = ['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED'];

        $total     = Shipment::count();
        $delivered = Shipment::where('status', 'DELIVERED')->count();
        $inTransit = Shipment::where('status', 'IN_TRANSIT')->count();

        // Delayed dihitung dari ETA yang sudah lewat.
        $delayedQuery = Shipment::query()
            ->where('status', '!=', 'DELIVERED')
            ->whereNotNull('estimated_arrival')
            ->where('estimated_arrival', '<', $now);

        $delayed   = (clone $delayedQuery)->count();
        $delayRate = $total > 0 ? round(($delayed / $total) * 100, 1) : 0;

        // Tabel exception belum dibuat.
        $exceptionList = collect();
        $exceptionShipmentNumbers = [];
        $openExceptions = 0;
        $exceptionRate = 0;

        $stats = [
            'total' => $total,
            'delivered' => $delivered,
            'in_transit' => $inTransit,
            'delayed' => $delayed,
            'open_exceptions' => $openExceptions,
            'delay_rate' => $delayRate,
        ];

        $byStatus = Shipment::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $byCarrier = Shipment::query()
            ->select('carrier', DB::raw('COUNT(*) as total'))
            ->whereNotNull('carrier')
            ->where('carrier', '!=', '')
            ->groupBy('carrier')
            ->pluck('total', 'carrier');

        $delayStats = [
            'delayed_pct' => $delayRate,
            'exception_pct' => $exceptionRate,
            'normal_pct' => max(0, round(100 - $delayRate - $exceptionRate, 1)),
        ];

        // Tren shipment dibuat 14 hari terakhir.
        $days = collect(range(13, 0))->map(fn ($i) => $now->copy()->subDays($i)->startOfDay());
        $perDay = Shipment::where('created_at', '>=', $days->first())
            ->pluck('created_at')
            ->groupBy(fn ($d) => Carbon::parse($d)->format('Y-m-d'))
            ->map->count();
        $trend = $days->map(fn ($d) => [
            'label' => $d->format('d M'),
            'value' => (int) ($perDay[$d->format('Y-m-d')] ?? 0),
        ])->values();

        $carriers = Shipment::query()
            ->whereNotNull('carrier')
            ->where('carrier', '!=', '')
            ->distinct()
            ->orderBy('carrier')
            ->pluck('carrier');

        // Filter daftar shipment.
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
            $shipmentQuery->where('status', '!=', 'DELIVERED')
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

        $delayedList = (clone $delayedQuery)->orderBy('estimated_arrival')->limit(6)->get();

        // Fasilitas aktif beserta koordinat.
        $facilities = Schema::hasTable('facilities')
            ? DB::table('facilities')
                ->whereIn(DB::raw('UPPER(status)'), ['ACTIVE', 'AKTIF'])
                ->orderBy('name')
                ->get(['id', 'facility_code', 'name', 'type', 'address', 'city', 'province', 'country', 'latitude', 'longitude'])
                ->map(fn ($f) => (array) $f)
                ->values()
            : collect();

        // Cari koordinat fasilitas dari nama kota/lokasi shipment.
        $facilityCoord = function (?string $place) use ($facilities) {
            $place = mb_strtolower(trim((string) $place));
            if ($place === '') return null;
            foreach ($facilities as $f) {
                if (!is_numeric($f['latitude']) || !is_numeric($f['longitude'])) continue;
                foreach (['name', 'city', 'facility_code', 'address'] as $key) {
                    $v = mb_strtolower(trim((string) ($f[$key] ?? '')));
                    if ($v !== '' && (str_contains($place, $v) || str_contains($v, $place))) {
                        return [(float) $f['latitude'], (float) $f['longitude']];
                    }
                }
            }
            return null;
        };

        // Nama relasi tracking mengikuti model Shipment kamu.
        $rel = method_exists(Shipment::class, 'trackingEvents') ? 'trackingEvents' : 'tracking';

        $mapData = Shipment::query()
            ->with([$rel => fn ($q) => $q->orderBy('event_time')])
            ->latest()
            ->limit(150)
            ->get()
            ->map(function ($s) use ($rel, $facilityCoord) {
                $points = $s->{$rel}->filter(fn ($p) => is_numeric($p->latitude) && is_numeric($p->longitude))
                    ->map(fn ($p) => [
                        'lat' => (float) $p->latitude,
                        'lng' => (float) $p->longitude,
                        'location' => $p->location,
                        'status' => $p->status,
                        'time' => $p->event_time ? Carbon::parse($p->event_time)->format('d M Y H:i') : null,
                    ])->values()->all();

                return [
                    'id' => $s->id,
                    'number' => $s->shipment_number,
                    'customer' => $s->customer_name,
                    'carrier' => $s->carrier,
                    'origin' => $s->origin,
                    'destination' => $s->destination,
                    'origin_pos' => $facilityCoord($s->origin),
                    'dest_pos' => $facilityCoord($s->destination),
                    'status' => $s->status,
                    'transport_type' => $s->transport_type,
                    'points' => $points,
                ];
            })
            ->values();

        $notifications = collect();
        $etaCol = 'estimated_arrival';

        return view('dashboard.index', compact(
            'etaCol', 'stats', 'byStatus', 'byCarrier', 'delayStats', 'trend', 'statuses', 'carriers',
            'shipments', 'delayedList', 'exceptionList', 'exceptionShipmentNumbers',
            'facilities', 'mapData', 'notifications'
        ));
    }
}
