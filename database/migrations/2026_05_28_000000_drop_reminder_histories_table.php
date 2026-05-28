<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('reminder_histories');
    }

    public function down(): void
    {
        // Tabel reminder_histories sudah tidak digunakan.
        // Jika perlu rollback, jalankan migration asli:
        // 2026_05_20_000000_create_reminder_histories_table.php
    }
};
