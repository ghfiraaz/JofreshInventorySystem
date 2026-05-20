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
use Illuminate\Support\Facades\Storage;

class ReminderService
{
    /**
     * Kirim reminder pembayaran ke mitra.
     *
     * @return array{success: bool, message: string, history: ?ReminderHistory}
     */
    public function sendReminder(Mitra $mitra, User $sender): array
    {
        // 1. Validasi email mitra
        if (empty($mitra->email)) {
            return [
                'success' => false,
                'message' => 'Email mitra belum diisi. Silakan update data mitra terlebih dahulu.',
                'history' => null,
            ];
        }

        // 2. Validasi max 1 reminder per hari per mitra
        $alreadySentToday = ReminderHistory::where('mitra_id', $mitra->id)
            ->whereDate('tanggal_pengiriman', today())
            ->where('status', 'berhasil')
            ->exists();

        if ($alreadySentToday) {
            return [
                'success' => false,
                'message' => 'Reminder sudah dikirim hari ini ke mitra ini. Maksimal 1 kali per hari.',
                'history' => null,
            ];
        }

        // 3. Hitung periode berdasarkan tanggal_jatuh_tempo mitra
        [$periodeAwal, $periodeAkhir] = $this->hitungPeriode($mitra->tanggal_jatuh_tempo);

        // 4. Ambil transaksi dalam periode (belum dibayar)
        $transaksiList = Transaksi::with('items')
            ->where('mitra_id', $mitra->id)
            ->where('status_pembayaran', 'Belum Dibayar')
            ->whereBetween('created_at', [
                $periodeAwal->copy()->startOfDay(),
                $periodeAkhir->copy()->endOfDay(),
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($transaksiList->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Tidak ada tagihan yang belum dibayar pada periode ini.',
                'history' => null,
            ];
        }

        $totalTagihan = $transaksiList->sum('total_harga');

        // 5. Build data
        $paymentLink  = url('/pembayaran/' . $mitra->payment_token);
        $tanggalTempo = $periodeAkhir->translatedFormat('d F Y');

        // 6. Generate PDF invoice rekapitulasi
        $pdfFilename = 'Invoice_Rekap_' . str_replace(' ', '_', $mitra->nama) . '_' . $periodeAwal->format('Ymd') . '_' . $periodeAkhir->format('Ymd') . '.pdf';
        $pdfPath     = storage_path('app/invoices/' . $pdfFilename);

        $this->generateInvoicePdf($mitra, $transaksiList, $totalTagihan, $periodeAwal, $periodeAkhir, $pdfPath);

        // 7. Kirim email
        try {
            Mail::to($mitra->email)->send(new PaymentReminderMail(
                mitra: $mitra,
                transaksiList: $transaksiList,
                totalTagihan: $totalTagihan,
                paymentLink: $paymentLink,
                tanggalTempo: $tanggalTempo,
                periodeAwal: $periodeAwal->translatedFormat('d F Y'),
                periodeAkhir: $periodeAkhir->translatedFormat('d F Y'),
                pdfPath: $pdfPath
            ));

            // 8. Simpan histori - berhasil
            $history = ReminderHistory::create([
                'mitra_id'           => $mitra->id,
                'user_id'            => $sender->id,
                'email_penerima'     => $mitra->email,
                'tanggal_pengiriman' => now(),
                'status'             => 'berhasil',
                'invoice_filename'   => $pdfFilename,
                'periode_awal'       => $periodeAwal->toDateString(),
                'periode_akhir'      => $periodeAkhir->toDateString(),
                'total_tagihan'      => $totalTagihan,
            ]);

            // 9. Update last_reminder_sent_at di transaksi
            Transaksi::where('mitra_id', $mitra->id)
                ->where('status_pembayaran', 'Belum Dibayar')
                ->update(['last_reminder_sent_at' => now()]);

            return [
                'success' => true,
                'message' => 'Reminder berhasil dikirim ke ' . $mitra->email,
                'history' => $history,
            ];

        } catch (\Exception $e) {
            Log::error('Gagal mengirim reminder email', [
                'mitra_id' => $mitra->id,
                'email'    => $mitra->email,
                'error'    => $e->getMessage(),
            ]);

            // Simpan histori - gagal
            $history = ReminderHistory::create([
                'mitra_id'           => $mitra->id,
                'user_id'            => $sender->id,
                'email_penerima'     => $mitra->email,
                'tanggal_pengiriman' => now(),
                'status'             => 'gagal',
                'error_message'      => $e->getMessage(),
                'invoice_filename'   => $pdfFilename,
                'periode_awal'       => $periodeAwal->toDateString(),
                'periode_akhir'      => $periodeAkhir->toDateString(),
                'total_tagihan'      => $totalTagihan,
            ]);

            return [
                'success' => false,
                'message' => 'Gagal mengirim email: ' . $e->getMessage(),
                'history' => $history,
            ];
        }
    }

    /**
     * Hitung periode rekapitulasi berdasarkan tanggal_jatuh_tempo mitra.
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

        // Handle overflow (misal jatuh tempo 31, awal = 32 → adjust)
        if ($tanggalAwal > $bulanSebelum->daysInMonth) {
            // Jika tanggal awal melebihi jumlah hari bulan, pakai hari pertama bulan periode akhir
            $periodeAwal = $periodeAkhir->copy()->startOfMonth();
        } else {
            $periodeAwal = $bulanSebelum->day($tanggalAwal);
        }

        return [$periodeAwal, $periodeAkhir];
    }

    /**
     * Generate PDF invoice rekapitulasi bulanan.
     */
    protected function generateInvoicePdf(
        Mitra $mitra,
        $transaksiList,
        int $totalTagihan,
        Carbon $periodeAwal,
        Carbon $periodeAkhir,
        string $outputPath
    ): void {
        // Pastikan directory ada
        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $pdf = Pdf::loadView('pdf.invoice-rekap', [
            'mitra'          => $mitra,
            'transaksiList'  => $transaksiList,
            'totalTagihan'   => $totalTagihan,
            'periodeAwal'    => $periodeAwal->translatedFormat('d F Y'),
            'periodeAkhir'   => $periodeAkhir->translatedFormat('d F Y'),
            'qrCodePath'     => public_path('images/qris-jofresh.png'),
        ])->setPaper('a4', 'portrait');

        $pdf->save($outputPath);
    }
}
