<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarrierController;
use App\Http\Controllers\Api\ExceptionController;
use App\Http\Controllers\Api\ExternalDataController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\ShipmentController;
use App\Http\Controllers\Api\TrackingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CargoTrace REST API
|--------------------------------------------------------------------------
| Path utama mengikuti brief:  /api/login, /api/shipments, ...
| Alias berversi yang sama:    /api/v1/...
|
| Autentikasi : Laravel Sanctum (Bearer token)
| Otorisasi   : dicek di server lewat middleware `role:` + scope data per user
|   - admin    : semua endpoint
|   - operator : baca semua, buat/ubah shipment, status, tracking, exception
|   - customer : hanya membaca shipment/tracking/exception miliknya sendiri
*/

$api = function () {

    // ---------- Auth ----------
    Route::post('register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register'])->middleware('throttle:10,1');
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    });

    // ---------- Wajib login ----------
    Route::middleware(['auth:sanctum', 'throttle:120,1'])
        ->where(['id' => '[0-9]+'])
        ->group(function () {

            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('user', [AuthController::class, 'me']);
            Route::post('auth/logout', [AuthController::class, 'logout']);
            Route::get('auth/me', [AuthController::class, 'me']);

            // Semua role yang login
            Route::get('shipments', [ShipmentController::class, 'index']);
            Route::get('shipments/{id}', [ShipmentController::class, 'show']);
            Route::get('shipments/{id}/tracking', [TrackingController::class, 'index']);
            Route::get('shipments/{id}/exceptions', [ExceptionController::class, 'forShipment']);
            Route::get('exceptions', [ExceptionController::class, 'index']);
            Route::get('exceptions/{id}', [ExceptionController::class, 'show']);
            Route::get('carriers', [CarrierController::class, 'index']);
            Route::get('carriers/{id}', [CarrierController::class, 'show']);
            Route::get('locations', [LocationController::class, 'index']);
            Route::get('locations/{id}', [LocationController::class, 'show']);

            // Layanan eksternal lewat server (geocoding & cuaca)
            Route::middleware('throttle:30,1')->group(function () {
                Route::get('geocode', [ExternalDataController::class, 'geocode']);
                Route::get('weather', [ExternalDataController::class, 'weather']);
            });

            // Admin + operator
            Route::middleware('role:admin,operator')->group(function () {
                Route::post('shipments', [ShipmentController::class, 'store']);
                Route::match(['put', 'patch'], 'shipments/{id}', [ShipmentController::class, 'update']);
                Route::patch('shipments/{id}/status', [ShipmentController::class, 'updateStatus']);
                Route::post('shipments/{id}/tracking', [TrackingController::class, 'store']);
                Route::post('shipments/{id}/exceptions', [ExceptionController::class, 'storeForShipment']);
                Route::post('exceptions', [ExceptionController::class, 'store']);
                Route::match(['put', 'patch'], 'exceptions/{id}', [ExceptionController::class, 'update']);
            });

            // Admin saja
            Route::middleware('role:admin')->group(function () {
                Route::delete('shipments/{id}', [ShipmentController::class, 'destroy']);

                Route::post('carriers', [CarrierController::class, 'store']);
                Route::put('carriers/{id}', [CarrierController::class, 'update']);
                Route::delete('carriers/{id}', [CarrierController::class, 'destroy']);

                Route::post('locations', [LocationController::class, 'store']);
                Route::put('locations/{id}', [LocationController::class, 'update']);
                Route::delete('locations/{id}', [LocationController::class, 'destroy']);
            });
        });
};

Route::prefix('v1')->group($api);   // /api/v1/...
Route::group([], $api);             // /api/...  (sesuai brief)
