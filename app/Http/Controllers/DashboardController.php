<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Produk;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // --- Summary cards ---
        $penjualanHariIni  = 0;  // Will be updated when transactions exist
        $totalTransaksi    = 0;  // Will be updated when transactions exist
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
            $trendData[]   = 0; // Will come from transactions once they exist
        }

        // --- Chart: Distribusi per produk ---
        $produkList   = Produk::orderBy('nama')->get();
        $distLabels   = $produkList->pluck('nama')->toArray();
        $distData     = array_fill(0, count($distLabels), 0); // All zero until transactions exist

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
}
