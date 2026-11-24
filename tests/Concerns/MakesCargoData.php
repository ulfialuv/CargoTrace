<?php

namespace Tests\Concerns;

use App\Models\Carrier;
use App\Models\Shipment;
use App\Models\User;

/** Helper pembuat data uji supaya test singkat dan mudah dibaca. */
trait MakesCargoData
{
    protected function makeUser(string $role = 'customer', array $attrs = []): User
    {
        return User::factory()->create(array_merge(['role' => $role], $attrs));
    }

    protected function makeShipment(array $attrs = [], ?User $owner = null): Shipment
    {
        static $n = 0;
        $n++;

        return Shipment::create(array_merge([
            'user_id'             => $owner?->id,
            'shipment_number'     => 'CT-TEST-' . str_pad((string) $n, 4, '0', STR_PAD_LEFT),
            'customer_name'       => $owner?->name ?? 'PT Uji Coba',
            'shipment_name'       => 'Barang Uji ' . $n,
            'cargo_type'          => 'General',
            'weight'              => 100,
            'volume'              => 1.5,
            'carrier'             => 'PT Samudera Logistik',
            'transport_type'      => 'laut',
            'origin'              => 'Belawan, Medan',
            'destination'         => 'Tanjung Priok, Jakarta',
            'estimated_departure' => now()->addDay(),
            'estimated_arrival'   => now()->addDays(5),
            'status'              => 'CREATED',
        ], $attrs));
    }

    protected function makeCarrier(array $attrs = []): Carrier
    {
        static $n = 0;
        $n++;

        return Carrier::create(array_merge([
            'name'         => 'Carrier ' . $n,
            'code'         => 'CR-' . $n,
            'service_type' => 'Sea Freight',
            'status'       => 'ACTIVE',
        ], $attrs));
    }

    /** Payload valid untuk POST /shipments. */
    protected function shipmentPayload(array $override = []): array
    {
        return array_merge([
            'customer_name'       => 'PT Maju Jaya',
            'shipment_name'       => 'Spare Part Mesin',
            'cargo_type'          => 'Machinery',
            'weight'              => 1250.5,
            'volume'              => 8.2,
            'carrier'             => 'PT Samudera Logistik',
            'transport_type'      => 'laut',
            'origin'              => 'Belawan, Medan',
            'destination'         => 'Tanjung Priok, Jakarta',
            'estimated_departure' => now()->addDay()->toDateTimeString(),
            'estimated_arrival'   => now()->addDays(5)->toDateTimeString(),
        ], $override);
    }
}
