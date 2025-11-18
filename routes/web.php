<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\ElectricReadingController;
use App\Http\Controllers\MaintenanceLogController;

//default page to login
Route::get('/', [IndexController::class, 'index'])->name('home');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Protected content routes
Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'contents.dashboard')->name('dashboard');

    // Bookings routes
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{id}', [BookingController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{id}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{id}', [BookingController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/{id}', [BookingController::class, 'destroy'])->name('bookings.destroy');
    Route::post('/bookings/{id}/checkin', [BookingController::class, 'checkin'])->name('bookings.checkin');
    Route::post('/bookings/{id}/checkout', [BookingController::class, 'checkout'])->name('bookings.checkout');
    Route::post('/bookings/{id}/renew', [BookingController::class, 'generateRenewalInvoice'])->name('bookings.renew');
    Route::post('/bookings/{id}/electricity', [BookingController::class, 'generateElectricityInvoice'])->name('bookings.electricity');
    Route::post('/bookings/{id}/refund', [RefundController::class, 'store'])->name('bookings.refund');
    Route::get('/api/bookings/check-availability', [BookingController::class, 'checkAvailability'])->name('bookings.check-availability');

    Route::get('/tenants', [TenantController::class, 'index'])->name('tenants');
    Route::post('/tenants', [TenantController::class, 'store'])->name('tenants.store');
    Route::get('/tenants/{id}', [TenantController::class, 'show'])->name('tenants.show');
    Route::get('/tenants/{id}/edit', [TenantController::class, 'edit'])->name('tenants.edit');
    Route::put('/tenants/{id}', [TenantController::class, 'update'])->name('tenants.update');
    Route::post('/tenants/{id}/archive', [TenantController::class, 'archive'])->name('tenants.archive');
    Route::post('/tenants/{id}/activate', [TenantController::class, 'activate'])->name('tenants.activate');
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{id}/receipt', [PaymentController::class, 'showReceipt'])->name('payments.receipt');
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs');
    Route::resource('rooms', RoomController::class);
    Route::resource('rates', RateController::class);
    Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
    Route::put('/assets/{id}', [AssetController::class, 'update'])->name('assets.update');
    Route::get('/electric-readings', [ElectricReadingController::class, 'index'])->name('electric-readings');
    Route::post('/electric-readings', [ElectricReadingController::class, 'store'])->name('electric-readings.store');
    Route::get('/maintenance-logs', [MaintenanceLogController::class, 'index'])->name('maintenance-logs');
    Route::post('/maintenance-logs', [MaintenanceLogController::class, 'store'])->name('maintenance-logs.store');
    Route::put('/maintenance-logs/{id}', [MaintenanceLogController::class, 'update'])->name('maintenance-logs.update');
    Route::get('/asset-inventory', [AssetController::class, 'index'])->name('asset-inventory');
    Route::get('/user-management', [UserController::class, 'index'])->name('user-management');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

});