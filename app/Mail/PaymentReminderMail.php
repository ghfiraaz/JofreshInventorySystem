<?php

namespace App\Mail;

use App\Models\Mitra;
use App\Models\ReminderHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class PaymentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public Mitra $mitra;
    public Collection $transaksiList;
    public int $totalTagihan;
    public string $paymentLink;
    public string $tanggalTempo;
    public string $periodeAwal;
    public string $periodeAkhir;
    public ReminderHistory $history;

    protected string $pdfPath;

    /**
     * Create a new message instance.
     */
    public function __construct(
        Mitra $mitra,
        Collection $transaksiList,
        int $totalTagihan,
        string $paymentLink,
        string $tanggalTempo,
        string $periodeAwal,
        string $periodeAkhir,
        string $pdfPath,
        ReminderHistory $history
    ) {
        $this->mitra          = $mitra;
        $this->transaksiList  = $transaksiList;
        $this->totalTagihan   = $totalTagihan;
        $this->paymentLink    = $paymentLink;
        $this->tanggalTempo   = $tanggalTempo;
        $this->periodeAwal    = $periodeAwal;
        $this->periodeAkhir   = $periodeAkhir;
        $this->pdfPath        = $pdfPath;
        $this->history        = $history;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Reminder Tagihan Pembayaran - JoFresh ({$this->tanggalTempo})",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-reminder',
            text: 'emails.payment-reminder-text',
        );
    }

    /**
     * Get the message headers.
     */
    public function headers(): Headers
    {
        $domain = parse_url(config('app.url'), PHP_URL_HOST) ?: 'jofresh.com';
        if ($domain === 'localhost') {
            $domain = 'jofresh.com';
        }
        $messageId = 'reminder.' . sha1($this->history->id . '_' . time()) . '@' . $domain;

        return new Headers(
            messageId: $messageId,
            references: [],
            text: [
                'X-Auto-Response-Suppress' => 'OOF, AutoReply',
                'Precedence' => 'bulk',
                'List-Unsubscribe' => '<' . $this->paymentLink . '>',
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        $attachments = [];

        // Attach PDF invoice
        if (file_exists($this->pdfPath)) {
            $attachments[] = Attachment::fromPath($this->pdfPath)
                ->as('Invoice_Rekap_JoFresh_' . str_replace(' ', '_', $this->mitra->nama) . '.pdf')
                ->withMime('application/pdf');
        }

        // Attach QR Code image
        $qrPath = public_path('images/qris-jofresh.png');
        if (file_exists($qrPath)) {
            $attachments[] = Attachment::fromPath($qrPath)
                ->as('QRIS_Pembayaran_JoFresh.png')
                ->withMime('image/png');
        }

        return $attachments;
    }

    /**
     * Handle job failure.
     */
    public function failed(\Throwable $exception): void
    {
        if ($this->history) {
            $this->history->update([
                'status' => 'gagal',
                'error_message' => $exception->getMessage(),
            ]);
        }
    }
}

