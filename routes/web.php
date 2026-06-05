<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\LogStokController;
use App\Http\Controllers\NotificationController;

// Auth routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public payment routes (no auth required)
Route::get('/pembayaran/{token}', [PaymentController::class, 'showUploadForm'])->name('pembayaran.upload');
Route::post('/pembayaran/{token}', [PaymentController::class, 'uploadBuktiBayar'])->name('pembayaran.store');
Route::get('/pembayaran/{token}/pdf', [PaymentController::class, 'downloadTagihanPdf'])->name('pembayaran.pdf');

// Log Stok — semua role bisa lihat (view-only)
Route::middleware(['role:Admin,Kasir,Superadmin'])->group(function () {
    Route::get('/log-stok', [LogStokController::class, 'index'])->name('log-stok.index');
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
});

// Superadmin routes
Route::middleware(['role:Superadmin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/transactions', [TransaksiController::class, 'index']);
    Route::get('/owner/laporan-harian', [DashboardController::class, 'laporanHarian']);
    Route::get('/owner/laporan-transaksi', [DashboardController::class, 'laporanTransaksi']);
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});

// Admin routes
Route::middleware(['role:Admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/produk', [ProdukController::class, 'index']);
    Route::post('/produk', [ProdukController::class, 'store']);
    Route::put('/produk/{id}', [ProdukController::class, 'update']);
    Route::delete('/produk/{id}', [ProdukController::class, 'destroy']);
    Route::post('/produk/{id}/stok', [ProdukController::class, 'tambahStok']);
    Route::post('/penyesuaian-stok', [LogStokController::class, 'storeAdjustment'])->name('admin.penyesuaian-stok.store');
    Route::get('/mitra', [MitraController::class, 'index']);
    Route::post('/mitra', [MitraController::class, 'store']);
    Route::put('/mitra/{id}', [MitraController::class, 'update']);
    Route::delete('/mitra/{id}', [MitraController::class, 'destroy']);
    Route::get('/transactions', [TransaksiController::class, 'index']);
});

// Kasir routes
Route::middleware(['role:Kasir'])->prefix('kasir')->group(function () {
    Route::get('/', fn() => redirect('/kasir/dashboard'));
    Route::get('/dashboard', [KasirController::class, 'dashboard']);
    Route::get('/transaksi', [KasirController::class, 'transaksi']);
    Route::post('/transaksi', [KasirController::class, 'storeTransaksi']);
    Route::get('/riwayat', [KasirController::class, 'riwayat']);
    Route::get('/transaksi/{id}/invoice', [KasirController::class, 'invoice']);
    Route::get('/tagihan', [KasirController::class, 'tagihan']);
    Route::post('/tagihan/bayar', [KasirController::class, 'bayarTagihan']);
    Route::post('/tagihan/send-reminder', [KasirController::class, 'sendReminder']);
    Route::post('/transaksi/{id}/validasi', [KasirController::class, 'validasiBuktiPembayaran']);
    Route::get('/transaksi/{id}/invoice-pdf', [KasirController::class, 'downloadInvoicePdf']);
    Route::post('/tagihan/validasi-mitra', [KasirController::class, 'validasiBuktiPerMitra']);
    Route::get('/bukti-pembayaran/{filename}', [KasirController::class, 'showBuktiPembayaran'])
        ->where('filename', '.*')
        ->name('kasir.bukti-pembayaran');
});
