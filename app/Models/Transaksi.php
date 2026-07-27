<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Model Transaksi
 * Merepresentasikan data transaksi penjualan.
 * Setiap transaksi terhubung ke user (kasir), mitra, dan memiliki banyak item.
 */
class Transaksi extends Model
{
    // Nama tabel di database
    protected $table = 'transaksi';

    // Menggunakan guarded (semua kolom boleh diisi kecuali yang di-guard)
    protected $guarded = [];

    // Casting tipe data
    protected $casts = [
        'jatuh_tempo' => 'date',
        'last_reminder_sent_at' => 'datetime',
        'total_item' => 'integer',
        'total_berat' => 'integer',
    ];

    /**
     * Relasi ke user (kasir) yang membuat transaksi.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke mitra pemilik transaksi.
     */
    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }

    /**
     * Relasi ke item-item dalam transaksi ini.
     * Satu transaksi memiliki banyak item produk.
     */
    public function items()
    {
        return $this->hasMany(TransaksiItem::class);
    }

    /**
     * Menghitung sisa hari menuju jatuh tempo.
     * Return negatif jika sudah lewat jatuh tempo.
     */
    public function sisaHariTempo(): ?int
    {
        if (!$this->jatuh_tempo) return null;
        return (int) now()->startOfDay()->diffInDays($this->jatuh_tempo, false);
    }

    /**
     * Mengecek apakah jatuh tempo dalam zona merah (≤ 3 hari).
     */
    public function isTempoMerah(): bool
    {
        $sisa = $this->sisaHariTempo();
        return $sisa !== null && $sisa <= 3;
    }

    /**
     * Mengecek apakah sudah melewati tanggal jatuh tempo.
     */
    public function isLewatTempo(): bool
    {
        $sisa = $this->sisaHariTempo();
        return $sisa !== null && $sisa < 0;
    }
}
