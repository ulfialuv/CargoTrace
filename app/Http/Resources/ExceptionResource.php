<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExceptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'shipment_id'     => $this->shipment_id,
            'shipment_number' => $this->whenLoaded('shipment', fn () => $this->shipment?->shipment_number),
            'type'            => $this->type,
            'location'        => $this->location,
            'occurred_at'     => $this->occurred_at?->toIso8601String(),
            'severity'        => $this->severity,
            'status'          => $this->status,
            'description'     => $this->description,
            'cause'           => $this->cause,
            'impact'          => $this->impact,
            'assigned_to'     => $this->assigned_to,
            'reported_by'     => $this->reported_by,
            'resolved_at'     => $this->resolved_at?->toIso8601String(),
        ];
    }
}
