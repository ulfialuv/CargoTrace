<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShipmentException extends Model
{
    protected $table = 'shipment_exceptions';

    protected $fillable = [
        'shipment_id',
        'type',
        'location',
        'occurred_at',
        'severity',
        'status',
        'description',
        'cause',
        'impact',
        'assigned_to',
        'reported_by',
        'resolved_at',
        'attachment_path',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    // Pengiriman yang mengalami pengecualian
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    // Petugas yang menangani pengecualian
    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Pengguna yang melaporkan pengecualian
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    // Riwayat penanganan pengecualian
    public function histories(): HasMany
    {
        return $this->hasMany(ExceptionHistory::class, 'exception_id')
                    ->latest();
    }
}
