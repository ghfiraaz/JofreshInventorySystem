<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Transaksi;
use Illuminate\Http\Request;

/**
 * Controller Pembayaran Publik
 * Mengelola halaman upload bukti pembayaran yang diakses oleh mitra (tanpa login).
 */
class PaymentController extends Controller
{
    /**
     * Menampilkan halaman upload bukti pembayaran (publik, tanpa login).
     * Menampilkan daftar tagihan belum dibayar milik mitra berdasarkan token.
     */
    public function showUploadForm($token)
    {
        // Cari mitra berdasarkan payment token
        $mitra = Mitra::where('payment_token', $token)->firstOrFail();

        // Ambil semua transaksi yang belum dibayar / ditolak / menunggu validasi
        $transaksiUnpaid = Transaksi::with('items')
            ->where('mitra_id', $mitra->id)
            ->whereIn('status_pembayaran', ['Belum Dibayar', 'Ditolak', 'Menunggu Validasi'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Hitung total tagihan (hanya yang Belum Dibayar dan Ditolak)
        $totalTagihan = $transaksiUnpaid->whereIn('status_pembayaran', ['Belum Dibayar', 'Ditolak'])->sum('total_harga');

        // Cek apakah ada transaksi yang ditolak
        $hasDitolak = $transaksiUnpaid->contains('status_pembayaran', 'Ditolak');

        return view('pembayaran-upload', compact('mitra', 'transaksiUnpaid', 'totalTagihan', 'token', 'hasDitolak'));
    }

    /**
     * Memproses upload bukti pembayaran dari mitra (publik, tanpa login).
     * Validasi file (format JPG/PNG/PDF, maks 5MB), simpan file, dan update status transaksi.
     */
    public function uploadBuktiBayar(Request $request, $token)
    {
        // Cari mitra berdasarkan payment token
        $mitra = Mitra::where('payment_token', $token)->firstOrFail();

        // Validasi file bukti pembayaran (format dan ukuran)
        $request->validate([
            'bukti_pembayaran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'bukti_pembayaran.required' => 'Bukti pembayaran wajib diunggah.',
            'bukti_pembayaran.file' => 'File tidak valid.',
            'bukti_pembayaran.mimes' => 'Format file tidak didukung. Hanya JPG, PNG, dan PDF yang diperbolehkan.',
            'bukti_pembayaran.max' => 'Ukuran file terlalu besar. Maksimal 5 MB.',
        ]);

        // Simpan file ke disk 'public' (storage/app/public/bukti-pembayaran/)
        $file = $request->file('bukti_pembayaran');
        $filename = 'bukti_' . $mitra->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('bukti-pembayaran', $filename, 'public');

        // Update semua transaksi belum dibayar/ditolak menjadi Menunggu Validasi
        Transaksi::where('mitra_id', $mitra->id)
            ->whereIn('status_pembayaran', ['Belum Dibayar', 'Ditolak'])
            ->update([
                'bukti_pembayaran' => 'bukti-pembayaran/' . $filename,
                'status_pembayaran' => 'Menunggu Validasi',
            ]);

        // Kunci upload pembayaran untuk mitra (mencegah upload ganda)
        $mitra->update(['payment_upload_locked' => true]);

        // Trigger notifikasi ke Kasir bahwa mitra telah mengunggah bukti pembayaran
        \App\Models\Notification::triggerBuktiPembayaran($mitra);

        // Response sesuai tipe request (JSON atau redirect)
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Bukti pembayaran berhasil diupload. Terima kasih!']);
        }

        return redirect()->back()->with('success', 'Bukti pembayaran berhasil diupload. Terima kasih!');
    }

}
