<?php

namespace Tests\Feature;

use App\Models\Mitra;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    private Mitra $mitra;
    private Transaksi $transaksi;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->mitra = Mitra::create([
            'nama' => 'Mitra Sukses',
            'alamat' => 'Jl. Sukses No.8',
            'status' => 'Aktif',
            'tanggal_jatuh_tempo' => 15,
        ]);

        $this->transaksi = Transaksi::create([
            'no_transaksi' => 'JFR-20260630-001',
            'user_id' => User::factory()->create(['role' => 'Kasir'])->id,
            'mitra_id' => $this->mitra->id,
            'total_item' => 10,
            'total_harga' => 450000,
            'total_berat' => 10,
            'metode_pembayaran' => 'Tempo',
            'status_pembayaran' => 'Belum Dibayar',
            'jatuh_tempo' => now()->addDays(5)->toDateString(),
        ]);
    }

    // ========================================================
    // PUBLIC UPLOAD FORM PAGE
    // ========================================================

    public function test_halaman_upload_pembayaran_publik_dapat_diakses_dengan_token_valid(): void
    {
        $response = $this->get("/pembayaran/{$this->mitra->payment_token}");

        $response->assertStatus(200);
        $response->assertSee('Upload Bukti Pembayaran');
        $response->assertSee($this->mitra->nama);
    }

    public function test_halaman_upload_pembayaran_publik_gagal_dengan_token_salah(): void
    {
        $response = $this->get('/pembayaran/token-salah-123');

        $response->assertStatus(404);
    }

    // ========================================================
    // UPLOAD BUKTI PEMBAYARAN
    // ========================================================

    public function test_upload_bukti_pembayaran_berhasil_dan_mengubah_status(): void
    {
        $file = UploadedFile::fake()->image('bukti_transfer.png');

        $response = $this->postJson("/pembayaran/{$this->mitra->payment_token}", [
            'bukti_pembayaran' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Bukti pembayaran berhasil diupload. Terima kasih!']);

        // Check transaction status changed
        $this->transaksi->refresh();
        $this->assertEquals('Menunggu Validasi', $this->transaksi->status_pembayaran);
        $this->assertNotNull($this->transaksi->bukti_pembayaran);

        // Check Mitra is locked for upload
        $this->mitra->refresh();
        $this->assertTrue((bool)$this->mitra->payment_upload_locked);

        // Check file exists in storage
        Storage::disk('public')->assertExists($this->transaksi->bukti_pembayaran);
    }

    public function test_upload_bukti_pembayaran_gagal_tanpa_file(): void
    {
        $response = $this->postJson("/pembayaran/{$this->mitra->payment_token}", [
            'bukti_pembayaran' => null,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('bukti_pembayaran');
    }

    public function test_upload_bukti_pembayaran_gagal_ekstensi_tidak_valid(): void
    {
        $file = UploadedFile::fake()->create('dokumen.txt', 100);

        $response = $this->postJson("/pembayaran/{$this->mitra->payment_token}", [
            'bukti_pembayaran' => $file,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('bukti_pembayaran');
    }
}
