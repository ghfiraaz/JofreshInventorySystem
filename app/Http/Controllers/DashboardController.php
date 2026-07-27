<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Controller Dashboard
 * Menampilkan halaman dashboard untuk Superadmin dan Admin.
 * Berisi ringkasan penjualan, stok, chart tren, dan distribusi produk.
 */
class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama.
     * Mendukung filter berdasarkan bulan, rentang tanggal, atau tanggal tertentu.
     */
    public function index(Request $request)
    {
        // --- Menentukan periode filter ---
        $filterMode  = $request->input('filter_mode', '');  // 'month' atau 'range'
        $filterMonth = $request->input('filter_month', '');
        $filterYear  = $request->input('filter_year', '');
        $filterStart = $request->input('filter_start', '');
        $filterEnd   = $request->input('filter_end', '');

        // Dukungan legacy untuk parameter filter_date lama
        $filterDate = $request->input('filter_date', null);

        // Hitung rentang tanggal untuk chart dan ringkasan
        $rangeStart = null;
        $rangeEnd   = null;
        $periodLabel = '';

        // Filter berdasarkan bulan
        if ($filterMode === 'month' && $filterMonth && $filterYear) {
            $rangeStart = Carbon::createFromDate($filterYear, $filterMonth, 1)->startOfMonth();
            $rangeEnd   = $rangeStart->copy()->endOfMonth();
            $periodLabel = $rangeStart->translatedFormat('F Y');
        }
        // Filter berdasarkan rentang tanggal
        elseif ($filterMode === 'range' && $filterStart && $filterEnd) {
            $rangeStart = Carbon::parse($filterStart)->startOfDay();
            $rangeEnd   = Carbon::parse($filterEnd)->endOfDay();
            $periodLabel = $rangeStart->format('d/m/Y') . ' — ' . $rangeEnd->format('d/m/Y');
        }
        // Dukungan legacy filter satu tanggal
        elseif ($filterDate) {
            $rangeStart = Carbon::parse($filterDate)->startOfDay();
            $rangeEnd   = Carbon::parse($filterDate)->endOfDay();
            $periodLabel = $rangeStart->translatedFormat('d F Y');
        }

        $hasFilter = $rangeStart && $rangeEnd;

        // --- Kartu Ringkasan ---
        $summaryQuery = Transaksi::where('status_pembayaran', 'Sudah Dibayar');
        if ($hasFilter) {
            // Gunakan rentang tanggal dari filter
            $summaryQuery->whereBetween('created_at', [$rangeStart, $rangeEnd]);
        } else {
            // Default: hanya transaksi hari ini
            $summaryQuery->whereDate('created_at', today());
        }
        $transaksiFiltered = $summaryQuery->get();

        // Hitung total penjualan dan transaksi
        $penjualanHariIni = $transaksiFiltered->sum('total_harga');
        $totalTransaksi   = $transaksiFiltered->count();
        
        // Data ringkasan mitra dan stok
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
            // Tentukan apakah menampilkan data harian atau bulanan berdasarkan panjang rentang
            $daysDiff = $rangeStart->diffInDays($rangeEnd);

            if ($daysDiff <= 31) {
                // Tampilkan data harian jika rentang ≤ 31 hari
                $trendChartTitle = 'Tren Penjualan Harian';
                $trendChartDesc  = 'Menampilkan perkembangan total penjualan per hari dalam periode yang dipilih.';

                // Ambil data penjualan harian
                $dailyData = Transaksi::where('status_pembayaran', 'Sudah Dibayar')
                    ->whereBetween('created_at', [$rangeStart, $rangeEnd])
                    ->selectRaw('DATE(created_at) as tanggal, SUM(total_harga) as total')
                    ->groupBy('tanggal')
                    ->orderBy('tanggal')
                    ->get()
                    ->keyBy('tanggal');

                // Susun data chart harian
                $current = $rangeStart->copy()->startOfDay();
                $end = $rangeEnd->copy()->startOfDay();
                while ($current->lt($end)) {
                    $key = $current->format('Y-m-d');
                    $trendLabels[] = $current->format('d M');
                    $trendData[]   = isset($dailyData[$key]) ? (int) $dailyData[$key]->total : 0;
                    $current->addDay();
                }
            } else {
                // Tampilkan data bulanan jika rentang > 31 hari
                $trendChartTitle = 'Tren Penjualan Bulanan';
                $trendChartDesc  = 'Menampilkan perkembangan total penjualan per bulan dalam periode yang dipilih.';

                // Sesuaikan query berdasarkan driver database (SQLite/MySQL)
                $isSqlite = \Illuminate\Support\Facades\DB::getDriverName() === 'sqlite';
                $selectRaw = $isSqlite 
                    ? "strftime('%Y', created_at) as tahun, strftime('%m', created_at) as bulan, SUM(total_harga) as total"
                    : "YEAR(created_at) as tahun, MONTH(created_at) as bulan, SUM(total_harga) as total";

                // Ambil data penjualan bulanan
                $monthlyData = Transaksi::where('status_pembayaran', 'Sudah Dibayar')
                    ->whereBetween('created_at', [$rangeStart, $rangeEnd])
                    ->selectRaw($selectRaw)
                    ->groupBy('tahun', 'bulan')
                    ->orderBy('tahun')
                    ->orderBy('bulan')
                    ->get()
                    ->keyBy(fn($row) => $row->tahun . '-' . str_pad($row->bulan, 2, '0', STR_PAD_LEFT));

                // Susun data chart bulanan
                $current = $rangeStart->copy()->startOfMonth();
                $end = $rangeEnd->copy()->startOfMonth();
                while ($current->lt($end)) {
                    $key = $current->format('Y-m');
                    $trendLabels[] = $monthNames[(int)$current->format('n') - 1] . ' ' . $current->format('Y');
                    $trendData[]   = isset($monthlyData[$key]) ? (int) $monthlyData[$key]->total : 0;
                    $current->addMonth();
                }
            }
        } else {
            // Default: tampilkan 12 bulan terakhir
            $startOf12Months = Carbon::now()->startOfMonth()->subMonths(11);

            // Sesuaikan query berdasarkan driver database (SQLite/MySQL)
            $isSqlite = \Illuminate\Support\Facades\DB::getDriverName() === 'sqlite';
            $selectRaw = $isSqlite 
                ? "strftime('%Y', created_at) as tahun, strftime('%m', created_at) as bulan, SUM(total_harga) as total"
                : "YEAR(created_at) as tahun, MONTH(created_at) as bulan, SUM(total_harga) as total";

            // Ambil data penjualan 12 bulan terakhir
            $monthlyData = Transaksi::where('status_pembayaran', 'Sudah Dibayar')
                ->where('created_at', '>=', $startOf12Months)
                ->selectRaw($selectRaw)
                ->groupBy('tahun', 'bulan')
                ->orderBy('tahun')
                ->orderBy('bulan')
                ->get()
                ->keyBy(fn($row) => $row->tahun . '-' . str_pad($row->bulan, 2, '0', STR_PAD_LEFT));

            // Susun data chart 12 bulan terakhir
            for ($i = 11; $i >= 0; $i--) {
                $month = Carbon::now()->startOfMonth()->subMonths($i);
                $key   = $month->format('Y-m');
                $trendLabels[] = $monthNames[(int)$month->format('n') - 1] . ' ' . $month->format('Y');
                $trendData[]   = isset($monthlyData[$key]) ? (int) $monthlyData[$key]->total : 0;
            }
        }

        // --- Chart: Distribusi penjualan per produk ---
        $produkList = Produk::orderBy('nama')->get();
        $distLabels = [];
        $distData   = [];
        
        // Hitung jumlah terjual per produk
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

}
