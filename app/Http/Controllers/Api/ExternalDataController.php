<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ExternalServiceException;
use App\Services\GeocodingService;
use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Proxy tipis ke layanan eksternal. Validasi di sini, logika & penanganan
 * error ada di GeocodingService / WeatherService.
 */
class ExternalDataController extends ApiController
{
    public function __construct(private GeocodingService $geocoding, private WeatherService $weather)
    {
    }

    public function geocode(Request $request): JsonResponse
    {
        $data = $request->validate(['q' => ['required', 'string', 'min:2', 'max:200']]);

        try {
            $result = $this->geocoding->search($data['q']);
        } catch (ExternalServiceException $e) {
            return $this->fail($e->getMessage(), $e->status());
        }

        return $result === null
            ? $this->fail('Lokasi tidak ditemukan.', 404)
            : $this->ok($result);
    }

    public function weather(Request $request): JsonResponse
    {
        $data = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        try {
            $result = $this->weather->current((float) $data['lat'], (float) $data['lng']);
        } catch (ExternalServiceException $e) {
            return $this->fail($e->getMessage(), $e->status());
        }

        return $this->ok($result);
    }
}
