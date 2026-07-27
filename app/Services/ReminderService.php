<?php

namespace App\Services;

use App\Mail\PaymentReminderMail;
use App\Models\Mitra;
use App\Models\ReminderHistory;
use App\Models\Transaksi;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Service Reminder Pembayaran
 * Mengelola pengiriman email reminder tagihan pembayaran ke mitra.
 * Termasuk perhitungan periode, generate PDF invoice, dan pencatatan histori.
 */
class ReminderService
{
    /**
     * Mengirim reminder pembayaran ke mitra.
     * Proses: validasi email → hitung periode → ambil transaksi → generate PDF → kirim email → catat histori.
     *
     * @return array{success: bool, message: string}
     */
    public function sendReminder(Mitra $mitra, User $sender): array
    {
        // 1. Validasi email mitra
        if (empty($mitra->email)) {
            return [
                'success' => false,
                'message' => 'Email mitra belum diisi. Silakan update data mitra terlebih dahulu.',
            ];
        }

        // 2. Hitung periode rekapitulasi berdasarkan tanggal jatuh tempo mitra
        [$periodeAwal, $periodeAkhir] = $this->hitungPeriode($mitra->tanggal_jatuh_tempo);

        // 3. Ambil transaksi dalam periode yang belum dibayar
        $transaksiList = Transaksi::with('items')
            ->where('mitra_id', $mitra->id)
            ->where('status_pembayaran', 'Belum Dibayar')
            ->whereBetween('created_at', [
                $periodeAwal->copy()->startOfDay(),
                $periodeAkhir->copy()->endOfDay(),
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        // Jika tidak ada tagihan, kembalikan pesan error
        if ($transaksiList->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Tidak ada tagihan yang belum dibayar pada periode ini.',
            ];
        }

        // Hitung total tagihan
        $totalTagihan = $transaksiList->sum('total_harga');

        // 4. Siapkan data untuk email
        $paymentLink  = url('/pembayaran/' . $mitra->payment_token);
        $tanggalTempo = $periodeAkhir->translatedFormat('d F Y');

        // 5. Generate PDF invoice rekapitulasi
        $pdfFilename = 'Invoice_Rekap_' . str_replace(' ', '_', $mitra->nama) . '_' . $periodeAwal->format('Ymd') . '_' . $periodeAkhir->format('Ymd') . '.pdf';
        $pdfPath     = storage_path('app/invoices/' . $pdfFilename);

        $this->generateInvoicePdf($mitra, $transaksiList, $totalTagihan, $periodeAwal, $periodeAkhir, $pdfPath);

        // 6. Kirim email reminder
        try {
            Mail::to($mitra->email)->send(new PaymentReminderMail(
                $mitra,
                $transaksiList,
                $totalTagihan,
                $paymentLink,
                $tanggalTempo,
                $periodeAwal->translatedFormat('d F Y'),
                $periodeAkhir->translatedFormat('d F Y'),
                $pdfPath
            ));

            // 7. Update tanggal terakhir reminder dikirim di semua transaksi
            Transaksi::where('mitra_id', $mitra->id)
                ->where('status_pembayaran', 'Belum Dibayar')
                ->update(['last_reminder_sent_at' => now()]);

            // Buka kunci upload pembayaran untuk mitra
            $mitra->update(['payment_upload_locked' => false]);

            // 8. Catat histori reminder BERHASIL
            ReminderHistory::create([
                'mitra_id'          => $mitra->id,
                'user_id'           => $sender->id,
                'email_penerima'    => $mitra->email,
                'tanggal_pengiriman'=> now(),
                'status'            => 'berhasil',
                'invoice_filename'  => $pdfFilename,
                'periode_awal'      => $periodeAwal->toDateString(),
                'periode_akhir'     => $periodeAkhir->toDateString(),
                'total_tagihan'     => (int) $totalTagihan,
                'jumlah_transaksi'  => $transaksiList->count(),
            ]);

            Log::info('Reminder email berhasil dikirim', [
                'mitra_id' => $mitra->id,
                'email'    => $mitra->email,
                'total'    => $totalTagihan,
            ]);

            return [
                'success' => true,
                'message' => 'Reminder berhasil dikirim ke ' . $mitra->email,
            ];
        } catch (\Exception $e) {
            // Catat histori reminder GAGAL
            ReminderHistory::create([
                'mitra_id'          => $mitra->id,
                'user_id'           => $sender->id,
                'email_penerima'    => $mitra->email,
                'tanggal_pengiriman'=> now(),
                'status'            => 'gagal',
                'error_message'     => $e->getMessage(),
                'invoice_filename'  => $pdfFilename,
                'periode_awal'      => $periodeAwal->toDateString(),
                'periode_akhir'     => $periodeAkhir->toDateString(),
                'total_tagihan'     => (int) $totalTagihan,
                'jumlah_transaksi'  => $transaksiList->count(),
            ]);

            Log::error('Gagal mengirim email reminder', [
                'mitra_id' => $mitra->id,
                'email'    => $mitra->email,
                'error'    => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal mengirim email: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Menghitung periode rekapitulasi berdasarkan tanggal jatuh tempo mitra.
     *
     * Contoh: jatuh tempo = 15
     *   → periode = 16 bulan lalu s/d 15 bulan ini
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    public function hitungPeriode(int $tanggalJatuhTempo): array
    {
        $now = now();

        // Periode akhir = tanggal jatuh tempo bulan ini
        $periodeAkhir = $now->copy()->day(min($tanggalJatuhTempo, $now->daysInMonth));

        // Jika hari ini sudah melewati tanggal jatuh tempo, gunakan bulan depan
        if ($periodeAkhir->lt($now->copy()->startOfDay())) {
            $bulanDepan   = $now->copy()->addMonth();
            $periodeAkhir = $bulanDepan->day(min($tanggalJatuhTempo, $bulanDepan->daysInMonth));
        }

        // Periode awal = tanggal (jatuh_tempo + 1) bulan sebelumnya
        $bulanSebelum = $periodeAkhir->copy()->subMonth();
        $tanggalAwal  = $tanggalJatuhTempo + 1;

        // Handle overflow (misal jatuh tempo 31, awal = 32 → sesuaikan)
        if ($tanggalAwal > $bulanSebelum->daysInMonth) {
            // Jika tanggal awal melebihi jumlah hari bulan, pakai hari pertama bulan periode akhir
            $periodeAwal = $periodeAkhir->copy()->startOfMonth();
        } else {
            $periodeAwal = $bulanSebelum->day($tanggalAwal);
        }

        return [$periodeAwal, $periodeAkhir];
    }

    /**
     * Membuat file PDF invoice rekapitulasi bulanan.
     * Menyimpan PDF ke path yang ditentukan.
     */
    protected function generateInvoicePdf(
        Mitra $mitra,
        $transaksiList,
        int $totalTagihan,
        Carbon $periodeAwal,
        Carbon $periodeAkhir,
        string $outputPath
    ): void {
        // Pastikan direktori penyimpanan ada
        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Generate PDF dari view
        $pdf = Pdf::loadView('pdf.invoice-rekap', [
            'mitra'          => $mitra,
            'transaksiList'  => $transaksiList,
            'totalTagihan'   => $totalTagihan,
            'periodeAwal'    => $periodeAwal->translatedFormat('d F Y'),
            'periodeAkhir'   => $periodeAkhir->translatedFormat('d F Y'),
            'qrCodePath'     => public_path('images/qris-jofresh.jpeg'),
        ])->setPaper('a4', 'portrait');

        // Simpan PDF ke file
        $pdf->save($outputPath);
    }
}
