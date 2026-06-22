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

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/db-debug-check-123', function() {
    return [
        'connection' => config('database.default'),
        'host' => config('database.connections.mysql.host'),
        'database' => config('database.connections.mysql.database'),
        'username' => config('database.connections.mysql.username'),
        'password' => config('database.connections.mysql.password'),
        'customers_count' => Illuminate\Support\Facades\DB::table('customers')->count(),
    ];
});

Route::get('/', function () {
    return redirect('/login');
});

/*
|--------------------------------------------------------------------------
| Protected ERP Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/activity-logs', [DashboardController::class, 'activityLogs'])->name('activity-logs.index');
    Route::get('/notifications', [DashboardController::class, 'getNotifications'])->name('notifications.index');

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