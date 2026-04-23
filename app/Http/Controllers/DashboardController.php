<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filterDate = $request->input('filter_date', null);
        $hariIni    = $filterDate ? Carbon::parse($filterDate) : today();

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

        // --- Chart: Tren Penjualan (7 hari, window around selected date) ---
        $trendLabels = [];
        $trendData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $hariIni->copy()->subDays($i);
            $trendLabels[] = $date->translatedFormat('d M');
            $trendData[]   = Transaksi::whereDate('created_at', $date)
                                ->where('status_pembayaran', 'Sudah Dibayar')
                                ->sum('total_harga');
        }

        // --- Chart: Distribusi per produk (for selected date or all-time) ---
        $produkList = Produk::orderBy('nama')->get();
        $distLabels = [];
        $distData   = [];
        
        foreach ($produkList as $p) {
            $distLabels[] = $p->nama;
            $q = TransaksiItem::whereHas('transaksi', function($q) use ($hariIni, $filterDate) {
                $q->where('status_pembayaran', 'Sudah Dibayar');
                if ($filterDate) $q->whereDate('created_at', $hariIni);
            })->where('produk_id', $p->id);
            $distData[] = $q->sum('jumlah') ?: 0;
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
            'distData',
            'filterDate'
        ));
    }

    public function laporanHarian(Request $request)
    {
        $hariIni = $request->has('date') ? Carbon::parse($request->date) : today();
        
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

    public function laporanTransaksi(Request $request)
    {
        $bulan = $request->input('bulan', '');
        $tahun = $request->input('tahun', date('Y'));
        $filterDate = $request->input('filter_date', '');

        $query = Transaksi::with(['mitra', 'items'])->where('status_pembayaran', 'Sudah Dibayar');
        
        if (!empty($filterDate)) {
            $query->whereDate('created_at', $filterDate);
        } else {
            if (!empty($bulan) && $bulan !== 'all') {
                $query->whereMonth('created_at', $bulan);
            }
            if (!empty($tahun)) {
                $query->whereYear('created_at', $tahun);
            }
        }

        $transaksiRaw = $query->orderBy('created_at', 'desc')->get();

        // Group by date
        $grouped = [];
        foreach ($transaksiRaw as $tx) {
            $dateKey = $tx->created_at->format('Y-m-d');
            if (!isset($grouped[$dateKey])) {
                $grouped[$dateKey] = [
                    'date' => $tx->created_at,
                    'total_transaksi' => 0,
                    'total_item' => 0,
                    'total_harga' => 0,
                    'transaksi' => []
                ];
            }
            $grouped[$dateKey]['total_transaksi'] += 1;
            $grouped[$dateKey]['total_item'] += $tx->total_item;
            $grouped[$dateKey]['total_harga'] += $tx->total_harga;
            $grouped[$dateKey]['transaksi'][] = $tx;
        }

        return view('admin.laporan-transaksi', compact('grouped', 'bulan', 'tahun'));
    }
}
