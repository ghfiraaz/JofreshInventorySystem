<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $guarded = [];

    protected $casts = [
        'jatuh_tempo' => 'date',
        'last_reminder_sent_at' => 'datetime',
        'total_item' => 'integer',
        'total_berat' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }

    public function items()
    {
        return $this->hasMany(TransaksiItem::class);
    }

    /**
     * Hitung sisa hari menuju jatuh tempo
     */
    public function sisaHariTempo(): ?int
    {
        if (!$this->jatuh_tempo) return null;
        return (int) now()->startOfDay()->diffInDays($this->jatuh_tempo, false);
    }

    /**
     * Apakah jatuh tempo dalam zona merah (≤ 3 hari)
     */
    public function isTempoMerah(): bool
    {
        $sisa = $this->sisaHariTempo();
        return $sisa !== null && $sisa <= 3;
    }

    /**
     * Apakah sudah lewat jatuh tempo
     */
    public function isLewatTempo(): bool
    {
        $sisa = $this->sisaHariTempo();
        return $sisa !== null && $sisa < 0;
    }

    /**
     * Apakah reminder sudah dikirim hari ini
     */
    public function isReminderSentToday(): bool
    {
        if (!$this->last_reminder_sent_at) return false;
        return $this->last_reminder_sent_at->isToday();
    }
}
