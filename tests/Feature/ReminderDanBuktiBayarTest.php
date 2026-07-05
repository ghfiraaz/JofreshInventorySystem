<?php

namespace Tests\Feature;

use App\Mail\PaymentReminderMail;
use App\Models\Mitra;
use App\Models\ReminderHistory;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReminderDanBuktiBayarTest extends TestCase
{
    use RefreshDatabase;

    private User $kasir;
    private Mitra $mitra;
    private Transaksi $transaksi;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Pastikan local domain mail terisi
        config(['mail.mailers.smtp.local_domain' => 'jofresh.com']);

        $this->kasir = User::factory()->create(['role' => 'Kasir']);
        
        $this->mitra = Mitra::create([
            'nama' => 'Mitra Pembayar',
            'kontak' => '081234567890',
            'email' => 'pembayar@gmail.com',
            'alamat' => 'Jl. Pembayar No. 1',
            'tanggal_jatuh_tempo' => (int) now()->day,
            'status' => 'Aktif',
        ]);

        // Buat transaksi dengan jatuh_tempo <= 3 hari dari sekarang agar lolos validasi H-3
        $this->transaksi = Transaksi::create([
            'user_id' => $this->kasir->id,
            'mitra_id' => $this->mitra->id,
            'no_transaksi' => 'JFR-REM-001',
            'status_pembayaran' => 'Belum Dibayar',
            'total_harga' => 200000,
            'total_item' => 4,
            'total_berat' => 4,
            'metode_pembayaran' => 'Tempo',
            'jatuh_tempo' => now()->addDays(2)->toDateString(), // H-2
            'created_at' => now(),
        ]);
    }

    // =========================================================================
    // Mengirim Email Reminder
    // =========================================================================

    /**
     * Mengirim Email Pembayaran Berhasil (Positive)
     * Berhasil mengirim email tagihan ke email mitra.
     */
    public function test_mengirim_email_pembayaran_berhasil(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->kasir)->postJson('/kasir/tagihan/send-reminder', [
            'mitra_id' => $this->mitra->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Reminder berhasil dikirim ke pembayar@gmail.com']);

        // Pastikan email terkirim
        Mail::assertSent(PaymentReminderMail::class, function ($mail) {
            return $mail->hasTo('pembayar@gmail.com') && $mail->totalTagihan === 200000;
        });

        // Pastikan history tercatat berhasil
        $this->assertDatabaseHas('reminder_histories', [
            'mitra_id' => $this->mitra->id,
            'status' => 'berhasil',
        ]);
    }

    /**
     * Gagal Mengirim Email Pembayaran (Negative)
     * Menangani kegagalan jika layanan email terganggu.
     */
    public function test_gagal_mengirim_email_pembayaran(): void
    {
        // Paksa mailer membuang exception untuk mensimulasikan kegagalan jaringan/SMTP
        Mail::shouldReceive('to')
            ->andThrow(new \Exception('Connection timeout'));

        $response = $this->actingAs($this->kasir)->postJson('/kasir/tagihan/send-reminder', [
            'mitra_id' => $this->mitra->id,
        ]);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'Gagal mengirim email: Connection timeout']);

        // Transaksi tidak boleh berubah status
        $this->assertEquals('Belum Dibayar', $this->transaksi->refresh()->status_pembayaran);

        // Pastikan history tercatat gagal
        $this->assertDatabaseHas('reminder_histories', [
            'mitra_id' => $this->mitra->id,
            'status' => 'gagal',
            'error_message' => 'Connection timeout',
        ]);
    }

    /**
     * Membatalkan Pengiriman Email Pembayaran (Negative)
     * Sesi pengiriman dibatalkan, tidak ada email terkirim (simulasi tidak mengirim request).
     */
    public function test_membatalkan_pengiriman_email_pembayaran(): void
    {
        $this->actingAs($this->kasir);

        // Tidak memanggil endpoint send-reminder
        $this->assertDatabaseMissing('reminder_histories', [
            'mitra_id' => $this->mitra->id,
        ]);
    }

    // =========================================================================
    //  UPLOAD BUKTI BAYAR
    // =========================================================================

    /**
     * Upload Bukti Pembayaran Berhasil (Positive)
     * Berhasil mengunggah bukti bayar dan mengubah status transaksi.
     */
    public function test_upload_bukti_pembayaran_berhasil(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('bukti.png', 500, 'image/png');

        $response = $this->from(route('pembayaran.upload', $this->mitra->payment_token))
            ->post(route('pembayaran.store', $this->mitra->payment_token), [
                'bukti_pembayaran' => $file,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('Menunggu Validasi', $this->transaksi->refresh()->status_pembayaran);
        $this->assertNotNull($this->transaksi->bukti_pembayaran);
        
        // Upload lock status
        $this->assertTrue((bool)$this->mitra->refresh()->payment_upload_locked);
    }

    /**
     * Tidak Memilih File (Negative)
     * Menolak jika tombol upload ditekan tanpa memilih file.
     */
    public function test_tidak_memilih_file(): void
    {
        $response = $this->from(route('pembayaran.upload', $this->mitra->payment_token))
            ->post(route('pembayaran.store', $this->mitra->payment_token), [
                'bukti_pembayaran' => null,
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('bukti_pembayaran');
    }

    /**
     * Format File Tidak Didukung (Negative)
     * Menolak format file di luar jpg, jpeg, png, pdf (misal .docx).
     */
    public function test_format_file_tidak_didukung(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('bukti.docx', 500, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $response = $this->from(route('pembayaran.upload', $this->mitra->payment_token))
            ->post(route('pembayaran.store', $this->mitra->payment_token), [
                'bukti_pembayaran' => $file,
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('bukti_pembayaran');
    }

    /**
     * Ukuran File Melebihi 5 MB (Max+1) (Negative)
     * Menolak jika ukuran file lebih besar dari 5120 KB.
     */
    public function test_ukuran_file_melebihi_lima_mb(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('bukti.png', 5121, 'image/png'); // 5121 KB (5 MB + 1 KB)

        $response = $this->from(route('pembayaran.upload', $this->mitra->payment_token))
            ->post(route('pembayaran.store', $this->mitra->payment_token), [
                'bukti_pembayaran' => $file,
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('bukti_pembayaran');
    }

    /**
     *  Ukuran File Tepat 5 MB (Max) (Positive / BVA)
     * Berhasil jika ukuran file tepat 5120 KB.
     */
    public function test_ukuran_file_tepat_lima_mb(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('bukti.png', 5120, 'image/png'); // Tepat 5120 KB (5 MB)

        $response = $this->from(route('pembayaran.upload', $this->mitra->payment_token))
            ->post(route('pembayaran.store', $this->mitra->payment_token), [
                'bukti_pembayaran' => $file,
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertEquals('Menunggu Validasi', $this->transaksi->refresh()->status_pembayaran);
    }
}
