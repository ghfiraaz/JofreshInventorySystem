<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add email, tanggal_jatuh_tempo, and payment_token to mitra table
        Schema::table('mitra', function (Blueprint $table) {
            $table->string('email')->nullable()->after('kontak');
            $table->unsignedTinyInteger('tanggal_jatuh_tempo')->default(1)->after('alamat');
            $table->string('payment_token')->nullable()->unique()->after('status');
        });

        // Add bukti_pembayaran, jatuh_tempo, last_reminder_sent_at to transaksi table
        Schema::table('transaksi', function (Blueprint $table) {
            $table->string('bukti_pembayaran')->nullable()->after('status_pembayaran');
            $table->date('jatuh_tempo')->nullable()->after('bukti_pembayaran');
            $table->timestamp('last_reminder_sent_at')->nullable()->after('jatuh_tempo');
        });
    }

    public function down(): void
    {
        Schema::table('mitra', function (Blueprint $table) {
            $table->dropColumn(['email', 'tanggal_jatuh_tempo', 'payment_token']);
        });

        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn(['bukti_pembayaran', 'jatuh_tempo', 'last_reminder_sent_at']);
        });
    }
};
