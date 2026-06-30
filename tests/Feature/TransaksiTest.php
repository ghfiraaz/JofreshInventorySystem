<?php

namespace Tests\Feature;

use App\Models\Mitra;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransaksiTest extends TestCase
{
    use RefreshDatabase;

    private User $kasir;
    private Mitra $mitra;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kasir = User::factory()->create(['role' => 'Kasir']);
        $this->mitra = Mitra::create([
            'nama' => 'Warung Test', 'alamat' => 'Jl. Test No.1',
            'status' => 'Aktif', 'tanggal_jatuh_tempo' => 25,
        ]);
    }

    // ========================================================
    // HALAMAN TRANSAKSI
    // ========================================================

    public function test_kasir_bisa_akses_halaman_input_transaksi(): void
    {
        $response = $this->actingAs($this->kasir)->get('/kasir/transaksi');
        $response->assertStatus(200);
        $response->assertSee('Transaksi Penjualan');
    }

    // ========================================================
    // STORE TRANSAKSI — BERHASIL
    // ========================================================

    public function test_kasir_bisa_membuat_transaksi_baru(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Potong', 'kategori' => 'Unggas',
            'stok' => 100, 'stok_minimal' => 10, 'satuan' => 'Ekor', 'harga' => 45000,
        ]);

        $response = $this->actingAs($this->kasir)->postJson('/kasir/transaksi', [
            'mitra_id' => $this->mitra->id,
            'items'    => [
                ['produk_id' => $produk->id, 'jumlah' => 10],
            ],
        ]);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'Transaksi berhasil disimpan']);

        // Cek transaksi tersimpan
        $this->assertDatabaseHas('transaksi', [
            'mitra_id'           => $this->mitra->id,
            'total_harga'        => 450000,
            'status_pembayaran'  => 'Belum Dibayar',
        ]);

        // Cek stok berkurang
        $this->assertDatabaseHas('produk', [
            'id'   => $produk->id,
            'stok' => 90,
        ]);
    }

    public function test_transaksi_dengan_multiple_item(): void
    {
        $produk1 = Produk::create([
            'nama' => 'Ayam Potong', 'kategori' => 'Unggas',
            'stok' => 100, 'stok_minimal' => 10, 'satuan' => 'Ekor', 'harga' => 45000,
        ]);
        $produk2 = Produk::create([
            'nama' => 'Bebek Peking', 'kategori' => 'Unggas',
            'stok' => 50, 'stok_minimal' => 5, 'satuan' => 'Ekor', 'harga' => 70000,
        ]);

        $response = $this->actingAs($this->kasir)->postJson('/kasir/transaksi', [
            'mitra_id' => $this->mitra->id,
            'items'    => [
                ['produk_id' => $produk1->id, 'jumlah' => 5],
                ['produk_id' => $produk2->id, 'jumlah' => 3],
            ],
        ]);

        $response->assertStatus(201);

        // Total: (5 × 45000) + (3 × 70000) = 225000 + 210000 = 435000
        $this->assertDatabaseHas('transaksi', [
            'total_harga' => 435000,
            'total_item'  => 8,
        ]);

        // Cek stok masing-masing
        $this->assertDatabaseHas('produk', ['id' => $produk1->id, 'stok' => 95]);
        $this->assertDatabaseHas('produk', ['id' => $produk2->id, 'stok' => 47]);
    }

    public function test_transaksi_menghasilkan_log_stok_keluar(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Potong', 'kategori' => 'Unggas',
            'stok' => 50, 'stok_minimal' => 10, 'satuan' => 'Ekor', 'harga' => 45000,
        ]);

        $this->actingAs($this->kasir)->postJson('/kasir/transaksi', [
            'mitra_id' => $this->mitra->id,
            'items'    => [
                ['produk_id' => $produk->id, 'jumlah' => 5],
            ],
        ]);

        $this->assertDatabaseHas('log_stok', [
            'produk_id'    => $produk->id,
            'tipe'         => 'Keluar',
            'jumlah'       => 5,
            'stok_sebelum' => 50,
            'stok_sesudah' => 45,
        ]);
    }

    public function test_transaksi_menghasilkan_nomor_transaksi(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Potong', 'kategori' => 'Unggas',
            'stok' => 50, 'stok_minimal' => 10, 'satuan' => 'Ekor', 'harga' => 45000,
        ]);

        $response = $this->actingAs($this->kasir)->postJson('/kasir/transaksi', [
            'mitra_id' => $this->mitra->id,
            'items'    => [
                ['produk_id' => $produk->id, 'jumlah' => 1],
            ],
        ]);

        $response->assertStatus(201);
        $data = $response->json('transaksi');
        $this->assertStringStartsWith('JFR-', $data['no_transaksi']);
    }

    // ========================================================
    // STORE TRANSAKSI — GAGAL VALIDASI
    // ========================================================

    public function test_transaksi_gagal_tanpa_mitra(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Potong', 'kategori' => 'Unggas',
            'stok' => 50, 'stok_minimal' => 10, 'satuan' => 'Ekor', 'harga' => 45000,
        ]);

        $response = $this->actingAs($this->kasir)->postJson('/kasir/transaksi', [
            'items' => [
                ['produk_id' => $produk->id, 'jumlah' => 1],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('mitra_id');
    }

    public function test_transaksi_gagal_mitra_tidak_ada(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Potong', 'kategori' => 'Unggas',
            'stok' => 50, 'stok_minimal' => 10, 'satuan' => 'Ekor', 'harga' => 45000,
        ]);

        $response = $this->actingAs($this->kasir)->postJson('/kasir/transaksi', [
            'mitra_id' => 99999,
            'items'    => [
                ['produk_id' => $produk->id, 'jumlah' => 1],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('mitra_id');
    }

    public function test_transaksi_gagal_tanpa_items(): void
    {
        $response = $this->actingAs($this->kasir)->postJson('/kasir/transaksi', [
            'mitra_id' => $this->mitra->id,
            'items'    => [],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('items');
    }

    public function test_transaksi_gagal_stok_tidak_cukup(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Potong', 'kategori' => 'Unggas',
            'stok' => 5, 'stok_minimal' => 10, 'satuan' => 'Ekor', 'harga' => 45000,
        ]);

        $response = $this->actingAs($this->kasir)->postJson('/kasir/transaksi', [
            'mitra_id' => $this->mitra->id,
            'items'    => [
                ['produk_id' => $produk->id, 'jumlah' => 10],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => "Stok Ayam Potong tidak cukup. Tersisa: 5 ekor."]);

        // Stok tidak berubah
        $this->assertDatabaseHas('produk', ['id' => $produk->id, 'stok' => 5]);
    }

    public function test_transaksi_gagal_jumlah_item_nol(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Potong', 'kategori' => 'Unggas',
            'stok' => 50, 'stok_minimal' => 10, 'satuan' => 'Ekor', 'harga' => 45000,
        ]);

        $response = $this->actingAs($this->kasir)->postJson('/kasir/transaksi', [
            'mitra_id' => $this->mitra->id,
            'items'    => [
                ['produk_id' => $produk->id, 'jumlah' => 0],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('items.0.jumlah');
    }

    public function test_transaksi_gagal_produk_tidak_ada(): void
    {
        $response = $this->actingAs($this->kasir)->postJson('/kasir/transaksi', [
            'mitra_id' => $this->mitra->id,
            'items'    => [
                ['produk_id' => 99999, 'jumlah' => 1],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('items.0.produk_id');
    }

    // ========================================================
    // RIWAYAT TRANSAKSI
    // ========================================================

    public function test_kasir_bisa_akses_halaman_riwayat(): void
    {
        $response = $this->actingAs($this->kasir)->get('/kasir/riwayat');
        $response->assertStatus(200);
    }

    public function test_kasir_bisa_akses_halaman_tagihan(): void
    {
        $response = $this->actingAs($this->kasir)->get('/kasir/tagihan');
        $response->assertStatus(200);
    }

    // ========================================================
    // DASHBOARD KASIR
    // ========================================================

    public function test_kasir_dashboard_menampilkan_data(): void
    {
        $response = $this->actingAs($this->kasir)->get('/kasir/dashboard');
        $response->assertStatus(200);
    }
}
