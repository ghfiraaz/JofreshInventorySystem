<?php

namespace Tests\Feature;

use App\Models\LogStok;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogStokTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $kasir;
    private User $superadmin;
    private Produk $produk;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'Admin']);
        $this->kasir = User::factory()->create(['role' => 'Kasir']);
        $this->superadmin = User::factory()->create(['role' => 'Superadmin']);

        $this->produk = Produk::create([
            'nama' => 'Ayam Potong',
            'kategori' => 'Unggas',
            'stok' => 50,
            'stok_minimal' => 10,
            'satuan' => 'Ekor',
            'harga' => 45000,
        ]);
    }

    // ========================================================
    // LOG STOK INDEX (VIEW LIST)
    // ========================================================

    public function test_log_stok_dapat_diakses_oleh_semua_role(): void
    {
        // Admin
        $response = $this->actingAs($this->admin)->get('/log-stok');
        $response->assertStatus(200);

        // Kasir
        $response = $this->actingAs($this->kasir)->get('/log-stok');
        $response->assertStatus(200);

        // Superadmin
        $response = $this->actingAs($this->superadmin)->get('/log-stok');
        $response->assertStatus(200);
    }

    public function test_log_stok_menampilkan_riwayat(): void
    {
        LogStok::create([
            'produk_id' => $this->produk->id,
            'user_id' => $this->admin->id,
            'tipe' => 'Masuk',
            'jumlah' => 10,
            'stok_sebelum' => 40,
            'stok_sesudah' => 50,
            'keterangan' => 'Restok supplier',
        ]);

        $response = $this->actingAs($this->admin)->get('/log-stok');
        $response->assertStatus(200);
        $response->assertSee('Restok supplier');
        $response->assertSee('Masuk');
    }

    // ========================================================
    // ADJUSTMENT STOK (ADMIN ONLY)
    // ========================================================

    public function test_admin_bisa_melakukan_adjustment_masuk(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/penyesuaian-stok', [
            'produk_id' => $this->produk->id,
            'tipe_adjustment' => 'Adjustment Masuk',
            'jumlah' => 5,
            'keterangan' => 'Kelebihan hitung',
        ]);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'Adjustment stok berhasil dicatat.']);

        $this->assertDatabaseHas('produk', [
            'id' => $this->produk->id,
            'stok' => 55,
        ]);

        $this->assertDatabaseHas('log_stok', [
            'produk_id' => $this->produk->id,
            'tipe' => 'Adjustment Masuk',
            'jumlah' => 5,
            'stok_sebelum' => 50,
            'stok_sesudah' => 55,
            'keterangan' => 'Kelebihan hitung',
        ]);
    }

    public function test_admin_bisa_melakukan_adjustment_keluar(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/penyesuaian-stok', [
            'produk_id' => $this->produk->id,
            'tipe_adjustment' => 'Adjustment Keluar',
            'jumlah' => 5,
            'keterangan' => 'Ayam mati',
        ]);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'Adjustment stok berhasil dicatat.']);

        $this->assertDatabaseHas('produk', [
            'id' => $this->produk->id,
            'stok' => 45,
        ]);

        $this->assertDatabaseHas('log_stok', [
            'produk_id' => $this->produk->id,
            'tipe' => 'Adjustment Keluar',
            'jumlah' => 5,
            'stok_sebelum' => 50,
            'stok_sesudah' => 45,
            'keterangan' => 'Ayam mati',
        ]);
    }

    public function test_kasir_tidak_bisa_melakukan_adjustment(): void
    {
        $response = $this->actingAs($this->kasir)->postJson('/admin/penyesuaian-stok', [
            'produk_id' => $this->produk->id,
            'tipe_adjustment' => 'Adjustment Masuk',
            'jumlah' => 5,
            'keterangan' => 'Coba adjustment',
        ]);

        $response->assertRedirect('/');
    }

    public function test_superadmin_tidak_bisa_melakukan_adjustment(): void
    {
        $response = $this->actingAs($this->superadmin)->postJson('/admin/penyesuaian-stok', [
            'produk_id' => $this->produk->id,
            'tipe_adjustment' => 'Adjustment Masuk',
            'jumlah' => 5,
            'keterangan' => 'Coba adjustment',
        ]);

        $response->assertRedirect('/');
    }

    public function test_adjustment_keluar_gagal_jika_stok_tidak_cukup(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/penyesuaian-stok', [
            'produk_id' => $this->produk->id,
            'tipe_adjustment' => 'Adjustment Keluar',
            'jumlah' => 60,
            'keterangan' => 'Ayam susut banyak',
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'message' => "Stok Ayam Potong tidak cukup untuk dikurangi. Stok saat ini: 50."
        ]);

        $this->assertDatabaseHas('produk', [
            'id' => $this->produk->id,
            'stok' => 50,
        ]);
    }

    public function test_adjustment_gagal_jika_jumlah_bukan_angka(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/admin/penyesuaian-stok', [
            'produk_id' => $this->produk->id,
            'tipe_adjustment' => 'Adjustment Masuk',
            'jumlah' => 'tiga',
            'keterangan' => 'Keterangan',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('jumlah');
    }
}
