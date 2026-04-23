<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Mitra;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    /**
     * Dashboard Kasir.
     */
    public function dashboard()
    {
        $hariIni = today();
        $transaksiHariIni = Transaksi::whereDate('created_at', $hariIni)
            ->where('user_id', Auth::id())
            ->where('status_pembayaran', 'Sudah Dibayar')
            ->get();
            
        $totalPenjualan = $transaksiHariIni->sum('total_harga');
        $totalTransaksi = $transaksiHariIni->count();
        $produkTersedia = Produk::where('stok', '>', 0)->count();

        return view('kasir.dashboard', compact('totalPenjualan', 'totalTransaksi', 'produkTersedia'));
    }

    /**
     * Transaksi Penjualan (POS) page.
     */
    public function transaksi()
    {
        $produk = Produk::where('stok', '>', 0)->orderBy('nama')->get();
        $mitra  = Mitra::where('status', 'Aktif')->orderBy('nama')->get();

        return view('kasir.transaksi', compact('produk', 'mitra'));
    }

    /**
     * Store a new transaction from POS.
     */
    public function storeTransaksi(Request $request)
    {
        $request->validate([
            'mitra_id'           => 'required|exists:mitra,id',
            'items'              => 'required|array|min:1',
            'items.*.produk_id'  => 'required|exists:produk,id',
            'items.*.jumlah'     => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request) {
            $totalHarga = 0;
            $totalItem  = 0;
            $totalBerat = 0;
            $itemsData  = [];

            foreach ($request->items as $item) {
                $produk = Produk::findOrFail($item['produk_id']);

                if ($produk->stok < $item['jumlah']) {
                    return response()->json([
                        'message' => "Stok {$produk->nama} tidak cukup. Tersisa: {$produk->stok} ekor."
                    ], 422);
                }

                $subtotal = $produk->harga * $item['jumlah'];
                $totalHarga += $subtotal;
                $totalItem  += $item['jumlah'];
                $totalBerat += $item['jumlah'];

                $itemsData[] = [
                    'produk_id'    => $produk->id,
                    'nama_produk'  => $produk->nama,
                    'jumlah'       => $item['jumlah'],
                    'harga_satuan' => $produk->harga,
                    'subtotal'     => $subtotal,
                ];

                $produk->decrement('stok', $item['jumlah']);
            }

            $lastTxn = Transaksi::whereDate('created_at', today())->count();
            $noTransaksi = 'JFR-' . date('Ymd') . '-' . str_pad($lastTxn + 1, 3, '0', STR_PAD_LEFT);

            $transaksi = Transaksi::create([
                'no_transaksi'       => $noTransaksi,
                'user_id'            => Auth::id(),
                'mitra_id'           => $request->mitra_id,
                'total_item'         => $totalItem,
                'total_harga'        => $totalHarga,
                'total_berat'        => $totalBerat,
                'metode_pembayaran'  => 'Tempo',
                'status_pembayaran'  => 'Belum Dibayar',
            ]);

            foreach ($itemsData as $itemData) {
                $transaksi->items()->create($itemData);
            }

            $transaksi->load('mitra', 'items');

            return response()->json([
                'message'   => 'Transaksi berhasil disimpan',
                'transaksi' => $transaksi,
            ], 201);
        });
    }

    /**
     * Riwayat Transaksi page.
     */
    public function riwayat(Request $request)
    {
        $query = Transaksi::with(['mitra', 'items'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        if ($request->has('date') && $request->date != '') {
            $query->whereDate('created_at', $request->date);
        }

        $transaksi = $query->get();

        $totalTransaksi  = $transaksi->count();
        $totalPendapatan = $transaksi->sum('total_harga');
        $totalItemSold   = $transaksi->sum('total_berat');
        $filterDate      = $request->get('date', '');

        return view('kasir.riwayat', compact(
            'transaksi', 'totalTransaksi', 'totalPendapatan', 'totalItemSold', 'filterDate'
        ));
    }

    /**
     * Tagihan Bulanan page.
     */
    public function tagihan(Request $request)
    {
        $bulan = $request->get('bulan', date('n'));
        $tahun = $request->get('tahun', date('Y'));

        $transaksi = Transaksi::with(['mitra', 'items'])
            ->where('user_id', Auth::id())
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->where('status_pembayaran', 'Belum Dibayar')
            ->orderBy('created_at', 'desc')
            ->get();

        $grouped = $transaksi->groupBy('mitra_id');
        $mitraTagihan = [];

        foreach ($grouped as $mitraId => $txns) {
            $mitra = $txns->first()->mitra;
            if (!$mitra) continue;
            $mitraTagihan[] = [
                'mitra'       => $mitra,
                'transaksi'   => $txns,
                'total'       => $txns->sum('total_harga'),
                'count'       => $txns->count(),
            ];
        }

        $totalMitra   = count($mitraTagihan);
        $totalTagihan = $transaksi->sum('total_harga');
        $sudahLunas   = 0;
        $belumLunas   = $totalMitra;

        $namaBulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];

        return view('kasir.tagihan', compact(
            'mitraTagihan', 'bulan', 'tahun',
            'totalMitra', 'totalTagihan', 'sudahLunas', 'belumLunas', 'namaBulan'
        ));
    }

    /**
     * Proses pembayaran tagihan mitra
     */
    public function bayarTagihan(Request $request)
    {
        $request->validate([
            'mitra_id' => 'required|exists:mitra,id',
            'bulan'    => 'required|integer|min:1|max:12',
            'tahun'    => 'required|integer',
        ]);

        Transaksi::where('user_id', Auth::id())
            ->where('mitra_id', $request->mitra_id)
            ->whereMonth('created_at', $request->bulan)
            ->whereYear('created_at', $request->tahun)
            ->where('status_pembayaran', 'Belum Dibayar')
            ->update([
                'status_pembayaran' => 'Sudah Dibayar',
                'updated_at'        => now()
            ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Pembayaran berhasil dikonfirmasi.']);
        }

        return redirect('/kasir/riwayat')->with('success', 'Pembayaran berhasil dikonfirmasi.');
    }

    /**
     * Tampilkan invoice digital untuk transaksi tertentu
     */
    public function invoice($id)
    {
        $transaksi = Transaksi::with(['mitra', 'items'])->where('user_id', Auth::id())->findOrFail($id);
        return view('kasir.invoice', compact('transaksi'));
    }
}
