<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with(['user', 'mitra', 'items'])
            ->orderBy('created_at', 'desc');

        // Kasir hanya melihat transaksi miliknya sendiri yang sudah lunas
        if (auth()->user()->role === 'Kasir') {
            $query->where('user_id', auth()->id())
                  ->where('status_pembayaran', 'Sudah Dibayar');
        }

        if ($request->has('date') && $request->date != '') {
            $query->whereDate('created_at', $request->date);
        }

        $transaksi = $query->get();

        $totalTransaksi  = $transaksi->count();
        $totalPendapatan = $transaksi->sum('total_harga');
        $totalItemSold   = $transaksi->sum('total_berat');
        $filterDate      = $request->get('date', '');

        return view('riwayat-transaksi', compact(
            'transaksi', 'totalTransaksi', 'totalPendapatan', 'totalItemSold', 'filterDate'
        ));
    }

    /**
     * Download PDF invoice (dengan watermark LUNAS jika sudah dibayar) untuk Admin, Superadmin, dan Kasir.
     */
    public function downloadInvoicePdf($id)
    {
        $query = Transaksi::with(['mitra', 'items']);
        
        // Kasir hanya boleh mencetak transaksi miliknya sendiri
        if (auth()->user()->role === 'Kasir') {
            $query->where('user_id', auth()->id());
        }
        
        $transaksi = $query->findOrFail($id);
        $mitra = $transaksi->mitra;
        $isLunas = $transaksi->status_pembayaran === 'Sudah Dibayar';

        $pdf = Pdf::loadView('pdf.invoice-lunas', [
            'transaksi'  => $transaksi,
            'mitra'      => $mitra,
            'isLunas'    => $isLunas,
        ])->setPaper('a4', 'portrait');

        $prefix = $isLunas ? 'Invoice-LUNAS-' : 'Invoice-';
        $filename = $prefix . $transaksi->no_transaksi . '.pdf';

        return $pdf->stream($filename);
    }
}
