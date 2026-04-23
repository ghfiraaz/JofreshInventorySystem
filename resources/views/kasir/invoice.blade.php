<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $transaksi->no_transaksi }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body { background-color: #f3f4f6; }
        @media print {
            body { background-color: white; -webkit-print-color-adjust: exact; print-color-adjust: exact; padding: 0; margin: 0; }
            .no-print { display: none !important; }
            .print-shadow-none { box-shadow: none !important; border: none !important; }
            @page { margin: 10mm; size: A4; }
            .invoice-wrap { padding: 24px !important; margin: 0 !important; box-shadow: none !important; border: none !important; }
        }
    </style>
</head>
<body class="font-sans text-gray-800 antialiased py-10">

<div class="max-w-3xl mx-auto bg-white p-8 rounded-2xl shadow-xl border border-gray-100 print-shadow-none invoice-wrap relative">
    
    {{-- Watermark Lunas --}}
    @if($transaksi->status_pembayaran === 'Sudah Dibayar')
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-10 pointer-events-none rotate-[-30deg]">
            <span class="text-9xl font-black text-green-600 uppercase tracking-widest border-8 border-green-600 rounded-3xl p-6">LUNAS</span>
        </div>
    @endif

    {{-- Actions --}}
    <div class="flex justify-end gap-3 mb-8 no-print">
        <a href="{{ url('/kasir/riwayat') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-200 transition-colors no-underline">Kembali</a>
        <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 transition-colors flex items-center gap-2 cursor-pointer border-none">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.724.092m6.524-4.31a5.25 5.25 0 0 0-5.25 5.25m5.25-5.25a5.25 5.25 0 0 1 5.25 5.25m-5.25-5.25v5.25m0-5.25a5.25 5.25 0 0 1 5.25 5.25m0 0v1.5m0-1.5c0 1.16-.311 2.247-.852 3.146M21 12c0 5.04-4.048 9.146-9.146 9.146m9.146-9.146a9.146 9.146 0 0 0-9.146-9.146M3 12c0-5.04 4.048-9.146 9.146-9.146m-9.146 9.146c0 1.16.311 2.247.852 3.146M3 12v1.5m0-1.5a5.25 5.25 0 0 0 5.25 5.25m0 0v1.5m0-1.5c.24.03.48.062.724.092" /></svg>
            Cetak / Download PDF
        </button>
    </div>

    {{-- Header --}}
    <div class="flex justify-between items-start mb-8">
        <div>
            <h1 class="text-3xl font-extrabold tracking-[4px] text-blue-900 mb-2">J I S</h1>
            <p class="text-sm text-gray-500">JoFresh Inventory System<br>Jl. Bintaro Raya</p>
        </div>
        <div class="text-right">
            <h2 class="text-2xl font-black text-gray-800 uppercase tracking-widest mb-2">INVOICE</h2>
            <div class="text-sm text-gray-500 mb-1"># <span class="font-bold text-gray-800">{{ $transaksi->no_transaksi }}</span></div>
            <div class="text-sm text-gray-500">Waktu: <span class="font-bold text-gray-800">{{ $transaksi->created_at->format('d/m/Y H:i') }} WIB</span></div>
        </div>
    </div>

    {{-- Billing Info --}}
    <div class="grid grid-cols-2 gap-6 mb-6 p-5 bg-gray-50 rounded-xl border border-gray-100">
        <div>
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Ditagihkan Kepada</div>
            <div class="font-bold text-gray-800 text-lg mb-1">{{ $transaksi->mitra->nama ?? 'Mitra (Terhapus)' }}</div>
            <div class="text-sm text-gray-500">{{ $transaksi->mitra->alamat ?? '-' }}</div>
            <div class="text-sm text-gray-500">{{ $transaksi->mitra->kontak ?? '-' }}</div>
        </div>
        <div class="text-right">
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Informasi Pembayaran</div>
            <div class="text-sm text-gray-500 mb-1">Metode: <span class="font-semibold text-gray-800">{{ $transaksi->metode_pembayaran }}</span></div>
            <div class="text-sm text-gray-500 mb-1">Status: 
                @if($transaksi->status_pembayaran === 'Sudah Dibayar')
                    <span class="font-bold text-green-600 bg-green-100 px-2 py-0.5 rounded text-xs">Lunas</span>
                @else
                    <span class="font-bold text-amber-600 bg-amber-100 px-2 py-0.5 rounded text-xs">Belum Dibayar</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Items --}}
    <div class="mb-6 border border-gray-200 rounded-xl overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="py-3 px-5 text-xs font-bold text-gray-500 uppercase tracking-wider">Produk</th>
                    <th class="py-3 px-5 text-xs font-bold text-gray-500 uppercase tracking-wider text-center w-24">Qty</th>
                    <th class="py-3 px-5 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Harga Satuan</th>
                    <th class="py-3 px-5 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaksi->items as $item)
                    <tr class="border-b border-gray-100 last:border-0">
                        <td class="py-4 px-5 text-sm font-semibold text-gray-800">{{ $item->nama_produk }}</td>
                        <td class="py-4 px-5 text-sm text-gray-600 text-center">{{ $item->jumlah }}</td>
                        <td class="py-4 px-5 text-sm text-gray-600 text-right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td class="py-4 px-5 text-sm font-bold text-gray-800 text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Summary --}}
    <div class="flex justify-end mb-8">
        <div class="w-1/2">
            <div class="flex justify-between py-2 border-b border-gray-100">
                <span class="text-sm font-medium text-gray-500">Total Item</span>
                <span class="text-sm font-bold text-gray-800">{{ $transaksi->total_item }}</span>
            </div>
            <div class="flex justify-between py-4">
                <span class="text-base font-bold text-gray-800">Grand Total</span>
                <span class="text-2xl font-black text-blue-600">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="text-center pt-8 border-t border-gray-200">
        <p class="text-sm font-medium text-gray-500 mb-1">Terima kasih atas bisnis Anda!</p>
        <p class="text-xs text-gray-400">Invoice ini sah dan diterbitkan oleh sistem secara otomatis.</p>
    </div>

</div>

</body>
</html>
