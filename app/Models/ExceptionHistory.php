<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExceptionHistory extends Model
{
    protected $table = 'exception_histories';

    protected $fillable = [
        'exception_id',
        'user_id',
        'action',
        'old_status',
        'new_status',
        'note',
    ];

    // Pengecualian yang memiliki riwayat ini
    public function exception(): BelongsTo
    {
        return $this->belongsTo(
            ShipmentException::class,
            'exception_id'
        );
    }

    // Pengguna yang melakukan aktivitas
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
