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

    /**
     * Melihat Log Stok Produk (Positive)
     */
    public function test_melihat_log_stok_produk(): void
    {
        LogStok::create([
            'produk_id' => $this->produk->id,
            'user_id' => $this->admin->id,
            'tipe' => 'Masuk',
            'jumlah' => 10,
            'stok_sebelum' => 40,
            'stok_sesudah' => 50,
            'keterangan' => 'Restok Ayam',
        ]);

        $response = $this->actingAs($this->admin)->get('/log-stok');

        $response->assertStatus(200);
        $response->assertSee('Restok Ayam');
    }

    /**
     * Filter Log Berdasarkan Periode (Positive)
     */
    public function test_filter_log_berdasarkan_periode(): void
    {
        LogStok::create([
            'produk_id' => $this->produk->id,
            'user_id' => $this->admin->id,
            'tipe' => 'Masuk',
            'jumlah' => 10,
            'stok_sebelum' => 40,
            'stok_sesudah' => 50,
            'keterangan' => 'Log Periode',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get('/log-stok?tanggal_dari=' . now()->toDateString() . '&tanggal_sampai=' . now()->toDateString());

        $response->assertStatus(200);
        $response->assertSee('Log Periode');
    }

    /**
     * Filter Log Berdasarkan Jenis (Positive)
     */
    public function test_filter_log_berdasarkan_jenis(): void
    {
        LogStok::create([
            'produk_id' => $this->produk->id,
            'user_id' => $this->admin->id,
            'tipe' => 'Adjustment Masuk',
            'jumlah' => 5,
            'stok_sebelum' => 45,
            'stok_sesudah' => 50,
            'keterangan' => 'Log Jenis Adj',
        ]);

        $response = $this->actingAs($this->admin)->get('/log-stok?tipe=Adjustment Masuk');

        $response->assertStatus(200);
        $response->assertSee('Log Jenis Adj');
    }

    /**
     * Filter Tidak Menemukan Data (Negative)
     */
    public function test_filter_tidak_menemukan_data_log(): void
    {
        $response = $this->actingAs($this->admin)->get('/log-stok?tanggal_dari=2099-01-01');

        $response->assertStatus(200);
        $response->assertSee('Belum ada log stok yang tercatat');
    }

    /**
     * Tanggal Awal Lebih Besar dari Tanggal Akhir (Negative)
     */
    public function test_tanggal_awal_lebih_besar_dari_tanggal_akhir(): void
    {
        $response = $this->from('/log-stok')
            ->actingAs($this->admin)
            ->get('/log-stok?tanggal_dari=' . now()->addDay()->toDateString() . '&tanggal_sampai=' . now()->toDateString());

        $response->assertStatus(302);
        $response->assertSessionHasErrors('tanggal_sampai');
    }
}
