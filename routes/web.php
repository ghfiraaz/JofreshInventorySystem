<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanTransaksiController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\LogStokController;
use App\Http\Controllers\NotificationController;

// ===========================
// Route Autentikasi (hanya untuk tamu/belum login)
// ===========================
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');        // Menampilkan halaman login
    Route::post('/login', [AuthController::class, 'login']);                      // Memproses login
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');       // Memproses logout

// ===========================
// Route Pembayaran Publik (tanpa login, diakses oleh mitra)
// ===========================
Route::get('/pembayaran/{token}', [PaymentController::class, 'showUploadForm'])->name('pembayaran.upload');   // Menampilkan halaman upload bukti pembayaran
Route::post('/pembayaran/{token}', [PaymentController::class, 'uploadBuktiBayar'])->name('pembayaran.store'); // Memproses upload bukti pembayaran

// ===========================
// Route Bersama (Admin, Kasir, Superadmin)
// ===========================
Route::middleware(['role:Admin,Kasir,Superadmin'])->group(function () {
    Route::get('/log-stok', [LogStokController::class, 'index'])->name('log-stok.index');                     // Menampilkan riwayat log stok
    Route::get('/notifications', [NotificationController::class, 'index']);                                     // Mengambil daftar notifikasi
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);                     // Menandai notifikasi sebagai dibaca
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);                   // Menandai semua notifikasi sebagai dibaca
    Route::get('/transaksi/{id}/invoice-pdf', [TransaksiController::class, 'downloadInvoicePdf']);              // Mengunduh invoice PDF
    Route::get('/bukti-pembayaran/{filename}', [KasirController::class, 'showBuktiPembayaran'])                 // Menampilkan file bukti pembayaran
        ->where('filename', '.*')
        ->name('kasir.bukti-pembayaran');
});

// ===========================
// Route Superadmin
// ===========================
Route::middleware(['role:Superadmin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);                                            // Menampilkan dashboard Superadmin
    Route::get('/transactions', [TransaksiController::class, 'index']);                                         // Menampilkan riwayat semua transaksi
    Route::get('/owner/laporan-harian', [LaporanTransaksiController::class, 'laporanHarian']);                  // Menampilkan laporan penjualan harian
    Route::get('/owner/laporan-transaksi', [LaporanTransaksiController::class, 'laporanTransaksi']);            // Menampilkan laporan transaksi bulanan
    Route::get('/users', [UserController::class, 'index']);                                                     // Menampilkan daftar pengguna
    Route::post('/users', [UserController::class, 'store']);                                                    // Menambah pengguna baru
    Route::put('/users/{id}', [UserController::class, 'update']);                                               // Mengubah data pengguna
    Route::delete('/users/{id}', [UserController::class, 'destroy']);                                           // Menghapus pengguna
});

// ===========================
// Route Admin
// ===========================
Route::middleware(['role:Admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);                                            // Menampilkan dashboard Admin
    Route::get('/produk', [ProdukController::class, 'index']);                                                  // Menampilkan daftar produk
    Route::post('/produk', [ProdukController::class, 'store']);                                                 // Menambah produk baru
    Route::put('/produk/{id}', [ProdukController::class, 'update']);                                            // Mengubah data produk
    Route::delete('/produk/{id}', [ProdukController::class, 'destroy']);                                        // Menghapus produk
    Route::post('/produk/{id}/stok', [ProdukController::class, 'tambahStok']);                                  // Menambah stok produk
    Route::post('/penyesuaian-stok', [LogStokController::class, 'storeAdjustment'])->name('admin.penyesuaian-stok.store');  // Menyimpan penyesuaian stok
    Route::get('/mitra', [MitraController::class, 'index']);                                                    // Menampilkan daftar mitra
    Route::post('/mitra', [MitraController::class, 'store']);                                                   // Menambah mitra baru
    Route::put('/mitra/{id}', [MitraController::class, 'update']);                                              // Mengubah data mitra
    Route::delete('/mitra/{id}', [MitraController::class, 'destroy']);                                          // Menghapus mitra
    Route::get('/transactions', [TransaksiController::class, 'index']);                                         // Menampilkan riwayat transaksi Admin
});

// ===========================
// Route Kasir
// ===========================
Route::middleware(['role:Kasir'])->prefix('kasir')->group(function () {
    Route::get('/', fn() => redirect('/kasir/dashboard'));                                                      // Redirect ke dashboard kasir
    Route::get('/dashboard', [KasirController::class, 'dashboard']);                                            // Menampilkan dashboard kasir
    Route::get('/transaksi', [KasirController::class, 'transaksi']);                                            // Menampilkan halaman POS (transaksi baru)
    Route::post('/transaksi', [KasirController::class, 'storeTransaksi']);                                      // Menyimpan transaksi baru
    Route::get('/riwayat', [TransaksiController::class, 'index']);                                              // Menampilkan riwayat transaksi kasir
    Route::get('/transaksi/{id}/invoice', [KasirController::class, 'invoice']);                                  // Menampilkan invoice digital
    Route::get('/tagihan', [KasirController::class, 'tagihan']);                                                // Menampilkan halaman tagihan
    Route::post('/tagihan/bayar', [KasirController::class, 'bayarTagihan']);                                    // Memproses pembayaran tagihan
    Route::post('/tagihan/send-reminder', [KasirController::class, 'sendReminder']);                            // Mengirim email reminder pembayaran
    Route::post('/transaksi/{id}/validasi', [KasirController::class, 'validasiBuktiPembayaran']);               // Memvalidasi bukti pembayaran (per transaksi)
    Route::post('/tagihan/validasi-mitra', [KasirController::class, 'validasiBuktiPerMitra']);                  // Memvalidasi bukti pembayaran (per mitra, bulk)
});
