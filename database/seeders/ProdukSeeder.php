<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produk;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'nama' => 'Ayam Broiler',
                'kategori' => 'Ayam',
                'stok_minimal' => 30,
                'harga' => 35000,
                'satuan' => 'Ekor',
            ],
            [
                'nama' => 'Ayam Pejantan',
                'kategori' => 'Ayam',
                'stok_minimal' => 20,
                'harga' => 30000,
                'satuan' => 'Ekor',
            ],
            [
                'nama' => 'Ayam Kampung',
                'kategori' => 'Ayam',
                'stok_minimal' => 50,
                'harga' => 45000,
                'satuan' => 'Ekor',
            ],
            [
                'nama' => 'Bebek',
                'kategori' => 'Bebek',
                'stok_minimal' => 40,
                'harga' => 40000,
                'satuan' => 'Ekor',
            ],
        ];

        foreach ($products as $product) {
            Produk::updateOrCreate(
                ['nama' => $product['nama']], // Unique identifier
                [
                    'kategori' => $product['kategori'],
                    'stok_minimal' => $product['stok_minimal'],
                    'harga' => $product['harga'],
                    'satuan' => $product['satuan'],
                    'stok' => 50, // Default stok for testing
                ]
            );
        }
        
        // Remove any other products that are not these 4
        Produk::whereNotIn('nama', collect($products)->pluck('nama'))->delete();
    }
}
