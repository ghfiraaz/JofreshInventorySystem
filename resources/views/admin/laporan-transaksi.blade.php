@extends('layouts.app')

@section('title', 'Laporan Transaksi')

@section('content')

<style>
.cal-picker-wrap { position:relative; display:inline-block; }
#lp-cal-trigger {
    display:flex; align-items:center; gap:8px;
    padding:8px 16px; border-radius:12px; cursor:pointer;
    background:linear-gradient(135deg,#e0e7ff,#f0fdf4);
    border:1.5px solid #a5b4fc; color:#3730a3;
    font-weight:600; font-size:0.93rem; transition:all 0.18s;
    user-select:none; box-shadow:0 2px 8px rgba(99,102,241,.08);
}
#lp-cal-trigger:hover { border-color:#6366f1; background:linear-gradient(135deg,#c7d2fe,#dcfce7); }
#lp-cal-popup {
    position:absolute; top:calc(100% + 8px); right:0; z-index:200;
    background:#fff; border-radius:18px; box-shadow:0 12px 40px rgba(99,102,241,.16);
    border:1.5px solid #e0e7ff; width:308px; overflow:hidden;
    animation: lp-calSlide 0.18s ease;
}
@keyframes lp-calSlide { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
.lp-cal-header {
    display:flex; align-items:center; justify-content:space-between;
    padding:14px 16px 10px;
    background:linear-gradient(135deg,#4f46e5,#6366f1);
}
.lp-cal-nav { background:rgba(255,255,255,.18); border:none; border-radius:8px; width:32px; height:32px; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#fff; transition:background 0.15s; }
.lp-cal-nav:hover { background:rgba(255,255,255,.32); }
.lp-cal-title { background:transparent; border:none; color:#fff; font-weight:700; font-size:1rem; cursor:pointer; padding:4px 10px; border-radius:8px; transition:background 0.15s; }
.lp-cal-title:hover { background:rgba(255,255,255,.18); }
.lp-cal-weekdays { display:grid; grid-template-columns:repeat(7,1fr); padding:8px 12px 4px; }
.lp-cal-weekdays span { text-align:center; font-size:0.72rem; font-weight:700; color:#6366f1; text-transform:uppercase; letter-spacing:.04em; }
.lp-cal-days { display:grid; grid-template-columns:repeat(7,1fr); gap:2px; padding:4px 12px 12px; }
.lp-cal-day { aspect-ratio:1; display:flex; align-items:center; justify-content:center; border-radius:10px; font-size:0.85rem; font-weight:500; cursor:pointer; color:#374151; transition:all 0.15s; border:none; background:transparent; }
.lp-cal-day:hover { background:#e0e7ff; color:#3730a3; }
.lp-cal-day.today { background:#f0fdf4; color:#16a34a; font-weight:700; border:1.5px solid #86efac; }
.lp-cal-day.selected { background:linear-gradient(135deg,#4f46e5,#6366f1); color:#fff !important; font-weight:700; box-shadow:0 2px 8px rgba(99,102,241,.3); }
.lp-cal-day.other-month { color:#d1d5db; }
.lp-cal-grid3 { display:grid; grid-template-columns:repeat(3,1fr); gap:6px; padding:12px 14px 14px; }
.lp-cal-grid3 button { padding:10px 4px; border:none; border-radius:10px; font-size:0.82rem; font-weight:600; cursor:pointer; text-align:center; color:#374151; background:transparent; transition:all 0.15s; }
.lp-cal-grid3 button:hover { background:#e0e7ff; color:#3730a3; }
.lp-cal-grid3 button.active { background:linear-gradient(135deg,#4f46e5,#6366f1); color:#fff; box-shadow:0 2px 8px rgba(99,102,241,.3); }
</style>

<div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-[0_2px_10px_rgba(0,0,0,0.02)] mb-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h3 class="font-bold text-xl text-slate-800">Riwayat Laporan Harian</h3>
            <p class="text-sm text-slate-500 mt-1">Daftar rekapitulasi transaksi lunas dikelompokkan per hari.</p>
        </div>
        
        <div class="flex items-center gap-3 flex-wrap">
            <form method="GET" action="{{ url('/owner/laporan-transaksi') }}" id="lp-filter-form">
                <input type="hidden" name="filter_date" id="lp-hidden-date" value="{{ request('filter_date','') }}">
                <input type="hidden" name="bulan" id="lp-hidden-bulan" value="{{ $bulan }}">
                <input type="hidden" name="tahun" id="lp-hidden-tahun" value="{{ $tahun }}">
            </form>

            {{-- Calendar picker --}}
            <div class="cal-picker-wrap" id="lp-picker-wrap">
                <button id="lp-cal-trigger" type="button">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                    <span id="lp-trigger-label">
                        @if(request('filter_date'))
                            {{ \Carbon\Carbon::parse(request('filter_date'))->translatedFormat('d M Y') }}
                        @elseif($bulan && $bulan !== 'all')
                            @php $mn=['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember']; @endphp
                            {{ ($mn[$bulan] ?? $bulan) . ' ' . $tahun }}
                        @else
                            Pilih Periode
                        @endif
                    </span>
                    <svg class="w-3.5 h-3.5 opacity-60" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                </button>
                <div id="lp-cal-popup" class="hidden">
                    <div class="lp-cal-header">
                        <button class="lp-cal-nav" id="lp-prev" type="button">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                        </button>
                        <button class="lp-cal-title" id="lp-title-btn" type="button"></button>
                        <button class="lp-cal-nav" id="lp-next" type="button">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                        </button>
                    </div>
                    <div id="lp-cal-body"></div>
                </div>
            </div>

            @if(request('filter_date') || ($bulan && $bulan !== 'all'))
                <a href="{{ url('/owner/laporan-transaksi') }}" class="text-xs font-semibold text-rose-500 hover:text-rose-700 bg-rose-50 border border-rose-100 px-3 py-1.5 rounded-lg no-underline transition-colors">
                    × Reset
                </a>
            @endif
        </div>
    </div>

    @if(count($grouped) > 0)
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-y border-slate-200">
                    <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                    <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Total Transaksi</th>
                    <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Total Item Keluar</th>
                    <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Total Pendapatan</th>
                    <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; $ri = 0;
                $rowPastels = [
                    ['bg'=>'#fff','border'=>'#f1f5f9'],
                    ['bg'=>'#f0f9ff','border'=>'#e0f2fe'],
                    ['bg'=>'#f5f3ff','border'=>'#ede9fe'],
                    ['bg'=>'#f0fdf4','border'=>'#dcfce7'],
                    ['bg'=>'#fefce8','border'=>'#fef9c3'],
                ];
                @endphp
                @foreach($grouped as $dateKey => $data)
                    @php $grandTotal += $data['total_harga']; $pc = $rowPastels[$ri % count($rowPastels)]; $ri++; @endphp
                    <tr class="border-b last:border-0 hover:brightness-95 transition-all" style="background:{{ $pc['bg'] }}; border-color:{{ $pc['border'] }};">
                        <td class="py-3 px-4 text-sm font-semibold text-slate-800">{{ $data['date']->translatedFormat('d F Y') }}</td>
                        <td class="py-3 px-4 text-sm text-slate-600 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-indigo-700 font-bold text-xs" style="background:#e0e7ff;">{{ $data['total_transaksi'] }}</span>
                        </td>
                        <td class="py-3 px-4 text-sm text-slate-600 text-center">{{ $data['total_item'] }} ekor</td>
                        <td class="py-3 px-4 text-sm font-bold text-emerald-600 text-right">Rp {{ number_format($data['total_harga'], 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" onclick="document.getElementById('detail-{{ $dateKey }}').classList.toggle('hidden'); this.querySelector('.chevron').classList.toggle('rotate-180')" class="px-3 py-1.5 bg-white border border-slate-200 shadow-sm hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded flex items-center gap-1 cursor-pointer transition-colors">
                                    Detail
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 chevron transition-transform">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                                <a href="{{ url('/owner/laporan-harian?date='.$dateKey) }}" target="_blank" class="px-3 py-1.5 bg-indigo-50 border border-indigo-100 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 text-indigo-700 text-xs font-semibold rounded flex items-center gap-1 cursor-pointer transition-colors no-underline">
                                    Cetak PDF
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr id="detail-{{ $dateKey }}" class="hidden">
                        <td colspan="5" class="p-0 border-b border-slate-200">
                            <div class="p-6 bg-slate-50 border-t border-slate-100 shadow-inner">
                                <h4 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-slate-400">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
                                    </svg>
                                    Rincian Transaksi ({{ count($data['transaksi']) }})
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($data['transaksi'] as $tx)
                                    <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-[0_2px_8px_rgba(0,0,0,0.02)] hover:shadow-md transition-shadow">
                                        <div class="flex justify-between items-start mb-3 border-b border-slate-100 pb-3">
                                            <div>
                                                <div class="text-xs text-slate-500 mb-1 flex items-center gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                                                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                    </svg>
                                                    {{ $tx->created_at->format('H:i') }} WIB &bull; <span class="text-slate-400 font-mono">{{ $tx->no_transaksi }}</span>
                                                </div>
                                                <div class="font-bold text-slate-800">{{ $tx->mitra->nama ?? 'Mitra Umum' }}</div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-[0.65rem] font-bold tracking-wider text-slate-400 uppercase">Subtotal</div>
                                                <div class="font-bold text-emerald-600">Rp {{ number_format($tx->total_harga, 0, ',', '.') }}</div>
                                            </div>
                                        </div>
                                        <div class="space-y-2 mt-3">
                                            @foreach($tx->items as $item)
                                            <div class="flex justify-between items-center text-sm">
                                                <div class="flex items-center gap-2 text-slate-600">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-indigo-300"></div>
                                                    {{ $item->nama_produk }}
                                                </div>
                                                <div class="font-semibold text-slate-700 bg-slate-50 px-2 py-0.5 rounded text-xs">{{ $item->jumlah }}x</div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-blue-50/30 border-t-2 border-blue-100">
                    <td colspan="3" class="py-4 px-4 text-sm font-bold text-slate-700 text-right uppercase tracking-wider">Total Pendapatan Terfilter</td>
                    <td class="py-4 px-4 text-lg font-black text-blue-700 text-right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @else
    <div class="text-center py-16 px-4">
        <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-blue-400">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>
        </div>
        <h4 class="text-lg font-bold text-slate-700 mb-1">Belum Ada Transaksi</h4>
        <p class="text-slate-500 text-sm">Tidak ditemukan riwayat transaksi lunas pada filter tersebut.</p>
    </div>
    @endif
</div>

<script>
(function() {
    const MONTHS_ID = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const MONTHS_SHORT = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    const DAYS_SHORT = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];

    let viewMode = 'days';
    const today = new Date();
    let curYear = today.getFullYear();
    let curMonth = today.getMonth();
    let selectedDate = null;

    const presetDate = '{{ request("filter_date", "") }}';
    if (presetDate) {
        const p = new Date(presetDate + 'T00:00:00');
        selectedDate = { y: p.getFullYear(), m: p.getMonth(), d: p.getDate() };
        curYear = p.getFullYear(); curMonth = p.getMonth();
    } else {
        const pb = '{{ $bulan ?? "" }}';
        const py = '{{ $tahun ?? "" }}';
        if (pb && pb !== 'all') curMonth = parseInt(pb) - 1;
        if (py) curYear = parseInt(py);
    }

    const trigger = document.getElementById('lp-cal-trigger');
    const popup   = document.getElementById('lp-cal-popup');
    const titleBtn = document.getElementById('lp-title-btn');
    const prevBtn  = document.getElementById('lp-prev');
    const nextBtn  = document.getElementById('lp-next');
    const body     = document.getElementById('lp-cal-body');
    const wrap     = document.getElementById('lp-picker-wrap');
    const hiddenDate = document.getElementById('lp-hidden-date');
    const hiddenBulan = document.getElementById('lp-hidden-bulan');
    const hiddenTahun = document.getElementById('lp-hidden-tahun');
    const form     = document.getElementById('lp-filter-form');
    const trigLabel = document.getElementById('lp-trigger-label');

    function render() {
        if (viewMode === 'days') renderDays();
        else if (viewMode === 'months') renderMonths();
        else renderYears();
    }

    function renderDays() {
        titleBtn.textContent = MONTHS_ID[curMonth] + ' ' + curYear;
        body.innerHTML = '';
        const wdRow = document.createElement('div');
        wdRow.className = 'lp-cal-weekdays';
        DAYS_SHORT.forEach(d => { const s = document.createElement('span'); s.textContent = d; wdRow.appendChild(s); });
        body.appendChild(wdRow);
        const grid = document.createElement('div');
        grid.className = 'lp-cal-days';
        const firstDay = new Date(curYear, curMonth, 1).getDay();
        const daysInMonth = new Date(curYear, curMonth + 1, 0).getDate();
        const daysInPrev = new Date(curYear, curMonth, 0).getDate();
        for (let i = firstDay - 1; i >= 0; i--) { grid.appendChild(makeDay(daysInPrev - i, 'other-month', true)); }
        for (let d = 1; d <= daysInMonth; d++) {
            const isToday = d === today.getDate() && curMonth === today.getMonth() && curYear === today.getFullYear();
            const isSel = selectedDate && selectedDate.y === curYear && selectedDate.m === curMonth && selectedDate.d === d;
            grid.appendChild(makeDay(d, (isToday ? 'today ' : '') + (isSel ? 'selected' : ''), false, d));
        }
        const trailing = (firstDay + daysInMonth) % 7 === 0 ? 0 : 7 - ((firstDay + daysInMonth) % 7);
        for (let i = 1; i <= trailing; i++) { grid.appendChild(makeDay(i, 'other-month', true)); }
        body.appendChild(grid);
    }

    function makeDay(num, classes, disabled, dayNum) {
        const btn = document.createElement('button');
        btn.type = 'button'; btn.className = 'lp-cal-day ' + classes; btn.textContent = num;
        if (!disabled && dayNum) {
            btn.onclick = () => {
                selectedDate = { y: curYear, m: curMonth, d: dayNum };
                const dd = String(dayNum).padStart(2,'0');
                const mm = String(curMonth + 1).padStart(2,'0');
                const ds = `${curYear}-${mm}-${dd}`;
                hiddenDate.value = ds;
                hiddenBulan.value = '';
                hiddenTahun.value = curYear;
                trigLabel.textContent = `${dd} ${MONTHS_SHORT[curMonth]} ${curYear}`;
                popup.classList.add('hidden');
                form.submit();
            };
        }
        return btn;
    }

    function renderMonths() {
        titleBtn.textContent = String(curYear);
        body.innerHTML = '';
        const grid = document.createElement('div');
        grid.className = 'lp-cal-grid3';
        MONTHS_SHORT.forEach((m, idx) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = (selectedDate && selectedDate.y === curYear && selectedDate.m === idx) ? 'active' : '';
            btn.textContent = m;
            btn.onclick = () => {
                // Select month (not day) → filter by month
                hiddenDate.value = '';
                const mm = String(idx + 1).padStart(2,'0');
                hiddenBulan.value = mm;
                hiddenTahun.value = curYear;
                trigLabel.textContent = MONTHS_ID[idx] + ' ' + curYear;
                popup.classList.add('hidden');
                form.submit();
            };
            grid.appendChild(btn);
        });
        body.appendChild(grid);
    }

    function renderYears() {
        const startYear = Math.floor(curYear / 12) * 12;
        titleBtn.textContent = `${startYear} – ${startYear + 11}`;
        body.innerHTML = '';
        const grid = document.createElement('div');
        grid.className = 'lp-cal-grid3';
        for (let y = startYear; y < startYear + 12; y++) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = y === curYear ? 'active' : '';
            btn.textContent = y;
            btn.onclick = () => { curYear = y; viewMode = 'months'; render(); };
            grid.appendChild(btn);
        }
        body.appendChild(grid);
    }

    titleBtn.addEventListener('click', () => {
        if (viewMode === 'days') viewMode = 'months';
        else if (viewMode === 'months') viewMode = 'years';
        render();
    });
    prevBtn.addEventListener('click', () => {
        if (viewMode === 'days') { curMonth--; if (curMonth < 0) { curMonth = 11; curYear--; } }
        else if (viewMode === 'months') curYear--;
        else curYear -= 12;
        render();
    });
    nextBtn.addEventListener('click', () => {
        if (viewMode === 'days') { curMonth++; if (curMonth > 11) { curMonth = 0; curYear++; } }
        else if (viewMode === 'months') curYear++;
        else curYear += 12;
        render();
    });
    trigger.addEventListener('click', (e) => {
        e.stopPropagation();
        const isHidden = popup.classList.contains('hidden');
        popup.classList.toggle('hidden');
        if (isHidden) { viewMode = 'days'; render(); }
    });
    document.addEventListener('click', (e) => { if (!wrap.contains(e.target)) popup.classList.add('hidden'); });
    render();
})();
</script>

@endsection
