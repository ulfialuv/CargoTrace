<?php

namespace Tests\Unit;

use App\Models\Shipment;
use App\Services\EtaService;
use Carbon\Carbon;
use Tests\TestCase;

/** Aturan ETA/delay: deterministik, tanpa database. */
class EtaServiceTest extends TestCase
{
    private Carbon $expected;

    protected function setUp(): void
    {
        parent::setUp();

        // Kunci parameter supaya tidak terpengaruh nilai di file .env
        config(['cargotrace.delay' => [
            'threshold_hours'  => 0,
            'low_max_hours'    => 6,
            'medium_max_hours' => 24,
            'exception_type'   => 'PORT_CONGESTION',
        ]]);

        $this->expected = Carbon::parse('2026-12-10 12:00:00');
    }

    private function late(int $minutes): array
    {
        return (new EtaService())->evaluateDates($this->expected, $this->expected->copy()->addMinutes($minutes));
    }

    public function test_eta_equal_to_expected_is_on_time(): void
    {
        $r = $this->late(0);

        $this->assertFalse($r['delayed']);
        $this->assertNull($r['severity']);
        $this->assertSame(0, $r['delay_minutes']);
    }

    public function test_early_eta_is_not_delayed(): void
    {
        $r = $this->late(-180);

        $this->assertFalse($r['delayed']);
        $this->assertSame(0, $r['delay_minutes']);
        $this->assertSame(-3.0, $r['difference_hours']);
    }

    public function test_seconds_of_difference_are_ignored(): void
    {
        $r = (new EtaService())->evaluateDates($this->expected, $this->expected->copy()->addSeconds(45));

        $this->assertFalse($r['delayed']);
    }

    public function test_severity_bands_and_their_boundaries(): void
    {
        $this->assertSame('LOW', $this->late(1)['severity']);
        $this->assertSame('LOW', $this->late(3 * 60)['severity']);
        $this->assertSame('LOW', $this->late(6 * 60)['severity']);        // tepat 6 jam = LOW
        $this->assertSame('MEDIUM', $this->late(6 * 60 + 1)['severity']); // >6 jam
        $this->assertSame('MEDIUM', $this->late(24 * 60)['severity']);    // tepat 24 jam = MEDIUM
        $this->assertSame('HIGH', $this->late(24 * 60 + 1)['severity']);  // >24 jam
    }

    public function test_delay_duration_is_reported(): void
    {
        $r = $this->late(5 * 60 + 30);

        $this->assertTrue($r['delayed']);
        $this->assertSame(330, $r['delay_minutes']);
        $this->assertSame(5.5, $r['delay_hours']);
    }

    public function test_threshold_is_configurable_per_group(): void
    {
        config(['cargotrace.delay.threshold_hours' => 12]);

        $ten = $this->late(10 * 60);
        $this->assertFalse($ten['delayed']);                       // 10 jam < threshold 12 jam
        $this->assertSame(12.0, $ten['threshold_hours']);

        $thirteen = $this->late(13 * 60);
        $this->assertTrue($thirteen['delayed']);
        $this->assertSame('MEDIUM', $thirteen['severity']);

        config(['cargotrace.delay.threshold_hours' => 24]);
        $this->assertFalse($this->late(20 * 60)['delayed']);
        $this->assertTrue($this->late(25 * 60)['delayed']);
    }

    public function test_reason_explains_the_values_used(): void
    {
        $delayed = $this->late(8 * 60);
        $this->assertStringContainsString('severity MEDIUM', $delayed['reason']);
        $this->assertStringContainsString('8 jam', $delayed['reason']);

        $this->assertStringContainsString('tepat waktu', $this->late(0)['reason']);
        $this->assertStringContainsString('LOW', $delayed['rule']);
    }

    public function test_delivered_shipment_is_not_monitored_for_delay(): void
    {
        $shipment = new Shipment([
            'status'            => 'DELIVERED',
            'expected_arrival'  => $this->expected,
            'estimated_arrival' => $this->expected->copy()->addHours(30),
        ]);

        $r = (new EtaService())->evaluate($shipment);

        $this->assertFalse($r['applicable']);
        $this->assertFalse($r['delayed']);
    }

    public function test_missing_dates_return_null(): void
    {
        $this->assertNull((new EtaService())->evaluate(new Shipment(['status' => 'CREATED'])));
        $this->assertNull((new EtaService())->evaluate(null));
    }
}
