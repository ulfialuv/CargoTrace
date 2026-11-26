<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\MakesCargoData;
use Tests\TestCase;

class TrackingApiTest extends TestCase
{
    use RefreshDatabase, MakesCargoData;

    private function trackingUrl($shipment): string
    {
        return "/api/v1/shipments/{$shipment->id}/tracking";
    }

    /** Tabel shipment_payments dibuat oleh migration; helper ini hanya mengisi satu pembayaran LUNAS. */
    private function markPaid(int $shipmentId): void
    {
        DB::table('shipment_payments')->insert([
            'shipment_id'    => $shipmentId,
            'invoice_number' => 'INV-TEST-' . $shipmentId,
            'method'         => 'VA',
            'subtotal'       => 100000,
            'tax'            => 0,
            'fee'            => 0,
            'total'          => 100000,
            'status'         => 'PAID',
            'paid_at'        => now(),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }

    public function test_operator_advances_status_through_tracking_event(): void
    {
        $shipment = $this->makeShipment(['status' => 'CREATED']);
        Sanctum::actingAs($this->makeUser('operator'));

        $this->postJson($this->trackingUrl($shipment), [
            'status' => 'BOOKED', 'location' => 'Belawan', 'latitude' => 3.78, 'longitude' => 98.69,
        ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'BOOKED')
            ->assertJsonPath('data.location', 'Belawan');

        $this->assertSame('BOOKED', $shipment->fresh()->status);
        $this->assertDatabaseHas('shipment_tracking', ['shipment_id' => $shipment->id, 'status' => 'BOOKED']);
    }

    public function test_invalid_transition_is_rejected_and_status_is_unchanged(): void
    {
        $shipment = $this->makeShipment(['status' => 'CREATED']);
        Sanctum::actingAs($this->makeUser('operator'));

        // Lompat CREATED -> DELIVERED
        $this->postJson($this->trackingUrl($shipment), ['status' => 'DELIVERED'])
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['status']);

        // Lompat CREATED -> IN_TRANSIT
        $this->patchJson("/api/v1/shipments/{$shipment->id}/status", ['status' => 'IN_TRANSIT'])
            ->assertStatus(422);

        $this->assertSame('CREATED', $shipment->fresh()->status);
        $this->assertDatabaseCount('shipment_tracking', 0);
    }

    public function test_status_cannot_go_backwards(): void
    {
        $shipment = $this->makeShipment(['status' => 'IN_TRANSIT']);
        Sanctum::actingAs($this->makeUser('admin'));

        $this->patchJson("/api/v1/shipments/{$shipment->id}/status", ['status' => 'BOOKED'])
            ->assertStatus(422);

        $this->assertSame('IN_TRANSIT', $shipment->fresh()->status);
    }

    public function test_full_lifecycle_reaches_delivered_and_then_is_terminal(): void
    {
        $shipment = $this->makeShipment(['status' => 'CREATED']);
        $this->markPaid($shipment->id); // gerbang pembayaran aktif karena tabelnya ada
        Sanctum::actingAs($this->makeUser('operator'));

        foreach (['BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED'] as $status) {
            $this->patchJson("/api/v1/shipments/{$shipment->id}/status", ['status' => $status])
                ->assertOk()
                ->assertJsonPath('data.status', $status);
        }

        // DELIVERED terminal: tidak bisa lanjut dan tidak bisa tambah event
        $this->patchJson("/api/v1/shipments/{$shipment->id}/status", ['status' => 'DELIVERED'])->assertStatus(422);
        $this->postJson($this->trackingUrl($shipment), ['status' => 'DELIVERED'])->assertStatus(422);

        $this->assertDatabaseCount('shipment_tracking', 4);
    }

    public function test_event_with_same_status_only_records_a_note(): void
    {
        $shipment = $this->makeShipment(['status' => 'IN_TRANSIT']);
        Sanctum::actingAs($this->makeUser('operator'));

        $this->postJson($this->trackingUrl($shipment), [
            'status' => 'IN_TRANSIT', 'location' => 'Selat Malaka', 'description' => 'Melewati Selat Malaka',
        ])->assertCreated();

        $this->assertSame('IN_TRANSIT', $shipment->fresh()->status);
        $this->assertDatabaseHas('shipment_tracking', ['shipment_id' => $shipment->id, 'location' => 'Selat Malaka']);
    }

    public function test_delivered_event_defaults_location_to_destination(): void
    {
        $shipment = $this->makeShipment(['status' => 'IN_TRANSIT', 'destination' => 'Surabaya']);
        Sanctum::actingAs($this->makeUser('operator'));

        $this->postJson($this->trackingUrl($shipment), ['status' => 'DELIVERED'])
            ->assertCreated()
            ->assertJsonPath('data.location', 'Surabaya');
    }

    public function test_payment_gate_blocks_pickup_until_paid(): void
    {
        $shipment = $this->makeShipment(['status' => 'BOOKED']);
        Sanctum::actingAs($this->makeUser('operator'));

        $this->patchJson("/api/v1/shipments/{$shipment->id}/status", ['status' => 'PICKED_UP'])
            ->assertStatus(422);
        $this->assertSame('BOOKED', $shipment->fresh()->status);

        $this->markPaid($shipment->id);

        $this->patchJson("/api/v1/shipments/{$shipment->id}/status", ['status' => 'PICKED_UP'])
            ->assertOk()
            ->assertJsonPath('data.status', 'PICKED_UP');
    }

    public function test_tracking_validates_coordinates(): void
    {
        $shipment = $this->makeShipment(['status' => 'CREATED']);
        Sanctum::actingAs($this->makeUser('operator'));

        $this->postJson($this->trackingUrl($shipment), ['status' => 'BOOKED', 'latitude' => 120, 'longitude' => 200])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['latitude', 'longitude']);

        $this->postJson($this->trackingUrl($shipment), ['status' => 'NGAWUR'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_customer_cannot_add_tracking_event(): void
    {
        $customer = $this->makeUser('customer');
        $shipment = $this->makeShipment(['status' => 'CREATED'], $customer);
        Sanctum::actingAs($customer);

        $this->postJson($this->trackingUrl($shipment), ['status' => 'BOOKED'])->assertStatus(403);
        $this->patchJson("/api/v1/shipments/{$shipment->id}/status", ['status' => 'BOOKED'])->assertStatus(403);
        $this->assertSame('CREATED', $shipment->fresh()->status);
    }

    public function test_customer_can_read_own_tracking_in_chronological_order(): void
    {
        $customer = $this->makeUser('customer');
        $shipment = $this->makeShipment(['status' => 'BOOKED'], $customer);

        DB::table('shipment_tracking')->insert([
            ['shipment_id' => $shipment->id, 'status' => 'BOOKED', 'location' => 'B', 'event_time' => now()->subHour(), 'created_at' => now(), 'updated_at' => now()],
            ['shipment_id' => $shipment->id, 'status' => 'CREATED', 'location' => 'A', 'event_time' => now()->subDay(), 'created_at' => now(), 'updated_at' => now()],
        ]);

        Sanctum::actingAs($customer);

        $res = $this->getJson($this->trackingUrl($shipment))->assertOk();

        $this->assertSame(['A', 'B'], array_column($res->json('data'), 'location'));
    }
}
