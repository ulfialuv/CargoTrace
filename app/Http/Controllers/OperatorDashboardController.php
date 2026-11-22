<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentTracking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperatorDashboardController extends Controller
{
    public function index(Request $request)
    {
        $now = now();

        $statuses = ['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED'];

        // ===== STATISTIK =====
        $byStatus = Shipment::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalShipments     = (int) $byStatus->sum();
        $createdShipments   = (int) ($byStatus['CREATED'] ?? 0);
        $inTransitShipments = (int) ($byStatus['IN_TRANSIT'] ?? 0);
        $deliveredShipments = (int) ($byStatus['DELIVERED'] ?? 0);

        // Pengiriman yang butuh tindakan operator (belum dijemput)
        $needActionShipments = (int) (($byStatus['CREATED'] ?? 0) + ($byStatus['BOOKED'] ?? 0));

        // Terlambat = ETA sudah lewat dan belum DELIVERED
        $delayedQuery = Shipment::query()
            ->where('status', '!=', 'DELIVERED')
            ->whereNotNull('estimated_arrival')
            ->where('estimated_arrival', '<', $now);

        $delayedShipments = (clone $delayedQuery)->count();
        $delayRate = $totalShipments > 0 ? round(($delayedShipments / $totalShipments) * 100, 1) : 0;

        // ===== TREN 14 HARI =====
        $days = collect(range(13, 0))->map(fn ($i) => $now->copy()->subDays($i)->startOfDay());

        $perDay = Shipment::where('created_at', '>=', $days->first())
            ->pluck('created_at')
            ->groupBy(fn ($d) => Carbon::parse($d)->format('Y-m-d'))
            ->map->count();

        $trend = $days->map(fn ($d) => [
            'label' => $d->format('d M'),
            'value' => (int) ($perDay[$d->format('Y-m-d')] ?? 0),
        ])->values();

        // ===== DAFTAR PENGIRIMAN (dengan filter) =====
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

        if ($request->input('delay') === 'late') {
            $shipmentQuery->where('status', '!=', 'DELIVERED')
                ->whereNotNull('estimated_arrival')
                ->where('estimated_arrival', '<', $now);
        }

        $recentShipments = $shipmentQuery
            ->orderByDesc('created_at')
            ->paginate(8)
            ->withQueryString();

        // ===== PENGIRIMAN TERLAMBAT =====
        $delayedList = (clone $delayedQuery)
            ->orderBy('estimated_arrival')
            ->limit(5)
            ->get();

        // ===== AKTIVITAS PELACAKAN TERBARU =====
        $recentActivities = ShipmentTracking::with('shipment')
            ->latest('event_time')
            ->take(8)
            ->get();

        return view('operator.dashboard', compact(
            'statuses',
            'totalShipments',
            'createdShipments',
            'inTransitShipments',
            'deliveredShipments',
            'needActionShipments',
            'delayedShipments',
            'delayRate',
            'byStatus',
            'trend',
            'recentShipments',
            'delayedList',
            'recentActivities'
        ));
    }
}
