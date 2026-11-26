<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use Database\Seeders\DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DemoSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Kunci aturan delay supaya hasil tidak bergantung pada nilai di file .env
        config(['cargotrace.delay' => [
            'threshold_hours'  => 0,
            'low_max_hours'    => 6,
            'medium_max_hours' => 24,
            'exception_type'   => 'PORT_CONGESTION',
        ]]);
    }

    public function test_seeds_accounts_for_every_role(): void
    {
        $this->seed(DemoSeeder::class);

        $this->assertSame(1, User::where('role', 'admin')->count());
        $this->assertSame(2, User::where('role', 'operator')->count());
        $this->assertSame(3, User::where('role', 'customer')->count());
    }

    public function test_demo_customer_can_log_in_with_the_documented_password(): void
    {
        $this->seed(DemoSeeder::class);

        $this->postJson('/api/login', [
            'email'    => 'customer1@cargotrace.test',
            'password' => config('cargotrace.demo.password'),
        ])->assertOk()->assertJsonPath('data.user.role', 'customer');
    }

    public function test_seeds_shipments_in_every_lifecycle_status(): void
    {
        $this->seed(DemoSeeder::class);

        $this->assertSame(12, Shipment::where('shipment_number', 'like', 'CT-DEMO-%')->count());

        foreach (['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'DELIVERED'] as $status) {
            $this->assertTrue(
                Shipment::where('shipment_number', 'like', 'CT-DEMO-%')->where('status', $status)->exists(),
                "Tidak ada shipment demo berstatus {$status}"
            );
        }
    }

    public function test_every_shipment_has_a_consistent_tracking_timeline(): void
    {
        $this->seed(DemoSeeder::class);

        foreach (Shipment::where('shipment_number', 'like', 'CT-DEMO-%')->get() as $shipment) {
            $events = $shipment->trackingEvents()->get();

            $this->assertGreaterThan(0, $events->count(), "{$shipment->shipment_number} tanpa tracking event");
            $this->assertSame($shipment->status, $events->last()->status, "{$shipment->shipment_number}: status akhir timeline berbeda");

            $times = $events->pluck('event_time')->map->getTimestamp()->all();
            $sorted = $times;
            sort($sorted);
            $this->assertSame($sorted, $times, "{$shipment->shipment_number}: event tidak berurutan waktu");

            foreach ($events as $event) {
                $this->assertLessThanOrEqual(now()->addMinute()->getTimestamp(), $event->event_time->getTimestamp(), 'Event berada di masa depan');
            }
        }
    }

    public function test_delayed_shipments_get_automatic_exceptions_with_the_right_severity(): void
    {
        $this->seed(DemoSeeder::class);

        $expected = [
            'CT-DEMO-005' => 'LOW',     // ETA +4 jam
            'CT-DEMO-006' => 'MEDIUM',  // ETA +15 jam
            'CT-DEMO-007' => 'HIGH',    // ETA +30 jam
            'CT-DEMO-012' => 'LOW',     // ETA +2 jam
        ];

        foreach ($expected as $number => $severity) {
            $shipment = Shipment::where('shipment_number', $number)->firstOrFail();

            $auto = ShipmentException::where('shipment_id', $shipment->id)
                ->where('description', 'like', '[AUTO-DELAY]%')
                ->firstOrFail();

            $this->assertSame($severity, $auto->severity, $number);
            $this->assertSame('OPEN', $auto->status, $number);
        }

        // Shipment tepat waktu / sudah terkirim tidak punya exception delay otomatis.
        $this->assertSame(4, ShipmentException::where('description', 'like', '[AUTO-DELAY]%')->count());
    }

    public function test_manual_exceptions_cover_open_investigating_and_resolved(): void
    {
        $this->seed(DemoSeeder::class);

        foreach (['OPEN', 'INVESTIGATING', 'RESOLVED'] as $status) {
            $this->assertTrue(ShipmentException::where('status', $status)->exists(), "Tidak ada exception {$status}");
        }

        $resolved = ShipmentException::where('status', 'RESOLVED')->where('description', 'not like', '[AUTO-DELAY]%')->get();

        $this->assertTrue($resolved->isNotEmpty());
        $resolved->each(fn ($e) => $this->assertNotNull($e->resolved_at));
    }

    public function test_seeding_twice_does_not_duplicate_anything(): void
    {
        $this->seed(DemoSeeder::class);
        $this->seed(DemoSeeder::class);

        $this->assertSame(6, User::count());
        $this->assertSame(12, Shipment::count());
        $this->assertSame(4, ShipmentException::where('description', 'like', '[AUTO-DELAY]%')->count());
    }

    public function test_seeds_audit_log_entries_so_the_audit_page_is_not_empty(): void
    {
        $this->seed(DemoSeeder::class);

        foreach (['LOGIN', 'LOGOUT', 'CREATE', 'STATUS CHANGE', 'EXCEPTION UPDATE', 'USER MANAGEMENT'] as $action) {
            $this->assertTrue(AuditLog::where('action', $action)->exists(), "Tidak ada audit log {$action}");
        }

        $this->assertTrue(AuditLog::where('created_at', '>=', now()->subDays(7))->exists(), 'Tidak ada aktivitas 7 hari terakhir');
        $this->assertSame(0, AuditLog::where('created_at', '>', now()->addMinute())->count(), 'Ada audit log di masa depan');
    }

    public function test_audit_log_is_not_duplicated_when_seeding_twice(): void
    {
        $this->seed(DemoSeeder::class);
        $first = AuditLog::count();

        $this->seed(DemoSeeder::class);

        $this->assertGreaterThan(0, $first);
        $this->assertSame($first, AuditLog::count());
    }

    public function test_does_not_overwrite_an_existing_admin_password(): void
    {
        User::factory()->create(['email' => 'admin@cargotrace.com', 'role' => 'admin', 'password' => 'rahasia-lama-123']);

        $this->seed(DemoSeeder::class);

        $this->assertTrue(Hash::check('rahasia-lama-123', User::where('email', 'admin@cargotrace.com')->value('password')));
    }

    public function test_is_skipped_in_production(): void
    {
        $this->app['env'] = 'production';

        // Dipanggil langsung (bukan lewat `db:seed`) karena perintah artisan di production
        // meminta konfirmasi interaktif yang tidak ada di dalam test.
        $this->app->make(DemoSeeder::class)->run();

        $this->assertSame(0, Shipment::count());
        $this->assertSame(0, User::count());
    }
}
