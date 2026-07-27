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

/**
 * Controller Kasir
 * Mengelola seluruh fitur kasir: dashboard, transaksi POS, tagihan, reminder,
 * validasi bukti pembayaran, dan invoice.
 */
class KasirController extends Controller
{
    /**
     * Menampilkan dashboard kasir.
     * Berisi ringkasan penjualan hari ini, tagihan, stok, dan chart statistik.
     */
    public function dashboard()
    {
        $hariIni = today();

        // Ambil transaksi hari ini yang sudah dibayar oleh kasir yang login
        $transaksiHariIni = Transaksi::whereDate('created_at', $hariIni)
            ->where('user_id', Auth::id())
            ->where('status_pembayaran', 'Sudah Dibayar')
            ->get();
            
        // Hitung total penjualan dan transaksi hari ini
        $totalPenjualan = $transaksiHariIni->sum('total_harga');
        $totalTransaksi = $transaksiHariIni->count();
        $produkTersedia = Produk::where('stok', '>', 0)->count();

        // Hitung jumlah tagihan belum dibayar
        $belumBayar = Transaksi::where('user_id', Auth::id())
            ->whereIn('status_pembayaran', ['Belum Dibayar', 'Menunggu Validasi'])
            ->count();

        // Cek apakah ada tagihan mendekati jatuh tempo (≤ 3 hari)
        $tagihanMendesak = Transaksi::where('user_id', Auth::id())
            ->where('status_pembayaran', 'Belum Dibayar')
            ->whereNotNull('jatuh_tempo')
            ->where('jatuh_tempo', '<=', now()->addDays(3)->toDateString())
            ->count();

        // Hitung jumlah transaksi menunggu validasi
        $menungguValidasi = Transaksi::where('user_id', Auth::id())
            ->where('status_pembayaran', 'Menunggu Validasi')
            ->count();

        // Data ringkasan untuk kartu statistik
        $totalMitra    = Mitra::count();
        $totalStok     = Produk::sum('stok');
        $stokRendahCount = Produk::whereColumn('stok', '<', 'stok_minimal')->count();
        $isStokRendah  = $stokRendahCount > 0;

        // Ambil daftar produk untuk tabel stok
        $produkList = Produk::orderBy('nama')->get()->map(function ($p) {
            $isRendah = $p->stok < $p->stok_minimal;
            $isHabis  = $p->stok <= 0;
            $p->harga_format = 'Rp ' . number_format($p->harga, 0, ',', '.');
            $p->status = $isHabis ? 'Stok Habis' : ($isRendah ? 'Stok Rendah' : 'Tersedia');
            return $p;
        });

        // --- Chart: Tren Penjualan Bulanan (12 bulan terakhir) ---
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $startOf12Months = Carbon::now()->startOfMonth()->subMonths(11);

        // Sesuaikan query berdasarkan driver database (SQLite/MySQL)
        $isSqlite = \Illuminate\Support\Facades\DB::getDriverName() === 'sqlite';
        $selectRaw = $isSqlite 
            ? "strftime('%Y', created_at) as tahun, strftime('%m', created_at) as bulan, SUM(total_harga) as total"
            : "YEAR(created_at) as tahun, MONTH(created_at) as bulan, SUM(total_harga) as total";

        // Ambil data penjualan bulanan
        $monthlyDataRaw = Transaksi::where('status_pembayaran', 'Sudah Dibayar')
            ->where('created_at', '>=', $startOf12Months)
            ->selectRaw($selectRaw)
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get()
            ->keyBy(fn($row) => $row->tahun . '-' . str_pad($row->bulan, 2, '0', STR_PAD_LEFT));

        // Susun data chart tren penjualan
        $trendLabels = [];
        $trendData   = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->startOfMonth()->subMonths($i);
            $key   = $month->format('Y-m');
            $trendLabels[] = $monthNames[(int)$month->format('n') - 1] . ' ' . $month->format('Y');
            $trendData[]   = isset($monthlyDataRaw[$key]) ? (int) $monthlyDataRaw[$key]->total : 0;
        }

        // --- Chart: Produk Terlaris (Bar Chart) ---
        $produkTerlaris = DB::table('transaksi_items')
            ->join('transaksi', 'transaksi.id', '=', 'transaksi_items.transaksi_id')
            ->where('transaksi.status_pembayaran', 'Sudah Dibayar')
            ->select('transaksi_items.nama_produk', DB::raw('SUM(transaksi_items.jumlah) as total_terjual'))
            ->groupBy('transaksi_items.nama_produk')
            ->orderByDesc('total_terjual')
            ->limit(6)
            ->get();

        // Susun data chart produk terlaris
        $distLabels = $produkTerlaris->pluck('nama_produk')->toArray();
        $distData   = $produkTerlaris->pluck('total_terjual')->map(fn($v) => (int) $v)->toArray();

        // Data chart untuk quick actions
        $chartLabels = $distLabels;
        $chartData   = $distData;

        // Fallback jika data kosong
        if (empty($distLabels)) {
            $chartLabels = [];
            $chartData   = [];
        }

        // Variabel filter periode (untuk konsistensi view)
        $hasFilter   = false;
        $periodLabel = '';
        $filterMode  = '';
        $filterMonth = '';
        $filterYear  = '';
        $filterStart = '';
        $filterEnd   = '';

        return view('kasir.dashboard', compact(
            'totalPenjualan', 'totalTransaksi', 'produkTersedia',
            'belumBayar', 'tagihanMendesak', 'menungguValidasi',
            'totalMitra', 'totalStok', 'isStokRendah', 'stokRendahCount',
            'produkList',
            'trendLabels', 'trendData',
            'distLabels', 'distData',
            'chartLabels', 'chartData',
            'hasFilter', 'periodLabel',
            'filterMode', 'filterMonth', 'filterYear', 'filterStart', 'filterEnd'
        ));
    }

    /**
     * Menampilkan halaman transaksi penjualan (POS).
     * Menampilkan daftar produk yang tersedia dan mitra aktif.
     */
    public function transaksi()
    {
        // Ambil produk yang stoknya masih ada dan mitra yang aktif
        $produk = Produk::where('stok', '>', 0)->orderBy('nama')->get();
        $mitra  = Mitra::where('status', 'Aktif')->orderBy('nama')->get();

        return view('kasir.transaksi', compact('produk', 'mitra'));
    }

    /**
     * Menyimpan transaksi baru dari POS.
     * Validasi stok, kurangi stok produk, catat log stok keluar, dan buat transaksi.
     */
    public function storeTransaksi(Request $request)
    {
        // Validasi input transaksi
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

            // Proses setiap item dalam transaksi
            foreach ($request->items as $item) {
                $produk = Produk::findOrFail($item['produk_id']);

                // Validasi ketersediaan stok
                if ($produk->stok < $item['jumlah']) {
                    return response()->json([
                        'message' => "Stok {$produk->nama} tidak cukup. Tersisa: {$produk->stok} ekor."
                    ], 422);
                }

                // Hitung subtotal per item
                $subtotal = $produk->harga * $item['jumlah'];
                $totalHarga += $subtotal;
                $totalItem  += $item['jumlah'];
                $totalBerat += $item['jumlah'];

                // Simpan data item untuk disimpan ke database
                $itemsData[] = [
                    'produk_id'    => $produk->id,
                    'nama_produk'  => $produk->nama,
                    'jumlah'       => $item['jumlah'],
                    'harga_satuan' => $produk->harga,
                    'subtotal'     => $subtotal,
                ];

                // Catat stok sebelum pengurangan untuk log
                $stokSebelumKasir = $produk->stok;

                // Kurangi stok produk
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

                // Trigger notifikasi stok rendah untuk Admin
                \App\Models\Notification::triggerLowStockAlert($produk);
            }

            // Hitung tanggal jatuh tempo berdasarkan pengaturan mitra
            $mitra = Mitra::findOrFail($request->mitra_id);
            $jatuhTempo = $this->hitungJatuhTempo($mitra->tanggal_jatuh_tempo);

            // Generate nomor transaksi unik (format: JFR-YYYYMMDD-XXX)
            $lastTxn = Transaksi::whereDate('created_at', today())->count();
            $noTransaksi = 'JFR-' . date('Ymd') . '-' . str_pad($lastTxn + 1, 3, '0', STR_PAD_LEFT);

            // Simpan transaksi ke database
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

            // Simpan item-item transaksi
            foreach ($itemsData as $itemData) {
                $transaksi->items()->create($itemData);
            }

            // Muat relasi mitra dan items
            $transaksi->load('mitra', 'items');

            return response()->json([
                'message'   => 'Transaksi berhasil disimpan',
                'transaksi' => $transaksi,
            ], 201);
        });
    }

    /**
     * Menghitung tanggal jatuh tempo berikutnya berdasarkan tanggal mitra.
     * Jika tanggal bulan ini sudah lewat, gunakan bulan depan.
     */
    private function hitungJatuhTempo(int $tanggal): Carbon
    {
        $now = now();
        $bulanIni = $now->copy()->day(min($tanggal, $now->daysInMonth));
        
        // Jika tanggal jatuh tempo bulan ini sudah lewat, pakai bulan depan
        if ($bulanIni->lt($now)) {
            $bulanDepan = $now->copy()->addMonth();
            return $bulanDepan->day(min($tanggal, $bulanDepan->daysInMonth));
        }
        
        return $bulanIni;
    }

    /**
     * Menampilkan halaman tagihan / belum dibayar.
     * Mengelompokkan transaksi per mitra dengan informasi jatuh tempo.
     */
    public function tagihan(Request $request)
    {
        // Ambil semua transaksi belum dibayar milik kasir yang login
        $transaksi = Transaksi::with(['mitra', 'items'])
            ->where('user_id', Auth::id())
            ->whereIn('status_pembayaran', ['Belum Dibayar', 'Menunggu Validasi', 'Ditolak'])
            ->orderBy('jatuh_tempo', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Kelompokkan transaksi berdasarkan mitra
        $grouped = $transaksi->groupBy('mitra_id');
        $mitraTagihan = [];

        foreach ($grouped as $mitraId => $txns) {
            $mitra = $txns->first()->mitra;
            if (!$mitra) continue;

            // Hitung jatuh tempo terdekat untuk mitra ini
            $closestTempo = $txns->whereNotNull('jatuh_tempo')
                ->pluck('jatuh_tempo')
                ->sort()
                ->first();

            // Hitung sisa hari dan status jatuh tempo
            $sisaHari = null;
            $isTempoMerah = false;
            $isLewatTempo = false;
            if ($closestTempo) {
                $sisaHari = (int) now()->startOfDay()->diffInDays($closestTempo, false);
                $isTempoMerah = $sisaHari <= 3;
                $isLewatTempo = $sisaHari < 0;
            }

            // Logika H-3: reminder hanya boleh dikirim jika sisa hari ≤ 3
            // dan harus ada transaksi yang perlu di-remind (Belum Dibayar / Ditolak)
            $hasRemindableTransaksi = $txns->contains(function ($txn) {
                return in_array($txn->status_pembayaran, ['Belum Dibayar', 'Ditolak']);
            });
            $canSendReminder = $sisaHari !== null && $sisaHari <= 3 && $hasRemindableTransaksi;

            // Cek apakah reminder sudah dikirim hari ini
            $reminderSentToday = $txns->contains(function ($txn) {
                return $txn->last_reminder_sent_at && $txn->last_reminder_sent_at->isToday();
            });

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
                'reminderSentToday'  => $reminderSentToday,
            ];
        }

        // Urutkan berdasarkan jatuh tempo terdekat (yang paling mendesak di atas)
        usort($mitraTagihan, function ($a, $b) {
            if ($a['sisaHari'] === null && $b['sisaHari'] === null) return 0;
            if ($a['sisaHari'] === null) return 1;
            if ($b['sisaHari'] === null) return -1;
            
            $diffA = abs($a['sisaHari']);
            $diffB = abs($b['sisaHari']);
            
            if ($diffA === $diffB) {
                return $a['sisaHari'] <=> $b['sisaHari'];
            }
            return $diffA <=> $diffB;
        });

        // Hitung ringkasan tagihan
        $totalMitra   = count($mitraTagihan);
        $totalTagihan = $transaksi->sum('total_harga');
        $menungguValidasi = $transaksi->where('status_pembayaran', 'Menunggu Validasi')->count();

        return view('kasir.tagihan', compact(
            'mitraTagihan', 'totalMitra', 'totalTagihan', 'menungguValidasi'
        ));
    }

    /**
     * Mengirim reminder pembayaran via email ke mitra.
     * Hanya bisa dikirim jika sisa hari ≤ 3 (H-3 sebelum jatuh tempo).
     */
    public function sendReminder(Request $request, ReminderService $reminderService)
    {
        // Validasi input mitra ID
        $request->validate([
            'mitra_id' => 'required|exists:mitra,id',
        ]);

        $mitra  = Mitra::findOrFail($request->mitra_id);
        $sender = Auth::user();

        // Ambil transaksi yang perlu diingatkan
        $transaksiList = Transaksi::where('mitra_id', $mitra->id)
            ->whereIn('status_pembayaran', ['Belum Dibayar', 'Menunggu Validasi', 'Ditolak'])
            ->get();
            
        // Cari jatuh tempo terdekat
        $closestTempo = $transaksiList->whereNotNull('jatuh_tempo')
            ->pluck('jatuh_tempo')
            ->sort()
            ->first();
            
        // Validasi aturan H-3
        $canSendReminder = false;
        if ($closestTempo) {
            $sisaHari = (int) now()->startOfDay()->diffInDays($closestTempo, false);
            $canSendReminder = $sisaHari <= 3;
        }

        // Tolak jika belum memasuki zona H-3
        if (!$canSendReminder) {
            return response()->json([
                'message' => 'Email reminder hanya dapat dikirim maksimal H-3 sebelum tanggal jatuh tempo.'
            ], 422);
        }

        // Kirim email reminder melalui ReminderService
        $result = $reminderService->sendReminder($mitra, $sender);

        if (!$result['success']) {
            return response()->json(['message' => $result['message']], 422);
        }

        return response()->json([
            'message' => $result['message'],
        ]);
    }

    /**
     * Memvalidasi bukti pembayaran dari mitra (terima atau tolak) untuk satu transaksi.
     * Jika diterima, kirim email konfirmasi. Jika ditolak, kirim email penolakan.
     */
    public function validasiBuktiPembayaran(Request $request, $id)
    {
        // Validasi aksi (terima/tolak)
        $request->validate([
            'action' => 'required|in:terima,tolak',
        ]);

        // Cari transaksi milik kasir yang login
        $transaksi = Transaksi::with(['mitra', 'items'])->where('user_id', Auth::id())->findOrFail($id);

        // Pastikan transaksi dalam status menunggu validasi
        if ($transaksi->status_pembayaran !== 'Menunggu Validasi') {
            return response()->json(['message' => 'Transaksi tidak dalam status menunggu validasi.'], 422);
        }

        $mitra = $transaksi->mitra;

        if ($request->action === 'terima') {
            // Ubah status menjadi Sudah Dibayar
            $transaksi->update([
                'status_pembayaran' => 'Sudah Dibayar',
                'updated_at' => now(),
            ]);

            // Trigger notifikasi laporan penjualan untuk Owner
            \App\Models\Notification::triggerLaporanPenjualan();

            // Generate PDF invoice LUNAS dan kirim email konfirmasi
            $this->sendPaymentAcceptedEmail($transaksi, $mitra);

            return response()->json(['message' => 'Pembayaran berhasil diterima. Email konfirmasi telah dikirim.', 'status' => 'Sudah Dibayar']);
        } else {
            // Ubah status menjadi Ditolak
            $transaksi->update([
                'status_pembayaran' => 'Ditolak',
                'updated_at' => now(),
            ]);

            // Buka kunci upload pembayaran untuk mitra
            $mitra->update(['payment_upload_locked' => false]);

            // Kirim email notifikasi penolakan ke mitra
            $this->sendPaymentRejectedEmail($transaksi, $mitra);

            return response()->json(['message' => 'Pembayaran berhasil ditolak. Email notifikasi telah dikirim ke mitra.', 'status' => 'Ditolak']);
        }
    }

    /**
     * Memvalidasi semua bukti pembayaran per mitra secara bulk (terima atau tolak semua).
     */
    public function validasiBuktiPerMitra(Request $request)
    {
        // Validasi input
        $request->validate([
            'mitra_id' => 'required|exists:mitra,id',
            'action'   => 'required|in:terima,tolak',
        ]);

        $mitra = Mitra::findOrFail($request->mitra_id);

        // Ambil semua transaksi yang menunggu validasi untuk mitra ini
        $transaksiList = Transaksi::with('items')
            ->where('user_id', Auth::id())
            ->where('mitra_id', $request->mitra_id)
            ->where('status_pembayaran', 'Menunggu Validasi')
            ->get();

        // Pastikan ada transaksi yang bisa divalidasi
        if ($transaksiList->isEmpty()) {
            return response()->json(['message' => 'Tidak ada transaksi yang menunggu validasi.'], 422);
        }

        if ($request->action === 'terima') {
            // Terima semua transaksi
            foreach ($transaksiList as $transaksi) {
                $transaksi->update([
                    'status_pembayaran' => 'Sudah Dibayar',
                    'updated_at' => now(),
                ]);
            }

            // Trigger notifikasi laporan penjualan untuk Owner
            \App\Models\Notification::triggerLaporanPenjualan();

            // Kirim 1 email konfirmasi untuk semua transaksi yang diterima
            $this->sendPaymentAcceptedEmailBulk($transaksiList, $mitra);

            return response()->json(['message' => "Berhasil menerima {$transaksiList->count()} transaksi. Email konfirmasi telah dikirim."]);
        } else {
            // Tolak semua transaksi
            foreach ($transaksiList as $transaksi) {
                $transaksi->update([
                    'status_pembayaran' => 'Ditolak',
                    'updated_at' => now(),
                ]);
            }

            // Buka kunci upload pembayaran untuk mitra
            $mitra->update(['payment_upload_locked' => false]);

            // Kirim 1 email notifikasi penolakan
            $noInvoices = $transaksiList->pluck('no_transaksi')->join(', ');
            $this->sendPaymentRejectedEmail($transaksiList->first(), $mitra);

            return response()->json(['message' => "Berhasil menolak {$transaksiList->count()} transaksi. Email notifikasi telah dikirim ke mitra."]);
        }
    }

    /**
     * Memproses pembayaran tagihan mitra secara manual (legacy, untuk kompatibilitas).
     */
    public function bayarTagihan(Request $request)
    {
        // Validasi input pembayaran
        $request->validate([
            'mitra_id' => 'required|exists:mitra,id',
            'bulan'    => 'required|integer|min:1|max:12',
            'tahun'    => 'required|integer',
        ]);

        // Update status pembayaran semua transaksi belum dibayar di bulan/tahun tersebut
        Transaksi::where('user_id', Auth::id())
            ->where('mitra_id', $request->mitra_id)
            ->whereMonth('created_at', $request->bulan)
            ->whereYear('created_at', $request->tahun)
            ->where('status_pembayaran', 'Belum Dibayar')
            ->update([
                'status_pembayaran' => 'Sudah Dibayar',
                'updated_at'        => now()
            ]);

        // Trigger notifikasi laporan penjualan untuk Owner
        \App\Models\Notification::triggerLaporanPenjualan();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Pembayaran berhasil dikonfirmasi.']);
        }

        return redirect('/kasir/riwayat')->with('success', 'Pembayaran berhasil dikonfirmasi.');
    }

    /**
     * Menampilkan invoice digital untuk transaksi tertentu.
     */
    public function invoice($id)
    {
        // Ambil transaksi milik kasir yang login beserta relasi mitra dan items
        $transaksi = Transaksi::with(['mitra', 'items'])->where('user_id', Auth::id())->findOrFail($id);
        return view('kasir.invoice', compact('transaksi'));
    }

    /**
     * Mengunduh invoice dalam format PDF.
     * Menampilkan watermark LUNAS jika transaksi sudah dibayar.
     */
    public function downloadInvoicePdf($id)
    {
        // Ambil transaksi milik kasir yang login
        $transaksi = Transaksi::with(['mitra', 'items'])->where('user_id', Auth::id())->findOrFail($id);
        $mitra = $transaksi->mitra;

        // Cek status lunas untuk watermark
        $isLunas = $transaksi->status_pembayaran === 'Sudah Dibayar';

        // Generate PDF invoice
        $pdf = Pdf::loadView('pdf.invoice-lunas', [
            'transaksi'  => $transaksi,
            'mitra'      => $mitra,
            'isLunas'    => $isLunas,
        ])->setPaper('a4', 'portrait');

        // Tentukan nama file berdasarkan status lunas
        $prefix = $isLunas ? 'Invoice-LUNAS-' : 'Invoice-';
        $filename = $prefix . $transaksi->no_transaksi . '.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Menampilkan file bukti pembayaran.
     * Bypass masalah junction pada PHP built-in server.
     */
    public function showBuktiPembayaran($filename)
    {
        $path = 'bukti-pembayaran/' . $filename;

        // Cek apakah file ada di storage
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File bukti pembayaran tidak ditemukan.');
        }

        return response()->file(
            Storage::disk('public')->path($path)
        );
    }

    // ========================
    // Method Helper Privat
    // ========================

    /**
     * Mengirim email konfirmasi pembayaran diterima (untuk satu transaksi).
     * Generate PDF invoice LUNAS dan lampirkan ke email.
     */
    private function sendPaymentAcceptedEmail(Transaksi $transaksi, Mitra $mitra): void
    {
        // Lewati jika email mitra kosong
        if (empty($mitra->email)) return;

        try {
            // Generate PDF invoice LUNAS
            $pdfPath = $this->generateInvoiceLunasPdf($transaksi, $mitra);

            // Kirim email dengan lampiran PDF
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
     * Mengirim email konfirmasi pembayaran diterima secara bulk (untuk semua transaksi mitra).
     * Menggabungkan beberapa invoice dalam satu email.
     */
    private function sendPaymentAcceptedEmailBulk($transaksiList, Mitra $mitra): void
    {
        // Lewati jika email mitra kosong
        if (empty($mitra->email)) return;

        try {
            // Generate PDF untuk transaksi pertama sebagai representasi
            $firstTransaksi = $transaksiList->first();
            $pdfPath = $this->generateInvoiceLunasPdf($firstTransaksi, $mitra);

            // Gabungkan semua nomor invoice
            $noInvoices = $transaksiList->pluck('no_transaksi')->join(', ');

            // Kirim email dengan lampiran PDF
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
     * Mengirim email notifikasi pembayaran ditolak ke mitra.
     */
    private function sendPaymentRejectedEmail(Transaksi $transaksi, Mitra $mitra): void
    {
        // Lewati jika email mitra kosong
        if (empty($mitra->email)) return;

        try {
            // Kirim email notifikasi penolakan
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
     * Membuat file PDF invoice LUNAS untuk satu transaksi.
     * Menyimpan PDF ke storage/app/invoices/.
     */
    private function generateInvoiceLunasPdf(Transaksi $transaksi, Mitra $mitra): string
    {
        // Pastikan direktori penyimpanan ada
        $dir = storage_path('app/invoices');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Generate nama file dan path
        $filename = 'Invoice-LUNAS-' . $transaksi->no_transaksi . '.pdf';
        $pdfPath = $dir . '/' . $filename;

        // Generate PDF dari view
        $pdf = Pdf::loadView('pdf.invoice-lunas', [
            'transaksi'  => $transaksi,
            'mitra'      => $mitra,
            'isLunas'    => true,
        ])->setPaper('a4', 'portrait');

        // Simpan PDF ke file
        $pdf->save($pdfPath);

        return $pdfPath;
    }
}
