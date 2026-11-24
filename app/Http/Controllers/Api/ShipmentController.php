<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ShipmentResource;
use App\Models\Shipment;
use App\Services\ShipmentService;
use App\Services\TrackingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Hanya HTTP: validasi input, panggil service, bentuk respons. */
class ShipmentController extends ApiController
{
    public function __construct(
        private ShipmentService $shipments,
        private TrackingService $tracking,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'status'         => ['nullable', Rule::in(TrackingService::STATUSES)],
            'transport_type' => ['nullable', Rule::in(['udara', 'laut', 'darat'])],
        ]);

        $query = $this->shipmentQuery($request)
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->when($request->filled('transport_type'), fn ($q) => $q->where('transport_type', $request->query('transport_type')))
            ->when($request->filled('carrier'), fn ($q) => $q->where('carrier', $request->query('carrier')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = '%' . $request->query('search') . '%';
                $q->where(fn ($w) => $w
                    ->where('shipment_number', 'like', $s)
                    ->orWhere('shipment_name', 'like', $s)
                    ->orWhere('customer_name', 'like', $s));
            })
            ->latest('id');

        return $this->paginated($query->paginate($this->perPage($request)), ShipmentResource::class);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $shipment = $this->findShipment($request, $id)->load('trackingEvents');

        return $this->item(ShipmentResource::class, $shipment);
    }

    public function store(Request $request): JsonResponse
    {
        $shipment = $this->shipments->create($request->validate($this->rules()), $request->user());

        return $this->item(ShipmentResource::class, $shipment->load('trackingEvents'), 'Pengiriman berhasil dibuat.', 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $shipment = Shipment::findOrFail($id);

        $shipment = $this->shipments->update($shipment, $request->validate($this->rules($shipment->id)), $request->user());

        return $this->item(ShipmentResource::class, $shipment, 'Pengiriman berhasil diperbarui.');
    }

    /** Naikkan status satu langkah. Transisi tidak valid -> 422. */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $shipment = Shipment::findOrFail($id);

        $data = $request->validate([
            'status'      => ['required', Rule::in(TrackingService::STATUSES)],
            'location'    => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'latitude'    => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'   => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $user = $request->user();
        $this->tracking->transition($shipment, $data['status'], $data, $user->name, $user->id);

        return $this->item(ShipmentResource::class, $shipment->fresh()->load('trackingEvents'), 'Status berhasil diperbarui.');
    }

    public function destroy(string $id): JsonResponse
    {
        $this->shipments->delete(Shipment::findOrFail($id));

        return $this->ok(null, 'Pengiriman berhasil dihapus.');
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'shipment_number'     => ['nullable', 'string', 'max:50', Rule::unique('shipments', 'shipment_number')->ignore($ignoreId)],
            'user_id'             => ['nullable', 'integer', 'exists:users,id'],
            'customer_name'       => ['required', 'string', 'max:255'],
            'shipment_name'       => ['required', 'string', 'max:255'],
            'cargo_type'          => ['required', 'string', 'max:100'],
            'weight'              => ['required', 'numeric', 'min:0.01'],
            'volume'              => ['nullable', 'numeric', 'min:0'],
            'carrier'             => ['required', 'string', 'max:255'],
            'transport_type'      => ['required', Rule::in(['udara', 'laut', 'darat'])],
            'origin'              => ['required', 'string', 'max:255'],
            'destination'         => ['required', 'string', 'max:255'],
            'estimated_departure' => ['required', 'date'],
            'estimated_arrival'   => ['required', 'date', 'after_or_equal:estimated_departure'],
            'expected_arrival'    => ['nullable', 'date', 'after_or_equal:estimated_departure'],
            'notes'               => ['nullable', 'string'],
        ];
    }
}
