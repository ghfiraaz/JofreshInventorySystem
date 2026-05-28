<?php

namespace App\Mail;

use App\Models\Mitra;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Mitra $mitra;
    public string $kodeInvoice;

    public function __construct(Mitra $mitra, string $kodeInvoice)
    {
        $this->mitra = $mitra;
        $this->kodeInvoice = $kodeInvoice;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bukti Pembayaran Tidak Valid - JoFresh Inventory System',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-rejected',
        );
    }
}
