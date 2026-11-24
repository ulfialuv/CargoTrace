<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'name',
    'email',
    'password',
    'role',
    'phone',
    'birth_date',
    'gender',
    'profile_photo_path',
    'whatsapp_number',
    'address',
    'city',
    'province',
    'postal_code',
    'country',
    'notify_shipment_updates',
    'notify_tracking_updates',
    'notify_delay_alerts',
    'notify_exception_alerts',
    'notify_email',
    'last_login_at',
    'profile_updated_at',
    'password_changed_at',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function customerAddresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'notify_shipment_updates' => 'boolean',
            'notify_tracking_updates' => 'boolean',
            'notify_delay_alerts' => 'boolean',
            'notify_exception_alerts' => 'boolean',
            'notify_email' => 'boolean',
            'last_login_at' => 'datetime',
            'profile_updated_at' => 'datetime',
            'password_changed_at' => 'datetime',
        ];
    }
}
