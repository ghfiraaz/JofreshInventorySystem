<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class MitraController extends Controller
{
    public function index()
    {
        $mitra = Mitra::orderBy('created_at', 'desc')->get();
        return view('admin.mitra', compact('mitra'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'                => 'required|string|max:255',
            'kontak'              => ['nullable', 'numeric', 'digits_between:10,13'],
            'email'               => ['nullable', 'email', 'max:255', 'regex:/@gmail\.com$/i'],
            'alamat'              => 'required|string|max:500',
            'tanggal_jatuh_tempo' => 'nullable|integer|min:1|max:31',
        ], [
            'nama.required'       => 'Nama mitra wajib diisi.',
            'alamat.required'     => 'Alamat mitra wajib diisi.',
            'kontak.numeric'      => 'no telpon harus diisi dengan angka',
            'kontak.digits_between' => 'no telpon harus berisi 10-13 digit',
            'email.regex'         => 'Email mitra harus menggunakan domain @gmail.com.',
            'email.email'         => 'Format email tidak valid. Email harus menggunakan domain @gmail.com.',
        ]);

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

    public function update(Request $request, $id)
    {
        $mitra = Mitra::findOrFail($id);

        $request->validate([
            'nama'                => 'required|string|max:255',
            'kontak'              => ['nullable', 'numeric', 'digits_between:10,13'],
            'email'               => ['nullable', 'email', 'max:255', 'regex:/@gmail\.com$/i'],
            'alamat'              => 'required|string|max:500',
            'tanggal_jatuh_tempo' => 'nullable|integer|min:1|max:31',
        ], [
            'nama.required'       => 'Nama mitra wajib diisi.',
            'alamat.required'     => 'Alamat mitra wajib diisi.',
            'kontak.numeric'      => 'no telpon harus diisi dengan angka',
            'kontak.digits_between' => 'no telpon harus berisi 10-13 digit',
            'email.regex'         => 'Email mitra harus menggunakan domain @gmail.com.',
            'email.email'         => 'Format email tidak valid. Email harus menggunakan domain @gmail.com.',
        ]);

        $oldTanggal = $mitra->tanggal_jatuh_tempo;
        $mitra->update($request->only('nama', 'kontak', 'email', 'alamat', 'tanggal_jatuh_tempo'));

        // Sync jatuh_tempo on unpaid transaksi if tanggal changed
        $newTanggal = $mitra->tanggal_jatuh_tempo;
        if ($oldTanggal != $newTanggal) {
            $this->syncJatuhTempo($mitra, $newTanggal);
        }

        return response()->json(['message' => 'Mitra berhasil diperbarui', 'mitra' => $mitra]);
    }

    /**
     * Recalculate jatuh_tempo for all unpaid transaksi of a mitra
     */
    private function syncJatuhTempo(Mitra $mitra, int $tanggal): void
    {
        $now = now();
        $bulanIni = $now->copy()->day(min($tanggal, $now->daysInMonth));
        
        if ($bulanIni->lte($now)) {
            $bulanDepan = $now->copy()->addMonth();
            $newDate = $bulanDepan->day(min($tanggal, $bulanDepan->daysInMonth));
        } else {
            $newDate = $bulanIni;
        }

        Transaksi::where('mitra_id', $mitra->id)
            ->whereIn('status_pembayaran', ['Belum Dibayar', 'Menunggu Validasi'])
            ->update(['jatuh_tempo' => $newDate->toDateString()]);
    }

    public function destroy($id)
    {
        Mitra::findOrFail($id)->delete();
        return response()->json(['message' => 'Mitra berhasil dihapus']);
    }
}
