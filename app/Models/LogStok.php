<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogStok extends Model
{
    protected $table = 'log_stok';

    protected $fillable = [
        'produk_id',
        'user_id',
        'tipe',
        'jumlah',
        'stok_sebelum',
        'stok_sesudah',
        'keterangan',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'stok_sebelum' => 'integer',
        'stok_sesudah' => 'integer',
    ];

    /**
     * Relasi ke produk.
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    /**
     * Relasi ke user yang melakukan aksi.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor: "Nama (Role)" — e.g. "Fira (Admin)"
     */
    public function getOlehAttribute(): string
    {
        if ($this->user) {
            return $this->user->name . ' (' . $this->user->role . ')';
        }
        return '-';
    }

    /**
     * Accessor: badge CSS class per tipe transaksi.
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
