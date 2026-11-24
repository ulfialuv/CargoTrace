<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\MakesCargoData;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase, MakesCargoData;

    public function test_register_creates_customer_and_returns_token(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Budi', 'email' => 'budi@example.com',
            'password' => 'password123', 'password_confirmation' => 'password123',
        ])
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.role', 'customer')
            ->assertJsonStructure(['data' => ['token', 'token_type', 'user' => ['id', 'email']]]);
    }

    public function test_register_cannot_choose_admin_role(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Hacker', 'email' => 'h@example.com', 'role' => 'admin',
            'password' => 'password123', 'password_confirmation' => 'password123',
        ])->assertCreated()->assertJsonPath('data.user.role', 'customer');
    }

    public function test_register_validates_input(): void
    {
        $this->postJson('/api/v1/auth/register', ['email' => 'bukan-email'])
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_login_success_returns_token(): void
    {
        $this->makeUser('operator', ['email' => 'op@example.com']); // password factory = "password"

        $this->postJson('/api/v1/auth/login', ['email' => 'op@example.com', 'password' => 'password'])
            ->assertOk()
            ->assertJsonPath('data.user.role', 'operator')
            ->assertJsonStructure(['data' => ['token']]);
    }

    public function test_login_with_wrong_password_returns_401(): void
    {
        $this->makeUser('admin', ['email' => 'a@example.com']);

        $this->postJson('/api/v1/auth/login', ['email' => 'a@example.com', 'password' => 'salah'])
            ->assertStatus(401)
            ->assertJsonPath('success', false);
    }

    public function test_me_requires_authentication(): void
    {
        $this->getJson('/api/v1/auth/me')
            ->assertStatus(401)
            ->assertJsonPath('success', false);
    }

    public function test_me_returns_current_user_with_valid_token(): void
    {
        $this->makeUser('customer', ['email' => 'c@example.com']);

        $token = $this->postJson('/api/v1/auth/login', ['email' => 'c@example.com', 'password' => 'password'])
            ->json('data.token');

        $this->withToken($token)->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.email', 'c@example.com');
    }

    public function test_logout_deletes_the_token(): void
    {
        $this->makeUser('customer', ['email' => 'c@example.com']);

        $token = $this->postJson('/api/v1/auth/login', ['email' => 'c@example.com', 'password' => 'password'])
            ->json('data.token');

        $this->assertSame(1, DB::table('personal_access_tokens')->count());

        $this->withToken($token)->postJson('/api/v1/auth/logout')->assertOk();

        $this->assertSame(0, DB::table('personal_access_tokens')->count());
    }
}
