<?php

namespace App\Mail;

use App\Models\Mitra;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
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
}
