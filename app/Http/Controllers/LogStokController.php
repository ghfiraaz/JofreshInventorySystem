<?php

namespace App\Http\Controllers;

use App\Models\LogStok;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Controller Log Stok
 * Mengelola riwayat perubahan stok dan penyesuaian (adjustment) stok produk.
 */
class LogStokController extends Controller
{
    /**
     * Menampilkan halaman riwayat log stok.
     * Bisa dilihat semua role (view-only), mendukung filter tipe dan tanggal.
     */
    public function index(Request $request)
    {
        // Validasi input filter tanggal
        $request->validate([
            'tanggal_dari'   => 'nullable|date',
            'tanggal_sampai' => 'nullable|date|after_or_equal:tanggal_dari',
        ], [
            'tanggal_sampai.after_or_equal' => 'Tanggal awal tidak boleh melebihi tanggal akhir.',
        ]);

        // Query dasar: ambil semua log stok dengan relasi produk dan user
        $query = LogStok::with(['produk', 'user'])->orderBy('created_at', 'desc');

        // Filter berdasarkan tipe transaksi (Masuk/Keluar/Adjustment)
        if ($request->filled('tipe') && $request->tipe !== '') {
            $query->where('tipe', $request->tipe);
        }

        // Filter berdasarkan tanggal awal
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }

        // Filter berdasarkan tanggal akhir
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }

        $logs = $query->get();

        // Statistik ringkasan hari ini
        $today = today();
        $logHariIni = LogStok::whereDate('created_at', $today)->get();

        // Hitung total masing-masing tipe log hari ini
        $totalLogHariIni = $logHariIni->count();
        $totalMasuk      = $logHariIni->where('tipe', 'Masuk')->sum('jumlah');
        $totalKeluar     = $logHariIni->where('tipe', 'Keluar')->sum('jumlah');
        $totalAdjustment = $logHariIni->whereIn('tipe', ['Adjustment Masuk', 'Adjustment Keluar'])->count();

        // Simpan state filter untuk view
        $filterTipe          = $request->get('tipe', '');
        $filterTanggalDari   = $request->get('tanggal_dari', '');
        $filterTanggalSampai = $request->get('tanggal_sampai', '');

        return view('log-stok', compact(
            'logs',
            'totalLogHariIni',
            'totalMasuk',
            'totalKeluar',
            'totalAdjustment',
            'filterTipe',
            'filterTanggalDari',
            'filterTanggalSampai'
        ));
    }


    /**
     * Menyimpan penyesuaian (adjustment) stok produk.
     * Hanya Admin yang bisa melakukan adjustment. Mendukung Adjustment Masuk dan Keluar.
     */
    public function storeAdjustment(Request $request)
    {
        // Validasi input adjustment
        $request->validate([
            'produk_id'       => 'required|exists:produk,id',
            'tipe_adjustment' => 'required|in:Adjustment Masuk,Adjustment Keluar',
            'jumlah'          => 'required|integer|min:1',
            'keterangan'      => 'required|string|max:500',
        ], [
            'jumlah.integer'  => 'Jumlah penyesuaian harus berupa angka (digit) saja, tidak boleh mengandung huruf.',
            'jumlah.min'      => 'Jumlah penyesuaian minimal 1.',
        ]);

        return DB::transaction(function () use ($request) {
            // Cari produk dan simpan stok sebelum adjustment
            $produk = Produk::findOrFail($request->produk_id);
            $stokSebelum = $produk->stok;

            if ($request->tipe_adjustment === 'Adjustment Masuk') {
                // Tambah stok
                $produk->stok += $request->jumlah;
            } else {
                // Kurangi stok — validasi stok cukup
                if ($produk->stok < $request->jumlah) {
                    return response()->json([
                        'message' => "Stok {$produk->nama} tidak cukup untuk dikurangi. Stok saat ini: {$produk->stok}."
                    ], 422);
                }
                $produk->stok -= $request->jumlah;
            }

            // Simpan perubahan stok
            $produk->save();

            // Trigger notifikasi stok rendah untuk Admin
            \App\Models\Notification::triggerLowStockAlert($produk);

            // Catat log penyesuaian stok
            $log = LogStok::create([
                'produk_id'    => $produk->id,
                'user_id'      => Auth::id(),
                'tipe'         => $request->tipe_adjustment,
                'jumlah'       => $request->jumlah,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $produk->stok,
                'keterangan'   => $request->keterangan,
            ]);

            return response()->json([
                'message' => 'Adjustment stok berhasil dicatat.',
                'log'     => $log->load(['produk', 'user']),
                'produk'  => array_merge($produk->toArray(), [
                    'status'       => $produk->status,
                    'status_badge' => $produk->status_badge,
                ]),
            ], 201);
        });
    }
}
