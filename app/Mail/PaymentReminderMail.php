<?php

namespace App\Mail;

use App\Models\Mitra;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class PaymentReminderMail extends Mailable
{
    use SerializesModels;

    public Mitra $mitra;
    public Collection $transaksiList;
    public int $totalTagihan;
    public string $paymentLink;
    public string $tanggalTempo;
    public string $periodeAwal;
    public string $periodeAkhir;

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
        string $pdfPath
    ) {
        $this->mitra          = $mitra;
        $this->transaksiList  = $transaksiList;
        $this->totalTagihan   = $totalTagihan;
        $this->paymentLink    = $paymentLink;
        $this->tanggalTempo   = $tanggalTempo;
        $this->periodeAwal    = $periodeAwal;
        $this->periodeAkhir   = $periodeAkhir;
        $this->pdfPath        = $pdfPath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Rekapitulasi Transaksi ' . $this->mitra->nama . ' - JoFresh',
            replyTo: [config('mail.from.address')],
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
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        $attachments = [];

        // Attach PDF invoice only
        if (file_exists($this->pdfPath)) {
            $attachments[] = Attachment::fromPath($this->pdfPath)
                ->as('Rekapitulasi_' . str_replace(' ', '_', $this->mitra->nama) . '.pdf')
                ->withMime('application/pdf');
        } else {
            Log::warning('Invoice PDF not found for email attachment', ['path' => $this->pdfPath]);
        }

        return $attachments;
    }
}
