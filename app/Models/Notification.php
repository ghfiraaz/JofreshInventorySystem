<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'source_id',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Relationship to the user who owns this notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor for formatted time.
     */
    public function getTimeAgoAttribute(): string
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    /**
     * Send notification to all users of a specific role (checks for duplicates).
     */
    public static function sendToRole(string $role, string $title, string $message, string $type = null, string $sourceId = null)
    {
        $users = User::where('role', $role)->get();
        foreach ($users as $user) {
            if ($type && $sourceId) {
                $exists = self::where('user_id', $user->id)
                    ->where('type', $type)
                    ->where('source_id', $sourceId)
                    ->exists();
                if ($exists) {
                    continue;
                }
            }

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
     * Low stock alert trigger.
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
     * Partner uploaded proof of payment trigger.
     */
    public static function triggerBuktiPembayaran(Mitra $mitra)
    {
        $title = "[{$mitra->nama}] mengirimkan bukti pembayaran, segera cek untuk validasi pembayaran";
        $message = "Mitra {$mitra->nama} telah mengunggah bukti pembayaran. Segera lakukan validasi tagihan.";
        self::sendToRole('Kasir', $title, $message, 'bukti_pembayaran', $mitra->id . '_' . time());
    }

    /**
     * Daily sales report trigger (completed transaction).
     */
    public static function triggerLaporanPenjualan()
    {
        $today = today();
        $totalPendapatan = Transaksi::whereDate('created_at', $today)
            ->where('status_pembayaran', 'Sudah Dibayar')
            ->sum('total_harga');

        $title = 'Laporan Penjualan Hari Ini';
        $message = "Laporan penjualan hari ini terupdate. Total pendapatan masuk per hari ini: Rp " . number_format($totalPendapatan, 0, ',', '.') . ".";
        self::sendToRole('Superadmin', $title, $message, 'laporan_harian', 'laporan_' . $today->toDateString() . '_' . time());
    }

    /**
     * Dynamic check for H-3 due dates (run on notification fetch).
     */
    public static function checkJatuhTempoReminders()
    {
        $mendesakList = Transaksi::with('mitra')
            ->where('status_pembayaran', 'Belum Dibayar')
            ->whereNotNull('jatuh_tempo')
            ->whereBetween('jatuh_tempo', [now()->toDateString(), now()->addDays(3)->toDateString()])
            ->get();

        foreach ($mendesakList as $transaksi) {
            $daysRemaining = (int) now()->startOfDay()->diffInDays(Carbon::parse($transaksi->jatuh_tempo), false);
            $dayText = $daysRemaining === 0 ? "hari ini" : "dalam {$daysRemaining} hari";
            
            $title = "[{$transaksi->mitra->nama}] H-{$daysRemaining}, Segera Kirim Email Reminder!";
            
            $message = "Tagihan {$transaksi->no_transaksi} untuk Mitra {$transaksi->mitra->nama} jatuh tempo {$dayText} (Tgl " . Carbon::parse($transaksi->jatuh_tempo)->format('d-m-Y') . "). Segera kirimkan email tagihan.";
            self::sendToRole('Kasir', $title, $message, 'jatuh_tempo', $transaksi->id);
        }
    }
}
