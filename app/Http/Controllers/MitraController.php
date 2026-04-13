<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use Illuminate\Http\Request;

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
            'nama'    => 'required|string|max:255',
            'kontak'  => 'nullable|string|max:50',
            'alamat'  => 'nullable|string|max:500',
        ]);

        $mitra = Mitra::create([
            'nama'   => $request->nama,
            'kontak' => $request->kontak,
            'alamat' => $request->alamat,
            'status' => 'Aktif',
        ]);

        return response()->json(['message' => 'Mitra berhasil ditambahkan', 'mitra' => $mitra], 201);
    }

    public function update(Request $request, $id)
    {
        $mitra = Mitra::findOrFail($id);

        $request->validate([
            'nama'   => 'required|string|max:255',
            'kontak' => 'nullable|string|max:50',
            'alamat' => 'nullable|string|max:500',
        ]);

        $mitra->update($request->only('nama', 'kontak', 'alamat'));

        return response()->json(['message' => 'Mitra berhasil diperbarui', 'mitra' => $mitra]);
    }

    public function destroy($id)
    {
        Mitra::findOrFail($id)->delete();
        return response()->json(['message' => 'Mitra berhasil dihapus']);
    }
}
