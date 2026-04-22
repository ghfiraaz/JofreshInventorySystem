<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add mitra_id and total_berat to transaksi
        Schema::table('transaksi', function (Blueprint $table) {
            $table->foreignId('mitra_id')->nullable()->after('user_id')->constrained('mitra')->onDelete('set null');
            $table->decimal('total_berat', 10, 2)->default(0)->after('total_harga');
        });

        // Create transaksi_items table
        Schema::create('transaksi_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksi')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produk')->onDelete('cascade');
            $table->string('nama_produk');
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_items');

        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropForeign(['mitra_id']);
            $table->dropColumn(['mitra_id', 'total_berat']);
        });
    }
};
