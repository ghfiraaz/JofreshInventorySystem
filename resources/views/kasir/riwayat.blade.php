@extends('layouts.kasir')
@section('title', 'Riwayat Transaksi')
@section('content')

{{-- ===== SUMMARY CARDS ===== --}}
<div class="grid grid-cols-3 gap-5 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="text-xs text-gray-400 font-medium mb-1">Total Transaksi</div>
        <div class="text-3xl font-extrabold text-gray-800">{{ $totalTransaksi }}</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="text-xs text-gray-400 font-medium mb-1">Total Pendapatan</div>
        <div class="text-3xl font-extrabold text-gray-800">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="text-xs text-gray-400 font-medium mb-1">Item Terjual</div>
        <div class="text-3xl font-extrabold text-gray-800">{{ $totalItemSold }} kg</div>
    </div>
</div>

{{-- ===== SEARCH BAR ===== --}}
<div class="mb-5">
    <div class="relative w-full max-w-md">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
        </svg>
        <input type="text" id="search-riwayat" placeholder="Cari transaksi..." class="w-full pl-11 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all bg-white">
    </div>
</div>

{{-- ===== DATE FILTER ===== --}}
<div class="mb-5 flex items-center gap-3">
    <form method="GET" action="{{ url('/kasir/riwayat') }}" class="flex items-center gap-3">
        <input type="date" name="date" value="{{ $filterDate }}" class="px-4 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:border-blue-500 transition-all bg-white cursor-pointer">
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg border-none cursor-pointer hover:bg-blue-700 transition-all">Filter</button>
        @if($filterDate)
            <a href="{{ url('/kasir/riwayat') }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-all no-underline">Reset</a>
        @endif
    </form>
</div>

{{-- ===== TRANSACTIONS TABLE ===== --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b border-gray-200">
                <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide w-8"></th>
                <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">Tanggal</th>
                <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">Referensi</th>
                <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">Mitra</th>
                <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">Item</th>
                <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">Status & Metode</th>
                <th class="text-right px-6 py-3.5 text-xs font-semibold text-gray-400 uppercase tracking-wide">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $t)
                {{-- Main Row --}}
                <tr class="riwayat-row border-b border-gray-100 cursor-pointer hover:bg-gray-50 transition-colors" data-id="{{ $t->id }}">
                    <td class="px-6 py-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-400 expand-icon transition-transform duration-200">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $t->created_at->format('d/n/Y') }}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ $t->no_transaksi }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $t->mitra->nama ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $t->total_item }} item</td>
                    <td class="px-6 py-4">
                        @if($t->status_pembayaran === 'Sudah Dibayar')
                            <span class="inline-block px-3 py-1 rounded-md text-xs font-semibold bg-green-100 text-green-700 mb-1 block w-max">Lunas</span>
                        @else
                            <span class="inline-block px-3 py-1 rounded-md text-xs font-semibold bg-amber-100 text-amber-700 mb-1 block w-max">Belum Dibayar</span>
                        @endif
                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-600 border border-gray-200">{{ $t->metode_pembayaran }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-gray-800 text-right">Rp {{ number_format($t->total_harga, 0, ',', '.') }}</td>
                </tr>

                {{-- Detail Row (hidden) --}}
                <tr class="detail-row hidden" data-parent="{{ $t->id }}">
                    <td colspan="7" class="px-6 py-0">
                        <div class="py-4 px-4 bg-gray-50 rounded-lg mb-3">
                            <div class="flex items-center justify-between mb-3">
                                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Detail Item</div>
                                <a href="{{ url('/kasir/transaksi/'.$t->id.'/invoice') }}" target="_blank" class="text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5 no-underline">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                                    Invoice
                                </a>
                            </div>
                            <table class="w-full">
                                <thead>
                                    <tr>
                                        <th class="text-left pb-2 text-xs text-gray-400 font-medium">Produk</th>
                                        <th class="text-center pb-2 text-xs text-gray-400 font-medium">Qty</th>
                                        <th class="text-right pb-2 text-xs text-gray-400 font-medium">Harga</th>
                                        <th class="text-right pb-2 text-xs text-gray-400 font-medium">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($t->items as $item)
                                        <tr>
                                            <td class="py-1.5 text-sm text-gray-700">{{ $item->nama_produk }}</td>
                                            <td class="py-1.5 text-sm text-gray-600 text-center">{{ $item->jumlah }}</td>
                                            <td class="py-1.5 text-sm text-gray-600 text-right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                            <td class="py-1.5 text-sm font-semibold text-gray-800 text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center text-gray-400 text-sm">
                        Belum ada transaksi yang tercatat.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
