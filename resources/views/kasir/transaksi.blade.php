@extends('layouts.kasir')
@section('title', 'Transaksi Penjualan')
@section('content')

<div class="flex gap-6 items-start">
    {{-- ===== LEFT COLUMN (2/3) ===== --}}
    <div class="flex-1 flex flex-col gap-6 min-w-0">

        {{-- Tambah Item Card --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-base font-bold text-gray-800 mb-5">Tambah Item</h3>
            <div class="flex items-end gap-4">
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Produk</label>
                    <select id="kasir-produk" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 20 20%27%3E%3Cpath stroke=%27%236b7280%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%271.5%27 d=%27m6 8 4 4 4-4%27/%3E%3C/svg%3E'); background-position: right 12px center; background-repeat: no-repeat; background-size: 16px; padding-right: 36px;">
                        <option value="">Pilih produk</option>
                        @foreach($produk as $p)
                            <option value="{{ $p->id }}" data-nama="{{ $p->nama }}" data-harga="{{ $p->harga }}" data-stok="{{ $p->stok }}">
                                {{ $p->nama }} — Rp {{ number_format($p->harga, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-28">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Jumlah</label>
                    <input type="number" id="kasir-jumlah" value="1" min="1" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-center outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                </div>
                <button type="button" id="btn-tambah-keranjang" class="w-10 h-10 rounded-full bg-gray-800 text-white flex items-center justify-center hover:bg-gray-700 transition-all cursor-pointer border-none flex-shrink-0 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                </button>
            </div>
        </div>

        {{-- Keranjang Card --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 flex-1">
            <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                </svg>
                Keranjang (<span id="cart-count">0</span>)
            </h3>

            {{-- Empty State --}}
            <div id="cart-empty" class="flex flex-col items-center justify-center py-16 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-16 h-16 mb-3 text-gray-300">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                </svg>
                <span class="text-sm">Keranjang kosong</span>
            </div>

            {{-- Cart Items (hidden initially) --}}
            <div id="cart-items" class="hidden">
                <table class="w-full">
                    <tbody id="cart-tbody"></tbody>
                </table>
                <div class="mt-4 flex justify-end">
                    <button type="button" id="btn-clear-cart" class="text-xs text-red-500 hover:text-red-700 cursor-pointer bg-transparent border-none font-medium">Kosongkan Keranjang</button>
                </div>
            </div>
        </div>

    </div>

    {{-- ===== RIGHT COLUMN (checkout sidebar) ===== --}}
    <div class="w-[280px] flex-shrink-0">
        <div class="bg-white rounded-xl border border-gray-200 p-6 sticky top-8">
            <h3 class="text-base font-bold text-gray-800 mb-5">Checkout</h3>

            {{-- Mitra --}}
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Mitra <span class="text-red-500">*</span></label>
                <select id="kasir-mitra" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-white outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 20 20%27%3E%3Cpath stroke=%27%236b7280%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%271.5%27 d=%27m6 8 4 4 4-4%27/%3E%3C/svg%3E'); background-position: right 12px center; background-repeat: no-repeat; background-size: 16px; padding-right: 36px;">
                    <option value="">Pilih mitra</option>
                    @foreach($mitra as $m)
                        <option value="{{ $m->id }}">{{ $m->nama }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Metode Pembayaran --}}
            <div class="mb-5 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                <div class="text-sm font-semibold text-gray-800">Metode Pembayaran</div>
                <div class="text-xs text-gray-500 mt-0.5">Termin (Sesuai MoU - Tagihan Bulanan)</div>
            </div>

            {{-- Total --}}
            <div class="flex items-center justify-between mb-5">
                <span class="text-sm font-medium text-gray-500">Total</span>
                <span id="cart-total" class="text-2xl font-extrabold text-gray-800">Rp 0</span>
            </div>

            {{-- Checkout Button --}}
            <button type="button" id="btn-checkout" disabled class="w-full py-3 bg-blue-600 text-white rounded-xl text-sm font-semibold border-none cursor-pointer transition-all hover:bg-blue-700 disabled:opacity-40 disabled:cursor-not-allowed shadow-sm">
                Selesaikan Penjualan
            </button>

            {{-- Item count --}}
            <div class="mt-4 text-xs text-gray-400 text-center">
                Item dalam keranjang: <span id="cart-count-summary">0</span>
            </div>
        </div>
    </div>
</div>

@endsection
