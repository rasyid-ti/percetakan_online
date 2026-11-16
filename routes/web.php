<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route(auth()->user()->role->dashboardRoute())
        : redirect()->route('login');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/masuk', [AuthController::class, 'login']);
    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register']);
});

Route::post('/keluar', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:customer'])
    ->prefix('pelanggan')->name('customer.')
    ->group(function () {
        Route::view('/dashboard', 'customer.dashboard')->name('dashboard');
    });

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
    });

Route::middleware(['auth', 'role:operator'])
    ->prefix('operator')->name('operator.')
    ->group(function () {
        Route::view('/dashboard', 'operator.dashboard')->name('dashboard');
    });

Route::middleware(['auth', 'role:owner'])
    ->prefix('owner')->name('owner.')
    ->group(function () {
        Route::view('/dashboard', 'owner.dashboard')->name('dashboard');
    });