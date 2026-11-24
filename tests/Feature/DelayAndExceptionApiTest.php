<?php

namespace Tests\Feature;

use App\Models\ShipmentException;
use App\Services\ExceptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\MakesCargoData;
use Tests\TestCase;

class DelayAndExceptionApiTest extends TestCase
{
    use RefreshDatabase, MakesCargoData;

    protected function setUp(): void
    {
        parent::setUp();

        config(['cargotrace.delay' => [
            'threshold_hours'  => 0,
            'low_max_hours'    => 6,
            'medium_max_hours' => 24,
            'exception_type'   => 'PORT_CONGESTION',
        ]]);
    }

    /** PUT shipment dengan ETA digeser sekian jam dari expected_arrival. */
    private function moveEta($shipment, int $hoursAfterExpected)
    {
        return $this->putJson("/api/shipments/{$shipment->id}", $this->shipmentPayload([
            'estimated_arrival' => $shipment->fresh()->expected_arrival->copy()->addHours($hoursAfterExpected)->toDateTimeString(),
        ]));
    }

    public function test_new_shipment_gets_expected_arrival_equal_to_its_first_eta(): void
    {
        $shipment = $this->makeShipment();

        $fresh = $shipment->fresh();

        $this->assertNotNull($fresh->expected_arrival);
        $this->assertSame($fresh->estimated_arrival->toDateTimeString(), $fresh->expected_arrival->toDateTimeString());
    }

    public function test_late_eta_creates_an_auto_delay_exception_with_severity(): void
    {
        $shipment = $this->makeShipment();
        Sanctum::actingAs($this->makeUser('operator'));

        $this->moveEta($shipment, 10)
            ->assertOk()
            ->assertJsonPath('data.delay.delayed', true)
            ->assertJsonPath('data.delay.severity', 'MEDIUM')
            ->assertJsonPath('data.delay.delay_hours', fn ($hours) => abs($hours - 10) < 0.001);

        $exception = ShipmentException::where('shipment_id', $shipment->id)->firstOrFail();

        $this->assertSame('MEDIUM', $exception->severity);
        $this->assertSame('OPEN', $exception->status);
        $this->assertStringStartsWith(ExceptionService::AUTO_DELAY, $exception->description);
    }

    public function test_changing_delay_updates_the_same_exception(): void
    {
        $shipment = $this->makeShipment();
        Sanctum::actingAs($this->makeUser('operator'));

        $this->moveEta($shipment, 3)->assertOk();
        $this->moveEta($shipment, 30)->assertOk()->assertJsonPath('data.delay.severity', 'HIGH');

        $this->assertSame(1, ShipmentException::where('shipment_id', $shipment->id)->count());
        $this->assertSame('HIGH', ShipmentException::where('shipment_id', $shipment->id)->value('severity'));
    }

    public function test_back_on_time_resolves_the_auto_exception_and_late_again_reopens_it(): void
    {
        $shipment = $this->makeShipment();
        Sanctum::actingAs($this->makeUser('operator'));

        $this->moveEta($shipment, 8)->assertOk();
        $this->moveEta($shipment, 0)->assertOk()->assertJsonPath('data.delay.delayed', false);

        $exception = ShipmentException::where('shipment_id', $shipment->id)->firstOrFail();
        $this->assertSame('RESOLVED', $exception->status);
        $this->assertNotNull($exception->resolved_at);

        $this->moveEta($shipment, 8)->assertOk();
        $this->assertSame('OPEN', $exception->fresh()->status);
        $this->assertNull($exception->fresh()->resolved_at);
    }

    public function test_delivery_closes_the_auto_delay_exception(): void
    {
        $shipment = $this->makeShipment(['status' => 'IN_TRANSIT']);
        $shipment->update(['estimated_arrival' => $shipment->expected_arrival->copy()->addHours(30)]);

        app(ExceptionService::class)->syncDelayFor($shipment->fresh());
        $this->assertSame('HIGH', ShipmentException::where('shipment_id', $shipment->id)->value('severity'));

        Sanctum::actingAs($this->makeUser('operator'));
        $this->patchJson("/api/shipments/{$shipment->id}/status", ['status' => 'DELIVERED'])->assertOk();

        $this->assertSame('RESOLVED', ShipmentException::where('shipment_id', $shipment->id)->value('status'));
    }

    public function test_threshold_from_config_controls_what_counts_as_delayed(): void
    {
        config(['cargotrace.delay.threshold_hours' => 12]);

        $shipment = $this->makeShipment();
        Sanctum::actingAs($this->makeUser('operator'));

        $this->moveEta($shipment, 10)->assertOk()->assertJsonPath('data.delay.delayed', false);
        $this->assertSame(0, ShipmentException::where('shipment_id', $shipment->id)->count());

        $this->moveEta($shipment, 13)->assertOk()->assertJsonPath('data.delay.delayed', true);
        $this->assertSame(1, ShipmentException::where('shipment_id', $shipment->id)->count());
    }

    public function test_nested_shipment_exception_endpoints(): void
    {
        $shipment = $this->makeShipment();
        Sanctum::actingAs($this->makeUser('operator'));

        $this->postJson("/api/shipments/{$shipment->id}/exceptions", [
            'type' => 'CUSTOMS_HOLD', 'location' => 'Bea Cukai Belawan', 'occurred_at' => now()->toDateTimeString(),
            'severity' => 'HIGH', 'description' => 'Dokumen impor ditahan.',
        ])
            ->assertCreated()
            ->assertJsonPath('data.shipment_id', $shipment->id)
            ->assertJsonPath('data.status', 'OPEN');

        $this->getJson("/api/shipments/{$shipment->id}/exceptions")
            ->assertOk()
            ->assertJsonPath('meta.total', 1);
    }

    public function test_customer_reads_exceptions_of_own_shipment_only(): void
    {
        $me    = $this->makeUser('customer');
        $other = $this->makeUser('customer');
        $mine  = $this->makeShipment([], $me);
        $theirs = $this->makeShipment([], $other);
        Sanctum::actingAs($me);

        $this->getJson("/api/shipments/{$mine->id}/exceptions")->assertOk();
        $this->getJson("/api/shipments/{$theirs->id}/exceptions")->assertStatus(403);
        $this->postJson("/api/shipments/{$mine->id}/exceptions", [
            'type' => 'DAMAGED', 'location' => 'X', 'occurred_at' => now()->toDateTimeString(),
            'severity' => 'LOW', 'description' => 'Tes',
        ])->assertStatus(403);
    }

    public function test_brief_style_paths_work_without_version_prefix(): void
    {
        $this->makeUser('operator', ['email' => 'op@example.com']);

        $token = $this->postJson('/api/login', ['email' => 'op@example.com', 'password' => 'password'])
            ->assertOk()
            ->json('data.token');

        $this->withToken($token)->getJson('/api/user')->assertOk()->assertJsonPath('data.email', 'op@example.com');
        $this->withToken($token)->getJson('/api/shipments')->assertOk();

        $shipment = $this->makeShipment();

        $this->withToken($token)->patchJson("/api/shipments/{$shipment->id}", $this->shipmentPayload(['notes' => 'via PATCH']))
            ->assertOk()
            ->assertJsonPath('data.notes', 'via PATCH');

        $this->withToken($token)->postJson("/api/shipments/{$shipment->id}/tracking", ['status' => 'BOOKED'])->assertCreated();
        $this->withToken($token)->postJson('/api/logout')->assertOk();
    }
}
