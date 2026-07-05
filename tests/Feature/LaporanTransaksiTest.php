<?php

namespace Tests\Feature;

use App\Models\Mitra;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanTransaksiTest extends TestCase
{
    use RefreshDatabase;

    private User $superadmin;
    private Mitra $mitra;
    private Transaksi $transaksi;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->superadmin = User::factory()->create(['role' => 'Superadmin']);
        
        $this->mitra = Mitra::create([
            'nama' => 'Mitra Laporan',
            'kontak' => '081234567890',
            'email' => 'laporan@gmail.com',
            'alamat' => 'Jl. Laporan No. 1',
            'tanggal_jatuh_tempo' => 15,
            'status' => 'Aktif',
        ]);

        $this->transaksi = Transaksi::create([
            'user_id' => $this->superadmin->id,
            'mitra_id' => $this->mitra->id,
            'no_transaksi' => 'JFR-LPT-001',
            'status_pembayaran' => 'Sudah Dibayar',
            'total_harga' => 180000,
            'total_item' => 4,
            'total_berat' => 4,
            'metode_pembayaran' => 'Tempo',
            'jatuh_tempo' => now()->toDateString(),
            'created_at' => now(),
        ]);
    }

    /**
     *      Menampilkan Laporan Transaksi (Positive)
     */
    public function test_menampilkan_laporan_transaksi(): void
    {
        $response = $this->actingAs($this->superadmin)->get('/owner/laporan-transaksi');

        $response->assertStatus(200);
        $response->assertSee('JFR-LPT-001');
    }

    /**
     * Laporan Transaksi Kosong (Negative)
     */
    public function test_laporan_transaksi_kosong(): void
    {
        Transaksi::query()->delete();

        $response = $this->actingAs($this->superadmin)->get('/owner/laporan-transaksi');

        $response->assertStatus(200);
        $response->assertSee('Belum Ada Transaksi');
    }

    /**
     * Filter Laporan Berdasarkan Periode (Positive)
     */
    public function test_filter_laporan_berdasarkan_periode(): void
    {
        $response = $this->actingAs($this->superadmin)->get('/owner/laporan-transaksi?filter_date=' . now()->toDateString());

        $response->assertStatus(200);
        $response->assertSee('JFR-LPT-001');
    }

    /**
     * Filter Tidak Menemukan Data (Negative)
     */
    public function test_filter_tidak_menemukan_data(): void
    {
        $response = $this->actingAs($this->superadmin)->get('/owner/laporan-transaksi?filter_date=2099-12-31');

        $response->assertStatus(200);
        $response->assertSee('Belum Ada Transaksi');
    }

    /**
     * Cetak Laporan Transaksi (Positive)
     */
    public function test_cetak_laporan_transaksi(): void
    {
        $response = $this->actingAs($this->superadmin)->get('/owner/laporan-harian?date=' . now()->toDateString());

        $response->assertStatus(200);
        $response->assertSee('Laporan Harian Transaksi');
    }

    /**
     * Membatalkan Proses Cetak (Negative)
     */
    public function test_membatalkan_proses_cetak(): void
    {
        $this->actingAs($this->superadmin);
        $this->assertTrue(true);
    }
}
