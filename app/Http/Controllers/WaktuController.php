<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DateTimeImmutable;
use DateTimeZone;
use Exception;

class WaktuController extends Controller
{
    /**
     * Halaman utama waktu dunia.
     */
    public function index()
    {
        return view('waktu.index');
    }

    /**
     * Daftar kota dan zona waktu.
     */
    public function locations()
    {
        return response()->json([
            [
                'name' => 'Jakarta',
                'country' => 'Indonesia',
                'timezone' => 'Asia/Jakarta',
                'flag' => '🇮🇩',
            ],
            [
                'name' => 'Makassar',
                'country' => 'Indonesia',
                'timezone' => 'Asia/Makassar',
                'flag' => '🇮🇩',
            ],
            [
                'name' => 'Jayapura',
                'country' => 'Indonesia',
                'timezone' => 'Asia/Jayapura',
                'flag' => '🇮🇩',
            ],
            [
                'name' => 'Singapore',
                'country' => 'Singapore',
                'timezone' => 'Asia/Singapore',
                'flag' => '🇸🇬',
            ],
            [
                'name' => 'Kuala Lumpur',
                'country' => 'Malaysia',
                'timezone' => 'Asia/Kuala_Lumpur',
                'flag' => '🇲🇾',
            ],
            [
                'name' => 'Bangkok',
                'country' => 'Thailand',
                'timezone' => 'Asia/Bangkok',
                'flag' => '🇹🇭',
            ],
            [
                'name' => 'Tokyo',
                'country' => 'Japan',
                'timezone' => 'Asia/Tokyo',
                'flag' => '🇯🇵',
            ],
            [
                'name' => 'Seoul',
                'country' => 'South Korea',
                'timezone' => 'Asia/Seoul',
                'flag' => '🇰🇷',
            ],
            [
                'name' => 'Beijing',
                'country' => 'China',
                'timezone' => 'Asia/Shanghai',
                'flag' => '🇨🇳',
            ],
            [
                'name' => 'Dubai',
                'country' => 'United Arab Emirates',
                'timezone' => 'Asia/Dubai',
                'flag' => '🇦🇪',
            ],
            [
                'name' => 'London',
                'country' => 'United Kingdom',
                'timezone' => 'Europe/London',
                'flag' => '🇬🇧',
            ],
            [
                'name' => 'Paris',
                'country' => 'France',
                'timezone' => 'Europe/Paris',
                'flag' => '🇫🇷',
            ],
            [
                'name' => 'Berlin',
                'country' => 'Germany',
                'timezone' => 'Europe/Berlin',
                'flag' => '🇩🇪',
            ],
            [
                'name' => 'New York',
                'country' => 'United States',
                'timezone' => 'America/New_York',
                'flag' => '🇺🇸',
            ],
            [
                'name' => 'Los Angeles',
                'country' => 'United States',
                'timezone' => 'America/Los_Angeles',
                'flag' => '🇺🇸',
            ],
            [
                'name' => 'Sydney',
                'country' => 'Australia',
                'timezone' => 'Australia/Sydney',
                'flag' => '🇦🇺',
            ],
        ]);
    }

    /**
     * Mendapatkan waktu dari zona waktu tertentu.
     */
    public function time(Request $request)
    {
        $timezone = $request->query('timezone', 'Asia/Jakarta');

        try {
            $zone = new DateTimeZone($timezone);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Zona waktu tidak valid.',
            ], 422);
        }

        $now = new DateTimeImmutable('now', $zone);

        return response()->json([
            'success' => true,
            'timezone' => $timezone,
            'datetime' => $now->format('Y-m-d H:i:s'),
            'date' => $now->format('Y-m-d'),
            'time' => $now->format('H:i:s'),
            'hour' => $now->format('H'),
            'minute' => $now->format('i'),
            'second' => $now->format('s'),
            'day' => $now->format('l'),
            'formatted_date' => $now->format('d F Y'),
            'formatted_time' => $now->format('H:i:s'),
            'utc_offset' => $now->format('P'),
        ]);
    }

    /**
     * Mengubah waktu dari satu zona waktu ke zona waktu lain.
     */
    public function convert(Request $request)
    {
        $validated = $request->validate([
            'from' => ['required', 'string'],
            'to' => ['required', 'string'],
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => ['required', 'date_format:H:i'],
        ]);

        try {
            $fromZone = new DateTimeZone($validated['from']);
            $toZone = new DateTimeZone($validated['to']);

            $sourceDateTime = new DateTimeImmutable(
                $validated['date'] . ' ' . $validated['time'],
                $fromZone
            );

            $convertedDateTime = $sourceDateTime->setTimezone($toZone);

            return response()->json([
                'success' => true,
                'from' => [
                    'timezone' => $validated['from'],
                    'datetime' => $sourceDateTime->format('Y-m-d H:i:s'),
                    'date' => $sourceDateTime->format('Y-m-d'),
                    'time' => $sourceDateTime->format('H:i'),
                    'formatted_date' => $sourceDateTime->format('d F Y'),
                ],
                'to' => [
                    'timezone' => $validated['to'],
                    'datetime' => $convertedDateTime->format('Y-m-d H:i:s'),
                    'date' => $convertedDateTime->format('Y-m-d'),
                    'time' => $convertedDateTime->format('H:i'),
                    'formatted_date' => $convertedDateTime->format('d F Y'),
                ],
                'difference' => $this->formatDifference(
                    $sourceDateTime,
                    $convertedDateTime
                ),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Zona waktu atau waktu yang dimasukkan tidak valid.',
            ], 422);
        }
    }

    /**
     * Menghitung perbedaan offset zona waktu.
     */
    private function formatDifference(
        DateTimeImmutable $from,
        DateTimeImmutable $to
    ): string {
        $fromOffset = $from->getOffset();
        $toOffset = $to->getOffset();

        $difference = ($toOffset - $fromOffset) / 3600;

        if ($difference == 0) {
            return 'Tidak ada perbedaan waktu';
        }

        $hours = abs($difference);
        $direction = $difference > 0 ? 'lebih cepat' : 'lebih lambat';

        return $hours . ' jam ' . $direction;
    }
}
