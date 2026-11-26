<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Shipment;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\MakesCargoData;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase, MakesCargoData;

    public function test_creating_a_shipment_is_logged_with_who_and_what(): void
    {
        $operator = $this->makeUser('operator');
        Sanctum::actingAs($operator);

        $id = $this->postJson('/api/shipments', $this->shipmentPayload())->assertCreated()->json('data.id');

        $log = AuditLog::where('action', 'CREATE')->where('module', 'Pengiriman')->firstOrFail();

        $this->assertSame($operator->id, $log->user_id);
        $this->assertSame('operator', $log->user_role);
        $this->assertSame('Shipment', $log->subject_type);
        $this->assertSame($id, (int) $log->subject_id);
        $this->assertSame(Shipment::find($id)->shipment_number, $log->reference_number);
        $this->assertArrayHasKey('customer_name', $log->new_values);
        $this->assertStringContainsString('Menambah pengiriman', $log->description);
    }

    public function test_status_change_is_logged_with_old_and_new_status(): void
    {
        $shipment = $this->makeShipment();
        Sanctum::actingAs($this->makeUser('operator'));

        $this->patchJson("/api/shipments/{$shipment->id}/status", ['status' => 'BOOKED'])->assertOk();

        $log = AuditLog::where('action', 'STATUS CHANGE')->firstOrFail();

        $this->assertSame('CREATED', $log->old_values['status']);
        $this->assertSame('BOOKED', $log->new_values['status']);
        $this->assertStringContainsString('CREATED → BOOKED', $log->description);
        $this->assertSame($shipment->shipment_number, $log->reference_number);
    }

    public function test_editing_and_deleting_a_shipment_are_logged(): void
    {
        $shipment = $this->makeShipment();
        Sanctum::actingAs($this->makeUser('admin'));

        $this->putJson("/api/shipments/{$shipment->id}", $this->shipmentPayload(['notes' => 'Catatan baru']))->assertOk();

        $update = AuditLog::where('action', 'UPDATE')->where('module', 'Pengiriman')->firstOrFail();
        $this->assertSame('Catatan baru', $update->new_values['notes']);

        $this->deleteJson("/api/shipments/{$shipment->id}")->assertOk();

        $delete = AuditLog::where('action', 'DELETE')->firstOrFail();
        $this->assertSame($shipment->shipment_number, $delete->old_values['shipment_number']);
    }

    public function test_exception_actions_are_logged(): void
    {
        $shipment = $this->makeShipment();
        Sanctum::actingAs($this->makeUser('operator'));

        $id = $this->postJson("/api/shipments/{$shipment->id}/exceptions", [
            'type' => 'CUSTOMS_HOLD', 'location' => 'Bea Cukai', 'occurred_at' => now()->toDateTimeString(),
            'severity' => 'HIGH', 'description' => 'Dokumen ditahan.',
        ])->assertCreated()->json('data.id');

        $this->assertTrue(AuditLog::where('action', 'CREATE')->where('module', 'Pengecualian')->exists());

        $this->patchJson("/api/exceptions/{$id}", ['status' => 'RESOLVED'])->assertOk();

        $log = AuditLog::where('action', 'EXCEPTION UPDATE')->firstOrFail();
        $this->assertSame('RESOLVED', $log->new_values['status']);
        $this->assertSame($shipment->shipment_number, $log->reference_number);
    }

    public function test_master_data_changes_are_logged(): void
    {
        Sanctum::actingAs($this->makeUser('admin'));

        $id = $this->postJson('/api/carriers', [
            'name' => 'Carrier Uji', 'code' => 'UJI-1', 'service_type' => 'Sea Freight', 'status' => 'ACTIVE',
        ])->assertCreated()->json('data.id');

        $this->assertTrue(AuditLog::where('action', 'CREATE')->where('module', 'Perusahaan Pengiriman')->where('reference_number', 'UJI-1')->exists());

        $this->deleteJson("/api/carriers/{$id}")->assertOk();
        $this->assertTrue(AuditLog::where('action', 'DELETE')->where('module', 'Perusahaan Pengiriman')->exists());
    }

    public function test_user_changes_are_logged_but_the_password_is_never_stored(): void
    {
        $admin    = $this->makeUser('admin');
        $customer = $this->makeUser('customer');
        Sanctum::actingAs($admin);

        $customer->update(['name' => 'Nama Baru', 'password' => 'rahasia-baru-123']);

        $log = AuditLog::where('action', 'USER MANAGEMENT')->latest('id')->firstOrFail();

        $this->assertSame('***', $log->new_values['password']);
        $this->assertSame('***', $log->old_values['password']);
        $this->assertStringNotContainsString('rahasia-baru-123', json_encode($log->new_values));
        $this->assertSame('Nama Baru', $log->new_values['name']);
    }

    public function test_updating_only_last_login_is_not_logged(): void
    {
        $admin    = $this->makeUser('admin');
        $customer = $this->makeUser('customer');
        Sanctum::actingAs($admin);

        $customer->forceFill(['last_login_at' => now()])->save();

        $this->assertSame(0, AuditLog::count());
    }

    public function test_actions_without_a_logged_in_user_are_not_logged(): void
    {
        $this->makeShipment(); // dibuat tanpa login (seperti seeder / artisan)

        $this->assertSame(0, AuditLog::count());
    }

    public function test_api_login_and_logout_are_logged(): void
    {
        $user = $this->makeUser('operator', ['email' => 'op@example.com']);

        $token = $this->postJson('/api/login', ['email' => 'op@example.com', 'password' => 'password'])
            ->assertOk()->json('data.token');

        $this->assertTrue(AuditLog::where('action', 'LOGIN')->where('user_id', $user->id)->exists());

        $this->withToken($token)->postJson('/api/logout')->assertOk();

        $this->assertTrue(AuditLog::where('action', 'LOGOUT')->where('user_id', $user->id)->exists());

        // login memperbarui last_login_at, tetapi itu bukan "perubahan pengguna"
        $this->assertSame(0, AuditLog::where('action', 'USER MANAGEMENT')->count());
    }

    public function test_web_login_and_logout_events_are_logged(): void
    {
        $user = $this->makeUser('customer');

        event(new Login('web', $user, false));
        event(new Logout('web', $user));

        $this->assertTrue(AuditLog::where('action', 'LOGIN')->where('user_id', $user->id)->exists());
        $this->assertTrue(AuditLog::where('action', 'LOGOUT')->where('user_id', $user->id)->exists());
    }

    public function test_audit_page_shows_entries_to_admin_only(): void
    {
        $admin = $this->makeUser('admin');

        AuditLog::create([
            'user_id' => $admin->id, 'user_name' => $admin->name, 'user_email' => $admin->email, 'user_role' => 'admin',
            'action' => 'CREATE', 'module' => 'Pengiriman', 'description' => 'Menambah pengiriman CT-TES-777',
        ]);

        $this->actingAs($admin)->get('/audit-log')->assertOk()->assertSee('CT-TES-777');

        $this->actingAs($this->makeUser('operator'))->get('/audit-log')->assertForbidden();
    }
}
