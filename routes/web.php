<?php

use Illuminate\Support\Facades\Route;
use App\Models\Shipment;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengirimanController;
use App\Http\Controllers\WaktuController;
use App\Http\Controllers\PelacakanController;
use App\Http\Controllers\FasilitasController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\MataUangController;
use App\Http\Controllers\ShipmentExceptionController;
use App\Http\Controllers\CarrierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\OperatorDashboardController;
use App\Http\Controllers\OperatorShipmentController;
use App\Http\Controllers\OperatorExceptionController;
use App\Http\Controllers\OperatorNotificationController;
use App\Http\Controllers\CustomerShipmentController;
use App\Http\Controllers\CustomerTrackingController;
use App\Http\Controllers\CustomerNotificationController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\Api\ExternalDataController;

/*
|--------------------------------------------------------------------------
| HALAMAN UTAMA
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});


/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [LoginController::class, 'login'])
    ->name('login.process');

Route::post('/register', [LoginController::class, 'register'])
    ->name('register.process');


/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| SEMUA HALAMAN YANG MEMBUTUHKAN LOGIN
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Geocoding & cuaca lewat server untuk semua role yang login (dipanggil via fetch dari halaman)
    Route::middleware('throttle:30,1')->prefix('tools')->name('tools.')->group(function () {
        Route::get('/geocode', [ExternalDataController::class, 'geocode'])->name('geocode');
        Route::get('/weather', [ExternalDataController::class, 'weather'])->name('weather');
    });



    /*
    |--------------------------------------------------------------------------
    | DASHBOARD DAN FITUR KHUSUS OPERATOR
    |--------------------------------------------------------------------------
    */

    Route::prefix('operator')
        ->name('operator.')
        ->middleware('role:operator')
        ->group(function () {

            // DASHBOARD OPERATOR
            Route::get('/dashboard', [OperatorDashboardController::class, 'index'])
                ->name('dashboard');


            // MANAJEMEN PENGIRIMAN
            Route::get('/pengiriman', [OperatorShipmentController::class, 'index'])
                ->name('pengiriman.index');

            Route::post('/pengiriman', [OperatorShipmentController::class, 'store'])
                ->name('pengiriman.store');

            Route::put('/pengiriman/{shipment}', [OperatorShipmentController::class, 'update'])
                ->name('pengiriman.update');

            Route::put('/pengiriman/{shipment}/status', [OperatorShipmentController::class, 'updateStatus'])
                ->name('pengiriman.update-status');


            // PELACAKAN OPERATOR
            Route::get('/pelacakan', [OperatorShipmentController::class, 'trackingIndex'])
                ->name('pelacakan.index');

            Route::post('/pengiriman/{shipment}/tracking', [OperatorShipmentController::class, 'addTrackingEvent'])
                ->name('pengiriman.tracking.store');


            // PEMBARUAN STATUS
            Route::get('/pembaruan-status', [OperatorShipmentController::class, 'statusUpdateIndex'])
                ->name('pembaruan-status.index');


            // PENGECUALIAN OPERATOR
            Route::get('/pengecualian', [OperatorExceptionController::class, 'index'])
                ->name('exceptions.index');

            Route::post('/pengecualian', [OperatorExceptionController::class, 'store'])
                ->name('exceptions.store');

            Route::get('/pengecualian/{id}', [OperatorExceptionController::class, 'show'])
                ->name('exceptions.show');

            Route::put('/pengecualian/{id}', [OperatorExceptionController::class, 'update'])
                ->name('exceptions.update');


            // NOTIFIKASI OPERATOR
            Route::get('/notifikasi', [OperatorNotificationController::class, 'index'])
                ->name('notifications.index');

            Route::patch('/notifikasi/{id}/read', [OperatorNotificationController::class, 'markAsRead'])
                ->name('notifications.read');

            Route::patch('/notifikasi/read-all', [OperatorNotificationController::class, 'markAllAsRead'])
                ->name('notifications.read-all');

            Route::delete('/notifikasi/{id}', [OperatorNotificationController::class, 'destroy'])
                ->name('notifications.destroy');

        });



    /*
    |--------------------------------------------------------------------------
    | DASHBOARD DAN FITUR KHUSUS CUSTOMER
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:customer')
        ->prefix('customer')
        ->name('customer.')
        ->group(function () {

            /*
            |--------------------------------------------------------------------------
            | DASHBOARD CUSTOMER
            |--------------------------------------------------------------------------
            */

            Route::get('/dashboard', function () {

                $user = auth()->user();

                // Mengambil pengiriman milik Customer yang sedang login
                $shipments = Shipment::with([
                    'trackingEvents',
                    'exceptions'
                ])
                    ->where('user_id', $user->id)
                    ->latest()
                    ->get();


                // PENCARIAN NOMOR RESI
                $search = trim(request('search', ''));

                $filteredShipments = $shipments;

                if ($search !== '') {

                    $filteredShipments = $shipments
                        ->filter(function ($shipment) use ($search) {

                            return stripos(
                                $shipment->shipment_number ?? '',
                                $search
                            ) !== false;

                        })
                        ->values();

                }


                // TOTAL PENGIRIMAN
                $totalShipments = $shipments->count();


                // PENGIRIMAN DALAM PERJALANAN
                $inTransit = $shipments->filter(function ($shipment) {

                    return in_array(strtoupper($shipment->status ?? ''), [
                        'PICKED_UP',
                        'IN_TRANSIT'
                    ]);

                })->count();


                // PENGIRIMAN TERKIRIM
                $delivered = $shipments->filter(function ($shipment) {

                    return strtoupper($shipment->status ?? '') === 'DELIVERED';

                })->count();


                // PENGIRIMAN TERLAMBAT ATAU BERMASALAH
                $delayed = $shipments->filter(function ($shipment) {

                    $hasOpenException = $shipment->exceptions->contains(function ($exception) {

                        return strtoupper($exception->status ?? '') !== 'RESOLVED';

                    });

                    return strtoupper($shipment->status ?? '') === 'DELAYED'
                        || $hasOpenException;

                })->count();


                // PENGIRIMAN UNTUK RINGKASAN TRACKING
                $selectedShipment = $filteredShipments->first(function ($shipment) {

                    return in_array(strtoupper($shipment->status ?? ''), [
                        'PICKED_UP',
                        'IN_TRANSIT'
                    ]);

                }) ?? $filteredShipments->first();


                // LIMA PENGIRIMAN TERBARU
                $recentShipments = $filteredShipments->take(5);


                // RIWAYAT PENGECUALIAN
                $recentExceptions = $shipments
                    ->flatMap(function ($shipment) {
                        return $shipment->exceptions;
                    })
                    ->sortByDesc('created_at')
                    ->take(5);


                // NOTIFIKASI CUSTOMER
                $notifications = $user->notifications()
                    ->latest()
                    ->take(5)
                    ->get();


                // STATISTIK NOTIFIKASI
                $notificationTotal = $user->notifications()->count();

                $notificationUnread = $user->unreadNotifications()->count();

                $notificationRead = $notificationTotal - $notificationUnread;


                // TAMPILKAN DASHBOARD CUSTOMER
                return view('customer.dashboard', compact(
                    'user',
                    'shipments',
                    'filteredShipments',
                    'selectedShipment',
                    'totalShipments',
                    'inTransit',
                    'delivered',
                    'delayed',
                    'recentShipments',
                    'recentExceptions',
                    'notifications',
                    'notificationTotal',
                    'notificationUnread',
                    'notificationRead',
                    'search'
                ));

            })->name('dashboard');


// HALAMAN PELACAKAN CUSTOMER
Route::get('/pelacakan', [CustomerTrackingController::class, 'index'])
    ->name('pelacakan.index');



/*
|--------------------------------------------------------------------------
| NOTIFIKASI CUSTOMER
|--------------------------------------------------------------------------
*/

Route::get('/notifikasi', [
    CustomerNotificationController::class,
    'index'
])->name('notifikasi.index');

Route::patch('/notifikasi/read-all', [
    CustomerNotificationController::class,
    'markAllRead'
])->name('notifikasi.read-all');

Route::patch('/notifikasi/{id}/read', [
    CustomerNotificationController::class,
    'markRead'
])->name('notifikasi.read');

Route::delete('/notifikasi/{id}', [
    CustomerNotificationController::class,
    'destroy'
])->name('notifikasi.destroy');


            /*
            |--------------------------------------------------------------------------
            | PENGIRIMAN SAYA - CUSTOMER
            |--------------------------------------------------------------------------
            */

            // DAFTAR PENGIRIMAN CUSTOMER
            Route::get('/pengiriman', [
                CustomerShipmentController::class,
                'index'
            ])->name('pengiriman.index');

            /*
|--------------------------------------------------------------------------
| MY PROFILE CUSTOMER
|--------------------------------------------------------------------------
*/

Route::get('/profil', [
    CustomerProfileController::class,
    'index'
])->name('profil.index');

Route::put('/profil', [
    CustomerProfileController::class,
    'update'
])->name('profil.update');

Route::put('/profil/password', [
    CustomerProfileController::class,
    'updatePassword'
])->name('profil.password');

Route::put('/profil/preferences', [
    CustomerProfileController::class,
    'updatePreferences'
])->name('profil.preferences');

Route::post('/profil/alamat', [
    CustomerProfileController::class,
    'storeAddress'
])->name('profil.alamat.store');

Route::put('/profil/alamat/{id}', [
    CustomerProfileController::class,
    'updateAddress'
])->name('profil.alamat.update');

Route::delete('/profil/alamat/{id}', [
    CustomerProfileController::class,
    'destroyAddress'
])->name('profil.alamat.destroy');

Route::patch('/profil/alamat/{id}/utama', [
    CustomerProfileController::class,
    'setPrimaryAddress'
])->name('profil.alamat.utama');

            // DETAIL PENGIRIMAN CUSTOMER
            Route::get('/pengiriman/{id}', [
                CustomerShipmentController::class,
                'show'
            ])->name('pengiriman.show');

        });



    /*
    |--------------------------------------------------------------------------
    | HALAMAN ADMINISTRATOR
    |--------------------------------------------------------------------------
    | Seluruh route di bawah ini hanya untuk Administrator.
    | Operator dan Customer punya route sendiri (prefix operator/ dan customer/).
    | Dicek di server lewat RoleMiddleware, bukan hanya menyembunyikan menu.
    */
    Route::middleware('role:admin')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD UMUM
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');



        /*
        |--------------------------------------------------------------------------
        | PENGIRIMAN UMUM
        |--------------------------------------------------------------------------
        */

        Route::prefix('pengiriman')
            ->name('pengiriman.')
            ->group(function () {

                Route::get('/', [PengirimanController::class, 'index'])
                    ->name('index');

                Route::get('/create', [PengirimanController::class, 'create'])
                    ->name('create');

                Route::post('/', [PengirimanController::class, 'store'])
                    ->name('store');

                Route::get('/{id}/edit', [PengirimanController::class, 'edit'])
                    ->name('edit');

                Route::put('/{id}', [PengirimanController::class, 'update'])
                    ->name('update');

                Route::delete('/{id}', [PengirimanController::class, 'destroy'])
                    ->name('destroy');

                Route::get('/{id}', [PengirimanController::class, 'show'])
                    ->name('show');

            });



        /*
        |--------------------------------------------------------------------------
        | PELACAKAN UMUM
        |--------------------------------------------------------------------------
        */

        Route::get('/pelacakan', [PelacakanController::class, 'index'])
            ->name('pelacakan.index');

        Route::get('/pelacakan/{shipment}', [PelacakanController::class, 'show'])
            ->name('pelacakan.show');



        /*
        |--------------------------------------------------------------------------
        | FASILITAS DAN LOKASI
        |--------------------------------------------------------------------------
        */

        Route::prefix('fasilitas')
            ->name('fasilitas.')
            ->group(function () {

                Route::get('/', [FasilitasController::class, 'index'])
                    ->name('index');

                Route::get('/create', [FasilitasController::class, 'create'])
                    ->name('create');

                Route::post('/', [FasilitasController::class, 'store'])
                    ->name('store');

                Route::get('/{fasilita}/edit', [FasilitasController::class, 'edit'])
                    ->name('edit');

                Route::put('/{fasilita}', [FasilitasController::class, 'update'])
                    ->name('update');

                Route::delete('/{fasilita}', [FasilitasController::class, 'destroy'])
                    ->name('destroy');

                Route::get('/{fasilita}', [FasilitasController::class, 'show'])
                    ->name('show');

            });



        /*
        |--------------------------------------------------------------------------
        | PERUSAHAAN PENGIRIMAN
        |--------------------------------------------------------------------------
        */

        Route::resource('perusahaan-pengiriman', CarrierController::class)
            ->names('carriers');



        /*
        |--------------------------------------------------------------------------
        | PENGECUALIAN ADMIN / UMUM
        |--------------------------------------------------------------------------
        */

        Route::resource('pengecualian', ShipmentExceptionController::class)
            ->names('exceptions');



        /*
        |--------------------------------------------------------------------------
        | PENGGUNA DAN HAK AKSES
        |--------------------------------------------------------------------------
        */

        Route::resource('pengguna', UserController::class)
            ->names('users');



        /*
        |--------------------------------------------------------------------------
        | BERITA
        |--------------------------------------------------------------------------
        */

        Route::get('/berita', [BeritaController::class, 'index'])
            ->name('berita.index');

        Route::get('/berita/api', [BeritaController::class, 'api'])
            ->name('berita.api');



        /*
        |--------------------------------------------------------------------------
        | CUACA
        |--------------------------------------------------------------------------
        */

        Route::get('/cuaca', function () {
            return view('cuaca.index');
        })->name('cuaca.index');



        /*
        |--------------------------------------------------------------------------
        | MATA UANG
        |--------------------------------------------------------------------------
        */

        Route::prefix('mata-uang')
            ->name('mata-uang.')
            ->group(function () {

                Route::get('/', [MataUangController::class, 'index'])
                    ->name('index');

                Route::get('/currencies', [MataUangController::class, 'currencies'])
                    ->name('currencies');

                Route::get('/api', [MataUangController::class, 'api'])
                    ->name('api');

                Route::get('/convert', [MataUangController::class, 'convert'])
                    ->name('convert');

            });



        /*
        |--------------------------------------------------------------------------
        | WAKTU DUNIA
        |--------------------------------------------------------------------------
        */

        Route::prefix('waktu')
            ->name('waktu.')
            ->group(function () {

                Route::get('/', [WaktuController::class, 'index'])
                    ->name('index');

                Route::get('/locations', [WaktuController::class, 'locations'])
                    ->name('locations');

                Route::get('/time', [WaktuController::class, 'time'])
                    ->name('time');

                Route::get('/convert', [WaktuController::class, 'convert'])
                    ->name('convert');

            });



        /*
        |--------------------------------------------------------------------------
        | NOTIFIKASI UMUM
        |--------------------------------------------------------------------------
        */

        Route::prefix('notifikasi')
            ->name('notifications.')
            ->group(function () {

                Route::get('/', [NotificationController::class, 'index'])
                    ->name('index');

                Route::patch('/read-all', [NotificationController::class, 'markAllRead'])
                    ->name('readAll');

                Route::patch('/{id}/read', [NotificationController::class, 'markRead'])
                    ->name('read');

                Route::delete('/{id}', [NotificationController::class, 'destroy'])
                    ->name('destroy');

            });



        /*
        |--------------------------------------------------------------------------
        | AUDIT LOG - KHUSUS ADMINISTRATOR
        |--------------------------------------------------------------------------
        */

        Route::get('/audit-log', [AuditLogController::class, 'index'])
            ->name('audit-logs.index');

        Route::get('/audit-log/export', [AuditLogController::class, 'export'])
            ->name('audit-logs.export');

        Route::get('/audit-log/{id}', [AuditLogController::class, 'show'])
            ->name('audit-logs.show');


    });

});
