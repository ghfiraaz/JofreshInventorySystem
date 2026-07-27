<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Riwayat Reminder
 * Mencatat histori pengiriman email reminder pembayaran ke mitra.
 * Menyimpan status berhasil/gagal beserta detail invoice dan periode.
 */
class ReminderHistory extends Model
{
    // Nama tabel di database
    protected $table = 'reminder_histories';

    // Kolom yang boleh diisi secara massal
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

    // Casting tipe data
    protected $casts = [
        'tanggal_pengiriman' => 'datetime',
        'periode_awal'       => 'date',
        'periode_akhir'      => 'date',
        'total_tagihan'      => 'integer',
        'jumlah_transaksi'   => 'integer',
    ];

    /**
     * Relasi ke mitra yang menerima reminder.
     */
    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }

    /**
     * Relasi ke user yang mengirim reminder.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
