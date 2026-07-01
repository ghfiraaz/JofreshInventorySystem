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
}
