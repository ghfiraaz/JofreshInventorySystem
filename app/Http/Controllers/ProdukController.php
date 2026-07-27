<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

/**
 * Controller Produk
 * Mengelola data produk: menampilkan, menambah, mengubah, menghapus, dan menambah stok produk.
 */
class ProdukController extends Controller
{
    /**
     * Menampilkan daftar semua produk.
     * Menghitung jumlah produk dengan stok rendah dan stok habis.
     */
    public function index()
    {
        // Ambil semua produk, urutkan berdasarkan terbaru
        $produk = Produk::orderBy('created_at', 'desc')->get();

        // Hitung jumlah produk stok rendah dan stok habis
        $stokRendahCount = $produk->filter(fn($p) => $p->status === 'Stok Rendah')->count();
        $stokHabisCount  = $produk->filter(fn($p) => $p->status === 'Stok Habis')->count();

        return view('admin.produk', compact('produk', 'stokRendahCount', 'stokHabisCount'));
    }

    /**
     * Menambah produk baru.
     * Validasi input, lalu simpan ke database dengan stok awal 0.
     */
    public function store(Request $request)
    {
        // Validasi input produk
        $request->validate([
            'nama'         => 'required|string|max:255',
            'harga'        => 'required|numeric|min:0',
            'stok_minimal' => 'required|integer|min:1',
        ], [
            'stok_minimal.required' => 'Batas stok minimal wajib diisi.',
            'stok_minimal.integer'  => 'Batas stok minimal harus berupa angka (digit) saja.',
            'stok_minimal.min'      => 'Batas stok minimal harus minimal 1.',
        ]);

        // Simpan produk baru ke database
        $produk = Produk::create([
            'nama'         => $request->nama,
            'kategori'     => 'Unggas',
            'stok'         => 0,
            'stok_minimal' => $request->stok_minimal,
            'satuan'       => 'Ekor',
            'harga'        => $request->harga,
        ]);

        // Kembalikan response JSON dengan data produk baru
        return response()->json([
            'message' => 'Produk berhasil ditambahkan',
            'produk'  => array_merge($produk->toArray(), [
                'status'       => $produk->status,
                'status_badge' => $produk->status_badge,
                'harga_format' => $produk->harga_format,
            ]),
        ], 201);
    }

    /**
     * Mengubah data produk yang sudah ada.
     * Validasi input, lalu update data produk di database.
     */
    public function update(Request $request, $id)
    {
        // Cari produk berdasarkan ID
        $produk = Produk::findOrFail($id);

        // Validasi input produk
        $request->validate([
            'nama'         => 'required|string|max:255',
            'harga'        => 'required|numeric|min:0',
            'stok_minimal' => 'required|integer|min:1',
        ], [
            'stok_minimal.required' => 'Batas stok minimal wajib diisi.',
            'stok_minimal.integer'  => 'Batas stok minimal harus berupa angka (digit) saja.',
            'stok_minimal.min'      => 'Batas stok minimal harus minimal 1.',
        ]);

        // Update data produk
        $produk->update([
            'nama'         => $request->nama,
            'stok_minimal' => $request->stok_minimal,
            'harga'        => $request->harga,
        ]);

        // Kembalikan response JSON dengan data produk yang diperbarui
        return response()->json([
            'message' => 'Produk berhasil diperbarui',
            'produk'  => array_merge($produk->toArray(), [
                'status'       => $produk->status,
                'status_badge' => $produk->status_badge,
                'harga_format' => $produk->harga_format,
            ]),
        ]);
    }

    /**
     * Menghapus produk berdasarkan ID.
     */
    public function destroy($id)
    {
        Produk::findOrFail($id)->delete();
        return response()->json(['message' => 'Produk berhasil dihapus']);
    }

    /**
     * Menambah stok produk.
     * Validasi jumlah input, lalu tambahkan ke stok produk yang ada.
     */
    public function tambahStok(Request $request, $id)
    {
        // Validasi jumlah stok yang akan ditambahkan
        $request->validate([
            'jumlah' => 'required|integer|min:1',
        ], [
            'jumlah.integer' => 'Jumlah stok harus berupa angka (digit) saja, tidak boleh mengandung huruf.',
            'jumlah.min'     => 'Jumlah stok minimal 1.',
        ]);

        // Cari produk dan tambahkan stok
        $produk = Produk::findOrFail($id);
        $produk->stok += $request->jumlah;
        $produk->save();

        // Kembalikan response JSON dengan data produk yang diperbarui
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
