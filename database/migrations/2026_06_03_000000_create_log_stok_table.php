<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_stok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('tipe', ['Masuk', 'Keluar', 'Adjustment Masuk', 'Adjustment Keluar']);
            $table->integer('jumlah');
            $table->integer('stok_sebelum')->default(0);
            $table->integer('stok_sesudah')->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['produk_id', 'created_at']);
            $table->index('tipe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_stok');
    }
};
