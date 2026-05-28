<?php

namespace Tests\Feature;

use App\Mail\PaymentReminderMail;
use App\Models\Mitra;
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

    public function test_send_reminder_successfully_dispatches_email(): void
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
            'created_at' => now(),
        ]);

        // 4. Run ReminderService
        $service = new ReminderService();
        $result = $service->sendReminder($mitra, $user);

        // 5. Assertions
        $this->assertTrue($result['success']);

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

    public function test_payment_reminder_mail_envelope_and_content(): void
    {
        $mitra = Mitra::create([
            'nama' => 'Budi Santoso',
            'kontak' => '08123456789',
            'email' => 'budi@example.com',
            'alamat' => 'Jl. Mawar No. 123',
            'tanggal_jatuh_tempo' => 15,
            'status' => 'Aktif',
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
            '/path/to/nonexistent/file.pdf'
        );

        // Check envelope subject
        $envelope = $mail->envelope();
        $this->assertEquals('Reminder Tagihan Pembayaran - JoFresh (15 June 2026)', $envelope->subject);

        // Check view and text fallback
        $content = $mail->content();
        $this->assertEquals('emails.payment-reminder', $content->view);
        $this->assertEquals('emails.payment-reminder-text', $content->text);
    }

    public function test_send_reminder_fails_without_email(): void
    {
        $user = User::factory()->create();

        $mitra = Mitra::create([
            'nama' => 'Tanpa Email',
            'kontak' => '08123456789',
            'email' => null,
            'alamat' => 'Jl. Mawar No. 123',
            'tanggal_jatuh_tempo' => 15,
            'status' => 'Aktif',
        ]);

        $service = new ReminderService();
        $result = $service->sendReminder($mitra, $user);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Email mitra belum diisi', $result['message']);
    }
}
