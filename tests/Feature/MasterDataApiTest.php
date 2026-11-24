<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\MakesCargoData;
use Tests\TestCase;

class MasterDataApiTest extends TestCase
{
    use RefreshDatabase, MakesCargoData;

    private function facilityPayload(array $o = []): array
    {
        return array_merge([
            'facility_code' => 'IDBLW', 'name' => 'Pelabuhan Belawan', 'type' => 'PORT',
            'address' => 'Jl. Raya Pelabuhan', 'city' => 'Medan', 'province' => 'Sumatera Utara',
            'latitude' => 3.7826, 'longitude' => 98.6932, 'status' => 'ACTIVE',
        ], $o);
    }

    public function test_every_role_can_read_carriers_and_locations(): void
    {
        $this->makeCarrier();

        foreach (['admin', 'operator', 'customer'] as $role) {
            Sanctum::actingAs($this->makeUser($role));
            $this->getJson('/api/v1/carriers')->assertOk()->assertJsonPath('meta.total', 1);
            $this->getJson('/api/v1/locations')->assertOk();
        }
    }

    public function test_admin_can_crud_carrier(): void
    {
        Sanctum::actingAs($this->makeUser('admin'));

        $id = $this->postJson('/api/v1/carriers', [
            'name' => 'PT Baru', 'code' => 'NEW-1', 'service_type' => 'Air Freight', 'status' => 'ACTIVE',
        ])->assertCreated()->json('data.id');

        $this->putJson("/api/v1/carriers/{$id}", [
            'name' => 'PT Baru Banget', 'code' => 'NEW-1', 'service_type' => 'Air Freight', 'status' => 'INACTIVE',
        ])->assertOk()->assertJsonPath('data.status', 'INACTIVE');

        $this->deleteJson("/api/v1/carriers/{$id}")->assertOk();
        $this->assertDatabaseMissing('carriers', ['id' => $id]);
    }

    public function test_carrier_code_must_be_unique(): void
    {
        $this->makeCarrier(['code' => 'DUP-1']);
        Sanctum::actingAs($this->makeUser('admin'));

        $this->postJson('/api/v1/carriers', [
            'name' => 'Lain', 'code' => 'DUP-1', 'service_type' => 'Sea', 'status' => 'ACTIVE',
        ])->assertStatus(422)->assertJsonValidationErrors(['code']);
    }

    public function test_carrier_in_use_cannot_be_deleted(): void
    {
        $carrier = $this->makeCarrier(['name' => 'PT Dipakai']);
        $this->makeShipment(['carrier' => 'PT Dipakai']);
        Sanctum::actingAs($this->makeUser('admin'));

        $this->deleteJson("/api/v1/carriers/{$carrier->id}")->assertStatus(409);
        $this->assertDatabaseHas('carriers', ['id' => $carrier->id]);
    }

    public function test_only_admin_can_write_carriers_and_locations(): void
    {
        $carrier = $this->makeCarrier();

        foreach (['operator', 'customer'] as $role) {
            Sanctum::actingAs($this->makeUser($role));

            $this->postJson('/api/v1/carriers', ['name' => 'X', 'code' => 'X', 'service_type' => 'S', 'status' => 'ACTIVE'])->assertStatus(403);
            $this->deleteJson("/api/v1/carriers/{$carrier->id}")->assertStatus(403);
            $this->postJson('/api/v1/locations', $this->facilityPayload())->assertStatus(403);
        }
    }

    public function test_admin_can_create_location_and_validates_coordinates(): void
    {
        Sanctum::actingAs($this->makeUser('admin'));

        $this->postJson('/api/v1/locations', $this->facilityPayload())
            ->assertCreated()
            ->assertJsonPath('data.type', 'PORT');

        $this->postJson('/api/v1/locations', $this->facilityPayload(['facility_code' => 'X2', 'latitude' => 999, 'type' => 'KANDANG']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['latitude', 'type']);
    }
}
