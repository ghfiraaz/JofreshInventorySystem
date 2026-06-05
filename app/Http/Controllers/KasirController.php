<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Mitra;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use App\Models\LogStok;
use App\Services\ReminderService;
use App\Mail\PaymentRejectedMail;
use App\Mail\PaymentAcceptedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class KasirController extends Controller
{
    /**
     * Dashboard Kasir.
     */
    public function dashboard()
    {
        $hariIni = today();
        $transaksiHariIni = Transaksi::whereDate('created_at', $hariIni)
            ->where('user_id', Auth::id())
            ->where('status_pembayaran', 'Sudah Dibayar')
            ->get();
            
        $totalPenjualan = $transaksiHariIni->sum('total_harga');
        $totalTransaksi = $transaksiHariIni->count();
        $produkTersedia = Produk::where('stok', '>', 0)->count();

        // Counting belum bayar
        $belumBayar = Transaksi::where('user_id', Auth::id())
            ->whereIn('status_pembayaran', ['Belum Dibayar', 'Menunggu Validasi'])
            ->count();

        // Cek apakah ada tagihan mendekati jatuh tempo (≤ 3 hari)
        $tagihanMendesak = Transaksi::where('user_id', Auth::id())
            ->where('status_pembayaran', 'Belum Dibayar')
            ->whereNotNull('jatuh_tempo')
            ->where('jatuh_tempo', '<=', now()->addDays(3)->toDateString())
            ->count();

        // Menunggu validasi count
        $menungguValidasi = Transaksi::where('user_id', Auth::id())
            ->where('status_pembayaran', 'Menunggu Validasi')
            ->count();

        // Query produk terlaris berdasarkan total kuantitas terjual
        $produkTerlaris = DB::table('transaksi_items')
            ->join('transaksi', 'transaksi.id', '=', 'transaksi_items.transaksi_id')
            ->where('transaksi.status_pembayaran', 'Sudah Dibayar')
            ->where('transaksi.user_id', Auth::id())
            ->select('transaksi_items.nama_produk', DB::raw('SUM(transaksi_items.jumlah) as total_terjual'))
            ->groupBy('transaksi_items.nama_produk')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        $chartLabels = $produkTerlaris->pluck('nama_produk')->toArray();
        $chartData = $produkTerlaris->pluck('total_terjual')->map(function($val) {
            return (int) $val;
        })->toArray();

        // Fallback ke data global jika data spesifik kasir ini masih kosong
        if (empty($chartLabels)) {
            $produkTerlarisGlobal = DB::table('transaksi_items')
                ->join('transaksi', 'transaksi.id', '=', 'transaksi_items.transaksi_id')
                ->where('transaksi.status_pembayaran', 'Sudah Dibayar')
                ->select('transaksi_items.nama_produk', DB::raw('SUM(transaksi_items.jumlah) as total_terjual'))
                ->groupBy('transaksi_items.nama_produk')
                ->orderByDesc('total_terjual')
                ->limit(5)
                ->get();
            $chartLabels = $produkTerlarisGlobal->pluck('nama_produk')->toArray();
            $chartData = $produkTerlarisGlobal->pluck('total_terjual')->map(function($val) {
                return (int) $val;
            })->toArray();
        }

        return view('kasir.dashboard', compact(
            'totalPenjualan', 'totalTransaksi', 'produkTersedia',
            'belumBayar', 'tagihanMendesak', 'menungguValidasi',
            'chartLabels', 'chartData'
        ));
    }

    /**
     * Transaksi Penjualan (POS) page.
     */
    public function transaksi()
    {
        $produk = Produk::where('stok', '>', 0)->orderBy('nama')->get();
        $mitra  = Mitra::where('status', 'Aktif')->orderBy('nama')->get();

        return view('kasir.transaksi', compact('produk', 'mitra'));
    }

    /**
     * Store a new transaction from POS.
     */
    public function storeTransaksi(Request $request)
    {
        $request->validate([
            'mitra_id'           => 'required|exists:mitra,id',
            'items'              => 'required|array|min:1',
            'items.*.produk_id'  => 'required|exists:produk,id',
            'items.*.jumlah'     => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request) {
            $totalHarga = 0;
            $totalItem  = 0;
            $totalBerat = 0;
            $itemsData  = [];

            foreach ($request->items as $item) {
                $produk = Produk::findOrFail($item['produk_id']);

                if ($produk->stok < $item['jumlah']) {
                    return response()->json([
                        'message' => "Stok {$produk->nama} tidak cukup. Tersisa: {$produk->stok} ekor."
                    ], 422);
                }

                $subtotal = $produk->harga * $item['jumlah'];
                $totalHarga += $subtotal;
                $totalItem  += $item['jumlah'];
                $totalBerat += $item['jumlah'];

                $itemsData[] = [
                    'produk_id'    => $produk->id,
                    'nama_produk'  => $produk->nama,
                    'jumlah'       => $item['jumlah'],
                    'harga_satuan' => $produk->harga,
                    'subtotal'     => $subtotal,
                ];

                // Catat stok sebelum decrement untuk log
                $stokSebelumKasir = $produk->stok;
                $produk->decrement('stok', $item['jumlah']);
                $produk->refresh();

                // Catat log stok keluar otomatis
                LogStok::create([
                    'produk_id'    => $produk->id,
                    'user_id'      => Auth::id(),
                    'tipe'         => 'Keluar',
                    'jumlah'       => $item['jumlah'],
                    'stok_sebelum' => $stokSebelumKasir,
                    'stok_sesudah' => $produk->stok,
                    'keterangan'   => 'Penjualan kasir',
                ]);

                // Trigger low stock alert for Admin
                \App\Models\Notification::triggerLowStockAlert($produk);
            }

            // Calculate jatuh tempo based on mitra's tanggal_jatuh_tempo
            $mitra = Mitra::findOrFail($request->mitra_id);
            $jatuhTempo = $this->hitungJatuhTempo($mitra->tanggal_jatuh_tempo);

            $lastTxn = Transaksi::whereDate('created_at', today())->count();
            $noTransaksi = 'JFR-' . date('Ymd') . '-' . str_pad($lastTxn + 1, 3, '0', STR_PAD_LEFT);

            $transaksi = Transaksi::create([
                'no_transaksi'       => $noTransaksi,
                'user_id'            => Auth::id(),
                'mitra_id'           => $request->mitra_id,
                'total_item'         => $totalItem,
                'total_harga'        => $totalHarga,
                'total_berat'        => $totalBerat,
                'metode_pembayaran'  => 'Tempo',
                'status_pembayaran'  => 'Belum Dibayar',
                'jatuh_tempo'        => $jatuhTempo,
            ]);

            foreach ($itemsData as $itemData) {
                $transaksi->items()->create($itemData);
            }

            $transaksi->load('mitra', 'items');

            return response()->json([
                'message'   => 'Transaksi berhasil disimpan',
                'transaksi' => $transaksi,
            ], 201);
        });
    }

    /**
     * Hitung tanggal jatuh tempo berikutnya berdasarkan tanggal mitra
     */
    private function hitungJatuhTempo(int $tanggal): Carbon
    {
        $now = now();
        $bulanIni = $now->copy()->day(min($tanggal, $now->daysInMonth));
        
        // Jika tanggal jatuh tempo bulan ini sudah lewat, pakai bulan depan
        if ($bulanIni->lte($now)) {
            $bulanDepan = $now->copy()->addMonth();
            return $bulanDepan->day(min($tanggal, $bulanDepan->daysInMonth));
        }
        
        return $bulanIni;
    }

    /**
     * Riwayat Transaksi page.
     */
    public function riwayat(Request $request)
    {
        $query = Transaksi::with(['mitra', 'items'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        if ($request->has('date') && $request->date != '') {
            $query->whereDate('created_at', $request->date);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status_pembayaran', $request->status);
        }

        $transaksi = $query->get();

        $totalTransaksi  = $transaksi->count();
        $totalPendapatan = $transaksi->sum('total_harga');
        $totalItemSold   = $transaksi->sum('total_berat');
        $filterDate      = $request->get('date', '');
        $filterStatus    = $request->get('status', '');

        return view('kasir.riwayat', compact(
            'transaksi', 'totalTransaksi', 'totalPendapatan', 'totalItemSold', 'filterDate', 'filterStatus'
        ));
    }

    /**
     * Tagihan / Belum Dibayar page.
     */
    public function tagihan(Request $request)
    {
        $transaksi = Transaksi::with(['mitra', 'items'])
            ->where('user_id', Auth::id())
            ->whereIn('status_pembayaran', ['Belum Dibayar', 'Menunggu Validasi', 'Ditolak'])
            ->orderBy('jatuh_tempo', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        $grouped = $transaksi->groupBy('mitra_id');
        $mitraTagihan = [];

        foreach ($grouped as $mitraId => $txns) {
            $mitra = $txns->first()->mitra;
            if (!$mitra) continue;

            // Calculate closest jatuh tempo for this mitra
            $closestTempo = $txns->whereNotNull('jatuh_tempo')
                ->pluck('jatuh_tempo')
                ->sort()
                ->first();

            $sisaHari = null;
            $isTempoMerah = false;
            $isLewatTempo = false;
            if ($closestTempo) {
                $sisaHari = (int) now()->startOfDay()->diffInDays($closestTempo, false);
                $isTempoMerah = $sisaHari <= 3;
                $isLewatTempo = $sisaHari < 0;
            }

            // H-3 logic: reminder hanya boleh dikirim jika sisa hari <= 3 (termasuk lewat tempo)
            $canSendReminder = $sisaHari !== null && $sisaHari <= 3;

            $mitraTagihan[] = [
                'mitra'              => $mitra,
                'transaksi'          => $txns,
                'total'              => $txns->sum('total_harga'),
                'count'              => $txns->count(),
                'closestTempo'       => $closestTempo,
                'sisaHari'           => $sisaHari,
                'isTempoMerah'       => $isTempoMerah,
                'isLewatTempo'       => $isLewatTempo,
                'canSendReminder'    => $canSendReminder,
            ];
        }

        // Sort by closest jatuh tempo (nearest first, null last)
        usort($mitraTagihan, function ($a, $b) {
            if ($a['sisaHari'] === null && $b['sisaHari'] === null) return 0;
            if ($a['sisaHari'] === null) return 1;
            if ($b['sisaHari'] === null) return -1;
            return $a['sisaHari'] <=> $b['sisaHari'];
        });

        $totalMitra   = count($mitraTagihan);
        $totalTagihan = $transaksi->sum('total_harga');
        $menungguValidasi = $transaksi->where('status_pembayaran', 'Menunggu Validasi')->count();

        return view('kasir.tagihan', compact(
            'mitraTagihan', 'totalMitra', 'totalTagihan', 'menungguValidasi'
        ));
    }

    /**
     * Kirim reminder pembayaran via email otomatis
     */
    public function sendReminder(Request $request, ReminderService $reminderService)
    {
        $request->validate([
            'mitra_id' => 'required|exists:mitra,id',
        ]);

        $mitra  = Mitra::findOrFail($request->mitra_id);
        $sender = Auth::user();

        $result = $reminderService->sendReminder($mitra, $sender);

        if (!$result['success']) {
            return response()->json(['message' => $result['message']], 422);
        }

        return response()->json([
            'message' => $result['message'],
        ]);
    }

    /**
     * Validasi bukti pembayaran dari mitra (terima/tolak)
     */
    public function validasiBuktiPembayaran(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:terima,tolak',
        ]);

        $transaksi = Transaksi::with(['mitra', 'items'])->where('user_id', Auth::id())->findOrFail($id);

        if ($transaksi->status_pembayaran !== 'Menunggu Validasi') {
            return response()->json(['message' => 'Transaksi tidak dalam status menunggu validasi.'], 422);
        }

        $mitra = $transaksi->mitra;

        if ($request->action === 'terima') {
            $transaksi->update([
                'status_pembayaran' => 'Sudah Dibayar',
                'updated_at' => now(),
            ]);

            // Trigger Laporan Penjualan Hari Ini notification for Owner
            \App\Models\Notification::triggerLaporanPenjualan();

            // Generate PDF invoice LUNAS dan kirim email
            $this->sendPaymentAcceptedEmail($transaksi, $mitra);

            return response()->json(['message' => 'Pembayaran berhasil diterima. Email konfirmasi telah dikirim.', 'status' => 'Sudah Dibayar']);
        } else {
            $transaksi->update([
                'status_pembayaran' => 'Ditolak',
                'updated_at' => now(),
            ]);

            // Unlock payment upload for the Mitra
            $mitra->update(['payment_upload_locked' => false]);

            // Kirim email notifikasi penolakan
            $this->sendPaymentRejectedEmail($transaksi, $mitra);

            return response()->json(['message' => 'Pembayaran berhasil ditolak. Email notifikasi telah dikirim ke mitra.', 'status' => 'Ditolak']);
        }
    }

    /**
     * Validasi semua bukti pembayaran per mitra (terima/tolak)
     */
    public function validasiBuktiPerMitra(Request $request)
    {
        $request->validate([
            'mitra_id' => 'required|exists:mitra,id',
            'action'   => 'required|in:terima,tolak',
        ]);

        $mitra = Mitra::findOrFail($request->mitra_id);

        $transaksiList = Transaksi::with('items')
            ->where('user_id', Auth::id())
            ->where('mitra_id', $request->mitra_id)
            ->where('status_pembayaran', 'Menunggu Validasi')
            ->get();

        if ($transaksiList->isEmpty()) {
            return response()->json(['message' => 'Tidak ada transaksi yang menunggu validasi.'], 422);
        }

        if ($request->action === 'terima') {
            foreach ($transaksiList as $transaksi) {
                $transaksi->update([
                    'status_pembayaran' => 'Sudah Dibayar',
                    'updated_at' => now(),
                ]);
            }

            // Trigger Laporan Penjualan Hari Ini notification for Owner
            \App\Models\Notification::triggerLaporanPenjualan();

            // Kirim 1 email konfirmasi untuk semua transaksi yang diterima
            $this->sendPaymentAcceptedEmailBulk($transaksiList, $mitra);

            return response()->json(['message' => "Berhasil menerima {$transaksiList->count()} transaksi. Email konfirmasi telah dikirim."]);
        } else {
            foreach ($transaksiList as $transaksi) {
                $transaksi->update([
                    'status_pembayaran' => 'Ditolak',
                    'updated_at' => now(),
                ]);
            }

            // Unlock payment upload for the Mitra
            $mitra->update(['payment_upload_locked' => false]);

            // Kirim 1 email notifikasi penolakan
            $noInvoices = $transaksiList->pluck('no_transaksi')->join(', ');
            $this->sendPaymentRejectedEmail($transaksiList->first(), $mitra);

            return response()->json(['message' => "Berhasil menolak {$transaksiList->count()} transaksi. Email notifikasi telah dikirim ke mitra."]);
        }
    }

    /**
     * Proses pembayaran tagihan mitra (legacy - kept for compatibility)
     */
    public function bayarTagihan(Request $request)
    {
        $request->validate([
            'mitra_id' => 'required|exists:mitra,id',
            'bulan'    => 'required|integer|min:1|max:12',
            'tahun'    => 'required|integer',
        ]);

        Transaksi::where('user_id', Auth::id())
            ->where('mitra_id', $request->mitra_id)
            ->whereMonth('created_at', $request->bulan)
            ->whereYear('created_at', $request->tahun)
            ->where('status_pembayaran', 'Belum Dibayar')
            ->update([
                'status_pembayaran' => 'Sudah Dibayar',
                'updated_at'        => now()
            ]);

        // Trigger Laporan Penjualan Hari Ini notification for Owner
        \App\Models\Notification::triggerLaporanPenjualan();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Pembayaran berhasil dikonfirmasi.']);
        }

        return redirect('/kasir/riwayat')->with('success', 'Pembayaran berhasil dikonfirmasi.');
    }

    /**
     * Tampilkan invoice digital untuk transaksi tertentu
     */
    public function invoice($id)
    {
        $transaksi = Transaksi::with(['mitra', 'items'])->where('user_id', Auth::id())->findOrFail($id);
        return view('kasir.invoice', compact('transaksi'));
    }

    /**
     * Download PDF invoice (dengan watermark LUNAS jika sudah dibayar)
     */
    public function downloadInvoicePdf($id)
    {
        $transaksi = Transaksi::with(['mitra', 'items'])->where('user_id', Auth::id())->findOrFail($id);
        $mitra = $transaksi->mitra;

        $isLunas = $transaksi->status_pembayaran === 'Sudah Dibayar';

        $pdf = Pdf::loadView('pdf.invoice-lunas', [
            'transaksi'  => $transaksi,
            'mitra'      => $mitra,
            'isLunas'    => $isLunas,
        ])->setPaper('a4', 'portrait');

        $prefix = $isLunas ? 'Invoice-LUNAS-' : 'Invoice-';
        $filename = $prefix . $transaksi->no_transaksi . '.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Serve bukti pembayaran file (bypasses PHP built-in server junction issues)
     */
    public function showBuktiPembayaran($filename)
    {
        $path = 'bukti-pembayaran/' . $filename;

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File bukti pembayaran tidak ditemukan.');
        }

        return response()->file(
            Storage::disk('public')->path($path)
        );
    }

    // ========================
    // Private Helper Methods
    // ========================

    /**
     * Kirim email pembayaran diterima (single transaksi)
     */
    private function sendPaymentAcceptedEmail(Transaksi $transaksi, Mitra $mitra): void
    {
        if (empty($mitra->email)) return;

        try {
            // Generate PDF invoice LUNAS
            $pdfPath = $this->generateInvoiceLunasPdf($transaksi, $mitra);

            Mail::to($mitra->email)->send(new PaymentAcceptedMail(
                $mitra,
                $transaksi->no_transaksi,
                $pdfPath
            ));

            Log::info('Email pembayaran diterima terkirim', [
                'mitra' => $mitra->nama,
                'invoice' => $transaksi->no_transaksi,
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email pembayaran diterima', [
                'mitra_id' => $mitra->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Kirim email pembayaran diterima (bulk/semua transaksi mitra)
     */
    private function sendPaymentAcceptedEmailBulk($transaksiList, Mitra $mitra): void
    {
        if (empty($mitra->email)) return;

        try {
            // Generate PDF untuk transaksi pertama sebagai representasi
            $firstTransaksi = $transaksiList->first();
            $pdfPath = $this->generateInvoiceLunasPdf($firstTransaksi, $mitra);

            $noInvoices = $transaksiList->pluck('no_transaksi')->join(', ');

            Mail::to($mitra->email)->send(new PaymentAcceptedMail(
                $mitra,
                $noInvoices,
                $pdfPath
            ));

            Log::info('Email bulk pembayaran diterima terkirim', [
                'mitra' => $mitra->nama,
                'jumlah' => $transaksiList->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email bulk pembayaran diterima', [
                'mitra_id' => $mitra->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Kirim email pembayaran ditolak
     */
    private function sendPaymentRejectedEmail(Transaksi $transaksi, Mitra $mitra): void
    {
        if (empty($mitra->email)) return;

        try {
            Mail::to($mitra->email)->send(new PaymentRejectedMail(
                $mitra,
                $transaksi->no_transaksi
            ));

            Log::info('Email pembayaran ditolak terkirim', [
                'mitra' => $mitra->nama,
                'invoice' => $transaksi->no_transaksi,
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email pembayaran ditolak', [
                'mitra_id' => $mitra->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate PDF invoice LUNAS untuk satu transaksi
     */
    private function generateInvoiceLunasPdf(Transaksi $transaksi, Mitra $mitra): string
    {
        $dir = storage_path('app/invoices');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = 'Invoice-LUNAS-' . $transaksi->no_transaksi . '.pdf';
        $pdfPath = $dir . '/' . $filename;

        $pdf = Pdf::loadView('pdf.invoice-lunas', [
            'transaksi'  => $transaksi,
            'mitra'      => $mitra,
            'isLunas'    => true,
        ])->setPaper('a4', 'portrait');

        $pdf->save($pdfPath);

        return $pdfPath;
    }
}
