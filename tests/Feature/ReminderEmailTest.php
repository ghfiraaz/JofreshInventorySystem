<?php

namespace Tests\Feature;

use App\Mail\PaymentReminderMail;
use App\Models\Mitra;
use App\Models\ReminderHistory;
use App\Models\Transaksi;
use App\Models\User;
use App\Services\ReminderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReminderEmailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Pastikan config mail.php local_domain terisi
        config(['mail.mailers.smtp.local_domain' => 'jofresh.com']);
    }

    public function test_send_reminder_successfully_dispatches_email_and_logs_history(): void
    {
        Mail::fake();

        // 1. Create a user
        $user = User::factory()->create();

        // 2. Create a mitra
        $mitra = Mitra::create([
            'nama' => 'Budi Santoso',
            'kontak' => '08123456789',
            'email' => 'budi@example.com',
            'alamat' => 'Jl. Mawar No. 123',
            'tanggal_jatuh_tempo' => 15,
            'status' => 'Aktif',
        ]);

        // 3. Create a transaction for this mitra (Belum Dibayar)
        $transaksi = Transaksi::create([
            'user_id' => $user->id,
            'mitra_id' => $mitra->id,
            'no_transaksi' => 'TX-001',
            'status_pembayaran' => 'Belum Dibayar',
            'total_harga' => 500000,
            'total_item' => 5,
            'total_berat' => 10,
            'created_at' => now(), // Masuk ke range
        ]);

        // 4. Run ReminderService
        $service = new ReminderService();
        $result = $service->sendReminder($mitra, $user);

        // 5. Assertions
        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('reminder_histories', [
            'mitra_id' => $mitra->id,
            'user_id' => $user->id,
            'email_penerima' => 'budi@example.com',
            'status' => 'berhasil',
            'total_tagihan' => 500000,
        ]);

        $this->assertDatabaseHas('transaksi', [
            'id' => $transaksi->id,
        ]);
        
        $updatedTransaksi = Transaksi::find($transaksi->id);
        $this->assertNotNull($updatedTransaksi->last_reminder_sent_at);

        Mail::assertSent(PaymentReminderMail::class, function ($mail) use ($mitra) {
            return $mail->hasTo('budi@example.com') &&
                   $mail->mitra->id === $mitra->id &&
                   $mail->totalTagihan === 500000;
        });
    }

    public function test_payment_reminder_mail_headers_and_views(): void
    {
        $user = User::factory()->create();
        $mitra = Mitra::create([
            'nama' => 'Budi Santoso',
            'kontak' => '08123456789',
            'email' => 'budi@example.com',
            'alamat' => 'Jl. Mawar No. 123',
            'tanggal_jatuh_tempo' => 15,
            'status' => 'Aktif',
        ]);
        
        $history = ReminderHistory::create([
            'mitra_id' => $mitra->id,
            'user_id' => $user->id,
            'email_penerima' => $mitra->email,
            'tanggal_pengiriman' => now(),
            'status' => 'berhasil',
            'invoice_filename' => 'invoice.pdf',
            'periode_awal' => now()->startOfMonth()->toDateString(),
            'periode_akhir' => now()->endOfMonth()->toDateString(),
            'total_tagihan' => 100000,
        ]);

        $transaksiList = collect([]);

        // Instantiate mail
        $mail = new PaymentReminderMail(
            $mitra,
            $transaksiList,
            100000,
            'http://localhost/pembayaran/' . $mitra->payment_token,
            '15 June 2026',
            '01 June 2026',
            '15 June 2026',
            '/path/to/nonexistent/file.pdf',
            $history
        );

        // Check envelope subject
        $envelope = $mail->envelope();
        $this->assertEquals('Reminder Tagihan Pembayaran - JoFresh (15 June 2026)', $envelope->subject);

        // Check view and text fallback
        $content = $mail->content();
        $this->assertEquals('emails.payment-reminder', $content->view);
        $this->assertEquals('emails.payment-reminder-text', $content->text);

        // Check custom anti-spam headers
        $headers = $mail->headers();
        $this->assertStringContainsString('reminder.', $headers->messageId);
        $this->assertStringContainsString('@jofresh.com', $headers->messageId);

        $customHeaders = $headers->text;
        $this->assertEquals('OOF, AutoReply', $customHeaders['X-Auto-Response-Suppress']);
        $this->assertEquals('bulk', $customHeaders['Precedence']);
        $this->assertEquals('<http://localhost/pembayaran/' . $mitra->payment_token . '>', $customHeaders['List-Unsubscribe']);
    }

    public function test_mailable_failed_handler_updates_database_to_gagal(): void
    {
        $user = User::factory()->create();
        $mitra = Mitra::create([
            'nama' => 'Budi Santoso',
            'kontak' => '08123456789',
            'email' => 'budi@example.com',
            'alamat' => 'Jl. Mawar No. 123',
            'tanggal_jatuh_tempo' => 15,
            'status' => 'Aktif',
        ]);
        
        $history = ReminderHistory::create([
            'mitra_id' => $mitra->id,
            'user_id' => $user->id,
            'email_penerima' => $mitra->email,
            'tanggal_pengiriman' => now(),
            'status' => 'berhasil',
            'invoice_filename' => 'invoice.pdf',
            'periode_awal' => now()->startOfMonth()->toDateString(),
            'periode_akhir' => now()->endOfMonth()->toDateString(),
            'total_tagihan' => 100000,
        ]);

        $mail = new PaymentReminderMail(
            $mitra,
            collect([]),
            100000,
            'http://localhost/pembayaran/' . $mitra->payment_token,
            '15 June 2026',
            '01 June 2026',
            '15 June 2026',
            '/path/to/nonexistent/file.pdf',
            $history
        );

        // Simulate queue failure
        $mail->failed(new \Exception('SMTP Connection Timeout'));

        // Check if database updated
        $this->assertDatabaseHas('reminder_histories', [
            'id' => $history->id,
            'status' => 'gagal',
            'error_message' => 'SMTP Connection Timeout',
        ]);
    }
}
