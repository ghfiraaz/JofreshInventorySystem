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

/**
 * Email Reminder Pembayaran
 * Mengirim email pengingat tagihan pembayaran ke mitra.
 * Berisi detail transaksi, total tagihan, link pembayaran, dan lampiran PDF invoice.
 */
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
     * Membuat instance email reminder pembayaran.
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
     * Menentukan subjek email.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Reminder Tagihan Pembayaran - JoFresh ({$this->tanggalTempo})",
        );
    }

    /**
     * Menentukan konten/template email.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-reminder',
            text: 'emails.payment-reminder-text',
        );
    }

    /**
     * Menentukan lampiran email.
     * Melampirkan PDF invoice rekapitulasi dan gambar QRIS.
     */
    public function attachments(): array
    {
        $attachments = [];

        // Lampirkan PDF invoice rekapitulasi
        if (file_exists($this->pdfPath)) {
            $attachments[] = Attachment::fromPath($this->pdfPath)
                ->as('Invoice_Rekap_JoFresh_' . str_replace(' ', '_', $this->mitra->nama) . '.pdf')
                ->withMime('application/pdf');
        }

        // Lampirkan gambar QR Code pembayaran
        $qrPath = public_path('images/qris-jofresh.jpeg');
        if (file_exists($qrPath)) {
            $attachments[] = Attachment::fromPath($qrPath)
                ->as('QRIS_Pembayaran_JoFresh.jpeg')
                ->withMime('image/jpeg');
        }

        return $attachments;
    }
}
