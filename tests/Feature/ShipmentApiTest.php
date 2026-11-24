<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\MakesCargoData;
use Tests\TestCase;

class ShipmentApiTest extends TestCase
{
    use RefreshDatabase, MakesCargoData;

    public function test_guest_cannot_list_shipments(): void
    {
        $this->getJson('/api/v1/shipments')->assertStatus(401);
    }

    public function test_admin_can_create_shipment_with_initial_tracking(): void
    {
        Sanctum::actingAs($this->makeUser('admin'));

        $res = $this->postJson('/api/v1/shipments', $this->shipmentPayload())
            ->assertCreated()
            ->assertJsonPath('data.status', 'CREATED')
            ->assertJsonPath('data.next_status', 'BOOKED');

        $this->assertNotEmpty($res->json('data.shipment_number'));
        $this->assertCount(1, $res->json('data.tracking'));
        $this->assertDatabaseHas('shipments', ['shipment_name' => 'Spare Part Mesin', 'status' => 'CREATED']);
    }

    public function test_new_shipment_ignores_status_sent_by_client(): void
    {
        Sanctum::actingAs($this->makeUser('operator'));

        $this->postJson('/api/v1/shipments', $this->shipmentPayload(['status' => 'DELIVERED']))
            ->assertCreated()
            ->assertJsonPath('data.status', 'CREATED');
    }

    public function test_create_validates_required_fields_and_dates(): void
    {
        Sanctum::actingAs($this->makeUser('admin'));

        $this->postJson('/api/v1/shipments', [])
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['customer_name', 'weight', 'carrier', 'origin', 'destination']);

        $this->postJson('/api/v1/shipments', $this->shipmentPayload([
            'estimated_departure' => '2026-12-10 08:00:00',
            'estimated_arrival'   => '2026-12-01 08:00:00',
        ]))->assertStatus(422)->assertJsonValidationErrors(['estimated_arrival']);

        $this->postJson('/api/v1/shipments', $this->shipmentPayload(['transport_type' => 'roket']))
            ->assertStatus(422)->assertJsonValidationErrors(['transport_type']);
    }

    public function test_customer_cannot_create_update_or_delete(): void
    {
        $customer = $this->makeUser('customer');
        $shipment = $this->makeShipment([], $customer);
        Sanctum::actingAs($customer);

        $this->postJson('/api/v1/shipments', $this->shipmentPayload())->assertStatus(403);
        $this->putJson("/api/v1/shipments/{$shipment->id}", $this->shipmentPayload())->assertStatus(403);
        $this->deleteJson("/api/v1/shipments/{$shipment->id}")->assertStatus(403);
    }

    public function test_customer_only_sees_own_shipments(): void
    {
        $me    = $this->makeUser('customer');
        $other = $this->makeUser('customer');
        $this->makeShipment([], $me);
        $this->makeShipment([], $me);
        $this->makeShipment([], $other);
        Sanctum::actingAs($me);

        $this->getJson('/api/v1/shipments')
            ->assertOk()
            ->assertJsonPath('meta.total', 2);
    }

    public function test_customer_cannot_open_someone_elses_shipment(): void
    {
        $me    = $this->makeUser('customer');
        $other = $this->makeUser('customer');
        $mine  = $this->makeShipment([], $me);
        $theirs = $this->makeShipment([], $other);
        Sanctum::actingAs($me);

        $this->getJson("/api/v1/shipments/{$mine->id}")->assertOk();
        $this->getJson("/api/v1/shipments/{$theirs->id}")->assertStatus(403);
        $this->getJson("/api/v1/shipments/{$theirs->id}/tracking")->assertStatus(403);
    }

    public function test_admin_and_operator_see_all_shipments(): void
    {
        $this->makeShipment([], $this->makeUser('customer'));
        $this->makeShipment([], $this->makeUser('customer'));

        Sanctum::actingAs($this->makeUser('operator'));
        $this->getJson('/api/v1/shipments')->assertOk()->assertJsonPath('meta.total', 2);
    }

    public function test_unknown_shipment_returns_json_404(): void
    {
        Sanctum::actingAs($this->makeUser('admin'));

        $this->getJson('/api/v1/shipments/99999')
            ->assertStatus(404)
            ->assertJsonPath('success', false);
    }

    public function test_filter_by_status_and_search(): void
    {
        Sanctum::actingAs($this->makeUser('admin'));
        $this->makeShipment(['status' => 'BOOKED', 'shipment_name' => 'Kopi Gayo']);
        $this->makeShipment(['status' => 'CREATED', 'shipment_name' => 'Teh Hijau']);

        $this->getJson('/api/v1/shipments?status=BOOKED')->assertJsonPath('meta.total', 1);
        $this->getJson('/api/v1/shipments?search=Teh')->assertJsonPath('meta.total', 1);
        $this->getJson('/api/v1/shipments?status=NGAWUR')->assertStatus(422);
    }

    public function test_operator_can_update_but_not_delete(): void
    {
        $shipment = $this->makeShipment();
        Sanctum::actingAs($this->makeUser('operator'));

        $this->putJson("/api/v1/shipments/{$shipment->id}", $this->shipmentPayload(['notes' => 'diubah']))
            ->assertOk()
            ->assertJsonPath('data.notes', 'diubah');

        $this->deleteJson("/api/v1/shipments/{$shipment->id}")->assertStatus(403);
    }

    public function test_update_cannot_change_shipment_number_or_status(): void
    {
        $shipment = $this->makeShipment(['shipment_number' => 'CT-ASLI-001']);
        Sanctum::actingAs($this->makeUser('admin'));

        $this->putJson("/api/v1/shipments/{$shipment->id}", $this->shipmentPayload([
            'shipment_number' => 'CT-GANTI-999',
            'status'          => 'DELIVERED',
        ]))->assertOk();

        $shipment->refresh();
        $this->assertSame('CT-ASLI-001', $shipment->shipment_number);
        $this->assertSame('CREATED', $shipment->status);
    }

    public function test_operator_cannot_edit_delivered_shipment_but_admin_can(): void
    {
        $shipment = $this->makeShipment(['status' => 'DELIVERED']);

        Sanctum::actingAs($this->makeUser('operator'));
        $this->putJson("/api/v1/shipments/{$shipment->id}", $this->shipmentPayload())->assertStatus(422);

        Sanctum::actingAs($this->makeUser('admin'));
        $this->putJson("/api/v1/shipments/{$shipment->id}", $this->shipmentPayload())->assertOk();
    }

    public function test_admin_can_delete_shipment(): void
    {
        $shipment = $this->makeShipment();
        Sanctum::actingAs($this->makeUser('admin'));

        $this->deleteJson("/api/v1/shipments/{$shipment->id}")->assertOk();
        $this->assertDatabaseMissing('shipments', ['id' => $shipment->id]);
    }
}
