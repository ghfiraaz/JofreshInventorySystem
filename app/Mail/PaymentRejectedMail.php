<?php

namespace App\Mail;

use App\Models\Mitra;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email Pembayaran Ditolak
 * Mengirim email notifikasi ke mitra bahwa bukti pembayaran yang diunggah tidak valid/ditolak.
 * Mitra diminta untuk mengunggah ulang bukti pembayaran yang benar.
 */
class PaymentRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Mitra $mitra;
    public string $kodeInvoice;

    /**
     * Membuat instance email pembayaran ditolak.
     */
    public function __construct(Mitra $mitra, string $kodeInvoice)
    {
        $this->mitra = $mitra;
        $this->kodeInvoice = $kodeInvoice;
    }

    /**
     * Menentukan subjek email.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bukti Pembayaran Tidak Valid - JoFresh Inventory System',
        );
    }

    /**
     * Menentukan konten/template email.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-rejected',
        );
    }
}
