<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\BookingController;

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
    Route::get('/api/bookings/check-availability', [BookingController::class, 'checkAvailability'])->name('bookings.check-availability');
    
    Route::get('/tenants', [TenantController::class, 'index'])->name('tenants');
    Route::post('/tenants', [TenantController::class, 'store'])->name('tenants.store');
    Route::view('/invoices', 'contents.invoices')->name('invoices');
    Route::view('/payments', 'contents.payments')->name('payments');
    Route::resource('rooms', RoomController::class);
    Route::resource('rates', RateController::class);
    Route::view('/electric-readings', 'contents.electric-readings')->name('electric-readings');
    Route::view('/maintenance-logs', 'contents.maintenance-logs')->name('maintenance-logs');
    Route::view('/asset-inventory', 'contents.asset-inventory')->name('asset-inventory');
    Route::view('/user-management', 'contents.user-management')->name('user-management');

});