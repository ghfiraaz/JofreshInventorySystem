<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Mitra extends Model
{
    protected $table = 'mitra';

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

    protected static function booted()
    {
        static::creating(function ($mitra) {
            if (empty($mitra->payment_token)) {
                $mitra->payment_token = Str::uuid()->toString();
            }
        });
    }

    public function transaksi()
    {
        return $this->hasMany(\App\Models\Transaksi::class);
    }
}
