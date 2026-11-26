<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\MakesCargoData;
use Tests\TestCase;

/** Geocoding & cuaca dipanggil dari server; semua respons layanan eksternal di-fake. */
class ExternalDataApiTest extends TestCase
{
    use RefreshDatabase, MakesCargoData;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    private function nominatim(?array $rows = null): array
    {
        return ['nominatim.openstreetmap.org/*' => Http::response($rows ?? [[
            'lat' => '3.7826', 'lon' => '98.6932', 'display_name' => 'Belawan, Medan, Indonesia',
        ]])];
    }

    private function openMeteo(?array $body = null): array
    {
        return ['api.open-meteo.com/*' => Http::response($body ?? ['current' => [
            'time' => '2026-11-26T10:00', 'temperature_2m' => 29.4, 'precipitation' => 0.2,
            'wind_speed_10m' => 12.5, 'weather_code' => 63,
        ]])];
    }

    public function test_geocode_requires_authentication(): void
    {
        $this->getJson('/api/geocode?q=Belawan')->assertUnauthorized();
        $this->getJson('/api/weather?lat=1&lng=1')->assertUnauthorized();
    }

    public function test_geocode_returns_coordinates_and_sends_user_agent(): void
    {
        Http::fake($this->nominatim());
        Sanctum::actingAs($this->makeUser('customer'));

        $this->getJson('/api/geocode?q=Belawan')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.latitude', 3.7826)
            ->assertJsonPath('data.longitude', 98.6932);

        Http::assertSent(fn (Request $r) => $r->hasHeader('User-Agent')
            && str_contains($r->header('User-Agent')[0], 'CargoTrace')
            && $r['q'] === 'Belawan');
    }

    public function test_geocode_is_cached(): void
    {
        Http::fake($this->nominatim());
        Sanctum::actingAs($this->makeUser('operator'));

        $this->getJson('/api/geocode?q=Belawan')->assertOk();
        $this->getJson('/api/geocode?q=belawan')->assertOk();

        Http::assertSentCount(1);
    }

    public function test_geocode_empty_result_returns_404(): void
    {
        Http::fake($this->nominatim([]));
        Sanctum::actingAs($this->makeUser('admin'));

        $this->getJson('/api/geocode?q=zzzzzz')
            ->assertNotFound()
            ->assertJsonPath('success', false);
    }

    public function test_geocode_timeout_returns_504_without_leaking_details(): void
    {
        Http::fake(function () {
            throw new ConnectionException('cURL error 28: secret-host timed out');
        });
        Sanctum::actingAs($this->makeUser('admin'));

        $this->getJson('/api/geocode?q=Belawan')
            ->assertStatus(504)
            ->assertJsonPath('success', false)
            ->assertJsonMissingExact(['message' => 'cURL error 28: secret-host timed out']);
    }

    public function test_geocode_upstream_error_returns_502(): void
    {
        Http::fake(['nominatim.openstreetmap.org/*' => Http::response('boom', 500)]);
        Sanctum::actingAs($this->makeUser('admin'));

        $this->getJson('/api/geocode?q=Belawan')->assertStatus(502);
    }

    public function test_geocode_malformed_payload_returns_502(): void
    {
        Http::fake($this->nominatim([['display_name' => 'No coordinates']]));
        Sanctum::actingAs($this->makeUser('admin'));

        $this->getJson('/api/geocode?q=Belawan')->assertStatus(502);
    }

    public function test_geocode_validates_query(): void
    {
        Http::fake();
        Sanctum::actingAs($this->makeUser('admin'));

        $this->getJson('/api/geocode')->assertStatus(422)->assertJsonValidationErrors('q');
        Http::assertNothingSent();
    }

    public function test_weather_returns_current_conditions(): void
    {
        Http::fake($this->openMeteo());
        Sanctum::actingAs($this->makeUser('customer'));

        $this->getJson('/api/weather?lat=3.78&lng=98.69')
            ->assertOk()
            ->assertJsonPath('data.temperature_c', 29.4)
            ->assertJsonPath('data.summary', 'Rain')
            ->assertJsonPath('data.note', 'Informational only. Weather is not used to predict delays.');
    }

    public function test_weather_validates_coordinates(): void
    {
        Http::fake();
        Sanctum::actingAs($this->makeUser('admin'));

        $this->getJson('/api/weather?lat=120&lng=98')->assertStatus(422)->assertJsonValidationErrors('lat');
        $this->getJson('/api/weather?lat=10')->assertStatus(422)->assertJsonValidationErrors('lng');
        Http::assertNothingSent();
    }

    public function test_weather_timeout_returns_504(): void
    {
        Http::fake(function () {
            throw new ConnectionException('timed out');
        });
        Sanctum::actingAs($this->makeUser('admin'));

        $this->getJson('/api/weather?lat=1&lng=1')->assertStatus(504)->assertJsonPath('success', false);
    }

    public function test_weather_upstream_error_returns_502(): void
    {
        Http::fake(['api.open-meteo.com/*' => Http::response([], 503)]);
        Sanctum::actingAs($this->makeUser('admin'));

        $this->getJson('/api/weather?lat=2&lng=2')->assertStatus(502);
    }

    public function test_weather_empty_data_returns_502(): void
    {
        Http::fake($this->openMeteo(['current' => []]));
        Sanctum::actingAs($this->makeUser('admin'));

        $this->getJson('/api/weather?lat=3&lng=3')->assertStatus(502);
    }

    public function test_session_users_can_use_the_web_endpoints_and_guests_cannot(): void
    {
        Http::fake($this->nominatim());

        $this->getJson('/tools/geocode?q=Belawan')->assertUnauthorized();

        $this->actingAs($this->makeUser('customer'))
            ->getJson('/tools/geocode?q=Belawan')
            ->assertOk()
            ->assertJsonPath('data.name', 'Belawan, Medan, Indonesia');
    }
}
