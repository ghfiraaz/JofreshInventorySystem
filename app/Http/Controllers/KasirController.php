<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Mitra;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use App\Models\ReminderHistory;
use App\Services\ReminderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

            // Check if reminder was sent today from reminder_histories
            $reminderSentToday = ReminderHistory::where('mitra_id', $mitra->id)
                ->whereDate('tanggal_pengiriman', today())
                ->where('status', 'berhasil')
                ->exists();

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
     * Kirim reminder pembayaran via email otomatis
     */
    public function sendReminder(Request $request, ReminderService $reminderService)
    {
        $request->validate([
            'mitra_id' => 'required|exists:mitra,id',
        ]);

        $mitra  = Mitra::findOrFail($request->mitra_id);
        $sender = Auth::user();

        // Backend validation: cek apakah sudah H-3 sebelum jatuh tempo
        $closestTempo = Transaksi::where('mitra_id', $mitra->id)
            ->where('status_pembayaran', 'Belum Dibayar')
            ->whereNotNull('jatuh_tempo')
            ->min('jatuh_tempo');

        if ($closestTempo) {
            $sisaHari = (int) now()->startOfDay()->diffInDays($closestTempo, false);
            if ($sisaHari > 3) {
                return response()->json([
                    'message' => 'Reminder hanya dapat dikirim maksimal 3 hari sebelum jatuh tempo. Sisa ' . $sisaHari . ' hari lagi.'
                ], 422);
            }
        }

        $result = $reminderService->sendReminder($mitra, $sender);

        if (!$result['success']) {
            return response()->json(['message' => $result['message']], 422);
        }

        return response()->json([
            'message' => $result['message'],
            'history' => $result['history'],
        ]);
    }

    /**
     * Halaman histori pengiriman reminder dengan filter periode tanggal
     */
    public function reminderHistory(Request $request)
    {
        $dari   = $request->get('dari', now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));
        $status = $request->get('status', '');

        $query = ReminderHistory::with('mitra')
            ->where('user_id', Auth::id())
            ->whereBetween('tanggal_pengiriman', [
                Carbon::parse($dari)->startOfDay(),
                Carbon::parse($sampai)->endOfDay(),
            ])
            ->orderBy('tanggal_pengiriman', 'desc');

        if ($status !== '') {
            $query->where('status', $status);
        }

        $histories = $query->get();

        $totalReminder = $histories->count();
        $totalBerhasil = $histories->where('status', 'berhasil')->count();
        $totalGagal    = $histories->where('status', 'gagal')->count();

        return view('kasir.reminder-history', compact(
            'histories', 'totalReminder', 'totalBerhasil', 'totalGagal'
        ));
    }

    /**
     * Validasi bukti pembayaran dari mitra (terima/tolak)
     */
    public function validasiBuktiPembayaran(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:terima,tolak',
        ]);

        $transaksi = Transaksi::where('user_id', Auth::id())->findOrFail($id);

        if ($transaksi->status_pembayaran !== 'Menunggu Validasi') {
            return response()->json(['message' => 'Transaksi tidak dalam status menunggu validasi.'], 422);
        }

        if ($request->action === 'terima') {
            $transaksi->update([
                'status_pembayaran' => 'Sudah Dibayar',
                'updated_at' => now(),
            ]);
            return response()->json(['message' => 'Pembayaran berhasil diterima.', 'status' => 'Sudah Dibayar']);
        } else {
            $transaksi->update([
                'status_pembayaran' => 'Ditolak',
                'bukti_pembayaran' => null,
                'updated_at' => now(),
            ]);
            return response()->json(['message' => 'Pembayaran berhasil ditolak. Mitra dapat upload ulang bukti pembayaran.', 'status' => 'Ditolak']);
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

        $query = Transaksi::where('user_id', Auth::id())
            ->where('mitra_id', $request->mitra_id)
            ->where('status_pembayaran', 'Menunggu Validasi');

        if ($request->action === 'terima') {
            $updated = $query->update([
                'status_pembayaran' => 'Sudah Dibayar',
                'updated_at' => now(),
            ]);
            return response()->json(['message' => "Berhasil menerima {$updated} transaksi."]);
        } else {
            $updated = $query->update([
                'status_pembayaran' => 'Ditolak',
                'bukti_pembayaran' => null,
                'updated_at' => now(),
            ]);
            return response()->json(['message' => "Berhasil menolak {$updated} transaksi. Mitra dapat upload ulang."]);
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
}
