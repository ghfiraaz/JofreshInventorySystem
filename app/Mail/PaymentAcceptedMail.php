<?php

namespace App\Mail;

use App\Models\Mitra;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class PaymentAcceptedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Mitra $mitra;
    public string $kodeInvoice;
    protected string $pdfPath;

    public function __construct(Mitra $mitra, string $kodeInvoice, string $pdfPath)
    {
        $this->mitra = $mitra;
        $this->kodeInvoice = $kodeInvoice;
        $this->pdfPath = $pdfPath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pembayaran Berhasil Diverifikasi - JoFresh Inventory System',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-accepted',
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        if (file_exists($this->pdfPath)) {
            // Extract invoice code for filename
            $invoiceCode = str_replace(', ', '_', $this->kodeInvoice);
            $attachments[] = Attachment::fromPath($this->pdfPath)
                ->as('Invoice-LUNAS-' . $invoiceCode . '.pdf')
                ->withMime('application/pdf');
        }

        return $attachments;
    }
}
