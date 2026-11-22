<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentTracking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PelacakanController extends Controller
{
    public function index(Request $request)
    {
        $now = now();
        $search = trim((string) $request->input('search', ''));

        $base = Shipment::query()
            ->with(['trackingEvents' => fn ($q) => $q->orderBy('event_time')]);

        // Hasil pencarian (atau pengiriman terbaru jika kolom pencarian kosong).
        $results = (clone $base)
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

        // Shipment yang sedang dilacak.
        $current = null;

        if ($request->filled('id')) {
            $current = (clone $base)->find($request->input('id'));
        }

        if (!$current) {
            $current = $search === ''
                ? ($results->firstWhere('status', 'IN_TRANSIT') ?? $results->first())
                : $results->first();
        }

        if ($current && !$results->contains('id', $current->id)) {
            $results->prepend($current);
        }

        // Koordinat fasilitas untuk mencocokkan lokasi asal dan tujuan.
        $facilities = Schema::hasTable('facilities')
            ? DB::table('facilities')
                ->whereIn(DB::raw('UPPER(status)'), ['ACTIVE', 'AKTIF'])
                ->get(['name', 'city', 'facility_code', 'address', 'latitude', 'longitude'])
                ->map(fn ($f) => (array) $f)
            : collect();

        $facilityCoord = function (?string $place) use ($facilities) {
            $place = mb_strtolower(trim((string) $place));
            if ($place === '') {
                return null;
            }
            foreach ($facilities as $f) {
                if (!is_numeric($f['latitude']) || !is_numeric($f['longitude'])) {
                    continue;
                }
                foreach (['name', 'city', 'facility_code', 'address'] as $key) {
                    $v = mb_strtolower(trim((string) ($f[$key] ?? '')));
                    if ($v !== '' && (str_contains($place, $v) || str_contains($v, $place))) {
                        return [(float) $f['latitude'], (float) $f['longitude']];
                    }
                }
            }
            return null;
        };

        $iso = fn ($v) => $v ? Carbon::parse($v)->toIso8601String() : null;
        $txt = fn ($v) => $v ? Carbon::parse($v)->format('d M Y H:i') : '-';

        $selected = null;

        if ($current) {
            $events = $current->trackingEvents->map(fn ($e) => [
                'status' => $e->status,
                'location' => $e->location,
                'desc' => $e->description,
                'time' => $iso($e->event_time),
                'time_text' => $txt($e->event_time),
                'lat' => is_numeric($e->latitude) ? (float) $e->latitude : null,
                'lng' => is_numeric($e->longitude) ? (float) $e->longitude : null,
                'by' => $e->updated_by,
            ])->values();

            $selected = [
                'id' => $current->id,
                'number' => $current->shipment_number,
                'name' => $current->shipment_name,
                'customer' => $current->customer_name,
                'carrier' => $current->carrier,
                'mode' => $current->transport_type,
                'cargo' => $current->cargo_type,
                'weight' => $current->weight,
                'volume' => $current->volume,
                'notes' => $current->notes,
                'origin' => $current->origin,
                'destination' => $current->destination,
                'origin_pos' => $facilityCoord($current->origin),
                'dest_pos' => $facilityCoord($current->destination),
                'status' => $current->status,
                'etd' => $iso($current->estimated_departure),
                'eta' => $iso($current->estimated_arrival),
                'etd_text' => $txt($current->estimated_departure),
                'eta_text' => $txt($current->estimated_arrival),
                'delay_cause' => optional($current->trackingEvents->where('status', 'DELAYED')->last())->description,
                'events' => $events,
            ];
        }

        $picker = $results->map(fn ($s) => [
            'id' => $s->id,
            'number' => $s->shipment_number,
            'customer' => $s->customer_name,
            'origin' => $s->origin,
            'destination' => $s->destination,
            'status' => $s->status,
        ])->values();

        $stats = [
            'events' => ShipmentTracking::count(),
            'in_transit' => Shipment::where('status', 'IN_TRANSIT')->count(),
            'delayed' => Shipment::where('status', '!=', 'DELIVERED')
                ->whereNotNull('estimated_arrival')
                ->where('estimated_arrival', '<', $now)
                ->count(),
            'delivered' => Shipment::where('status', 'DELIVERED')->count(),
        ];

        return view('pelacakan.index', compact('search', 'selected', 'picker', 'stats'));
    }

    // Link lama /pelacakan/{shipment} tetap berfungsi: diarahkan ke halaman utama pelacakan.
    public function show(Shipment $shipment)
    {
        return redirect()->route('pelacakan.index', ['id' => $shipment->id]);
    }
}
