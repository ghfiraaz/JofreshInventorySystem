<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Log Stok
 * Merepresentasikan catatan perubahan stok produk.
 * Mencatat setiap aktivitas stok: Masuk, Keluar, Adjustment Masuk, dan Adjustment Keluar.
 */
class LogStok extends Model
{
    // Nama tabel di database
    protected $table = 'log_stok';

    // Kolom yang boleh diisi secara massal
    protected $fillable = [
        'produk_id',
        'user_id',
        'tipe',
        'jumlah',
        'stok_sebelum',
        'stok_sesudah',
        'keterangan',
    ];

    // Casting tipe data
    protected $casts = [
        'jumlah' => 'integer',
        'stok_sebelum' => 'integer',
        'stok_sesudah' => 'integer',
    ];

    /**
     * Relasi ke produk yang stoknya berubah.
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    /**
     * Relasi ke user yang melakukan perubahan stok.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor: menampilkan nama user beserta role-nya.
     * Contoh: "Fira (Admin)"
     */
    public function getOlehAttribute(): string
    {
        if ($this->user) {
            return $this->user->name . ' (' . $this->user->role . ')';
        }
        return '-';
    }

    /**
     * Accessor: menentukan CSS class badge berdasarkan tipe log stok.
     */
    public function getTipeBadgeAttribute(): string
    {
        return match ($this->tipe) {
            'Masuk'             => 'badge-masuk',
            'Keluar'            => 'badge-keluar',
            'Adjustment Masuk'  => 'badge-adj-masuk',
            'Adjustment Keluar' => 'badge-adj-keluar',
            default             => '',
        };
    }
}
