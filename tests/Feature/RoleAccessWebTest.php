<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MakesCargoData;
use Tests\TestCase;

/** Memastikan pembatasan role di halaman web dicek di SERVER, bukan hanya disembunyikan di menu. */
class RoleAccessWebTest extends TestCase
{
    use RefreshDatabase, MakesCargoData;

    private const ADMIN_ONLY = [
        '/dashboard', '/pengiriman', '/pelacakan', '/fasilitas',
        '/perusahaan-pengiriman', '/pengecualian', '/pengguna', '/audit-log',
    ];

    public function test_guest_is_redirected_to_login(): void
    {
        foreach (array_merge(self::ADMIN_ONLY, ['/operator/dashboard', '/customer/dashboard']) as $url) {
            $this->get($url)->assertRedirect('/login');
        }
    }

    public function test_customer_cannot_open_admin_pages(): void
    {
        $this->actingAs($this->makeUser('customer'));

        foreach (self::ADMIN_ONLY as $url) {
            $this->get($url)->assertForbidden();
        }
    }

    public function test_operator_cannot_open_admin_pages(): void
    {
        $this->actingAs($this->makeUser('operator'));

        foreach (self::ADMIN_ONLY as $url) {
            $this->get($url)->assertForbidden();
        }
    }

    public function test_operator_and_customer_areas_are_separated(): void
    {
        $this->actingAs($this->makeUser('customer'));
        $this->get('/operator/dashboard')->assertForbidden();

        $this->actingAs($this->makeUser('operator'));
        $this->get('/customer/dashboard')->assertForbidden();
    }

    public function test_customer_cannot_modify_users_or_carriers_via_web(): void
    {
        $this->actingAs($this->makeUser('customer'));
        $victim = $this->makeUser('customer');

        $this->delete("/pengguna/{$victim->id}")->assertForbidden();
        $this->post('/perusahaan-pengiriman', ['name' => 'X', 'code' => 'X'])->assertForbidden();
        $this->post('/pengiriman', [])->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $victim->id]);
    }

    public function test_admin_is_not_blocked_by_role_middleware(): void
    {
        $this->actingAs($this->makeUser('admin'));

        foreach (self::ADMIN_ONLY as $url) {
            $this->assertNotSame(403, $this->get($url)->getStatusCode(), "Admin ditolak di {$url}");
        }
    }
}
