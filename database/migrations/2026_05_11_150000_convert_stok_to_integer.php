<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change stok and stok_minimal from decimal to integer in produk table
        DB::statement('ALTER TABLE produk MODIFY stok INT DEFAULT 0');
        DB::statement('ALTER TABLE produk MODIFY stok_minimal INT DEFAULT 0');

        // Change total_item and total_berat to integer in transaksi table
        DB::statement('ALTER TABLE transaksi MODIFY total_berat INT DEFAULT 0');

        // Change jumlah to integer in transaksi_items table
        DB::statement('ALTER TABLE transaksi_items MODIFY jumlah INT DEFAULT 0');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE produk MODIFY stok DECIMAL(10,2) DEFAULT 0');
        DB::statement('ALTER TABLE produk MODIFY stok_minimal DECIMAL(10,2) DEFAULT 0');
        DB::statement('ALTER TABLE transaksi MODIFY total_berat DECIMAL(10,2) DEFAULT 0');
        DB::statement('ALTER TABLE transaksi_items MODIFY jumlah DECIMAL(10,2) DEFAULT 0');
    }
};
