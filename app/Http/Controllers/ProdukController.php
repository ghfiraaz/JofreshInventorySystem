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
            'nama'        => 'required|string|max:255',
            'kategori'    => 'required|string|max:255',
            'stok'        => 'required|numeric|min:0',
            'stok_minimal'=> 'required|numeric|min:0',
            'satuan'      => 'required|string',
            'harga'       => 'required|numeric|min:0',
        ]);

        $produk = Produk::create($request->only('nama','kategori','stok','stok_minimal','satuan','harga'));

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
            'nama'        => 'required|string|max:255',
            'kategori'    => 'required|string|max:255',
            'stok'        => 'required|numeric|min:0',
            'stok_minimal'=> 'required|numeric|min:0',
            'satuan'      => 'required|string',
            'harga'       => 'required|numeric|min:0',
        ]);

        $produk->update($request->only('nama','kategori','stok','stok_minimal','satuan','harga'));

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
}
