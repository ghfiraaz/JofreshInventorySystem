<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Produk
 * Merepresentasikan data produk dalam sistem inventori.
 * Memiliki accessor untuk status stok, badge, dan format harga.
 */
class Produk extends Model
{
    // Nama tabel di database
    protected $table = 'produk';

    // Kolom yang boleh diisi secara massal
    protected $fillable = [
        'nama',
        'kategori',
        'stok',
        'stok_minimal',
        'satuan',
        'harga',
    ];

    // Casting tipe data
    protected $casts = [
        'stok' => 'integer',
        'stok_minimal' => 'integer',
    ];

    /**
     * Menentukan status stok berdasarkan perbandingan stok vs stok_minimal.
     * Return: 'Stok Habis', 'Stok Rendah', atau 'Tersedia'.
     */
    public function getStatusAttribute(): string
    {
        if ($this->stok <= 0) return 'Stok Habis';
        if ($this->stok < $this->stok_minimal) return 'Stok Rendah';
        return 'Tersedia';
    }

    /**
     * Menentukan CSS class badge berdasarkan status stok.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'Stok Habis'  => 'badge-stok-habis',
            'Stok Rendah' => 'badge-stok-rendah',
            default       => 'badge-tersedia',
        };
    }

    /**
     * Menampilkan harga dalam format Rupiah (contoh: Rp 15.000).
     */
    public function getHargaFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }
}
