<?php

namespace Tests\Feature;

use App\Models\Mitra;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RiwayatTransaksiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-RWT-001: Melihat Riwayat Transaksi
     * Menampilkan daftar transaksi yang tersimpan di sistem.
     */
    public function test_tc_rwt_001_melihat_riwayat_transaksi(): void
    {
        $kasir = User::factory()->create(['role' => 'Kasir']);
        $mitra = Mitra::create([
            'nama' => 'Warung Pojok',
            'alamat' => 'Jl. Pojok No. 5',
            'status' => 'Aktif',
            'tanggal_jatuh_tempo' => 20,
        ]);

        $transaksi = Transaksi::create([
            'no_transaksi' => 'JFR-99990101-001',
            'user_id' => $kasir->id,
            'mitra_id' => $mitra->id,
            'total_item' => 3,
            'total_harga' => 150000,
            'total_berat' => 3,
            'metode_pembayaran' => 'Tempo',
            'status_pembayaran' => 'Sudah Dibayar',
            'jatuh_tempo' => now()->addDays(5)->toDateString(),
        ]);

        $response = $this->actingAs($kasir)->get('/kasir/riwayat');
        $response->assertStatus(200);
        $response->assertSee('JFR-99990101-001');
        $response->assertSee('Warung Pojok');
    }

    /**
     * TC-RWT-002: Melihat Detail Transaksi
     * Menampilkan informasi detail transaksi yang dipilih secara lengkap.
     */
    public function test_tc_rwt_002_melihat_detail_transaksi(): void
    {
        $kasir = User::factory()->create(['role' => 'Kasir']);
        $mitra = Mitra::create([
            'nama' => 'Mitra Detail',
            'alamat' => 'Jl. Detail No. 1',
            'status' => 'Aktif',
            'tanggal_jatuh_tempo' => 10,
        ]);

        $transaksi = Transaksi::create([
            'no_transaksi' => 'JFR-DETAILS-001',
            'user_id' => $kasir->id,
            'mitra_id' => $mitra->id,
            'total_item' => 5,
            'total_harga' => 250000,
            'total_berat' => 5,
            'metode_pembayaran' => 'Tempo',
            'status_pembayaran' => 'Belum Dibayar',
            'jatuh_tempo' => now()->addDays(5)->toDateString(),
        ]);

        $produk = Produk::create([
            'nama' => 'Bebek Madura',
            'kategori' => 'Unggas',
            'stok' => 50,
            'stok_minimal' => 5,
            'satuan' => 'Ekor',
            'harga' => 50000,
        ]);

        $transaksi->items()->create([
            'produk_id' => $produk->id,
            'nama_produk' => $produk->nama,
            'jumlah' => 5,
            'harga_satuan' => 50000,
            'subtotal' => 250000,
        ]);

        $response = $this->actingAs($kasir)->get("/kasir/transaksi/{$transaksi->id}/invoice");
        $response->assertStatus(200);
        $response->assertSee('JFR-DETAILS-001');
        $response->assertSee('Mitra Detail');
        $response->assertSee('Bebek Madura');
    }

    /**
     * TC-RWT-003: Riwayat Transaksi Kosong
     * Menampilkan informasi "Belum ada transaksi yang tercatat." ketika belum terdapat data transaksi.
     */
    public function test_tc_rwt_003_riwayat_transaksi_kosong(): void
    {
        $kasir = User::factory()->create(['role' => 'Kasir']);

        $response = $this->actingAs($kasir)->get('/kasir/riwayat');
        $response->assertStatus(200);
        $response->assertSee('Belum ada transaksi yang tercatat.');
    }

    /**
     * TC-RWT-004: Akses Riwayat Transaksi Tanpa Login
     * Mengarahkan ke halaman login saat mencoba mengakses halaman riwayat tanpa sesi.
     */
    public function test_tc_rwt_004_akses_riwayat_transaksi_tanpa_login(): void
    {
        $response = $this->get('/kasir/riwayat');
        $response->assertRedirect('/');
    }

    /**
     * TC-RWT-005: Melihat Bukti Pembayaran
     * Menampilkan file bukti pembayaran yang diunggah oleh mitra.
     */
    public function test_tc_rwt_005_melihat_bukti_pembayaran(): void
    {
        Storage::fake('public');
        
        $kasir = User::factory()->create(['role' => 'Kasir']);
        $mitra = Mitra::create([
            'nama' => 'Mitra Upload',
            'alamat' => 'Jl. Upload No. 1',
            'status' => 'Aktif',
            'tanggal_jatuh_tempo' => 15,
        ]);

        $transaksi = Transaksi::create([
            'no_transaksi' => 'JFR-BUKTI-001',
            'user_id' => $kasir->id,
            'mitra_id' => $mitra->id,
            'total_item' => 2,
            'total_harga' => 90000,
            'total_berat' => 2,
            'metode_pembayaran' => 'Tempo',
            'status_pembayaran' => 'Menunggu Validasi',
            'bukti_pembayaran' => 'bukti-pembayaran/receipt.png',
            'jatuh_tempo' => now()->addDays(5)->toDateString(),
        ]);

        Storage::disk('public')->put('bukti-pembayaran/receipt.png', 'fake image content');

        $response = $this->actingAs($kasir)->get('/kasir/bukti-pembayaran/receipt.png');
        $response->assertStatus(200);
    }

    /**
     * TC-RWT-006: Bukti Pembayaran Tidak Tersedia
     * Memastikan text/tautan bukti pembayaran belum tersedia jika belum diupload.
     */
    public function test_tc_rwt_006_bukti_pembayaran_tidak_tersedia(): void
    {
        $kasir = User::factory()->create(['role' => 'Kasir']);
        $mitra = Mitra::create([
            'nama' => 'Mitra No Bukti',
            'alamat' => 'Jl. No Bukti No. 12',
            'status' => 'Aktif',
            'tanggal_jatuh_tempo' => 15,
        ]);

        $transaksi = Transaksi::create([
            'no_transaksi' => 'JFR-NOBUKTI-001',
            'user_id' => $kasir->id,
            'mitra_id' => $mitra->id,
            'total_item' => 1,
            'total_harga' => 45000,
            'total_berat' => 1,
            'metode_pembayaran' => 'Tempo',
            'status_pembayaran' => 'Sudah Dibayar',
            'bukti_pembayaran' => null,
            'jatuh_tempo' => now()->addDays(5)->toDateString(),
        ]);

        $response = $this->actingAs($kasir)->get('/kasir/riwayat');
        $response->assertStatus(200);
        
        $response->assertDontSee('/kasir/bukti-pembayaran/JFR-NOBUKTI-001');
        
        $responseInvoice = $this->actingAs($kasir)->get("/kasir/transaksi/{$transaksi->id}/invoice");
        $responseInvoice->assertStatus(200);
        $responseInvoice->assertSee('Tempo');
    }

    /**
     * TC-RWT-007: Cetak Invoice Berhasil
     * Menghasilkan file PDF invoice transaksi yang dipilih.
     */
    public function test_tc_rwt_007_cetak_invoice_berhasil(): void
    {
        $kasir = User::factory()->create(['role' => 'Kasir']);
        $mitra = Mitra::create([
            'nama' => 'Mitra Cetak',
            'alamat' => 'Jl. Cetak No. 1',
            'status' => 'Aktif',
            'tanggal_jatuh_tempo' => 15,
        ]);

        $transaksi = Transaksi::create([
            'no_transaksi' => 'JFR-CETAK-001',
            'user_id' => $kasir->id,
            'mitra_id' => $mitra->id,
            'total_item' => 2,
            'total_harga' => 90000,
            'total_berat' => 2,
            'metode_pembayaran' => 'Tempo',
            'status_pembayaran' => 'Sudah Dibayar',
            'jatuh_tempo' => now()->addDays(5)->toDateString(),
        ]);

        $response = $this->actingAs($kasir)->get("/kasir/transaksi/{$transaksi->id}/invoice-pdf");
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
