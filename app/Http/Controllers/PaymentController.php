<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Tampilkan halaman upload bukti pembayaran (public, tanpa login).
     */
    public function showUploadForm($token)
    {
        $mitra = Mitra::where('payment_token', $token)->firstOrFail();

        $transaksiUnpaid = Transaksi::with('items')
            ->where('mitra_id', $mitra->id)
            ->where('status_pembayaran', 'Belum Dibayar')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalTagihan = $transaksiUnpaid->sum('total_harga');

        return view('pembayaran-upload', compact('mitra', 'transaksiUnpaid', 'totalTagihan', 'token'));
    }

    /**
     * Proses upload bukti pembayaran dari mitra (public, tanpa login).
     */
    public function uploadBuktiBayar(Request $request, $token)
    {
        $mitra = Mitra::where('payment_token', $token)->firstOrFail();

        $request->validate([
            'bukti_pembayaran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // Store the file
        $file = $request->file('bukti_pembayaran');
        $filename = 'bukti_' . $mitra->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/bukti-pembayaran', $filename);

        // Update all unpaid transactions for this mitra
        Transaksi::where('mitra_id', $mitra->id)
            ->where('status_pembayaran', 'Belum Dibayar')
            ->update([
                'bukti_pembayaran' => 'bukti-pembayaran/' . $filename,
                'status_pembayaran' => 'Menunggu Validasi',
            ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Bukti pembayaran berhasil diupload. Terima kasih!']);
        }

        return redirect()->back()->with('success', 'Bukti pembayaran berhasil diupload. Terima kasih!');
    }

    /**
     * Download PDF tagihan (public, via token)
     */
    public function downloadTagihanPdf($token)
    {
        $mitra = Mitra::where('payment_token', $token)->firstOrFail();

        $transaksiUnpaid = Transaksi::with('items')
            ->where('mitra_id', $mitra->id)
            ->whereIn('status_pembayaran', ['Belum Dibayar', 'Menunggu Validasi'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($transaksiUnpaid->isEmpty()) {
            abort(404, 'Tidak ada tagihan.');
        }

        $totalTagihan = $transaksiUnpaid->sum('total_harga');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.tagihan', compact('mitra', 'transaksiUnpaid', 'totalTagihan'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('Tagihan_JoFresh_' . str_replace(' ', '_', $mitra->nama) . '.pdf');
    }
}
