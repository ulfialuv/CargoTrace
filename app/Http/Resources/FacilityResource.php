<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FacilityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'facility_code' => $this->facility_code,
            'name'          => $this->name,
            'type'          => $this->type,
            'address'       => $this->address,
            'city'          => $this->city,
            'province'      => $this->province,
            'country'       => $this->country,
            'latitude'      => $this->latitude !== null ? (float) $this->latitude : null,
            'longitude'     => $this->longitude !== null ? (float) $this->longitude : null,
            'capacity'      => $this->capacity !== null ? (float) $this->capacity : null,
            'status'        => $this->status,
            'description'   => $this->description,
        ];
    }
}
