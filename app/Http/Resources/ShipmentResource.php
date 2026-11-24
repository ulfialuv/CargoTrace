<?php

namespace App\Http\Resources;

use App\Services\EtaService;
use App\Services\TrackingService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'shipment_number'     => $this->shipment_number,
            'user_id'             => $this->user_id,
            'customer_name'       => $this->customer_name,
            'shipment_name'       => $this->shipment_name,
            'cargo_type'          => $this->cargo_type,
            'weight'              => (float) $this->weight,
            'volume'              => $this->volume !== null ? (float) $this->volume : null,
            'carrier'             => $this->carrier,
            'transport_type'      => $this->transport_type,
            'origin'              => $this->origin,
            'destination'         => $this->destination,
            'estimated_departure' => $this->estimated_departure?->toIso8601String(),
            'estimated_arrival'   => $this->estimated_arrival?->toIso8601String(),
            'expected_arrival'    => $this->expected_arrival?->toIso8601String(),
            'status'              => $this->status,
            'next_status'         => app(TrackingService::class)->nextOf((string) $this->status),
            'delay'               => app(EtaService::class)->evaluate($this->resource),
            'notes'               => $this->notes,
            'tracking'            => TrackingResource::collection($this->whenLoaded('trackingEvents')),
            'created_at'          => $this->created_at?->toIso8601String(),
            'updated_at'          => $this->updated_at?->toIso8601String(),
        ];
    }
}
