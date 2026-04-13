<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

// Auth routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Superadmin routes
Route::middleware(['role:Superadmin'])->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'));
    Route::get('/transactions', fn() => view('transactions'));

    // User CRUD
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});

// Admin routes
Route::middleware(['role:Admin'])->prefix('admin')->group(function () {
    Route::get('/produk', fn() => view('admin.produk'));
    Route::get('/stok-masuk', fn() => view('admin.stok-masuk'));
    Route::get('/mitra', fn() => view('admin.mitra'));
    Route::get('/pembayaran-mitra', fn() => view('admin.pembayaran-mitra'));
});

// Kasir routes
Route::middleware(['role:Kasir'])->prefix('kasir')->group(function () {
    Route::get('/', fn() => view('kasir.dashboard'));
    Route::get('/transaksi', fn() => view('kasir.transaksi'));
    Route::get('/riwayat', fn() => view('kasir.riwayat'));
});
