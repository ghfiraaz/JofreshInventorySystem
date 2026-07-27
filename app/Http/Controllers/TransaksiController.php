<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Controller Transaksi
 * Menampilkan riwayat transaksi dan mengunduh invoice PDF.
 * Digunakan oleh Admin, Superadmin, dan Kasir.
 */
class TransaksiController extends Controller
{
    /**
     * Menampilkan daftar riwayat transaksi.
     * Mendukung filter berdasarkan tanggal.
     */
    public function index(Request $request)
    {
        // Query dasar: ambil semua transaksi dengan relasi user, mitra, dan items
        $query = Transaksi::with(['user', 'mitra', 'items'])
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan tanggal jika ada
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('created_at', $request->date);
        }

        $transaksi = $query->get();

        // Hitung ringkasan transaksi
        $totalTransaksi  = $transaksi->count();
        $totalPendapatan = $transaksi->sum('total_harga');
        $totalItemSold   = $transaksi->sum('total_berat');
        $filterDate      = $request->get('date', '');

        return view('riwayat-transaksi', compact(
            'transaksi', 'totalTransaksi', 'totalPendapatan', 'totalItemSold', 'filterDate'
        ));
    }

    /**
     * Mengunduh invoice dalam format PDF.
     * Menampilkan watermark LUNAS jika transaksi sudah dibayar.
     * Kasir hanya bisa mencetak transaksi miliknya sendiri.
     */
    public function downloadInvoicePdf($id)
    {
        $query = Transaksi::with(['mitra', 'items']);
        
        // Kasir hanya boleh mencetak transaksi miliknya sendiri
        if (auth()->user()->role === 'Kasir') {
            $query->where('user_id', auth()->id());
        }
        
        // Cari transaksi berdasarkan ID
        $transaksi = $query->findOrFail($id);
        $mitra = $transaksi->mitra;

        // Cek status lunas untuk watermark
        $isLunas = $transaksi->status_pembayaran === 'Sudah Dibayar';

        // Generate PDF invoice
        $pdf = Pdf::loadView('pdf.invoice-lunas', [
            'transaksi'  => $transaksi,
            'mitra'      => $mitra,
            'isLunas'    => $isLunas,
        ])->setPaper('a4', 'portrait');

        // Tentukan nama file berdasarkan status lunas
        $prefix = $isLunas ? 'Invoice-LUNAS-' : 'Invoice-';
        $filename = $prefix . $transaksi->no_transaksi . '.pdf';

        return $pdf->stream($filename);
    }
}
