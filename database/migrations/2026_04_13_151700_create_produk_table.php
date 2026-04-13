<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kategori')->default('Unggas');
            $table->decimal('stok', 10, 2)->default(0);
            $table->decimal('stok_minimal', 10, 2)->default(0);
            $table->string('satuan')->default('KG');
            $table->decimal('harga', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
