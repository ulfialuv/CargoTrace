<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OperatorShipmentController extends Controller
{
    private const STATUSES = [
        'CREATED',
        'BOOKED',
        'PICKED_UP',
        'IN_TRANSIT',
        'DELIVERED',
    ];

    private function shipmentRules(): array
    {
        return [
            'shipment_number' => ['required', 'string', 'max:255', 'unique:shipments,shipment_number'],
            'customer_name' => ['required', 'string', 'max:255'],
            'shipment_name' => ['required', 'string', 'max:255'],
            'cargo_type' => ['required', 'string', 'max:255'],
            'weight' => ['required', 'numeric', 'min:0.01'],
            'volume' => ['nullable', 'numeric', 'min:0'],
            'carrier' => ['required', 'string', 'max:255'],
            'transport_type' => ['required', 'string', 'max:255'],
            'origin' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'estimated_departure' => ['required', 'date'],
            'estimated_arrival' => ['required', 'date', 'after_or_equal:estimated_departure'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function index(Request $request)
    {
        $query = Shipment::with([
            'trackingEvents' => fn ($q) => $q->orderBy('event_time', 'desc')
        ]);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('shipment_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('shipment_name', 'like', "%{$search}%")
                    ->orWhere('origin', 'like', "%{$search}%")
                    ->orWhere('destination', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('carrier')) {
            $query->where('carrier', $request->carrier);
        }

        $shipments = $query->latest()->paginate(10)->withQueryString();

        $totalShipments = Shipment::count();
        $createdShipments = Shipment::where('status', 'CREATED')->count();
        $inTransitShipments = Shipment::where('status', 'IN_TRANSIT')->count();
        $deliveredShipments = Shipment::where('status', 'DELIVERED')->count();

        $carriers = Shipment::query()
            ->whereNotNull('carrier')
            ->distinct()
            ->orderBy('carrier')
            ->pluck('carrier');

        return view('operator.pengiriman.index', compact(
            'shipments',
            'totalShipments',
            'createdShipments',
            'inTransitShipments',
            'deliveredShipments',
            'carriers'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->shipmentRules());

        DB::transaction(function () use ($validated) {
            $shipment = Shipment::create([
                ...$validated,
                'user_id' => null,
                'status' => 'CREATED',
            ]);

            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'status' => 'CREATED',
                'location' => $shipment->origin,
                'event_time' => now(),
                'description' => 'Pengiriman berhasil dibuat oleh operator.',
                'updated_by' => auth()->user()->name,
            ]);
        });

        return redirect()
            ->route('operator.pengiriman.index')
            ->with('success', 'Pengiriman baru berhasil ditambahkan.');
    }

    public function update(Request $request, Shipment $shipment)
    {
        $rules = $this->shipmentRules();

        $rules['shipment_number'] = [
            'required',
            'string',
            'max:255',
            Rule::unique('shipments', 'shipment_number')->ignore($shipment->id),
        ];

        $validated = $request->validate($rules);

        $shipment->update($validated);

        return redirect()
            ->route('operator.pengiriman.index')
            ->with('success', 'Informasi pengiriman berhasil diperbarui.');
    }

    public function trackingIndex(Request $request)
    {
        $query = Shipment::with([
            'trackingEvents' => fn ($q) => $q->orderBy('event_time', 'asc')
        ]);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('shipment_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('carrier', 'like', "%{$search}%")
                    ->orWhere('origin', 'like', "%{$search}%")
                    ->orWhere('destination', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('carrier')) {
            $query->where('carrier', $request->carrier);
        }

        $shipments = $query->latest()->paginate(10)->withQueryString();

        $totalShipments = Shipment::count();
        $inTransit = Shipment::where('status', 'IN_TRANSIT')->count();
        $pickedUp = Shipment::where('status', 'PICKED_UP')->count();
        $delivered = Shipment::where('status', 'DELIVERED')->count();

        $delayed = Shipment::whereNotIn('status', ['DELIVERED'])
            ->whereNotNull('estimated_arrival')
            ->where('estimated_arrival', '<', now())
            ->count();

        $carriers = Shipment::whereNotNull('carrier')
            ->distinct()
            ->orderBy('carrier')
            ->pluck('carrier');

        $selected = null;

        if ($request->filled('id')) {
            $selected = Shipment::with([
                'trackingEvents' => fn ($q) => $q->orderBy('event_time', 'asc')
            ])->findOrFail($request->id);
        }

        if (!$selected) {
            $selected = Shipment::with([
                'trackingEvents' => fn ($q) => $q->orderBy('event_time', 'asc')
            ])->where('status', 'IN_TRANSIT')->latest()->first();
        }

        if (!$selected) {
            $selected = Shipment::with([
                'trackingEvents' => fn ($q) => $q->orderBy('event_time', 'asc')
            ])->latest()->first();
        }

        $latestEvent = $selected
            ? $selected->trackingEvents->last()
            : null;

        return view('operator.pelacakan.index', compact(
            'shipments',
            'selected',
            'latestEvent',
            'totalShipments',
            'inTransit',
            'pickedUp',
            'delivered',
            'delayed',
            'carriers'
        ));
    }

    public function statusUpdateIndex(Request $request)
    {
        // DAFTAR PENGIRIMAN
        $shipmentQuery = Shipment::query();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $shipmentQuery->where(function ($query) use ($search) {
                $query->where('shipment_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('carrier', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $shipmentQuery->where('status', $request->status);
        }

        $shipments = $shipmentQuery
            ->latest()
            ->paginate(10, ['*'], 'shipments_page')
            ->withQueryString();

        // PENGIRIMAN YANG DIPILIH
        $selected = null;

        if ($request->filled('id')) {
            $selected = Shipment::with('trackingEvents')
                ->find($request->id);
        }

        if (!$selected) {
            $selected = Shipment::with('trackingEvents')
                ->latest()
                ->first();
        }

        // RIWAYAT PEMBARUAN STATUS
        $historyQuery = ShipmentTracking::with('shipment');

        if ($request->filled('history_search')) {
            $search = trim($request->history_search);

            $historyQuery->where(function ($query) use ($search) {
                $query->where('location', 'like', "%{$search}%")
                    ->orWhere('updated_by', 'like', "%{$search}%")
                    ->orWhereHas('shipment', function ($shipmentQuery) use ($search) {
                        $shipmentQuery
                            ->where('shipment_number', 'like', "%{$search}%")
                            ->orWhere('customer_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('history_status')) {
            $historyQuery->where('status', $request->history_status);
        }

        if ($request->filled('operator')) {
            $historyQuery->where('updated_by', 'like', '%' . $request->operator . '%');
        }

        if ($request->filled('location')) {
            $historyQuery->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->filled('date_from')) {
            $historyQuery->whereDate('event_time', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $historyQuery->whereDate('event_time', '<=', $request->date_to);
        }

        $history = $historyQuery
            ->latest('event_time')
            ->paginate(10, ['*'], 'history_page')
            ->withQueryString();

        // STATISTIK
        $totalShipments = Shipment::count();

        $pendingShipments = Shipment::whereNotIn('status', ['DELIVERED'])
            ->count();

        $deliveredShipments = Shipment::where('status', 'DELIVERED')
            ->count();

        $totalUpdates = ShipmentTracking::count();

        // DAFTAR STATUS
        $statuses = self::STATUSES;

        return view('operator.pembaruan-status.index', compact(
            'shipments',
            'selected',
            'history',
            'statuses',
            'totalShipments',
            'pendingShipments',
            'deliveredShipments',
            'totalUpdates'
        ));
    }

    public function addTrackingEvent(Request $request, Shipment $shipment)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(self::STATUSES)],
            'location' => ['required', 'string', 'max:255'],
            'event_time' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:2000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        ShipmentTracking::create([
            'shipment_id' => $shipment->id,
            'status' => $validated['status'],
            'location' => $validated['location'],
            'event_time' => $validated['event_time'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'description' => $validated['description'] ?? null,
            'updated_by' => auth()->user()->name,
        ]);

        return redirect()
            ->route('operator.pelacakan.index', ['id' => $shipment->id])
            ->with('success', 'Tracking event berhasil ditambahkan.');
    }

    public function updateStatus(Request $request, Shipment $shipment)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(self::STATUSES)],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $statusOrder = array_flip(self::STATUSES);

        $currentStatus = $shipment->status;
        $newStatus = $validated['status'];

        if ($currentStatus === 'DELIVERED' && $newStatus !== 'DELIVERED') {
            return back()
                ->withErrors(['status' => 'Pengiriman yang sudah DELIVERED tidak dapat dikembalikan ke status sebelumnya.'])
                ->withInput();
        }

        if ($statusOrder[$newStatus] < $statusOrder[$currentStatus]) {
            return back()
                ->withErrors(['status' => 'Urutan status tidak valid. Status tidak boleh mundur.'])
                ->withInput();
        }

        DB::transaction(function () use ($validated, $shipment, $newStatus) {
            $shipment->update([
                'status' => $newStatus,
            ]);

            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'status' => $newStatus,
                'location' => $validated['location'],
                'event_time' => now(),
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'description' => $validated['description'] ?? null,
                'updated_by' => auth()->user()->name,
            ]);
        });

        return redirect()
            ->route('operator.pengiriman.index')
            ->with('success', 'Status dan riwayat tracking berhasil diperbarui.');
    }
}
