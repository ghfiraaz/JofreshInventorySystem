<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Model Mitra
 * Merepresentasikan data mitra (pelanggan/toko) dalam sistem.
 * Setiap mitra memiliki payment token unik untuk akses halaman upload bukti pembayaran.
 */
class Mitra extends Model
{
    // Nama tabel di database
    protected $table = 'mitra';

    // Kolom yang boleh diisi secara massal
    protected $fillable = [
        'nama',
        'kontak',
        'email',
        'alamat',
        'tanggal_jatuh_tempo',
        'status',
        'payment_token',
        'payment_upload_locked',
    ];

    /**
     * Event saat mitra baru dibuat.
     * Otomatis generate payment token (UUID) jika belum ada.
     */
    protected static function booted()
    {
        static::creating(function ($mitra) {
            if (empty($mitra->payment_token)) {
                $mitra->payment_token = Str::uuid()->toString();
            }
        });
    }

    /**
     * Relasi ke transaksi milik mitra ini.
     * Satu mitra memiliki banyak transaksi.
     */
    public function transaksi()
    {
        return $this->hasMany(\App\Models\Transaksi::class);
    }
}
