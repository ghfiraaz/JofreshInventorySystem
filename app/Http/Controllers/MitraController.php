<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

/**
 * Controller Mitra
 * Mengelola data mitra: menampilkan, menambah, mengubah, dan menghapus mitra.
 */
class MitraController extends Controller
{
    /**
     * Menampilkan daftar semua mitra.
     */
    public function index()
    {
        // Ambil semua mitra, urutkan berdasarkan terbaru
        $mitra = Mitra::orderBy('created_at', 'desc')->get();
        return view('admin.mitra', compact('mitra'));
    }

    /**
     * Menambah mitra baru.
     * Validasi input, generate payment token, lalu simpan ke database.
     */
    public function store(Request $request)
    {
        // Validasi input mitra
        $request->validate([
            'nama'                => 'required|string|max:255',
            'kontak'              => ['nullable', 'numeric', 'digits_between:10,13', 'unique:mitra,kontak'],
            'email'               => ['required', 'email', 'max:255', 'regex:/@gmail\.com$/i', 'unique:mitra,email'],
            'alamat'              => 'required|string|max:500',
            'tanggal_jatuh_tempo' => 'nullable|integer|min:1|max:31',
        ], [
            'nama.required'       => 'Nama mitra wajib diisi.',
            'alamat.required'     => 'Alamat mitra wajib diisi.',
            'email.required'      => 'Email mitra wajib diisi.',
            'kontak.numeric'      => 'no telpon harus diisi dengan angka',
            'kontak.digits_between' => 'no telpon harus berisi 10-13 digit',
            'kontak.unique'       => 'Nomor telepon ini sudah terdaftar oleh mitra lain.',
            'email.regex'         => 'Email mitra harus menggunakan domain @gmail.com.',
            'email.email'         => 'Format email tidak valid. Email harus menggunakan domain @gmail.com.',
            'email.unique'        => 'Email ini sudah terdaftar oleh mitra lain.',
        ]);

        // Simpan mitra baru ke database dengan payment token unik
        $mitra = Mitra::create([
            'nama'                => $request->nama,
            'kontak'              => $request->kontak,
            'email'               => $request->email,
            'alamat'              => $request->alamat,
            'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo ?? 1,
            'status'              => 'Aktif',
            'payment_token'       => Str::uuid()->toString(),
        ]);

        return response()->json(['message' => 'Mitra berhasil ditambahkan', 'mitra' => $mitra], 201);
    }

    /**
     * Mengubah data mitra yang sudah ada.
     * Jika tanggal jatuh tempo berubah, sinkronkan ke transaksi yang belum dibayar.
     */
    public function update(Request $request, $id)
    {
        // Cari mitra berdasarkan ID
        $mitra = Mitra::findOrFail($id);

        // Validasi input mitra
        $request->validate([
            'nama'                => 'required|string|max:255',
            'kontak'              => ['nullable', 'numeric', 'digits_between:10,13', 'unique:mitra,kontak,' . $id],
            'email'               => ['required', 'email', 'max:255', 'regex:/@gmail\.com$/i', 'unique:mitra,email,' . $id],
            'alamat'              => 'required|string|max:500',
            'tanggal_jatuh_tempo' => 'nullable|integer|min:1|max:31',
        ], [
            'nama.required'       => 'Nama mitra wajib diisi.',
            'alamat.required'     => 'Alamat mitra wajib diisi.',
            'email.required'      => 'Email mitra wajib diisi.',
            'kontak.numeric'      => 'no telpon harus diisi dengan angka',
            'kontak.digits_between' => 'no telpon harus berisi 10-13 digit',
            'kontak.unique'       => 'Nomor telepon ini sudah terdaftar oleh mitra lain.',
            'email.regex'         => 'Email mitra harus menggunakan domain @gmail.com.',
            'email.email'         => 'Format email tidak valid. Email harus menggunakan domain @gmail.com.',
            'email.unique'        => 'Email ini sudah terdaftar oleh mitra lain.',
        ]);

        // Simpan tanggal jatuh tempo lama untuk perbandingan
        $oldTanggal = $mitra->tanggal_jatuh_tempo;

        // Update data mitra
        $mitra->update($request->only('nama', 'kontak', 'email', 'alamat', 'tanggal_jatuh_tempo'));

        // Sinkronkan jatuh tempo transaksi jika tanggal berubah
        $newTanggal = $mitra->tanggal_jatuh_tempo;
        if ($oldTanggal != $newTanggal) {
            $this->syncJatuhTempo($mitra, $newTanggal);
        }

        return response()->json(['message' => 'Mitra berhasil diperbarui', 'mitra' => $mitra]);
    }

    /**
     * Hitung ulang jatuh tempo untuk semua transaksi belum dibayar milik mitra.
     * Dipanggil ketika tanggal jatuh tempo mitra diubah.
     */
    private function syncJatuhTempo(Mitra $mitra, int $tanggal): void
    {
        $now = now();
        $bulanIni = $now->copy()->day(min($tanggal, $now->daysInMonth));
        
        // Jika tanggal jatuh tempo bulan ini sudah lewat, pakai bulan depan
        if ($bulanIni->lt($now)) {
            $bulanDepan = $now->copy()->addMonth();
            $newDate = $bulanDepan->day(min($tanggal, $bulanDepan->daysInMonth));
        } else {
            $newDate = $bulanIni;
        }

        // Update jatuh tempo semua transaksi belum dibayar milik mitra ini
        Transaksi::where('mitra_id', $mitra->id)
            ->whereIn('status_pembayaran', ['Belum Dibayar', 'Menunggu Validasi'])
            ->update(['jatuh_tempo' => $newDate->toDateString()]);
    }

    /**
     * Menghapus mitra berdasarkan ID.
     */
    public function destroy($id)
    {
        Mitra::findOrFail($id)->delete();
        return response()->json(['message' => 'Mitra berhasil dihapus']);
    }
}
