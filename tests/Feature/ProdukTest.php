<?php

namespace Tests\Feature;

use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProdukTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'Admin']);
    }

    // ========================================================
    // HALAMAN PRODUK
    // ========================================================

    public function test_halaman_produk_dapat_diakses_admin(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/produk');
        $response->assertStatus(200);
    }

    public function test_halaman_produk_menampilkan_daftar_produk(): void
    {
        Produk::create([
            'nama' => 'Ayam Potong', 'kategori' => 'Unggas',
            'stok' => 100, 'stok_minimal' => 10, 'satuan' => 'Ekor', 'harga' => 45000,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/produk');
        $response->assertStatus(200);
        $response->assertSee('Ayam Potong');
    }

    // ========================================================
    // TAMBAH PRODUK — BERHASIL
    // ========================================================

    public function test_admin_bisa_tambah_produk_baru(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/produk', [
            'nama'         => 'Ayam Kampung',
            'harga'        => 55000,
            'stok_minimal' => 5,
        ]);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'Produk berhasil ditambahkan']);
        $this->assertDatabaseHas('produk', [
            'nama'  => 'Ayam Kampung',
            'harga' => 55000,
            'stok'  => 0,
        ]);
    }

    public function test_tambah_produk_tanpa_stok_minimal_default_nol(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/produk', [
            'nama'  => 'Bebek Peking',
            'harga' => 70000,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('produk', [
            'nama'         => 'Bebek Peking',
            'stok_minimal' => 0,
        ]);
    }

    // ========================================================
    // TAMBAH PRODUK — GAGAL VALIDASI
    // ========================================================

    public function test_tambah_produk_gagal_nama_kosong(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/produk', [
            'nama'  => '',
            'harga' => 45000,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('nama');
    }

    public function test_tambah_produk_gagal_harga_kosong(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/produk', [
            'nama' => 'Ayam Potong',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('harga');
    }

    public function test_tambah_produk_gagal_harga_negatif(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/produk', [
            'nama'  => 'Ayam Potong',
            'harga' => -5000,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('harga');
    }

    public function test_tambah_produk_gagal_stok_minimal_bukan_angka(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/produk', [
            'nama'         => 'Ayam Potong',
            'harga'        => 45000,
            'stok_minimal' => 'abc',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('stok_minimal');
    }

    // ========================================================
    // UPDATE PRODUK
    // ========================================================

    public function test_admin_bisa_update_produk(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Potong', 'kategori' => 'Unggas',
            'stok' => 50, 'stok_minimal' => 10, 'satuan' => 'Ekor', 'harga' => 45000,
        ]);

        $response = $this->actingAs($this->admin)->putJson("/admin/produk/{$produk->id}", [
            'nama'         => 'Ayam Potong Premium',
            'harga'        => 55000,
            'stok_minimal' => 15,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Produk berhasil diperbarui']);
        $this->assertDatabaseHas('produk', [
            'id'    => $produk->id,
            'nama'  => 'Ayam Potong Premium',
            'harga' => 55000,
        ]);
    }

    public function test_update_produk_yang_tidak_ada_gagal(): void
    {
        $response = $this->actingAs($this->admin)->putJson('/admin/produk/99999', [
            'nama'  => 'Test',
            'harga' => 10000,
        ]);

        $response->assertStatus(404);
    }

    // ========================================================
    // HAPUS PRODUK
    // ========================================================

    public function test_admin_bisa_hapus_produk(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Hapus', 'kategori' => 'Unggas',
            'stok' => 10, 'stok_minimal' => 5, 'satuan' => 'Ekor', 'harga' => 40000,
        ]);

        $response = $this->actingAs($this->admin)->deleteJson("/admin/produk/{$produk->id}");

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Produk berhasil dihapus']);
        $this->assertDatabaseMissing('produk', ['id' => $produk->id]);
    }

    // ========================================================
    // TAMBAH STOK
    // ========================================================

    public function test_admin_bisa_tambah_stok_produk(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Potong', 'kategori' => 'Unggas',
            'stok' => 50, 'stok_minimal' => 10, 'satuan' => 'Ekor', 'harga' => 45000,
        ]);

        $response = $this->actingAs($this->admin)->postJson("/admin/produk/{$produk->id}/stok", [
            'jumlah' => 30,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Stok berhasil ditambahkan']);
        $this->assertDatabaseHas('produk', [
            'id'   => $produk->id,
            'stok' => 80,
        ]);
    }

    public function test_tambah_stok_gagal_jumlah_nol(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Potong', 'kategori' => 'Unggas',
            'stok' => 50, 'stok_minimal' => 10, 'satuan' => 'Ekor', 'harga' => 45000,
        ]);

        $response = $this->actingAs($this->admin)->postJson("/admin/produk/{$produk->id}/stok", [
            'jumlah' => 0,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('jumlah');
    }

    public function test_tambah_stok_gagal_jumlah_bukan_angka(): void
    {
        $produk = Produk::create([
            'nama' => 'Ayam Potong', 'kategori' => 'Unggas',
            'stok' => 50, 'stok_minimal' => 10, 'satuan' => 'Ekor', 'harga' => 45000,
        ]);

        $response = $this->actingAs($this->admin)->postJson("/admin/produk/{$produk->id}/stok", [
            'jumlah' => 'abc',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('jumlah');
    }
}
