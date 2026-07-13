@extends('layouts.kasir')
@section('title', 'Transaksi Penjualan')
@section('content')

<div class="flex flex-col lg:flex-row gap-4 sm:gap-6 items-start pb-20 lg:pb-0">
    {{-- ===== LEFT COLUMN (2/3) ===== --}}
    <div class="flex-1 flex flex-col gap-4 sm:gap-6 min-w-0 w-full">

        {{-- Tambah Item Card --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-6 shadow-sm">
            <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 sm:mb-5">Tambah Item</h3>
            <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                <div class="flex-1 w-full">
                    <label class="block text-xs sm:text-sm font-semibold text-gray-500 mb-1.5">Produk <span class="text-red-500">*</span></label>
                    <select id="kasir-produk" required class="w-full h-11 px-4 border border-gray-300 rounded-xl text-sm bg-white outline-none focus:border-[#7B3911] focus:ring-2 focus:ring-[#7B3911]/20 transition-all appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 20 20%27%3E%3Cpath stroke=%27%236b7280%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%271.5%27 d=%27m6 8 4 4 4-4%27/%3E%3C/svg%3E'); background-position: right 12px center; background-repeat: no-repeat; background-size: 16px; padding-right: 36px;">
                        <option value="">Pilih produk</option>
                        @foreach($produk as $p)
                            <option value="{{ $p->id }}" data-nama="{{ $p->nama }}" data-harga="{{ intval($p->harga) }}" data-stok="{{ intval($p->stok) }}">
                                {{ $p->nama }} — Rp {{ number_format($p->harga, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    <div id="kasir-stok-info" class="mt-2.5 p-3 bg-[#FAF5EF] border border-[#E0D5CA] rounded-xl text-xs sm:text-sm text-[#7B3911] font-bold hidden flex items-center gap-2 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4.5 h-4.5 text-[#7B3911] flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" /></svg>
                        Sisa Stok Tersedia: <span class="text-sm sm:text-base text-[#3D1F0A] mx-0.5"></span> ekor
                    </div>
                </div>
                <div class="flex items-end gap-3 w-full sm:w-auto flex-shrink-0">
                    <div class="w-28 flex-1 sm:flex-initial">
                        <label class="block text-xs sm:text-sm font-semibold text-gray-500 mb-1.5">Jumlah <span class="text-red-500">*</span></label>
                        <input type="number" id="kasir-jumlah" required value="1" min="1" class="w-full h-11 px-4 border border-gray-300 rounded-xl text-sm text-center outline-none focus:border-[#7B3911] focus:ring-2 focus:ring-[#7B3911]/20 transition-all">
                    </div>
                    <button type="button" id="btn-tambah-keranjang" class="w-11 h-11 rounded-full bg-[#0F172A] hover:bg-[#1E293B] text-white flex items-center justify-center transition-all cursor-pointer border-none flex-shrink-0 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Keranjang Card --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-6 shadow-sm flex flex-col min-h-0">
            <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                </svg>
                Keranjang (<span id="cart-count">0</span>)
            </h3>

            {{-- Empty State --}}
            <div id="cart-empty" class="flex flex-col items-center justify-center py-10 sm:py-16 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-12 h-12 sm:w-16 sm:h-16 mb-2.5 text-gray-300">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                </svg>
                <span class="text-xs sm:text-sm font-medium">Keranjang kosong</span>
            </div>

            {{-- Cart Items (hidden initially) --}}
            <div id="cart-items" class="hidden">
                <div class="max-h-[300px] overflow-y-auto">
                    <table class="w-full">
                        <tbody id="cart-tbody"></tbody>
                    </table>
                </div>
                <div class="mt-4 flex justify-end">
                    <button type="button" id="btn-clear-cart" class="text-xs text-red-500 hover:text-red-700 cursor-pointer bg-transparent border-none font-semibold">Kosongkan Keranjang</button>
                </div>
            </div>
        </div>

    </div>

    {{-- ===== RIGHT COLUMN (checkout sidebar) ===== --}}
    <div class="w-full lg:w-[280px] flex-shrink-0">
        <div id="checkout-card" class="bg-white rounded-2xl border border-gray-200 p-4 sm:p-6 sticky top-8 shadow-sm">
            <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 sm:mb-5">Checkout</h3>

            {{-- Mitra --}}
            <div class="mb-4">
                <label class="block text-xs sm:text-sm font-semibold text-gray-500 mb-1.5">Mitra <span class="text-red-500">*</span></label>
                <select id="kasir-mitra" class="w-full h-11 px-4 border border-gray-300 rounded-xl text-sm bg-white outline-none focus:border-[#7B3911] focus:ring-2 focus:ring-[#7B3911]/20 transition-all appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 20 20%27%3E%3Cpath stroke=%27%236b7280%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%271.5%27 d=%27m6 8 4 4 4-4%27/%3E%3C/svg%3E'); background-position: right 12px center; background-repeat: no-repeat; background-size: 16px; padding-right: 36px;">
                    <option value="">Pilih mitra</option>
                    @foreach($mitra as $m)
                        <option value="{{ $m->id }}">{{ $m->nama }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Metode Pembayaran --}}
            <div class="mb-4 sm:mb-5 p-3 sm:p-4 bg-amber-50 border border-amber-200 rounded-xl">
                <div class="text-xs sm:text-sm font-bold text-gray-800">Metode Pembayaran</div>
                <div class="text-[10px] sm:text-xs text-gray-500 mt-0.5 font-medium">Termin (Sesuai MoU - Tagihan Bulanan)</div>
            </div>

            {{-- Total --}}
            <div class="flex items-center justify-between mb-4 sm:mb-5">
                <span class="text-xs sm:text-sm font-semibold text-gray-500">Total</span>
                <span id="cart-total" class="text-xl sm:text-2xl font-extrabold text-gray-800">Rp 0</span>
            </div>

            {{-- Checkout Button --}}
            <button type="button" id="btn-checkout" disabled class="w-full h-11 bg-[#7B3911] hover:bg-[#5A270B] text-white rounded-xl text-sm font-bold border-none cursor-pointer transition-all disabled:opacity-40 disabled:cursor-not-allowed shadow-sm">
                Selesaikan Penjualan
            </button>

            {{-- Item count --}}
            <div class="mt-4 text-xs text-gray-400 text-center font-medium">
                Item dalam keranjang: <span id="cart-count-summary">0</span>
            </div>
        </div>
    </div>
</div>

{{-- ===== STICKY BOTTOM CHECKOUT BAR (Mobile Only) ===== --}}
<div id="sticky-checkout-bar" class="fixed bottom-0 left-0 right-0 bg-[#FAF6F0] border-t border-[#E0D5CA] px-4 py-3 flex items-center justify-between z-40 lg:hidden shadow-[0_-4px_12px_rgba(123,57,17,0.06)] transition-all duration-300 translate-y-full">
    <div class="flex flex-col">
        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Total</span>
        <span id="sticky-total" class="text-lg font-extrabold text-gray-800">Rp 0</span>
    </div>
    <button type="button" id="btn-sticky-checkout" class="px-5 h-10 bg-[#7B3911] hover:bg-[#5A270B] text-white rounded-xl text-xs font-bold border-none cursor-pointer shadow-sm transition-all">
        Checkout
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btnStickyCheckout = document.getElementById('btn-sticky-checkout');
    if (btnStickyCheckout) {
        btnStickyCheckout.addEventListener('click', () => {
            const checkoutCard = document.getElementById('checkout-card');
            if (checkoutCard) {
                checkoutCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                // Focus the Mitra dropdown
                const selectMitra = document.getElementById('kasir-mitra');
                if (selectMitra) {
                    setTimeout(() => {
                        selectMitra.focus();
                        selectMitra.classList.add('ring-2', 'ring-[#7B3911]/30');
                        setTimeout(() => {
                            selectMitra.classList.remove('ring-2', 'ring-[#7B3911]/30');
                        }, 1000);
                    }, 500);
                }
            }
        });
    }
});
</script>

@endsection
