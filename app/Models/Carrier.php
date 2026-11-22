<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Carrier extends Model
{
    protected $fillable = [
        'name',
        'code',
        'logo',
        'service_type',
        'phone',
        'email',
        'address',
        'description',
        'status',
    ];

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'carrier_id');
    }
}
