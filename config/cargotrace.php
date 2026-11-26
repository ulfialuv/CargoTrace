<?php

/*
|--------------------------------------------------------------------------
| Parameter CargoTrace
|--------------------------------------------------------------------------
| Aturan delay bisa diubah lewat file .env tanpa mengubah kode, mis. untuk
| parameter kelompok 12 / 18 / 24 jam:
|
|   CARGOTRACE_DELAY_THRESHOLD_HOURS=12
|
| Aturan severity (selisih ETA terhadap expected arrival):
|   telat <= low_max_hours                  => LOW
|   low_max_hours < telat <= medium_max_hours => MEDIUM
|   telat > medium_max_hours                => HIGH
*/

return [
    'delay' => [
        // Minimal keterlambatan (jam) agar shipment dianggap DELAYED.
        'threshold_hours'  => (float) env('CARGOTRACE_DELAY_THRESHOLD_HOURS', 0),
        'low_max_hours'    => (float) env('CARGOTRACE_DELAY_LOW_MAX_HOURS', 6),
        'medium_max_hours' => (float) env('CARGOTRACE_DELAY_MEDIUM_MAX_HOURS', 24),

        // Jenis exception yang dibuat otomatis saat shipment terlambat.
        'exception_type'   => env('CARGOTRACE_DELAY_EXCEPTION_TYPE', 'PORT_CONGESTION'),
    ],

    // Password akun demo yang dibuat oleh `php artisan db:seed` (jangan dipakai di production).
    'demo' => [
        'password' => env('DEMO_PASSWORD', 'password'),
    ],
];
