<?php

namespace Tests\Feature;

use App\Models\ShipmentException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\MakesCargoData;
use Tests\TestCase;

class ExceptionApiTest extends TestCase
{
    use RefreshDatabase, MakesCargoData;

    private function payload(int $shipmentId, array $override = []): array
    {
        return array_merge([
            'shipment_id' => $shipmentId,
            'type'        => 'WEATHER_DELAY',
            'location'    => 'Selat Malaka',
            'occurred_at' => now()->toDateTimeString(),
            'severity'    => 'MEDIUM',
            'description' => 'Cuaca buruk menunda keberangkatan.',
        ], $override);
    }

    public function test_operator_can_report_exception_and_history_is_written(): void
    {
        $shipment = $this->makeShipment();
        Sanctum::actingAs($operator = $this->makeUser('operator'));

        $this->postJson('/api/v1/exceptions', $this->payload($shipment->id))
            ->assertCreated()
            ->assertJsonPath('data.status', 'OPEN')
            ->assertJsonPath('data.reported_by', $operator->id);

        $this->assertDatabaseHas('exception_histories', ['action' => 'CREATED', 'new_status' => 'OPEN']);
    }

    public function test_exception_validation(): void
    {
        $shipment = $this->makeShipment();
        Sanctum::actingAs($this->makeUser('operator'));

        $this->postJson('/api/v1/exceptions', $this->payload($shipment->id, ['severity' => 'PARAH', 'type' => 'ALIEN']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['severity', 'type']);

        $this->postJson('/api/v1/exceptions', $this->payload(99999))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['shipment_id']);
    }

    public function test_customer_cannot_create_or_update_exceptions(): void
    {
        $customer  = $this->makeUser('customer');
        $shipment  = $this->makeShipment([], $customer);
        $exception = ShipmentException::create($this->payload($shipment->id) + ['status' => 'OPEN']);
        Sanctum::actingAs($customer);

        $this->postJson('/api/v1/exceptions', $this->payload($shipment->id))->assertStatus(403);
        $this->patchJson("/api/v1/exceptions/{$exception->id}", ['status' => 'RESOLVED'])->assertStatus(403);
    }

    public function test_customer_only_sees_exceptions_of_own_shipments(): void
    {
        $me    = $this->makeUser('customer');
        $other = $this->makeUser('customer');

        $mine   = ShipmentException::create($this->payload($this->makeShipment([], $me)->id) + ['status' => 'OPEN']);
        $theirs = ShipmentException::create($this->payload($this->makeShipment([], $other)->id) + ['status' => 'OPEN']);

        Sanctum::actingAs($me);

        $this->getJson('/api/v1/exceptions')->assertOk()->assertJsonPath('meta.total', 1);
        $this->getJson("/api/v1/exceptions/{$mine->id}")->assertOk();
        $this->getJson("/api/v1/exceptions/{$theirs->id}")->assertStatus(403);
    }

    public function test_resolving_sets_resolved_at_and_reopening_clears_it(): void
    {
        $shipment  = $this->makeShipment();
        $exception = ShipmentException::create($this->payload($shipment->id) + ['status' => 'OPEN']);
        Sanctum::actingAs($this->makeUser('operator'));

        $this->patchJson("/api/v1/exceptions/{$exception->id}", ['status' => 'RESOLVED', 'note' => 'Sudah beres'])
            ->assertOk()
            ->assertJsonPath('data.status', 'RESOLVED');
        $this->assertNotNull($exception->fresh()->resolved_at);
        $this->assertDatabaseHas('exception_histories', ['action' => 'STATUS_CHANGED', 'old_status' => 'OPEN', 'new_status' => 'RESOLVED']);

        $this->patchJson("/api/v1/exceptions/{$exception->id}", ['status' => 'INVESTIGATING'])->assertOk();
        $this->assertNull($exception->fresh()->resolved_at);
    }

    public function test_filter_exceptions_by_severity(): void
    {
        $shipment = $this->makeShipment();
        ShipmentException::create($this->payload($shipment->id, ['severity' => 'HIGH']) + ['status' => 'OPEN']);
        ShipmentException::create($this->payload($shipment->id, ['severity' => 'LOW']) + ['status' => 'OPEN']);
        Sanctum::actingAs($this->makeUser('admin'));

        $this->getJson('/api/v1/exceptions?severity=HIGH')->assertJsonPath('meta.total', 1);
    }
}
