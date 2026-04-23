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

    public function tambahStok(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|numeric|min:1',
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
