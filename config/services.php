<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

'gnews' => [
    'key' => env('GNEWS_API_KEY'),
],

// Nominatim wajib User-Agent yang mengidentifikasi aplikasi (isi kontak sendiri di .env).
'geocoding' => [
    'base_url'   => env('GEOCODING_BASE_URL', 'https://nominatim.openstreetmap.org'),
    'user_agent' => env('GEOCODING_USER_AGENT', 'CargoTrace/1.0 (student project)'),
    'timeout'    => (int) env('GEOCODING_TIMEOUT', 5),
],

'weather' => [
    'base_url' => env('WEATHER_BASE_URL', 'https://api.open-meteo.com/v1'),
    'timeout'  => (int) env('WEATHER_TIMEOUT', 5),
],


];
