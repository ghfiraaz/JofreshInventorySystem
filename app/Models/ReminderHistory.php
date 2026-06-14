<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReminderHistory extends Model
{
    protected $table = 'reminder_histories';

    protected $fillable = [
        'mitra_id',
        'user_id',
        'email_penerima',
        'tanggal_pengiriman',
        'status',
        'error_message',
        'invoice_filename',
        'periode_awal',
        'periode_akhir',
        'total_tagihan',
        'jumlah_transaksi',
    ];

    protected $casts = [
        'tanggal_pengiriman' => 'datetime',
        'periode_awal'       => 'date',
        'periode_akhir'      => 'date',
        'total_tagihan'      => 'integer',
        'jumlah_transaksi'   => 'integer',
    ];

    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
