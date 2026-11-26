<?php

namespace App\Services;

use App\Exceptions\ExternalServiceException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Geocoding nama tempat -> koordinat lewat Nominatim (OpenStreetMap).
 *
 * - Dipanggil dari server dengan Laravel HTTP Client (bukan dari browser).
 * - Mengirim User-Agent sesuai kebijakan Nominatim dan men-cache hasil sukses
 *   supaya tidak membanjiri layanan publik.
 * - Hasil kosong -> null. Timeout / error HTTP -> ExternalServiceException.
 */
class GeocodingService
{
    private const CACHE_TTL_HOURS = 24;

    /** @return array{name:string,latitude:float,longitude:float}|null */
    public function search(string $query): ?array
    {
        $query = trim($query);

        if ($query === '') {
            return null;
        }

        $key = 'geocode:' . md5(mb_strtolower($query));

        if (($cached = Cache::get($key)) !== null) {
            return $cached;
        }

        $cfg = config('services.geocoding');

        try {
            $response = Http::timeout((int) $cfg['timeout'])
                ->withUserAgent($cfg['user_agent'])
                ->acceptJson()
                ->get(rtrim($cfg['base_url'], '/') . '/search', [
                    'format'          => 'jsonv2',
                    'limit'           => 1,
                    'accept-language' => 'en',
                    'q'               => $query,
                ]);
        } catch (ConnectionException $e) {
            Log::warning('Geocoding timeout/connection error', ['error' => $e->getMessage()]);

            throw new ExternalServiceException('Layanan geocoding tidak merespons. Coba lagi nanti.', 504, $e);
        }

        if ($response->failed()) {
            Log::warning('Geocoding HTTP error', ['status' => $response->status()]);

            throw new ExternalServiceException('Layanan geocoding sedang bermasalah. Coba lagi nanti.', 502);
        }

        $rows = $response->json();

        if (! is_array($rows) || $rows === []) {
            return null; // tidak ditemukan: bukan error layanan
        }

        $row = $rows[0];

        if (! isset($row['lat'], $row['lon']) || ! is_numeric($row['lat']) || ! is_numeric($row['lon'])) {
            throw new ExternalServiceException('Respons geocoding tidak dapat dibaca.', 502);
        }

        $result = [
            'name'      => (string) ($row['display_name'] ?? $query),
            'latitude'  => round((float) $row['lat'], 7),
            'longitude' => round((float) $row['lon'], 7),
        ];

        Cache::put($key, $result, now()->addHours(self::CACHE_TTL_HOURS));

        return $result;
    }
}
