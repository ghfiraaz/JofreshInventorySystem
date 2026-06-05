<?php

namespace App\Http\Controllers;

use App\Models\LogStok;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogStokController extends Controller
{
    /**
     * Halaman riwayat log stok — bisa dilihat semua role (view-only).
     */
    public function index(Request $request)
    {
        $query = LogStok::with(['produk', 'user'])->orderBy('created_at', 'desc');

        // Filter berdasarkan tipe transaksi
        if ($request->filled('tipe') && $request->tipe !== '') {
            $query->where('tipe', $request->tipe);
        }

        // Filter berdasarkan tanggal (range)
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }

        $logs = $query->get();

        // Statistik ringkasan hari ini
        $today = today();
        $logHariIni = LogStok::whereDate('created_at', $today)->get();

        $totalLogHariIni = $logHariIni->count();
        $totalMasuk      = $logHariIni->where('tipe', 'Masuk')->sum('jumlah');
        $totalKeluar     = $logHariIni->where('tipe', 'Keluar')->sum('jumlah');
        $totalAdjustment = $logHariIni->whereIn('tipe', ['Adjustment Masuk', 'Adjustment Keluar'])->count();

        // Filter state
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
     * Simpan adjustment stok (hanya Admin — POST).
     */
    public function storeAdjustment(Request $request)
    {
        $request->validate([
            'produk_id'       => 'required|exists:produk,id',
            'tipe_adjustment' => 'required|in:Adjustment Masuk,Adjustment Keluar',
            'jumlah'          => 'required|integer|min:1',
            'keterangan'      => 'required|string|max:500',
        ]);

        return DB::transaction(function () use ($request) {
            $produk = Produk::findOrFail($request->produk_id);
            $stokSebelum = $produk->stok;

            if ($request->tipe_adjustment === 'Adjustment Masuk') {
                $produk->stok += $request->jumlah;
            } else {
                // Adjustment Keluar — validasi stok cukup
                if ($produk->stok < $request->jumlah) {
                    return response()->json([
                        'message' => "Stok {$produk->nama} tidak cukup untuk dikurangi. Stok saat ini: {$produk->stok}."
                    ], 422);
                }
                $produk->stok -= $request->jumlah;
            }

            $produk->save();

            // Trigger low stock alert for Admin
            \App\Models\Notification::triggerLowStockAlert($produk);

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

