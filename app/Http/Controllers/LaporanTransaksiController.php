<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Controller Laporan Transaksi
 * Mengelola laporan penjualan harian dan riwayat transaksi bulanan.
 */
class LaporanTransaksiController extends Controller
{
    /**
     * Menampilkan laporan penjualan harian.
     * Menampilkan ringkasan stok keluar per produk, stok tersedia, dan total pendapatan.
     */
    public function laporanHarian(Request $request)
    {
        // Tentukan tanggal laporan (default: hari ini)
        $hariIni = $request->has('date') ? Carbon::parse($request->date) : today();
        
        // Ambil transaksi yang sudah dibayar pada tanggal tersebut
        $transaksi = Transaksi::with(['items', 'mitra'])
            ->whereDate('created_at', $hariIni)
            ->where('status_pembayaran', 'Sudah Dibayar')
            ->get();
            
        // Hitung total stok keluar per produk
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
        
        // Ambil daftar stok tersedia dan hitung total pendapatan
        $stokTersedia = Produk::orderBy('nama')->get();
        $totalPendapatan = $transaksi->sum('total_harga');

        return view('admin.laporan-harian', compact('transaksi', 'stokKeluar', 'stokTersedia', 'totalPendapatan', 'hariIni'));
    }

    /**
     * Menampilkan riwayat transaksi bulanan.
     * Mendukung filter berdasarkan bulan, tahun, atau tanggal tertentu.
     * Data dikelompokkan berdasarkan tanggal.
     */
    public function laporanTransaksi(Request $request)
    {
        // Ambil parameter filter
        $bulan = $request->input('bulan', '');
        $tahun = $request->input('tahun', date('Y'));
        $filterDate = $request->input('filter_date', '');

        // Query dasar: transaksi yang sudah dibayar
        $query = Transaksi::with(['mitra', 'items'])->where('status_pembayaran', 'Sudah Dibayar');
        
        // Filter berdasarkan tanggal spesifik
        if (!empty($filterDate)) {
            $query->whereDate('created_at', $filterDate);
        } else {
            // Filter berdasarkan bulan
            if (!empty($bulan) && $bulan !== 'all') {
                $query->whereMonth('created_at', $bulan);
            }
            // Filter berdasarkan tahun
            if (!empty($tahun)) {
                $query->whereYear('created_at', $tahun);
            }
        }

        $transaksiRaw = $query->orderBy('created_at', 'desc')->get();

        // Kelompokkan transaksi berdasarkan tanggal
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
