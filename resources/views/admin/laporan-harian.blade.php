<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Harian - {{ $hariIni->format('d M Y') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body { background-color: #f3f4f6; }
        @media print {
            body { background-color: white; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            .print-shadow-none { box-shadow: none !important; border: none !important; }
            @page { margin: 0; }
        }
    </style>
</head>
<body class="font-sans text-gray-800 antialiased py-10">

<div class="max-w-4xl mx-auto bg-white p-10 rounded-2xl shadow-xl border border-gray-100 print-shadow-none relative">
    
    {{-- Actions --}}
    <div class="flex justify-end gap-3 mb-8 no-print">
        <button onclick="window.close()" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-200 transition-colors no-underline border-none cursor-pointer">Tutup</button>
        <button onclick="window.print()" class="px-4 py-2 text-white rounded-lg text-sm font-bold hover:opacity-90 transition-colors flex items-center gap-2 cursor-pointer border-none" style="background: linear-gradient(135deg, #7B3911, #A1511E);">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.724.092m6.524-4.31a5.25 5.25 0 0 0-5.25 5.25m5.25-5.25a5.25 5.25 0 0 1 5.25 5.25m-5.25-5.25v5.25m0-5.25a5.25 5.25 0 0 1 5.25 5.25m0 0v1.5m0-1.5c0 1.16-.311 2.247-.852 3.146M21 12c0 5.04-4.048 9.146-9.146 9.146m9.146-9.146a9.146 9.146 0 0 0-9.146-9.146M3 12c0-5.04 4.048-9.146 9.146-9.146m-9.146 9.146c0 1.16.311 2.247.852 3.146M3 12v1.5m0-1.5a5.25 5.25 0 0 0 5.25 5.25m0 0v1.5m0-1.5c.24.03.48.062.724.092" /></svg>
            Cetak / Download PDF
        </button>
    </div>

    {{-- Header --}}
    <div class="flex justify-between items-center mb-10 border-b border-gray-200 pb-6">
        <div class="flex items-center gap-5">
            <img src="{{ asset('images/logo-jofresh.png') }}" alt="JoFresh Logo" style="height: 14mm; width: auto; display: block; object-fit: contain;">
            <div>
                <h1 class="text-3xl font-extrabold tracking-wide mb-1" style="color: #7B3911;">JoFresh</h1>
                <p class="text-xs text-gray-500">JoFresh Inventory System<br>Laporan Harian Transaksi & Stok</p>
            </div>
        </div>
        <div class="text-right">
            <h2 class="text-2xl font-black text-gray-800 uppercase tracking-widest mb-1">LAPORAN</h2>
            <div class="text-sm text-gray-500">Tanggal: <span class="font-bold text-gray-800">{{ $hariIni->format('d/m/Y') }}</span></div>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-3 gap-6 mb-10">
        <div class="p-5 rounded-xl border" style="background: #FAF5EF; border-color: #E0C4A8;">
            <div class="text-xs font-bold uppercase tracking-wider mb-2" style="color: #C8702A;">Total Transaksi</div>
            <div class="font-bold text-gray-800 text-3xl">{{ count($transaksi) }} <span class="text-base text-gray-500 font-medium">transaksi</span></div>
        </div>
        <div class="p-5 bg-green-50 rounded-xl border border-green-100">
            <div class="text-xs font-bold text-green-500 uppercase tracking-wider mb-2">Total Pendapatan</div>
            <div class="font-bold text-green-700 text-3xl">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
        </div>
        <div class="p-5 bg-orange-50 rounded-xl border border-orange-100">
            <div class="text-xs font-bold text-orange-500 uppercase tracking-wider mb-2">Sisa Stok Keseluruhan</div>
            <div class="font-bold text-orange-700 text-3xl">{{ intval($stokTersedia->sum('stok')) }} <span class="text-base text-orange-600/80 font-medium">ekor</span></div>
        </div>
    </div>

    {{-- Rekap Stok Keluar & Masuk --}}
    <div class="mb-10">
        <h3 class="text-lg font-bold text-gray-800 mb-4 pl-3" style="border-left: 4px solid #7B3911;">Rekapitulasi Stok Produk</h3>
        <table class="w-full text-left border-collapse border border-gray-200 rounded-lg overflow-hidden">
            <thead>
                <tr class="bg-gray-100 border-b border-gray-200">
                    <th class="py-3 px-4 text-xs font-bold text-gray-600 uppercase tracking-wider">Produk</th>
                    <th class="py-3 px-4 text-xs font-bold text-gray-600 uppercase tracking-wider text-center">Stok Terjual / Keluar</th>
                    <th class="py-3 px-4 text-xs font-bold text-gray-600 uppercase tracking-wider text-center">Sisa Stok Akhir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stokTersedia as $produk)
                    <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50">
                        <td class="py-3 px-4 text-sm font-semibold text-gray-800">{{ $produk->nama }}</td>
                        <td class="py-3 px-4 text-sm text-amber-600 font-bold text-center">
                            {{ intval($stokKeluar[$produk->nama] ?? 0) }} ekor
                        </td>
                        <td class="py-3 px-4 text-sm font-bold text-center" style="color: #7B3911;">
                            {{ intval($produk->stok) }} ekor
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Detail Transaksi --}}
    <div>
        <h3 class="text-lg font-bold text-gray-800 mb-4 pl-3" style="border-left: 4px solid #7B3911;">Rincian Transaksi Penjualan</h3>
        <table class="w-full text-left border-collapse border border-gray-200 rounded-lg overflow-hidden">
            <thead>
                <tr class="bg-gray-100 border-b border-gray-200">
                    <th class="py-3 px-4 text-xs font-bold text-gray-600 uppercase tracking-wider">Waktu</th>
                    <th class="py-3 px-4 text-xs font-bold text-gray-600 uppercase tracking-wider">No. Transaksi</th>
                    <th class="py-3 px-4 text-xs font-bold text-gray-600 uppercase tracking-wider">Mitra</th>
                    <th class="py-3 px-4 text-xs font-bold text-gray-600 uppercase tracking-wider text-right">Total Item</th>
                    <th class="py-3 px-4 text-xs font-bold text-gray-600 uppercase tracking-wider text-right">Nilai Transaksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksi as $tx)
                    <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50">
                        <td class="py-3 px-4 text-sm text-gray-600">{{ $tx->created_at->format('H:i') }} WIB</td>
                        <td class="py-3 px-4 text-sm font-semibold text-gray-800">{{ $tx->no_transaksi }}</td>
                        <td class="py-3 px-4 text-sm text-gray-600">{{ $tx->mitra->nama ?? 'N/A' }}</td>
                        <td class="py-3 px-4 text-sm text-gray-600 text-right">{{ intval($tx->total_item) }}</td>
                        <td class="py-3 px-4 text-sm font-bold text-gray-800 text-right">Rp {{ number_format($tx->total_harga, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-400 text-sm italic">Tidak ada transaksi lunas hari ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    <div class="text-center pt-8 mt-12 border-t border-gray-200">
        <p class="text-xs text-gray-400">Laporan ini dibuat secara otomatis oleh JoFresh Inventory System (JIS).</p>
        <p class="text-xs text-gray-400">Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} WIB</p>
    </div>

</div>

</body>
</html>
