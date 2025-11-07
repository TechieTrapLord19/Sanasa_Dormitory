<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\RoomController;

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
    Route::view('/bookings', 'contents.bookings')->name('bookings');
    Route::view('/tenants', 'contents.tenants')->name('tenants');
    Route::view('/invoices', 'contents.invoices')->name('invoices');
    Route::view('/payments', 'contents.payments')->name('payments');
    Route::resource('rooms', RoomController::class);
    Route::view('/rates', 'contents.rates')->name('rates');
    Route::view('/electric-readings', 'contents.electric-readings')->name('electric-readings');
    Route::view('/maintenance-logs', 'contents.maintenance-logs')->name('maintenance-logs');
    Route::view('/asset-inventory', 'contents.asset-inventory')->name('asset-inventory');
    Route::view('/user-management', 'contents.user-management')->name('user-management');

});