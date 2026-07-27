<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Item Transaksi
 * Merepresentasikan satu baris item produk dalam sebuah transaksi.
 * Setiap item memiliki relasi ke transaksi induk dan produk terkait.
 */
class TransaksiItem extends Model
{
    // Nama tabel di database
    protected $table = 'transaksi_items';

    // Menggunakan guarded (semua kolom boleh diisi kecuali yang di-guard)
    protected $guarded = [];

    // Casting tipe data
    protected $casts = [
        'jumlah' => 'integer',
    ];

    /**
     * Relasi ke transaksi induk.
     */
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    /**
     * Relasi ke produk yang ada di item ini.
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
