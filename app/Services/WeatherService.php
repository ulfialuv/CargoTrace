<?php

namespace App\Services;

use App\Exceptions\ExternalServiceException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Cuaca saat ini di sebuah koordinat (Open-Meteo, tanpa API key).
 *
 * Cuaca hanya konteks informasi. Data ini TIDAK dipakai untuk memprediksi
 * keterlambatan; aturan delay tetap murni dari EtaService.
 */
class WeatherService
{
    private const CACHE_TTL_MINUTES = 10;

    private const CODES = [
        0 => 'Clear sky', 1 => 'Mainly clear', 2 => 'Partly cloudy', 3 => 'Overcast',
        45 => 'Fog', 48 => 'Rime fog',
        51 => 'Light drizzle', 53 => 'Drizzle', 55 => 'Dense drizzle',
        61 => 'Light rain', 63 => 'Rain', 65 => 'Heavy rain',
        71 => 'Light snow', 73 => 'Snow', 75 => 'Heavy snow',
        80 => 'Rain showers', 81 => 'Rain showers', 82 => 'Violent rain showers',
        95 => 'Thunderstorm', 96 => 'Thunderstorm with hail', 99 => 'Thunderstorm with hail',
    ];

    /** @return array<string,mixed> */
    public function current(float $latitude, float $longitude): array
    {
        $latitude  = round($latitude, 2);
        $longitude = round($longitude, 2);
        $key       = "weather:{$latitude}:{$longitude}";

        if (($cached = Cache::get($key)) !== null) {
            return $cached;
        }

        $cfg = config('services.weather');

        try {
            $response = Http::timeout((int) $cfg['timeout'])
                ->acceptJson()
                ->get(rtrim($cfg['base_url'], '/') . '/forecast', [
                    'latitude'  => $latitude,
                    'longitude' => $longitude,
                    'current'   => 'temperature_2m,precipitation,wind_speed_10m,weather_code',
                    'timezone'  => 'UTC',
                ]);
        } catch (ConnectionException $e) {
            Log::warning('Weather timeout/connection error', ['error' => $e->getMessage()]);

            throw new ExternalServiceException('Layanan cuaca tidak merespons. Coba lagi nanti.', 504, $e);
        }

        if ($response->failed()) {
            Log::warning('Weather HTTP error', ['status' => $response->status()]);

            throw new ExternalServiceException('Layanan cuaca sedang bermasalah. Coba lagi nanti.', 502);
        }

        $current = $response->json('current');

        if (! is_array($current) || ! isset($current['temperature_2m']) || ! is_numeric($current['temperature_2m'])) {
            throw new ExternalServiceException('Data cuaca untuk lokasi ini tidak tersedia.', 502);
        }

        $code = isset($current['weather_code']) ? (int) $current['weather_code'] : null;

        $result = [
            'latitude'         => $latitude,
            'longitude'        => $longitude,
            'temperature_c'    => (float) $current['temperature_2m'],
            'precipitation_mm' => isset($current['precipitation']) ? (float) $current['precipitation'] : null,
            'wind_kmh'         => isset($current['wind_speed_10m']) ? (float) $current['wind_speed_10m'] : null,
            'weather_code'     => $code,
            'summary'          => $code !== null ? (self::CODES[$code] ?? 'Unknown') : null,
            'observed_at'      => $current['time'] ?? null,
            'note'             => 'Informational only. Weather is not used to predict delays.',
        ];

        Cache::put($key, $result, now()->addMinutes(self::CACHE_TTL_MINUTES));

        return $result;
    }
}
