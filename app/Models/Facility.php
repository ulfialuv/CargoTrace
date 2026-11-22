<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $table = 'facilities';

    protected $fillable = [
        'facility_code',
        'name',
        'type',
        'address',
        'city',
        'province',
        'country',
        'latitude',
        'longitude',
        'capacity',
        'status',
        'description',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'capacity' => 'decimal:2',
    ];
}
