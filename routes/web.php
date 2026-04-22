<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\KasirController;

// Auth routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Superadmin routes
Route::middleware(['role:Superadmin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/transactions', [TransaksiController::class, 'index']);

    // User CRUD
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});

// Admin routes
Route::middleware(['role:Admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    Route::get('/produk', [ProdukController::class, 'index']);
    Route::post('/produk/{id}/stok', [ProdukController::class, 'tambahStok']);

    Route::get('/mitra', [MitraController::class, 'index']);
    Route::post('/mitra', [MitraController::class, 'store']);
    Route::put('/mitra/{id}', [MitraController::class, 'update']);
    Route::delete('/mitra/{id}', [MitraController::class, 'destroy']);

    Route::get('/pembayaran-mitra', fn() => view('admin.pembayaran-mitra'));
});

// Kasir routes
Route::middleware(['role:Kasir'])->prefix('kasir')->group(function () {
    Route::get('/', fn() => redirect('/kasir/dashboard'));
    Route::get('/dashboard', [KasirController::class, 'dashboard']);
    Route::get('/transaksi', [KasirController::class, 'transaksi']);
    Route::post('/transaksi', [KasirController::class, 'storeTransaksi']);
    Route::get('/riwayat', [KasirController::class, 'riwayat']);
    Route::get('/tagihan', [KasirController::class, 'tagihan']);
    Route::post('/tagihan/bayar', [KasirController::class, 'bayarTagihan']);
});
