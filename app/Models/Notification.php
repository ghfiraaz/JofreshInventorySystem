<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Model Notifikasi
 * Mengelola notifikasi sistem untuk setiap pengguna.
 * Mendukung notifikasi stok rendah, bukti pembayaran, laporan harian, dan jatuh tempo.
 */
class Notification extends Model
{
    // Kolom yang boleh diisi secara massal
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'source_id',
        'is_read',
    ];

    // Casting tipe data
    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Relasi ke user pemilik notifikasi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor: menampilkan waktu notifikasi dalam format relatif.
     * Contoh: "2 jam yang lalu"
     */
    public function getTimeAgoAttribute(): string
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    /**
     * Mengirim notifikasi ke semua pengguna dengan role tertentu.
     * Mengecek duplikasi berdasarkan type dan source_id.
     */
    public static function sendToRole(string $role, string $title, string $message, string $type = null, string $sourceId = null)
    {
        // Ambil semua user dengan role yang ditentukan
        $users = User::where('role', $role)->get();
        foreach ($users as $user) {
            // Cek duplikasi jika ada type dan sourceId
            if ($type && $sourceId) {
                $exists = self::where('user_id', $user->id)
                    ->where('type', $type)
                    ->where('source_id', $sourceId)
                    ->exists();
                if ($exists) {
                    continue;
                }
            }

            // Buat notifikasi baru
            self::create([
                'user_id'   => $user->id,
                'title'     => $title,
                'message'   => $message,
                'type'      => $type,
                'source_id' => $sourceId,
                'is_read'   => false,
            ]);
        }
    }

    /**
     * Trigger notifikasi stok rendah untuk Admin.
     * Dipanggil ketika stok produk menyentuh atau melewati batas minimal.
     */
    public static function triggerLowStockAlert(Produk $produk)
    {
        if ($produk->stok <= $produk->stok_minimal) {
            $title = 'Stok Menipis (Low Stock Alert)';
            $message = "Produk {$produk->nama} menyentuh batas minimum. Stok saat ini: {$produk->stok} ekor (Batas minimal: {$produk->stok_minimal} ekor).";
            self::sendToRole('Admin', $title, $message, 'low_stock', $produk->id);
        }
    }

    /**
     * Trigger notifikasi bukti pembayaran untuk Kasir.
     * Dipanggil ketika mitra mengunggah bukti pembayaran.
     */
    public static function triggerBuktiPembayaran(Mitra $mitra)
    {
        $title = "[{$mitra->nama}] mengirimkan bukti pembayaran, segera cek untuk validasi pembayaran";
        $message = "Mitra {$mitra->nama} telah mengunggah bukti pembayaran. Segera lakukan validasi tagihan.";
        self::sendToRole('Kasir', $title, $message, 'bukti_pembayaran', $mitra->id . '_' . time());
    }

    /**
     * Trigger notifikasi laporan penjualan harian untuk Superadmin.
     * Dipanggil ketika ada transaksi yang selesai dibayar.
     */
    public static function triggerLaporanPenjualan()
    {
        $today = today();

        // Hitung total pendapatan hari ini
        $totalPendapatan = Transaksi::whereDate('created_at', $today)
            ->where('status_pembayaran', 'Sudah Dibayar')
            ->sum('total_harga');

        $title = 'Laporan Penjualan Hari Ini';
        $message = "Laporan penjualan hari ini terupdate. Total pendapatan masuk per hari ini: Rp " . number_format($totalPendapatan, 0, ',', '.') . ".";
        self::sendToRole('Superadmin', $title, $message, 'laporan_harian', 'laporan_' . $today->toDateString() . '_' . time());
    }

    /**
     * Pengecekan dinamis jatuh tempo H-3 (dijalankan saat notifikasi diambil).
     * Membuat notifikasi untuk Kasir jika ada tagihan yang mendekati jatuh tempo.
     */
    public static function checkJatuhTempoReminders()
    {
        // Ambil semua transaksi belum dibayar yang jatuh tempo dalam 3 hari ke depan
        $mendesakList = Transaksi::with('mitra')
            ->where('status_pembayaran', 'Belum Dibayar')
            ->whereNotNull('jatuh_tempo')
            ->whereBetween('jatuh_tempo', [now()->toDateString(), now()->addDays(3)->toDateString()])
            ->get();

        foreach ($mendesakList as $transaksi) {
            // Hitung sisa hari
            $daysRemaining = (int) now()->startOfDay()->diffInDays(Carbon::parse($transaksi->jatuh_tempo), false);
            $dayText = $daysRemaining === 0 ? "hari ini" : "dalam {$daysRemaining} hari";
            
            $title = "[{$transaksi->mitra->nama}] H-{$daysRemaining}, Segera Kirim Email Reminder!";
            
            $message = "Tagihan {$transaksi->no_transaksi} untuk Mitra {$transaksi->mitra->nama} jatuh tempo {$dayText} (Tgl " . Carbon::parse($transaksi->jatuh_tempo)->format('d-m-Y') . "). Segera kirimkan email tagihan.";

            // Kirim notifikasi ke semua Kasir
            self::sendToRole('Kasir', $title, $message, 'jatuh_tempo', $transaksi->id);
        }
    }
}
