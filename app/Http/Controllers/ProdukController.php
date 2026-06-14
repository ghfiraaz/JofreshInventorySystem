<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index()
    {
        $produk = Produk::orderBy('created_at', 'desc')->get();
        $stokRendahCount = $produk->filter(fn($p) => $p->status === 'Stok Rendah')->count();
        $stokHabisCount  = $produk->filter(fn($p) => $p->status === 'Stok Habis')->count();
        return view('admin.produk', compact('produk', 'stokRendahCount', 'stokHabisCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'         => 'required|string|max:255',
            'harga'        => 'required|numeric|min:0',
            'stok_minimal' => 'nullable|integer|min:0',
        ], [
            'stok_minimal.integer' => 'Batas stok minimal harus berupa angka (digit) saja.',
        ]);

        $produk = Produk::create([
            'nama'         => $request->nama,
            'kategori'     => 'Unggas',
            'stok'         => 0,
            'stok_minimal' => $request->stok_minimal ?? 0,
            'satuan'       => 'Ekor',
            'harga'        => $request->harga,
        ]);

        return response()->json([
            'message' => 'Produk berhasil ditambahkan',
            'produk'  => array_merge($produk->toArray(), [
                'status'       => $produk->status,
                'status_badge' => $produk->status_badge,
                'harga_format' => $produk->harga_format,
            ]),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        $request->validate([
            'nama'         => 'required|string|max:255',
            'harga'        => 'required|numeric|min:0',
            'stok_minimal' => 'nullable|integer|min:0',
        ], [
            'stok_minimal.integer' => 'Batas stok minimal harus berupa angka (digit) saja.',
        ]);

        $produk->update([
            'nama'         => $request->nama,
            'stok_minimal' => $request->stok_minimal ?? 0,
            'harga'        => $request->harga,
        ]);

        return response()->json([
            'message' => 'Produk berhasil diperbarui',
            'produk'  => array_merge($produk->toArray(), [
                'status'       => $produk->status,
                'status_badge' => $produk->status_badge,
                'harga_format' => $produk->harga_format,
            ]),
        ]);
    }

    public function destroy($id)
    {
        Produk::findOrFail($id)->delete();
        return response()->json(['message' => 'Produk berhasil dihapus']);
    }

    public function tambahStok(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
        ], [
            'jumlah.integer' => 'Jumlah stok harus berupa angka (digit) saja, tidak boleh mengandung huruf.',
            'jumlah.min'     => 'Jumlah stok minimal 1.',
        ]);

        $produk = Produk::findOrFail($id);
        $produk->stok += $request->jumlah;
        $produk->save();

        return response()->json([
            'message' => 'Stok berhasil ditambahkan',
            'produk'  => array_merge($produk->toArray(), [
                'status'       => $produk->status,
                'status_badge' => $produk->status_badge,
                'harga_format' => $produk->harga_format,
            ]),
        ]);
    }
}

