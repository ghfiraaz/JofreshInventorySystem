@extends('layouts.kasir')
@section('title', 'Riwayat Transaksi')
@section('content')

{{-- Summary Cards --}}
<div class="grid grid-cols-3 gap-5 mb-6">
    <div class="rounded-xl p-5" style="background:linear-gradient(135deg,#eef2ff,#e0f2fe);border:1.5px solid #c7d2fe;">
        <div class="text-xs font-bold uppercase tracking-wider mb-1" style="color:#6366f1;">Total Transaksi</div>
        <div class="text-3xl font-extrabold" style="color:#3730a3;">{{ $totalTransaksi }}</div>
    </div>
    <div class="rounded-xl p-5" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1.5px solid #86efac;">
        <div class="text-xs font-bold uppercase tracking-wider mb-1" style="color:#16a34a;">Total Pendapatan</div>
        <div class="text-3xl font-extrabold" style="color:#15803d;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
    </div>
    <div class="rounded-xl p-5" style="background:linear-gradient(135deg,#fef9c3,#fef3c7);border:1.5px solid #fde68a;">
        <div class="text-xs font-bold uppercase tracking-wider mb-1" style="color:#d97706;">Item Terjual</div>
        <div class="text-3xl font-extrabold" style="color:#92400e;">{{ $totalItemSold }} ekor</div>
    </div>
</div>

{{-- Search & Filters --}}
<div class="flex flex-wrap items-center gap-3 mb-5">
    <div class="relative flex-1 min-w-[200px] max-w-sm">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
        <input type="text" id="search-riwayat" placeholder="Cari transaksi..." class="w-full pl-11 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all bg-white">
    </div>
    <form method="GET" action="{{ url('/kasir/riwayat') }}" class="flex items-center gap-2">
        <input type="date" name="date" value="{{ $filterDate }}" class="px-4 py-2 border border-indigo-200 rounded-lg text-sm outline-none focus:border-indigo-500 transition-all bg-white cursor-pointer">
        <select name="status" class="px-3 py-2 border border-indigo-200 rounded-lg text-sm outline-none focus:border-indigo-500 transition-all bg-white cursor-pointer">
            <option value="">Semua Status</option>
            <option value="Belum Dibayar" {{ $filterStatus == 'Belum Dibayar' ? 'selected' : '' }}>Belum Dibayar</option>
            <option value="Menunggu Validasi" {{ $filterStatus == 'Menunggu Validasi' ? 'selected' : '' }}>Menunggu Validasi</option>
            <option value="Sudah Dibayar" {{ $filterStatus == 'Sudah Dibayar' ? 'selected' : '' }}>Sudah Dibayar</option>
        </select>
        <button type="submit" class="px-4 py-2 text-white text-sm font-semibold rounded-lg border-none cursor-pointer transition-all" style="background:#4f46e5;" onmouseover="this.style.background='#4338ca'" onmouseout="this.style.background='#4f46e5'">Filter</button>
        @if($filterDate || $filterStatus)
            <a href="{{ url('/kasir/riwayat') }}" class="px-4 py-2 bg-rose-50 text-rose-500 text-sm font-semibold rounded-lg hover:bg-rose-100 transition-all no-underline border border-rose-100">× Reset</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="rounded-xl overflow-hidden border border-indigo-100 shadow-sm">
    <table class="w-full" id="riwayat-table">
        <thead>
            <tr style="background:linear-gradient(135deg,#eef2ff,#f0fdf4);">
                <th class="text-left px-5 py-3.5 text-xs font-bold text-indigo-500 uppercase tracking-wide w-8"></th>
                <th class="text-left px-5 py-3.5 text-xs font-bold text-indigo-500 uppercase tracking-wide">Tanggal</th>
                <th class="text-left px-5 py-3.5 text-xs font-bold text-indigo-500 uppercase tracking-wide">No. Transaksi</th>
                <th class="text-left px-5 py-3.5 text-xs font-bold text-indigo-500 uppercase tracking-wide">Mitra</th>
                <th class="text-left px-5 py-3.5 text-xs font-bold text-indigo-500 uppercase tracking-wide">Item</th>
                <th class="text-left px-5 py-3.5 text-xs font-bold text-indigo-500 uppercase tracking-wide">Status</th>
                <th class="text-right px-5 py-3.5 text-xs font-bold text-indigo-500 uppercase tracking-wide">Total</th>
                <th class="text-center px-5 py-3.5 text-xs font-bold text-indigo-500 uppercase tracking-wide">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $pastels = [
                    ['bg'=>'#ffffff','hover'=>'#f5f3ff'],
                    ['bg'=>'#f5f3ff','hover'=>'#ede9fe'],
                    ['bg'=>'#f0fdf4','hover'=>'#dcfce7'],
                    ['bg'=>'#fef9c3','hover'=>'#fef08a'],
                    ['bg'=>'#f0f9ff','hover'=>'#e0f2fe'],
                ];
                $ri = 0;
            @endphp
            @forelse($transaksi as $t)
                @php $pc = $pastels[$ri % count($pastels)]; $ri++; @endphp
                <tr class="riwayat-row border-b border-indigo-50 cursor-pointer transition-colors"
                    style="background:{{ $pc['bg'] }};"
                    onmouseover="this.style.background='{{ $pc['hover'] }}'"
                    onmouseout="this.style.background='{{ $pc['bg'] }}'"
                    data-id="{{ $t->id }}"
                    data-search="{{ strtolower($t->no_transaksi . ' ' . ($t->mitra->nama ?? '')) }}">
                    <td class="px-5 py-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-indigo-300 expand-icon transition-transform duration-200"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                    </td>
                    <td class="px-5 py-4 text-sm text-slate-600">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-5 py-4 text-sm font-bold text-indigo-700">{{ $t->no_transaksi }}</td>
                    <td class="px-5 py-4 text-sm text-slate-600">{{ $t->mitra->nama ?? '-' }}</td>
                    <td class="px-5 py-4 text-sm text-slate-600">{{ $t->total_item }} item</td>
                    <td class="px-5 py-4">
                        @if($t->status_pembayaran === 'Sudah Dibayar')
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold" style="background:#dcfce7;color:#15803d;">Lunas</span>
                        @elseif($t->status_pembayaran === 'Menunggu Validasi')
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold" style="background:#e0e7ff;color:#4338ca;">Menunggu Validasi</span>
                        @else
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold" style="background:#fef9c3;color:#b45309;">Belum Dibayar</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-sm font-bold text-right" style="color:#15803d;">Rp {{ number_format($t->total_harga, 0, ',', '.') }}</td>
                    <td class="px-5 py-4 text-center" onclick="event.stopPropagation();">
                        <div class="flex items-center justify-center gap-1.5">
                            {{-- Validasi button --}}
                            @if($t->status_pembayaran === 'Menunggu Validasi')
                                <button type="button" class="btn-validasi inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-white no-underline transition-all border-none cursor-pointer" style="background:#4f46e5;" onmouseover="this.style.background='#4338ca'" onmouseout="this.style.background='#4f46e5'" data-id="{{ $t->id }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                    Validasi
                                </button>
                            @endif

                            {{-- Bukti bayar link --}}
                            @if($t->bukti_pembayaran)
                                <a href="{{ asset('storage/' . $t->bukti_pembayaran) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 no-underline transition-all border border-indigo-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                    Bukti
                                </a>
                            @endif

                            {{-- Invoice link --}}
                            <a href="{{ url('/kasir/transaksi/'.$t->id.'/invoice') }}" target="_blank"
                               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-white no-underline transition-all"
                               style="background:#dc2626;" onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                Cetak
                            </a>
                        </div>
                    </td>
                </tr>

                {{-- Detail Row --}}
                <tr class="detail-row hidden" data-parent="{{ $t->id }}">
                    <td colspan="8" class="px-5 py-0">
                        <div class="py-4 px-4 mb-3 rounded-xl border border-indigo-100" style="background:#f5f3ff;">
                            <div class="text-xs font-bold text-indigo-500 uppercase tracking-wide mb-3">Detail Item</div>
                            <table class="w-full">
                                <thead>
                                    <tr>
                                        <th class="text-left pb-2 text-xs text-indigo-400 font-semibold">Produk</th>
                                        <th class="text-center pb-2 text-xs text-indigo-400 font-semibold">Qty</th>
                                        <th class="text-right pb-2 text-xs text-indigo-400 font-semibold">Harga</th>
                                        <th class="text-right pb-2 text-xs text-indigo-400 font-semibold">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($t->items as $item)
                                        <tr>
                                            <td class="py-1.5 text-sm text-slate-700">{{ $item->nama_produk }}</td>
                                            <td class="py-1.5 text-sm text-slate-600 text-center">{{ $item->jumlah }}</td>
                                            <td class="py-1.5 text-sm text-slate-600 text-right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                            <td class="py-1.5 text-sm font-semibold text-right" style="color:#15803d;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if($t->jatuh_tempo)
                                <div class="mt-3 pt-3 border-t border-indigo-100 text-xs text-gray-500">
                                    Jatuh Tempo: <span class="font-bold text-gray-700">{{ $t->jatuh_tempo->format('d/m/Y') }}</span>
                                </div>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-16 text-center text-gray-400 text-sm">
                        Belum ada transaksi yang tercatat.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

// Search
document.getElementById('search-riwayat').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.riwayat-row').forEach(row => {
        const show = row.dataset.search.includes(q);
        row.style.display = show ? '' : 'none';
        const detail = document.querySelector('.detail-row[data-parent="'+row.dataset.id+'"]');
        if (detail) detail.style.display = show ? '' : 'none';
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

// Validasi individual
document.querySelectorAll('.btn-validasi').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        if (!confirm('Validasi pembayaran transaksi ini?')) return;
        
        this.disabled = true;
        this.textContent = '...';
        
        fetch(`/kasir/transaksi/${id}/validasi`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        })
        .then(r => r.ok ? r.json() : r.json().then(e => { throw e; }))
        .then(() => window.location.reload())
        .catch(err => {
            alert(err.message || 'Gagal memvalidasi');
            this.disabled = false;
            this.textContent = 'Validasi';
        });
    });
});
</script>

@endsection
