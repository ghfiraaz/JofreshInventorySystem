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
    public $transaksiList;
    public $totalTagihan;
    public $periodeAwal = '-';
    public $periodeAkhir = '-';
    protected string $pdfPath;

    public function __construct(Mitra $mitra, string $kodeInvoice, string $pdfPath)
    {
        $this->mitra = $mitra;
        $this->kodeInvoice = $kodeInvoice;
        $this->pdfPath = $pdfPath;

        $invoiceCodes = array_map('trim', explode(',', $kodeInvoice));
        $this->transaksiList = \App\Models\Transaksi::whereIn('no_transaksi', $invoiceCodes)->get();
        $this->totalTagihan = $this->transaksiList->sum('total_harga');

        if ($this->transaksiList->isNotEmpty()) {
            $minDate = $this->transaksiList->min('created_at');
            $maxDate = $this->transaksiList->max('created_at');
            $this->periodeAwal = \Illuminate\Support\Carbon::parse($minDate)->translatedFormat('d M Y');
            $this->periodeAkhir = \Illuminate\Support\Carbon::parse($maxDate)->translatedFormat('d M Y');
        } else {
            $this->periodeAwal = '-';
            $this->periodeAkhir = '-';
        }
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
