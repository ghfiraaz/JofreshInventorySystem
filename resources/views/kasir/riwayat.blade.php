@extends('layouts.kasir')
@section('title', 'Riwayat Transaksi')
@section('content')

{{-- ===== HEADING ===== --}}
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-800 mb-1">Riwayat Transaksi</h2>
    <p class="text-sm text-gray-400">Semua transaksi yang tercatat di sistem</p>
</div>

{{-- ===== SUMMARY CARDS ===== --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center justify-between">
        <div>
            <div class="text-xs text-gray-400 font-medium mb-1">Total Transaksi</div>
            <div class="text-2xl font-extrabold text-gray-800">{{ $totalTransaksi }}</div>
        </div>
        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-blue-600"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center justify-between">
        <div>
            <div class="text-xs text-gray-400 font-medium mb-1">Total Pendapatan</div>
            <div class="text-2xl font-extrabold text-gray-800">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
        </div>
        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-emerald-600"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center justify-between">
        <div>
            <div class="text-xs text-gray-400 font-medium mb-1">Item Terjual</div>
            <div class="text-2xl font-extrabold text-gray-800">{{ $totalItemSold }} <span class="text-sm font-medium text-gray-400">ekor</span></div>
        </div>
        <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-amber-600"><path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" /></svg>
        </div>
    </div>
</div>

{{-- ===== FILTERS ===== --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
    <div class="flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-[200px] max-w-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
            <input type="text" id="search-riwayat" placeholder="Cari no. transaksi atau mitra..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-50 transition-all bg-white">
        </div>
        <form method="GET" action="{{ url('/kasir/riwayat') }}" class="flex items-center gap-2">
            <input type="date" name="date" value="{{ $filterDate }}" class="px-3 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:border-blue-400 transition-all bg-white cursor-pointer">
            <select name="status" class="px-3 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:border-blue-400 transition-all bg-white cursor-pointer">
                <option value="">Semua Status</option>
                <option value="Belum Dibayar" {{ $filterStatus == 'Belum Dibayar' ? 'selected' : '' }}>Belum Dibayar</option>
                <option value="Menunggu Validasi" {{ $filterStatus == 'Menunggu Validasi' ? 'selected' : '' }}>Menunggu Validasi</option>
                <option value="Sudah Dibayar" {{ $filterStatus == 'Sudah Dibayar' ? 'selected' : '' }}>Sudah Dibayar</option>
                <option value="Ditolak" {{ $filterStatus == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all hover:bg-slate-700">Filter</button>
            @if($filterDate || $filterStatus)
                <a href="{{ url('/kasir/riwayat') }}" class="px-4 py-2 bg-gray-100 text-gray-500 text-sm font-semibold rounded-lg hover:bg-gray-200 transition-all no-underline border border-gray-200">Reset</a>
            @endif
        </form>
    </div>
</div>

{{-- ===== TABLE ===== --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full" id="riwayat-table">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide w-8"></th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">No. Transaksi</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Mitra</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Item</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Total</th>
                <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $t)
                <tr class="riwayat-row border-b border-gray-100 cursor-pointer transition-colors hover:bg-slate-50"
                    data-id="{{ $t->id }}"
                    data-search="{{ strtolower($t->no_transaksi . ' ' . ($t->mitra->nama ?? '')) }}">
                    <td class="px-5 py-3.5">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-300 expand-icon transition-transform duration-200"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                    </td>
                    <td class="px-5 py-3.5 text-sm text-gray-600">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-5 py-3.5 text-sm font-semibold text-gray-800">{{ $t->no_transaksi }}</td>
                    <td class="px-5 py-3.5 text-sm text-gray-600">{{ $t->mitra->nama ?? '-' }}</td>
                    <td class="px-5 py-3.5 text-sm text-gray-500">{{ $t->total_item }} item</td>
                    <td class="px-5 py-3.5">
                        @if($t->status_pembayaran === 'Sudah Dibayar')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Lunas
                            </span>
                        @elseif($t->status_pembayaran === 'Menunggu Validasi')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Menunggu
                            </span>
                        @elseif($t->status_pembayaran === 'Ditolak')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Ditolak
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Belum Bayar
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-sm font-bold text-right text-gray-800">Rp {{ number_format($t->total_harga, 0, ',', '.') }}</td>
                    <td class="px-5 py-3.5 text-center" onclick="event.stopPropagation();">
                        <div class="flex items-center justify-center gap-1.5">
                            {{-- Terima/Tolak buttons --}}
                            @if($t->status_pembayaran === 'Menunggu Validasi')
                                <button type="button" class="btn-validasi inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white transition-all border border-emerald-200 hover:border-emerald-600 cursor-pointer"
                                    data-id="{{ $t->id }}" data-action="terima">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                    Terima
                                </button>
                                <button type="button" class="btn-validasi inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-red-50 text-red-700 hover:bg-red-600 hover:text-white transition-all border border-red-200 hover:border-red-600 cursor-pointer"
                                    data-id="{{ $t->id }}" data-action="tolak">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                    Tolak
                                </button>
                            @endif

                            {{-- Bukti bayar link --}}
                            @if($t->bukti_pembayaran)
                                <a href="{{ url('/kasir/bukti-pembayaran/' . basename($t->bukti_pembayaran)) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-gray-600 bg-gray-50 hover:bg-gray-100 no-underline transition-all border border-gray-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                    Bukti
                                </a>
                            @endif

                            {{-- Invoice link --}}
                            <a href="{{ url('/kasir/transaksi/'.$t->id.'/invoice') }}" target="_blank"
                               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-gray-600 bg-gray-50 hover:bg-gray-100 no-underline transition-all border border-gray-200">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                Cetak
                            </a>

                            {{-- Download PDF (with LUNAS watermark if paid) --}}
                            <a href="{{ url('/kasir/transaksi/'.$t->id.'/invoice-pdf') }}" target="_blank"
                               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold no-underline transition-all border
                               {{ $t->status_pembayaran === 'Sudah Dibayar' ? 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border-emerald-200' : 'text-gray-600 bg-gray-50 hover:bg-gray-100 border-gray-200' }}"
                               title="{{ $t->status_pembayaran === 'Sudah Dibayar' ? 'Download Invoice LUNAS' : 'Download Invoice PDF' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                                {{ $t->status_pembayaran === 'Sudah Dibayar' ? 'PDF LUNAS' : 'PDF' }}
                            </a>
                        </div>
                    </td>
                </tr>

                {{-- Detail Row --}}
                <tr class="detail-row hidden" data-parent="{{ $t->id }}">
                    <td colspan="8" class="px-5 py-0">
                        <div class="py-4 px-4 mb-3 rounded-xl bg-slate-50 border border-gray-200">
                            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Detail Item</div>
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
                                            <td class="py-1.5 text-sm font-semibold text-right text-gray-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if($t->jatuh_tempo)
                                <div class="mt-3 pt-3 border-t border-gray-200 text-xs text-gray-500">
                                    Jatuh Tempo: <span class="font-semibold text-gray-700">{{ $t->jatuh_tempo->format('d/m/Y') }}</span>
                                </div>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-16 text-center text-gray-400 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-12 h-12 mx-auto mb-3 text-gray-300"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                        Belum ada transaksi yang tercatat.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ===== TOAST ===== --}}
<div id="toast-riwayat" class="fixed top-6 right-6 z-50 hidden">
    <div class="flex items-center gap-3 px-5 py-3.5 rounded-xl shadow-lg border" id="toast-riwayat-inner">
        <div id="toast-riwayat-icon"></div>
        <div>
            <div class="text-sm font-bold" id="toast-riwayat-title"></div>
            <div class="text-xs text-gray-500" id="toast-riwayat-msg"></div>
        </div>
    </div>
</div>

{{-- ===== CONFIRMATION MODAL (CENTERED) ===== --}}
<div id="modal-confirm-riwayat" class="fixed inset-0 bg-slate-900/50 z-50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 [&.active]:opacity-100 [&.active]:pointer-events-auto">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl relative overflow-hidden transform scale-95 transition-transform duration-300 [.active_&]:scale-100">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-900 to-indigo-600"></div>
        <div class="px-8 pt-8 pb-2 flex justify-between items-start">
            <div>
                <h3 id="confirm-riwayat-title" class="text-lg font-bold text-slate-800"></h3>
            </div>
            <button type="button" onclick="closeConfirmRiwayatModal()" class="text-slate-400 hover:text-slate-700 text-2xl font-bold cursor-pointer bg-transparent border-none leading-none mt-1">&times;</button>
        </div>
        <div class="px-8 pb-3">
            <p id="confirm-riwayat-message" class="text-sm text-slate-600 leading-relaxed"></p>
        </div>
        <div class="px-8 pb-8 pt-4 flex justify-end gap-3">
            <button type="button" id="confirm-riwayat-no" onclick="closeConfirmRiwayatModal()" class="px-6 py-2.5 rounded-xl font-semibold text-sm text-slate-600 bg-slate-100 hover:bg-slate-200 transition-all cursor-pointer border-none">Tidak</button>
            <button type="button" id="confirm-riwayat-yes" class="px-6 py-2.5 rounded-xl font-semibold text-sm text-white border-none cursor-pointer transition-all" style="background:#1e3a5f;" onmouseover="this.style.background='#162d4a'" onmouseout="this.style.background='#1e3a5f'">Ya</button>
        </div>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

// Toast function
function showRiwayatToast(type, title, msg) {
    const toast = document.getElementById('toast-riwayat');
    const inner = document.getElementById('toast-riwayat-inner');
    const iconEl = document.getElementById('toast-riwayat-icon');
    const titleEl = document.getElementById('toast-riwayat-title');
    const msgEl = document.getElementById('toast-riwayat-msg');

    titleEl.textContent = title;
    msgEl.textContent = msg;

    if (type === 'success') {
        inner.className = 'flex items-center gap-3 px-5 py-3.5 rounded-xl shadow-lg border bg-emerald-50 border-emerald-200';
        titleEl.className = 'text-sm font-bold text-emerald-800';
        iconEl.innerHTML = '<div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg></div>';
    } else {
        inner.className = 'flex items-center gap-3 px-5 py-3.5 rounded-xl shadow-lg border bg-red-50 border-red-200';
        titleEl.className = 'text-sm font-bold text-red-800';
        iconEl.innerHTML = '<div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="white" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg></div>';
    }

    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 4000);
}

// Search
document.getElementById('search-riwayat').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.riwayat-row').forEach(row => {
        const show = row.dataset.search.includes(q);
        row.style.display = show ? '' : 'none';
        const detail = document.querySelector('.detail-row[data-parent="'+row.dataset.id+'"]');
        if (detail && !detail.classList.contains('hidden')) detail.style.display = show ? '' : 'none';
    });
});

// Row expand
document.querySelectorAll('.riwayat-row').forEach(row => {
    row.addEventListener('click', function() {
        const id = this.dataset.id;
        const detail = document.querySelector('.detail-row[data-parent="'+id+'"]');
        const icon = this.querySelector('.expand-icon');
        if (detail) {
            detail.classList.toggle('hidden');
            if (icon) icon.classList.toggle('rotate-180');
        }
    });
});

// ========== Confirm Modal Riwayat ==========
let _confirmRiwayatCallback = null;

function showConfirmRiwayat(title, message, yesLabel, onYes, noLabel = 'Tidak') {
    const modal = document.getElementById('modal-confirm-riwayat');
    document.getElementById('confirm-riwayat-title').textContent = title;
    document.getElementById('confirm-riwayat-message').textContent = message;
    const yesBtn = document.getElementById('confirm-riwayat-yes');
    const noBtn = document.getElementById('confirm-riwayat-no');
    
    yesBtn.textContent = yesLabel || 'Ya';
    if (noBtn) {
        noBtn.textContent = noLabel;
    }
    _confirmRiwayatCallback = onYes;
    modal.classList.add('active');
}

function closeConfirmRiwayatModal() {
    document.getElementById('modal-confirm-riwayat').classList.remove('active');
    _confirmRiwayatCallback = null;
}

document.getElementById('confirm-riwayat-yes')?.addEventListener('click', () => {
    if (_confirmRiwayatCallback) _confirmRiwayatCallback();
    closeConfirmRiwayatModal();
});

document.getElementById('modal-confirm-riwayat')?.addEventListener('click', (e) => {
    if (e.target === e.currentTarget) closeConfirmRiwayatModal();
});

// Validasi individual (Terima/Tolak)
document.querySelectorAll('.btn-validasi').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const action = this.dataset.action;
        const isTerima = action === 'terima';
        const title = isTerima ? 'Terima Pembayaran' : 'Tolak Pembayaran';
        const confirmMsg = isTerima
            ? 'Apakah anda yakin ingin validasi bukti pembayaran ini?'
            : 'Apakah anda yakin ingin menolak bukti pembayaran ini?';

        showConfirmRiwayat(title, confirmMsg, 'Ya', () => {
            this.disabled = true;
            const originalText = this.textContent;
            this.textContent = '...';

            fetch(`/kasir/transaksi/${id}/validasi`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ action: action }),
            })
            .then(r => r.ok ? r.json() : r.json().then(e => { throw e; }))
            .then(data => {
                showRiwayatToast('success', isTerima ? 'Pembayaran Diterima' : 'Pembayaran Ditolak', data.message);
                setTimeout(() => window.location.reload(), 1500);
            })
            .catch(err => {
                showRiwayatToast('error', 'Gagal', err.message || 'Terjadi kesalahan');
                this.disabled = false;
                this.textContent = originalText;
            });
        }, 'Tidak');
    });
});
</script>

@endsection
