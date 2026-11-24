<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarrierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'code'         => $this->code,
            'service_type' => $this->service_type,
            'phone'        => $this->phone,
            'email'        => $this->email,
            'address'      => $this->address,
            'description'  => $this->description,
            'status'       => $this->status,
        ];
    }
}
