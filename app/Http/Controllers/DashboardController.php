<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hariIni = today();

        // --- Summary cards ---
        $transaksiHariIni = Transaksi::whereDate('created_at', $hariIni)
            ->where('status_pembayaran', 'Sudah Dibayar')
            ->get();
            
        $penjualanHariIni = $transaksiHariIni->sum('total_harga');
        $totalTransaksi   = $transaksiHariIni->count();
        
        $totalMitra        = Mitra::count();
        $totalStok         = Produk::sum('stok');
        $stokRendahCount   = Produk::whereColumn('stok', '<', 'stok_minimal')->count();
        $isStokRendah      = $stokRendahCount > 0;

        // --- Chart: Tren Penjualan (7 hari terakhir) ---
        $trendLabels = [];
        $trendData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $trendLabels[] = $date->translatedFormat('d M');
            $trendData[]   = Transaksi::whereDate('created_at', $date)
                                ->where('status_pembayaran', 'Sudah Dibayar')
                                ->sum('total_harga');
        }

        // --- Chart: Distribusi per produk ---
        $produkList   = Produk::orderBy('nama')->get();
        $distLabels   = [];
        $distData     = [];
        
        foreach ($produkList as $p) {
            $distLabels[] = $p->nama;
            $sold = TransaksiItem::whereHas('transaksi', function($q) {
                $q->where('status_pembayaran', 'Sudah Dibayar');
            })->where('produk_id', $p->id)->sum('jumlah');
            
            $distData[] = $sold ?: 0;
        }

        return view('dashboard', compact(
            'penjualanHariIni',
            'totalTransaksi',
            'totalMitra',
            'totalStok',
            'isStokRendah',
            'stokRendahCount',
            'trendLabels',
            'trendData',
            'distLabels',
            'distData'
        ));
    }

    public function laporanHarian()
    {
        $hariIni = today();
        
        $transaksi = Transaksi::with(['items', 'mitra'])
            ->whereDate('created_at', $hariIni)
            ->where('status_pembayaran', 'Sudah Dibayar')
            ->get();
            
        $stokKeluar = [];
        foreach($transaksi as $tx) {
            foreach($tx->items as $item) {
                $nama = $item->nama_produk;
                if(!isset($stokKeluar[$nama])) {
                    $stokKeluar[$nama] = 0;
                }
                $stokKeluar[$nama] += $item->jumlah;
            }
        }
        
        $stokTersedia = Produk::orderBy('nama')->get();
        $totalPendapatan = $transaksi->sum('total_harga');

        return view('admin.laporan-harian', compact('transaksi', 'stokKeluar', 'stokTersedia', 'totalPendapatan', 'hariIni'));
    }
}
