<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MataUangController extends Controller
{
    private $apiUrl = 'https://open.er-api.com/v6/latest';

    public function index()
    {
        return view('mata-uang.index');
    }

    /**
     * Daftar mata uang berdasarkan data kurs API.
     */
    public function currencies()
    {
        try {
            $response = Http::timeout(30)
                ->get($this->apiUrl . '/USD');

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data mata uang.',
                ], 502);
            }

            $data = $response->json();

            if (
                !is_array($data)
                || !isset($data['rates'])
                || !is_array($data['rates'])
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format data API tidak valid.',
                ], 502);
            }

            $currencies = collect($data['rates'])
                ->map(function ($rate, $code) {
                    return [
                        'code' => strtoupper((string) $code),
                        'name' => strtoupper((string) $code),
                    ];
                })
                ->values();

            return response()->json([
                'success' => true,
                'total' => $currencies->count(),
                'currencies' => $currencies,
            ]);

        } catch (\Throwable $e) {
            Log::error('Currency API: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'API mata uang tidak dapat diakses.',
            ], 500);
        }
    }

    /**
     * Kurs berdasarkan mata uang dasar.
     */
    public function api(Request $request)
    {
        $request->validate([
            'base' => [
                'required',
                'string',
                'size:3',
                'regex:/^[A-Za-z]{3}$/',
            ],
        ]);

        $base = strtoupper($request->input('base'));

        try {
            $response = Http::timeout(30)
                ->get($this->apiUrl . '/' . $base);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil kurs ' . $base,
                ], 502);
            }

            $data = $response->json();

            if (
                !is_array($data)
                || !isset($data['rates'])
                || !is_array($data['rates'])
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format kurs tidak valid.',
                ], 502);
            }

            if (
                isset($data['result'])
                && $data['result'] !== 'success'
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'API tidak menemukan mata uang tersebut.',
                ], 422);
            }

            $rates = collect($data['rates'])
                ->map(function ($rate, $code) use ($base) {
                    return [
                        'date' => null,
                        'base' => $base,
                        'quote' => strtoupper((string) $code),
                        'rate' => (float) $rate,
                    ];
                })
                ->filter(function ($item) {
                    return $item['rate'] > 0;
                })
                ->values();

            return response()->json([
                'success' => true,
                'base' => $base,
                'total' => $rates->count(),
                'date' => $data['time_last_update_utc'] ?? null,
                'rates' => $rates,
            ]);

        } catch (\Throwable $e) {
            Log::error('Currency rates: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil kurs mata uang.',
            ], 500);
        }
    }

    /**
     * Konversi mata uang.
     */
    public function convert(Request $request)
    {
        $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999999999',
            ],

            'from' => [
                'required',
                'string',
                'size:3',
                'regex:/^[A-Za-z]{3}$/',
            ],

            'to' => [
                'required',
                'string',
                'size:3',
                'regex:/^[A-Za-z]{3}$/',
            ],
        ]);

        $amount = (float) $request->input('amount');
        $from = strtoupper($request->input('from'));
        $to = strtoupper($request->input('to'));

        try {
            $response = Http::timeout(30)
                ->get($this->apiUrl . '/' . $from);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil kurs ' . $from,
                ], 502);
            }

            $data = $response->json();

            if (
                !is_array($data)
                || !isset($data['rates'][$to])
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kurs ' . $from . ' ke ' . $to . ' tidak tersedia.',
                ], 422);
            }

            $rate = (float) $data['rates'][$to];

            if ($rate <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nilai kurs tidak valid.',
                ], 422);
            }

            return response()->json([
                'success' => true,
                'amount' => $amount,
                'from' => $from,
                'to' => $to,
                'rate' => $rate,
                'result' => $amount * $rate,
                'date' => $data['time_last_update_utc'] ?? null,
            ]);

        } catch (\Throwable $e) {
            Log::error('Currency convert: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Konversi mata uang gagal.',
            ], 500);
        }
    }
}
