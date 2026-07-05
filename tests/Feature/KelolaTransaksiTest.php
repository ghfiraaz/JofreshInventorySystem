<?php

namespace Tests\Feature;

use App\Models\Mitra;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KelolaTransaksiTest extends TestCase
{
    use RefreshDatabase;

    private User $kasir;
    private Mitra $mitra;
    private Produk $produk;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kasir = User::factory()->create(['role' => 'Kasir']);
        $this->mitra = Mitra::create([
            'nama' => 'Warung Pojok',
            'kontak' => '081234567890',
            'email' => 'pojok@gmail.com',
            'alamat' => 'Jl. Pojok No. 5',
            'tanggal_jatuh_tempo' => 15,
            'status' => 'Aktif',
        ]);
        $this->produk = Produk::create([
            'nama' => 'Ayam Potong',
            'kategori' => 'Unggas',
            'stok' => 50,
            'stok_minimal' => 5,
            'satuan' => 'Ekor',
            'harga' => 45000,
        ]);
    }

    /**
     * Input Transaksi Valid (Positive)
     * Berhasil menyimpan transaksi penjualan jika data lengkap dan valid.
     */
    public function test_input_transaksi_valid(): void
    {
        $response = $this->actingAs($this->kasir)->postJson('/kasir/transaksi', [
            'mitra_id' => $this->mitra->id,
            'items' => [
                [
                    'produk_id' => $this->produk->id,
                    'jumlah' => 5,
                ]
            ]
        ]);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'Transaksi berhasil disimpan']);
        
        $this->assertDatabaseHas('transaksi', [
            'mitra_id' => $this->mitra->id,
            'total_item' => 5,
            'total_harga' => 45000 * 5,
        ]);
        
        $this->assertEquals(45, $this->produk->refresh()->stok);
    }

    /**
     * Mitra Tidak Dipilih (Negative)
     * Menolak transaksi jika mitra tidak dipilih.
     */
    public function test_mitra_tidak_dipilih(): void
    {
        $response = $this->actingAs($this->kasir)->postJson('/kasir/transaksi', [
            'mitra_id' => '', // Kosong
            'items' => [
                [
                    'produk_id' => $this->produk->id,
                    'jumlah' => 5,
                ]
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('mitra_id');
    }

    /**
     * Produk Tidak Dipilih (Negative)
     * Menolak transaksi jika daftar item kosong.
     */
    public function test_produk_tidak_dipilih(): void
    {
        $response = $this->actingAs($this->kasir)->postJson('/kasir/transaksi', [
            'mitra_id' => $this->mitra->id,
            'items' => [] // Kosong
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('items');
    }

    /**
     * Jumlah Pembelian Kosong (Negative)
     * Menolak jika jumlah pembelian item kosong.
     */
    public function test_jumlah_pembelian_kosong(): void
    {
        $response = $this->actingAs($this->kasir)->postJson('/kasir/transaksi', [
            'mitra_id' => $this->mitra->id,
            'items' => [
                [
                    'produk_id' => $this->produk->id,
                    'jumlah' => '', // Kosong
                ]
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('items.0.jumlah');
    }

    /**
     *  Jumlah Pembelian Berupa Huruf (Negative)
     * Menolak jika jumlah pembelian diisi karakter huruf.
     */
    public function test_jumlah_pembelian_berupa_huruf(): void
    {
        $response = $this->actingAs($this->kasir)->postJson('/kasir/transaksi', [
            'mitra_id' => $this->mitra->id,
            'items' => [
                [
                    'produk_id' => $this->produk->id,
                    'jumlah' => 'sepuluh', // Huruf
                ]
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('items.0.jumlah');
    }

    /**
     * Jumlah Pembelian Mengandung Karakter Khusus (Negative)
     * Menolak jika jumlah pembelian mengandung karakter khusus.
     */
    public function test_jumlah_pembelian_mengandung_karakter_khusus(): void
    {
        $response = $this->actingAs($this->kasir)->postJson('/kasir/transaksi', [
            'mitra_id' => $this->mitra->id,
            'items' => [
                [
                    'produk_id' => $this->produk->id,
                    'jumlah' => '@#$%', // Karakter khusus
                ]
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('items.0.jumlah');
    }

    /**
     *  Perhitungan Total Transaksi Otomatis (Positive)
     * Memastikan total harga dihitung otomatis dari subtotal item yang ditambahkan.
     */
    public function test_perhitungan_total_transaksi_otomatis(): void
    {
        $response = $this->actingAs($this->kasir)->postJson('/kasir/transaksi', [
            'mitra_id' => $this->mitra->id,
            'items' => [
                [
                    'produk_id' => $this->produk->id,
                    'jumlah' => 3,
                ]
            ]
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('transaksi', [
            'mitra_id' => $this->mitra->id,
            'total_harga' => 45000 * 3,
        ]);
    }

    /**
     *  Jumlah Pembelian Melebihi Stok Produk (Negative)
     * Menolak transaksi jika jumlah pembelian melebihi stok yang tersedia.
     */
    public function test_jumlah_pembelian_melebihi_stok_produk(): void
    {
        $response = $this->actingAs($this->kasir)->postJson('/kasir/transaksi', [
            'mitra_id' => $this->mitra->id,
            'items' => [
                [
                    'produk_id' => $this->produk->id,
                    'jumlah' => 100, // Melebihi stok 50
                ]
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJson(['message' => "Stok Ayam Potong tidak cukup. Tersisa: 50 ekor."]);
    }

    /**
     *  Jumlah Pembelian Bernilai 0 (Negative)
     * Menolak jika jumlah pembelian bernilai 0.
     */
    public function test_jumlah_pembelian_bernilai_nol(): void
    {
        $response = $this->actingAs($this->kasir)->postJson('/kasir/transaksi', [
            'mitra_id' => $this->mitra->id,
            'items' => [
                [
                    'produk_id' => $this->produk->id,
                    'jumlah' => 0, // BVA Min - 1
                ]
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('items.0.jumlah');
    }

    /**
     *  Jumlah Pembelian Bernilai 1 (Positive / BVA)
     * Berhasil memproses transaksi jika jumlah pembelian tepat 1.
     */
    public function test_jumlah_pembelian_bernilai_satu(): void
    {
        $response = $this->actingAs($this->kasir)->postJson('/kasir/transaksi', [
            'mitra_id' => $this->mitra->id,
            'items' => [
                [
                    'produk_id' => $this->produk->id,
                    'jumlah' => 1, // BVA Min
                ]
            ]
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('transaksi', [
            'mitra_id' => $this->mitra->id,
            'total_item' => 1,
            'total_harga' => 45000,
        ]);
        $this->assertEquals(49, $this->produk->refresh()->stok);
    }

    /**
     *  Menambahkan Lebih dari Satu Produk dalam Satu Transaksi (Positive)
     * Berhasil jika membeli lebih dari satu jenis produk dalam satu transaksi.
     */
    public function test_menambahkan_lebih_dari_satu_produk_dalam_satu_transaksi(): void
    {
        $produk2 = Produk::create([
            'nama' => 'Ayam Kampung',
            'kategori' => 'Unggas',
            'stok' => 30,
            'stok_minimal' => 5,
            'satuan' => 'Ekor',
            'harga' => 60000,
        ]);

        $response = $this->actingAs($this->kasir)->postJson('/kasir/transaksi', [
            'mitra_id' => $this->mitra->id,
            'items' => [
                [
                    'produk_id' => $this->produk->id,
                    'jumlah' => 2,
                ],
                [
                    'produk_id' => $produk2->id,
                    'jumlah' => 3,
                ]
            ]
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('transaksi', [
            'mitra_id' => $this->mitra->id,
            'total_item' => 5,
            'total_harga' => (45000 * 2) + (60000 * 3),
        ]);
        
        $this->assertEquals(48, $this->produk->refresh()->stok);
        $this->assertEquals(27, $produk2->refresh()->stok);
    }
}
