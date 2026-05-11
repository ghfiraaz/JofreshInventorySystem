<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Mitra;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

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

        return view('kasir.dashboard', compact(
            'totalPenjualan', 'totalTransaksi', 'produkTersedia',
            'belumBayar', 'tagihanMendesak', 'menungguValidasi'
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

                $produk->decrement('stok', $item['jumlah']);
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
            ->whereIn('status_pembayaran', ['Belum Dibayar', 'Menunggu Validasi'])
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

            // Check if reminder was sent today for any transaction of this mitra
            $reminderSentToday = $txns->contains(function ($tx) {
                return $tx->last_reminder_sent_at && $tx->last_reminder_sent_at->isToday();
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
                'reminderSentToday'  => $reminderSentToday,
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
     * Record that reminder was sent (called after kasir opens Gmail compose)
     */
    public function recordReminder(Request $request)
    {
        $request->validate([
            'mitra_id' => 'required|exists:mitra,id',
        ]);

        // Check if reminder already sent today
        $alreadySent = Transaksi::where('user_id', Auth::id())
            ->where('mitra_id', $request->mitra_id)
            ->where('status_pembayaran', 'Belum Dibayar')
            ->whereDate('last_reminder_sent_at', today())
            ->exists();

        if ($alreadySent) {
            return response()->json(['message' => 'Reminder sudah dikirim hari ini.'], 422);
        }

        // Mark reminder as sent for all unpaid transactions of this mitra
        Transaksi::where('user_id', Auth::id())
            ->where('mitra_id', $request->mitra_id)
            ->where('status_pembayaran', 'Belum Dibayar')
            ->update(['last_reminder_sent_at' => now()]);

        return response()->json(['message' => 'Reminder berhasil dicatat.']);
    }

    /**
     * Validasi bukti pembayaran dari mitra
     */
    public function validasiBuktiPembayaran(Request $request, $id)
    {
        $transaksi = Transaksi::where('user_id', Auth::id())->findOrFail($id);

        if ($transaksi->status_pembayaran !== 'Menunggu Validasi') {
            return response()->json(['message' => 'Transaksi tidak dalam status menunggu validasi.'], 422);
        }

        $transaksi->update([
            'status_pembayaran' => 'Sudah Dibayar',
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Pembayaran berhasil divalidasi.']);
    }

    /**
     * Validasi semua bukti pembayaran per mitra
     */
    public function validasiBuktiPerMitra(Request $request)
    {
        $request->validate([
            'mitra_id' => 'required|exists:mitra,id',
        ]);

        $updated = Transaksi::where('user_id', Auth::id())
            ->where('mitra_id', $request->mitra_id)
            ->where('status_pembayaran', 'Menunggu Validasi')
            ->update([
                'status_pembayaran' => 'Sudah Dibayar',
                'updated_at' => now(),
            ]);

        return response()->json(['message' => "Berhasil memvalidasi {$updated} transaksi."]);
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
     * Get Gmail compose URL data for reminder email
     */
    public function getReminderData(Request $request)
    {
        $request->validate([
            'mitra_id' => 'required|exists:mitra,id',
        ]);

        $mitra = Mitra::findOrFail($request->mitra_id);
        
        if (!$mitra->email) {
            return response()->json(['message' => 'Email mitra belum diisi. Silakan update data mitra terlebih dahulu.'], 422);
        }

        $transaksiUnpaid = Transaksi::with('items')
            ->where('user_id', Auth::id())
            ->where('mitra_id', $mitra->id)
            ->where('status_pembayaran', 'Belum Dibayar')
            ->get();

        if ($transaksiUnpaid->isEmpty()) {
            return response()->json(['message' => 'Tidak ada tagihan yang belum dibayar.'], 422);
        }

        $totalTagihan = $transaksiUnpaid->sum('total_harga');
        $closestTempo = $transaksiUnpaid->whereNotNull('jatuh_tempo')->pluck('jatuh_tempo')->sort()->first();
        
        $sisaHari = null;
        if ($closestTempo) {
            $sisaHari = (int) now()->startOfDay()->diffInDays($closestTempo, false);
        }

        // Build tempo text
        $tempoText = 'segera';
        if ($sisaHari !== null) {
            if ($sisaHari <= 0) $tempoText = 'hari ini';
            elseif ($sisaHari === 1) $tempoText = 'besok';
            elseif ($sisaHari === 2) $tempoText = 'dalam 2 hari';
            elseif ($sisaHari === 3) $tempoText = 'dalam 3 hari';
            else $tempoText = "dalam {$sisaHari} hari";
        }

        $tanggalTempo = $closestTempo ? Carbon::parse($closestTempo)->translatedFormat('d F Y') : '-';

        // Build payment link
        $paymentLink = url('/pembayaran/' . $mitra->payment_token);

        // Build invoice list
        $invoiceList = '';
        foreach ($transaksiUnpaid as $tx) {
            $invoiceList .= '  - ' . $tx->no_transaksi . ' (Rp ' . number_format($tx->total_harga, 0, ',', '.') . ")\n";
        }

        // Build email body
        $subject = "Reminder Tagihan Pembayaran - JoFresh ({$tanggalTempo})";
        
        $body = "Yth. Bapak/Ibu {$mitra->nama},\n\n";
        $body .= "Semoga Bapak/Ibu dalam keadaan baik.\n\n";
        $body .= "Melalui email ini kami ingin mengingatkan bahwa tagihan berikut akan jatuh tempo {$tempoText}:\n\n";
        $body .= "Tanggal Jatuh Tempo: {$tanggalTempo}\n";
        $body .= "Total Tagihan: Rp " . number_format($totalTagihan, 0, ',', '.') . "\n\n";
        $body .= "Sebagai informasi, kami turut melampirkan:\n\n";
        $body .= "* Daftar invoice:\n{$invoiceList}\n";
        $body .= "* Link Download PDF Detail Transaksi: " . url('/pembayaran/' . $mitra->payment_token . '/pdf') . "\n";
        $body .= "* Informasi nomor rekening pembayaran\n";
        $body .= "* QRIS pembayaran\n\n";
        $body .= "Untuk melakukan konfirmasi pembayaran dan mengunggah bukti transfer, silakan klik link berikut:\n";
        $body .= "{$paymentLink}\n\n";
        $body .= "Kami mohon kesediaannya untuk segera melakukan pembayaran sesuai jadwal yang telah disepakati.\n\n";
        $body .= "Apabila pembayaran sudah dilakukan, mohon abaikan pesan ini. Terima kasih atas perhatian dan kerja sama yang baik.\n\n";
        $body .= "Hormat kami,\nJoFresh.";

        // Build Gmail compose URL
        $gmailUrl = 'https://mail.google.com/mail/?view=cm'
            . '&to=' . urlencode($mitra->email)
            . '&su=' . urlencode($subject)
            . '&body=' . urlencode($body);

        return response()->json([
            'gmail_url' => $gmailUrl,
            'mitra_nama' => $mitra->nama,
            'mitra_email' => $mitra->email,
        ]);
    }
}
