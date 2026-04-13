<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';

    protected $fillable = [
        'nama',
        'kategori',
        'stok',
        'stok_minimal',
        'satuan',
        'harga',
    ];

    /**
     * Compute the stock status based on stok vs stok_minimal.
     */
    public function getStatusAttribute(): string
    {
        if ($this->stok <= 0) return 'Stok Habis';
        if ($this->stok < $this->stok_minimal) return 'Stok Rendah';
        return 'Tersedia';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'Stok Habis'  => 'badge-stok-habis',
            'Stok Rendah' => 'badge-stok-rendah',
            default       => 'badge-tersedia',
        };
    }

    public function getHargaFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }
}
