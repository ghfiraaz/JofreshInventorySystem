@extends(Auth::user()->role === 'Admin' ? 'layouts.admin' : 'layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-[0_2px_10px_rgba(0,0,0,0.02)] flex items-center justify-between group hover:border-slate-300 transition-colors">
        <div>
            <span class="text-slate-500 text-[0.85rem] font-medium tracking-wide">{{ $hasFilter ? 'Penjualan Periode' : 'Penjualan Hari Ini' }}</span>
            <div class="flex items-center gap-2 mt-1">
                <h3 class="font-bold text-2xl text-slate-800">Rp {{ number_format($penjualanHariIni, 0, ',', '.') }}</h3>
            </div>
        </div>
        <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-emerald-50 text-emerald-600 group-hover:scale-110 transition-transform duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-[0_2px_10px_rgba(0,0,0,0.02)] flex items-center justify-between group hover:border-slate-300 transition-colors">
        <div>
            <span class="text-slate-500 text-[0.85rem] font-medium tracking-wide">Total Transaksi</span>
            <h3 class="font-bold text-2xl mt-1 text-slate-800">{{ $totalTransaksi }}</h3>
        </div>
        <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-[#FAF5EF] text-[#7B3911] group-hover:scale-110 transition-transform duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
            </svg>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-[0_2px_10px_rgba(0,0,0,0.02)] flex items-center justify-between group hover:border-slate-300 transition-colors">
        <div>
            <span class="text-slate-500 text-[0.85rem] font-medium tracking-wide">Total Mitra</span>
            <h3 class="font-bold text-2xl mt-1 text-slate-800">{{ $totalMitra }}</h3>
        </div>
        <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-[#FFF8F0] text-[#D2691E] group-hover:scale-110 transition-transform duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
            </svg>
        </div>
    </div>
    <div class="bg-white rounded-2xl p-6 border {{ $isStokRendah ? 'border-red-200 bg-red-50/30' : 'border-slate-200' }} shadow-[0_2px_10px_rgba(0,0,0,0.02)] flex items-center justify-between group hover:border-slate-300 transition-colors relative overflow-hidden">
        @if($isStokRendah)
            <div class="absolute top-0 right-0 w-2 h-full bg-red-500 animate-pulse"></div>
        @endif
        <div>
            <span class="{{ $isStokRendah ? 'text-red-500' : 'text-slate-500' }} text-[0.85rem] font-medium tracking-wide">Total Stok</span>
            <div class="flex items-baseline gap-1 mt-1">
                <h3 class="font-bold text-2xl {{ $isStokRendah ? 'text-red-700' : 'text-slate-800' }}">{{ number_format(intval($totalStok), 0, ',', '.') }}</h3>
                <span class="text-slate-400 text-xs font-semibold uppercase tracking-wider ml-1">Ekor</span>
            </div>
            @if($isStokRendah)
                <div class="flex items-center gap-1 mt-1.5 text-red-600">
                    <span class="text-[0.7rem] font-bold uppercase tracking-wider bg-red-100 px-2 py-0.5 rounded text-red-700">Peringatan: Stok Tipis ({{ $stokRendahCount }})</span>
                </div>
            @endif
        </div>
        <div class="w-12 h-12 rounded-xl flex items-center justify-center {{ $isStokRendah ? 'bg-red-100 text-red-600' : 'bg-slate-100 text-slate-600' }} group-hover:scale-110 transition-transform duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
            </svg>
        </div>
    </div>
</div>

{{-- ===== PERIOD PICKER STYLES ===== --}}
<style>
.period-wrap { position:relative; display:inline-block; }
.period-trigger {
    display:flex; align-items:center; gap:8px;
    padding:8px 16px; border-radius:12px; cursor:pointer;
    background:linear-gradient(135deg,#FAF5EF,#FFF8F0);
    border:1.5px solid #E0C4A8; color:#7B3911;
    font-weight:600; font-size:0.9rem; transition:all 0.18s;
    user-select:none; box-shadow:0 2px 8px rgba(123,57,17,.08);
}
.period-trigger:hover { border-color:#C8702A; background:linear-gradient(135deg,#F0E0D0,#FFF8F0); }
.period-popup {
    position:absolute; top:calc(100% + 8px); right:0; z-index:200;
    background:#fff; border-radius:18px; box-shadow:0 12px 40px rgba(107,52,16,.16);
    border:1.5px solid #E0D5CA; width:360px; overflow:hidden;
    animation: periodSlide 0.18s ease;
}
@keyframes periodSlide { from{opacity:0;transform:translateY(-8px);}to{opacity:1;transform:translateY(0);} }
.period-tabs { display:flex; border-bottom:1px solid #E0D5CA; }
.period-tab {
    flex:1; padding:12px; text-align:center; font-size:0.82rem; font-weight:700;
    text-transform:uppercase; letter-spacing:0.05em; cursor:pointer;
    color:#94a3b8; background:transparent; border:none; transition:all 0.15s;
}
.period-tab:hover { color:#C8702A; background:#FAF5EF; }
.period-tab.active { color:#7B3911; border-bottom:2.5px solid #7B3911; background:#FAF5EF; }
.period-section { padding:16px; }
.period-select {
    width:100%; padding:10px 14px; border-radius:10px; border:1.5px solid #e2e8f0;
    font-size:0.88rem; font-weight:500; color:#334155; background:#fff;
    cursor:pointer; transition:border-color 0.15s; appearance:auto;
}
.period-select:focus { outline:none; border-color:#7B3911; box-shadow:0 0 0 3px rgba(123,57,17,.1); }
.period-input {
    width:100%; padding:10px 14px; border-radius:10px; border:1.5px solid #e2e8f0;
    font-size:0.88rem; font-weight:500; color:#334155; background:#fff; transition:border-color 0.15s;
}
.period-input:focus { outline:none; border-color:#7B3911; box-shadow:0 0 0 3px rgba(123,57,17,.1); }
.period-apply {
    width:100%; padding:10px; border-radius:10px; border:none; cursor:pointer;
    font-size:0.88rem; font-weight:700; color:#fff; letter-spacing:0.03em;
    background:linear-gradient(135deg,#7B3911,#A1511E); transition:all 0.15s;
    box-shadow:0 2px 8px rgba(123,57,17,.25);
}
.period-apply:hover { background:linear-gradient(135deg,#5A270B,#7B3911); transform:translateY(-1px); box-shadow:0 4px 12px rgba(123,57,17,.35); }
</style>

<div class="flex justify-between items-center mb-6">
    <div>
        <h3 class="font-bold text-xl text-slate-800">Analisis Penjualan</h3>
        @if($hasFilter)
        <p class="text-sm text-slate-500 mt-0.5">Periode: <span class="font-semibold text-[#7B3911]">{{ $periodLabel }}</span> &nbsp;<a href="{{ Request::is('admin/*') ? url('/admin/dashboard') : url('/dashboard') }}" class="text-rose-500 hover:text-rose-700 no-underline text-xs font-bold">× Reset</a></p>
        @endif
    </div>
    <div class="flex items-center gap-3">
        <form method="GET" action="{{ Request::is('admin/*') ? url('/admin/dashboard') : url('/dashboard') }}" id="period-filter-form">
            <input type="hidden" name="filter_mode" id="pf-mode" value="">
            <input type="hidden" name="filter_month" id="pf-month" value="">
            <input type="hidden" name="filter_year" id="pf-year" value="">
            <input type="hidden" name="filter_start" id="pf-start" value="">
            <input type="hidden" name="filter_end" id="pf-end" value="">
        </form>
        <div class="period-wrap" id="period-wrap">
            <button class="period-trigger" id="period-trigger" type="button">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                <span id="period-label">{{ $hasFilter ? 'Periode: ' . $periodLabel : 'Periode' }}</span>
                <svg class="w-3.5 h-3.5 opacity-60" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
            </button>
            <div class="period-popup hidden" id="period-popup">
                {{-- Tabs --}}
                <div class="period-tabs">
                    <button type="button" class="period-tab active" data-tab="month">Bulan & Tahun</button>
                    <button type="button" class="period-tab" data-tab="range">Custom Range</button>
                </div>

                {{-- Tab: Bulan & Tahun --}}
                <div class="period-section" id="tab-month">
                    <div style="display:flex;gap:10px;margin-bottom:14px;">
                        <div style="flex:1;">
                            <label style="display:block;font-size:0.72rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px;">Bulan</label>
                            <select class="period-select" id="sel-month">
                                @php $bulanNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp
                                @foreach($bulanNames as $idx => $nama)
                                    <option value="{{ $idx + 1 }}" {{ ($filterMonth == $idx + 1) ? 'selected' : ((!$filterMonth && $idx + 1 == date('n')) ? 'selected' : '') }}>{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="flex:1;">
                            <label style="display:block;font-size:0.72rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px;">Tahun</label>
                            <select class="period-select" id="sel-year">
                                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                    <option value="{{ $y }}" {{ ($filterYear == $y) ? 'selected' : ((!$filterYear && $y == date('Y')) ? 'selected' : '') }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <button type="button" class="period-apply" id="apply-month">
                        Terapkan Periode
                    </button>
                </div>

                {{-- Tab: Custom Range --}}
                <div class="period-section hidden" id="tab-range">
                    <div style="display:flex;gap:10px;margin-bottom:14px;">
                        <div style="flex:1;">
                            <label style="display:block;font-size:0.72rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px;">Tanggal Mulai</label>
                            <input type="date" class="period-input" id="inp-start" value="{{ $filterStart ?: now()->startOfMonth()->format('Y-m-d') }}">
                        </div>
                        <div style="flex:1;">
                            <label style="display:block;font-size:0.72rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px;">Tanggal Selesai</label>
                            <input type="date" class="period-input" id="inp-end" value="{{ $filterEnd ?: now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    <button type="button" class="period-apply" id="apply-range">
                        Terapkan Range
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-7">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
        <h4 class="font-bold text-[1.15rem] text-slate-800">{{ $trendChartTitle }}</h4>
        <p class="text-xs text-slate-400 mt-1 mb-5">{{ $trendChartDesc }}</p>
        <div class="h-[280px]">
           <canvas id="chartTrend"></canvas>
        </div>
    </div>
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
        <h4 class="font-bold text-[1.15rem] mb-6 text-slate-800">Produk Terlaris</h4>
        <div class="h-[300px]">
           <canvas id="chartDist"></canvas>
        </div>
    </div>
</div>

<div class="mt-7 bg-white border border-slate-200 rounded-2xl p-6 shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
    <h4 class="font-bold text-[1.15rem] mb-6 text-slate-800">Stok Terkini</h4>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-y border-slate-200">
                    <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Produk</th>
                    <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kategori</th>
                    <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Harga</th>
                    <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Stok</th>
                    <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($produkList as $p)
                @php
                    $isRendah = $p->stok < $p->stok_minimal;
                    $isHabis = $p->stok <= 0;
                @endphp
                <tr class="border-b last:border-0 hover:bg-slate-50 transition-colors">
                    <td class="py-3 px-4 text-sm font-semibold text-slate-800">{{ $p->nama }}</td>
                    <td class="py-3 px-4 text-sm text-slate-600">{{ $p->kategori }}</td>
                    <td class="py-3 px-4 text-sm text-slate-600">{{ $p->harga_format }}</td>
                    <td class="py-3 px-4 text-sm font-bold text-center {{ $isHabis ? 'text-red-600' : ($isRendah ? 'text-red-500' : 'text-slate-700') }}">
                        {{ intval($p->stok) }}
                    </td>
                    <td class="py-3 px-4 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $p->status === 'Tersedia' ? 'bg-green-100 text-green-700' : ($p->status === 'Stok Rendah' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                            {{ $p->status }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Pass PHP data to JS --}}
<script>
    window._dashboardData = {
        trendLabels: @json($trendLabels),
        trendData:   @json($trendData),
        distLabels:  @json($distLabels),
        distData:    @json($distData),
    };
</script>

<script>
// Period Picker Logic
(function() {
    const trigger = document.getElementById('period-trigger');
    const popup   = document.getElementById('period-popup');
    const wrap    = document.getElementById('period-wrap');
    const form    = document.getElementById('period-filter-form');

    // Tab switching
    document.querySelectorAll('.period-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.period-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            const target = tab.getAttribute('data-tab');
            document.getElementById('tab-month').classList.toggle('hidden', target !== 'month');
            document.getElementById('tab-range').classList.toggle('hidden', target !== 'range');
        });
    });

    // Apply Month/Year
    document.getElementById('apply-month')?.addEventListener('click', () => {
        document.getElementById('pf-mode').value  = 'month';
        document.getElementById('pf-month').value = document.getElementById('sel-month').value;
        document.getElementById('pf-year').value  = document.getElementById('sel-year').value;
        document.getElementById('pf-start').value = '';
        document.getElementById('pf-end').value   = '';
        form.submit();
    });

    // Apply Range
    document.getElementById('apply-range')?.addEventListener('click', () => {
        const start = document.getElementById('inp-start').value;
        const end   = document.getElementById('inp-end').value;
        if (!start || !end) { alert('Silakan isi tanggal mulai dan selesai'); return; }
        if (start > end) { alert('Tanggal mulai tidak boleh lebih besar dari tanggal selesai'); return; }
        document.getElementById('pf-mode').value  = 'range';
        document.getElementById('pf-start').value = start;
        document.getElementById('pf-end').value   = end;
        document.getElementById('pf-month').value = '';
        document.getElementById('pf-year').value  = '';
        form.submit();
    });

    // Toggle popup
    trigger?.addEventListener('click', (e) => {
        e.stopPropagation();
        popup.classList.toggle('hidden');
    });

    // Close on outside click
    document.addEventListener('click', (e) => {
        if (!wrap.contains(e.target)) popup.classList.add('hidden');
    });
})();
</script>

@endsection
