<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrackingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'shipment_id' => $this->shipment_id,
            'status'      => $this->status,
            'location'    => $this->location,
            'event_time'  => $this->event_time?->toIso8601String(),
            'latitude'    => $this->latitude !== null ? (float) $this->latitude : null,
            'longitude'   => $this->longitude !== null ? (float) $this->longitude : null,
            'description' => $this->description,
            'updated_by'  => $this->updated_by,
        ];
    }
}
