<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminder_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mitra_id')->constrained('mitra')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('email_penerima');
            $table->dateTime('tanggal_pengiriman');
            $table->enum('status', ['berhasil', 'gagal'])->default('berhasil');
            $table->text('error_message')->nullable();
            $table->string('invoice_filename')->nullable();
            $table->date('periode_awal');
            $table->date('periode_akhir');
            $table->unsignedBigInteger('total_tagihan')->default(0);
            $table->timestamps();

            $table->index(['mitra_id', 'tanggal_pengiriman']);
            $table->index('tanggal_pengiriman');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminder_histories');
    }
};
