<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\TrackingController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/login');
});

if (config('app.demo')) {
    Route::get('/seed-guest', function () {
        try {
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
            $output = \Illuminate\Support\Facades\Artisan::output();
            return 'Seeder run completed!<br><br>Console Output:<br><pre>' . htmlspecialchars($output) . '</pre>';
        } catch (\Throwable $e) {
            return 'Error seeding database: ' . $e->getMessage();
        }
    });
}

Route::get('/share/invoices/{id}', [InvoiceController::class, 'printPublic'])->name('invoices.print-public');

/*
|--------------------------------------------------------------------------
| Protected ERP Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/activity-logs', [DashboardController::class, 'activityLogs'])->name('activity-logs.index');
    Route::get('/notifications', [DashboardController::class, 'getNotifications'])->name('notifications.index');

    Route::get('/db-diagnostic', function () {
        if (auth()->user()->isDriver()) {
            abort(403);
        }
        try {
            $dbName = \Illuminate\Support\Facades\DB::connection()->getDatabaseName();
            $dbHost = config('database.connections.mysql.host');
            
            $users = \App\Models\User::all();
            $userList = "";
            foreach ($users as $u) {
                $userList .= "- " . htmlspecialchars($u->email) . " (Role: " . htmlspecialchars($u->role) . ")<br>";
            }

            return "Database Name: <strong>" . htmlspecialchars($dbName) . "</strong><br>" .
                   "Database Host: <strong>" . htmlspecialchars($dbHost) . "</strong><br>" .
                   "Connection: <strong>Success!</strong><br><br>" .
                   "Registered Users:<br>" . $userList;
        } catch (\Throwable $e) {
            return "Database Connection Failed: " . $e->getMessage();
        }
    })->name('db-diagnostic');

    // Switch roles in demo mode
    if (config('app.demo')) {
        Route::get('/switch-to-driver', function () {
            $driverEmail = config('app.demo') ? 'driverguest@gmail.com' : 'driver@gmail.com';
            $driverUser = \App\Models\User::where('email', $driverEmail)->first();
            if ($driverUser) {
                auth()->login($driverUser);
                return redirect()->route('deliveries.index')->with('success', 'Berhasil beralih ke akun Driver');
            }
            return redirect()->back()->with('error', 'Akun Driver tidak ditemukan');
        })->name('switch-to-driver');

        Route::get('/switch-to-guest', function () {
            $guestUser = \App\Models\User::where('email', 'guest@gmail.com')->first();
            if ($guestUser) {
                auth()->login($guestUser);
                return redirect()->route('dashboard')->with('success', 'Berhasil beralih ke akun Guest');
            }
            return redirect()->back()->with('error', 'Akun Guest tidak ditemukan');
        })->name('switch-to-guest');
    }

    // ERP Modules
    Route::resource('customers', CustomerController::class);
    Route::resource('products', ProductController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('deliveries', DeliveryController::class);
    Route::resource('payments', PaymentController::class);
    Route::resource('invoices', InvoiceController::class);
    Route::resource('drivers', DriverController::class);
    Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
    Route::get('/reports/export-csv', [DashboardController::class, 'exportCSV'])->name('reports.export-csv');
    Route::post('/deliveries/{delivery}/confirm-arrival', [DeliveryController::class, 'confirmArrival'])->name('deliveries.confirm-arrival');

    // Suppliers & Purchases
    Route::resource('suppliers', SupplierController::class);
    Route::resource('purchases', PurchaseController::class);
    Route::post('/purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');
    Route::get('/purchases/{purchase}/print', [PurchaseController::class, 'print'])->name('purchases.print');

    // Live Tracking
    Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');
    Route::get('/api/driver-coordinates', [TrackingController::class, 'getCoordinates'])->name('tracking.coordinates');
    Route::post('/api/deliveries/{delivery}/location', [TrackingController::class, 'updateLocation'])->name('tracking.update-location');

    // Profile
    Route::get('/my-profile', function () {
        return view('profile.index');
    })->name('profile.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';