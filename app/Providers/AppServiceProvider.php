<?php

namespace App\Providers;

use App\Models\Carrier;
use App\Models\Facility;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use App\Observers\AuditObserver;
use App\Services\AuditLogService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Di hosting dengan HTTPS, semua URL yang dibuat Laravel harus https (hindari mixed content).
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Audit log: catat tambah / ubah / hapus data utama.
        foreach ([Shipment::class, ShipmentException::class, Carrier::class, Facility::class, User::class] as $model) {
            $model::observe(AuditObserver::class);
        }

        // Audit log: login & logout lewat halaman web (sesi).
        Event::listen(Login::class, fn (Login $event) => app(AuditLogService::class)
            ->recordFor($event->user, 'LOGIN', 'Autentikasi', 'Login ke sistem'));

        Event::listen(Logout::class, fn (Logout $event) => app(AuditLogService::class)
            ->recordFor($event->user, 'LOGOUT', 'Autentikasi', 'Logout dari sistem'));
    }
}
