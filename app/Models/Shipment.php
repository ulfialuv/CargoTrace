<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    protected $fillable = [
        'user_id',
        'shipment_number',
        'customer_name',
        'shipment_name',
        'cargo_type',
        'weight',
        'volume',
        'carrier',
        'transport_type',
        'origin',
        'destination',
        'estimated_departure',
        'estimated_arrival',
        'expected_arrival',
        'status',
        'notes',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'volume' => 'decimal:2',
        'estimated_departure' => 'datetime',
        'estimated_arrival' => 'datetime',
        'expected_arrival' => 'datetime',
    ];

    protected static function booted(): void
    {
        // Jadwal kedatangan yang dijanjikan (expected) default-nya sama dengan ETA awal.
        static::creating(function (Shipment $shipment) {
            if (empty($shipment->expected_arrival) && ! empty($shipment->estimated_arrival)) {
                $shipment->expected_arrival = $shipment->estimated_arrival;
            }
        });
    }

    // Pemilik pengiriman
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Riwayat pelacakan pengiriman
    public function tracking(): HasMany
    {
        return $this->hasMany(
            ShipmentTracking::class,
            'shipment_id'
        );
    }

    // Riwayat pelacakan berdasarkan waktu
    public function trackingEvents(): HasMany
    {
        return $this->hasMany(ShipmentTracking::class)
            ->orderBy('event_time', 'asc');
    }

    // Daftar pengecualian pada pengiriman
    public function exceptions(): HasMany
    {
        return $this->hasMany(ShipmentException::class, 'shipment_id');
    }
}
