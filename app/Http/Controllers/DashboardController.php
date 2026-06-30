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
        // --- Determine filter period ---
        $filterMode  = $request->input('filter_mode', ''); // 'month' or 'range'
        $filterMonth = $request->input('filter_month', '');
        $filterYear  = $request->input('filter_year', '');
        $filterStart = $request->input('filter_start', '');
        $filterEnd   = $request->input('filter_end', '');

        // Legacy support for old filter_date param
        $filterDate = $request->input('filter_date', null);

        // Calculate date range for charts & summary
        $rangeStart = null;
        $rangeEnd   = null;
        $periodLabel = '';

        if ($filterMode === 'month' && $filterMonth && $filterYear) {
            $rangeStart = Carbon::createFromDate($filterYear, $filterMonth, 1)->startOfMonth();
            $rangeEnd   = $rangeStart->copy()->endOfMonth();
            $periodLabel = $rangeStart->translatedFormat('F Y');
        } elseif ($filterMode === 'range' && $filterStart && $filterEnd) {
            $rangeStart = Carbon::parse($filterStart)->startOfDay();
            $rangeEnd   = Carbon::parse($filterEnd)->endOfDay();
            $periodLabel = $rangeStart->format('d/m/Y') . ' — ' . $rangeEnd->format('d/m/Y');
        } elseif ($filterDate) {
            // Legacy single-date support
            $rangeStart = Carbon::parse($filterDate)->startOfDay();
            $rangeEnd   = Carbon::parse($filterDate)->endOfDay();
            $periodLabel = $rangeStart->translatedFormat('d F Y');
        }

        $hasFilter = $rangeStart && $rangeEnd;

        // --- Summary cards ---
        $summaryQuery = Transaksi::where('status_pembayaran', 'Sudah Dibayar');
        if ($hasFilter) {
            $summaryQuery->whereBetween('created_at', [$rangeStart, $rangeEnd]);
        } else {
            $summaryQuery->whereDate('created_at', today());
        }
        $transaksiFiltered = $summaryQuery->get();

        $penjualanHariIni = $transaksiFiltered->sum('total_harga');
        $totalTransaksi   = $transaksiFiltered->count();
        
        $totalMitra        = Mitra::count();
        $totalStok         = Produk::sum('stok');
        $stokRendahCount   = Produk::whereColumn('stok', '<', 'stok_minimal')->count();
        $isStokRendah      = $stokRendahCount > 0;

        // --- Chart: Tren Penjualan ---
        $trendLabels = [];
        $trendData   = [];
        $trendChartTitle = 'Tren Penjualan Bulanan';
        $trendChartDesc  = 'Menampilkan perkembangan total penjualan setiap bulan untuk memantau performa bisnis dari waktu ke waktu.';

        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        if ($hasFilter) {
            // Determine if we show daily or monthly data based on range length
            $daysDiff = $rangeStart->diffInDays($rangeEnd);

            if ($daysDiff <= 31) {
                // Show daily data within the filtered period
                $trendChartTitle = 'Tren Penjualan Harian';
                $trendChartDesc  = 'Menampilkan perkembangan total penjualan per hari dalam periode yang dipilih.';

                $dailyData = Transaksi::where('status_pembayaran', 'Sudah Dibayar')
                    ->whereBetween('created_at', [$rangeStart, $rangeEnd])
                    ->selectRaw('DATE(created_at) as tanggal, SUM(total_harga) as total')
                    ->groupBy('tanggal')
                    ->orderBy('tanggal')
                    ->get()
                    ->keyBy('tanggal');

                $current = $rangeStart->copy()->startOfDay();
                $end = $rangeEnd->copy()->startOfDay();
                while ($current->lte($end)) {
                    $key = $current->format('Y-m-d');
                    $trendLabels[] = $current->format('d M');
                    $trendData[]   = isset($dailyData[$key]) ? (int) $dailyData[$key]->total : 0;
                    $current->addDay();
                }
            } else {
                // Show monthly data within the filtered period
                $trendChartTitle = 'Tren Penjualan Bulanan';
                $trendChartDesc  = 'Menampilkan perkembangan total penjualan per bulan dalam periode yang dipilih.';

                $isSqlite = \Illuminate\Support\Facades\DB::getDriverName() === 'sqlite';
                $selectRaw = $isSqlite 
                    ? "strftime('%Y', created_at) as tahun, strftime('%m', created_at) as bulan, SUM(total_harga) as total"
                    : "YEAR(created_at) as tahun, MONTH(created_at) as bulan, SUM(total_harga) as total";

                $monthlyData = Transaksi::where('status_pembayaran', 'Sudah Dibayar')
                    ->whereBetween('created_at', [$rangeStart, $rangeEnd])
                    ->selectRaw($selectRaw)
                    ->groupBy('tahun', 'bulan')
                    ->orderBy('tahun')
                    ->orderBy('bulan')
                    ->get()
                    ->keyBy(fn($row) => $row->tahun . '-' . str_pad($row->bulan, 2, '0', STR_PAD_LEFT));

                $current = $rangeStart->copy()->startOfMonth();
                $end = $rangeEnd->copy()->startOfMonth();
                while ($current->lte($end)) {
                    $key = $current->format('Y-m');
                    $trendLabels[] = $monthNames[(int)$current->format('n') - 1] . ' ' . $current->format('Y');
                    $trendData[]   = isset($monthlyData[$key]) ? (int) $monthlyData[$key]->total : 0;
                    $current->addMonth();
                }
            }
        } else {
            // Default: show last 12 months
            $startOf12Months = Carbon::now()->startOfMonth()->subMonths(11);

            $isSqlite = \Illuminate\Support\Facades\DB::getDriverName() === 'sqlite';
            $selectRaw = $isSqlite 
                ? "strftime('%Y', created_at) as tahun, strftime('%m', created_at) as bulan, SUM(total_harga) as total"
                : "YEAR(created_at) as tahun, MONTH(created_at) as bulan, SUM(total_harga) as total";

            $monthlyData = Transaksi::where('status_pembayaran', 'Sudah Dibayar')
                ->where('created_at', '>=', $startOf12Months)
                ->selectRaw($selectRaw)
                ->groupBy('tahun', 'bulan')
                ->orderBy('tahun')
                ->orderBy('bulan')
                ->get()
                ->keyBy(fn($row) => $row->tahun . '-' . str_pad($row->bulan, 2, '0', STR_PAD_LEFT));

            for ($i = 11; $i >= 0; $i--) {
                $month = Carbon::now()->startOfMonth()->subMonths($i);
                $key   = $month->format('Y-m');
                $trendLabels[] = $monthNames[(int)$month->format('n') - 1] . ' ' . $month->format('Y');
                $trendData[]   = isset($monthlyData[$key]) ? (int) $monthlyData[$key]->total : 0;
            }
        }

        // --- Chart: Distribusi per produk ---
        $produkList = Produk::orderBy('nama')->get();
        $distLabels = [];
        $distData   = [];
        
        foreach ($produkList as $p) {
            $distLabels[] = $p->nama;
            $q = TransaksiItem::whereHas('transaksi', function($q) use ($hasFilter, $rangeStart, $rangeEnd) {
                $q->where('status_pembayaran', 'Sudah Dibayar');
                if ($hasFilter) $q->whereBetween('created_at', [$rangeStart, $rangeEnd]);
            })->where('produk_id', $p->id);
            $distData[] = (int) ($q->sum('jumlah') ?: 0);
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
            'trendChartTitle',
            'trendChartDesc',
            'distLabels',
            'distData',
            'filterMode',
            'filterMonth',
            'filterYear',
            'filterStart',
            'filterEnd',
            'filterDate',
            'periodLabel',
            'hasFilter',
            'produkList'
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
