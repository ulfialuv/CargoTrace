<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\TrackingResource;
use App\Services\TrackingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TrackingController extends ApiController
{
    public function __construct(private TrackingService $tracking)
    {
    }

    /** Riwayat event shipment, urut dari yang paling awal. */
    public function index(Request $request, string $id): JsonResponse
    {
        $shipment = $this->findShipment($request, $id);

        return $this->ok(TrackingResource::collection($shipment->trackingEvents()->get())->resolve());
    }

    /**
     * Tambah tracking event.
     *  - status == status sekarang  -> hanya mencatat event (mis. update posisi)
     *  - status == status berikutnya -> status shipment ikut naik
     *  - selain itu                  -> 422 (transisi tidak valid)
     */
    public function store(Request $request, string $id): JsonResponse
    {
        $shipment = $this->findShipment($request, $id);

        $data = $request->validate([
            'status'      => ['required', Rule::in(TrackingService::STATUSES)],
            'location'    => ['nullable', 'string', 'max:255'],
            'event_time'  => ['nullable', 'date', 'before_or_equal:now'],
            'latitude'    => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'   => ['nullable', 'numeric', 'between:-180,180'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = $request->user();

        $event = $data['status'] === $shipment->status
            ? $this->tracking->recordEvent($shipment, $data, $user->name)
            : $this->tracking->transition($shipment, $data['status'], $data, $user->name, $user->id);

        return $this->item(TrackingResource::class, $event, 'Tracking event berhasil dicatat.', 201);
    }
}
