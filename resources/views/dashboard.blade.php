@extends(Auth::user()->role === 'Admin' ? 'layouts.admin' : 'layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-[0_2px_10px_rgba(0,0,0,0.02)] flex items-center justify-between group hover:border-slate-300 transition-colors">
        <div>
            <span class="text-slate-500 text-[0.85rem] font-medium tracking-wide">Penjualan Hari Ini</span>
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
        <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-blue-50 text-blue-600 group-hover:scale-110 transition-transform duration-300">
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
        <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-indigo-50 text-indigo-600 group-hover:scale-110 transition-transform duration-300">
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
                <h3 class="font-bold text-2xl {{ $isStokRendah ? 'text-red-700' : 'text-slate-800' }}">{{ number_format($totalStok, 0, ',', '.') }}</h3>
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

{{-- ===== CUSTOM CALENDAR STYLES ===== --}}
<style>
.dash-cal-wrap { position:relative; display:inline-block; }
#dash-cal-trigger {
    display:flex; align-items:center; gap:8px;
    padding:8px 16px; border-radius:12px; cursor:pointer;
    background:linear-gradient(135deg,#e0e7ff,#f0fdf4);
    border:1.5px solid #a5b4fc; color:#3730a3;
    font-weight:600; font-size:0.9rem; transition:all 0.18s;
    user-select:none; box-shadow:0 2px 8px rgba(99,102,241,.08);
}
#dash-cal-trigger:hover { border-color:#6366f1; background:linear-gradient(135deg,#c7d2fe,#dcfce7); }
#dash-cal-popup {
    position:absolute; top:calc(100% + 8px); right:0; z-index:200;
    background:#fff; border-radius:18px; box-shadow:0 12px 40px rgba(99,102,241,.16);
    border:1.5px solid #e0e7ff; width:308px; overflow:hidden;
    animation: dashCalSlide 0.18s ease;
}
@keyframes dashCalSlide { from{opacity:0;transform:translateY(-8px);}to{opacity:1;transform:translateY(0);} }
.dash-cal-header { display:flex; align-items:center; justify-content:space-between; padding:14px 16px 10px; background:linear-gradient(135deg,#4f46e5,#6366f1); }
.dash-cal-nav { background:rgba(255,255,255,.18); border:none; border-radius:8px; width:32px; height:32px; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#fff; transition:background 0.15s; }
.dash-cal-nav:hover { background:rgba(255,255,255,.32); }
.dash-cal-title { background:transparent; border:none; color:#fff; font-weight:700; font-size:1rem; cursor:pointer; padding:4px 10px; border-radius:8px; transition:background 0.15s; }
.dash-cal-title:hover { background:rgba(255,255,255,.18); }
.dash-cal-weekdays { display:grid; grid-template-columns:repeat(7,1fr); padding:8px 12px 4px; }
.dash-cal-weekdays span { text-align:center; font-size:0.72rem; font-weight:700; color:#6366f1; text-transform:uppercase; letter-spacing:.04em; }
.dash-cal-days { display:grid; grid-template-columns:repeat(7,1fr); gap:2px; padding:4px 12px 12px; }
.dash-cal-day { aspect-ratio:1; display:flex; align-items:center; justify-content:center; border-radius:10px; font-size:0.85rem; font-weight:500; cursor:pointer; color:#374151; transition:all 0.15s; border:none; background:transparent; }
.dash-cal-day:hover { background:#e0e7ff; color:#3730a3; }
.dash-cal-day.today { background:#f0fdf4; color:#16a34a; font-weight:700; border:1.5px solid #86efac; }
.dash-cal-day.selected { background:linear-gradient(135deg,#4f46e5,#6366f1); color:#fff!important; font-weight:700; box-shadow:0 2px 8px rgba(99,102,241,.3); }
.dash-cal-day.other-month { color:#d1d5db; }
.dash-cal-grid3 { display:grid; grid-template-columns:repeat(3,1fr); gap:6px; padding:12px 14px 14px; }
.dash-cal-grid3 button { padding:10px 4px; border:none; border-radius:10px; font-size:0.82rem; font-weight:600; cursor:pointer; text-align:center; color:#374151; background:transparent; transition:all 0.15s; }
.dash-cal-grid3 button:hover { background:#e0e7ff; color:#3730a3; }
.dash-cal-grid3 button.active { background:linear-gradient(135deg,#4f46e5,#6366f1); color:#fff; box-shadow:0 2px 8px rgba(99,102,241,.3); }
</style>

<div class="flex justify-between items-center mb-6">
    <div>
        <h3 class="font-bold text-xl text-slate-800">Analisis Penjualan</h3>
        @if($filterDate)
        <p class="text-sm text-slate-500 mt-0.5">Data untuk: <span class="font-semibold text-indigo-600">{{ \Carbon\Carbon::parse($filterDate)->translatedFormat('d F Y') }}</span> &nbsp;<a href="{{ Request::is('admin/*') ? url('/admin/dashboard') : url('/dashboard') }}" class="text-rose-500 hover:text-rose-700 no-underline text-xs font-bold">× Reset</a></p>
        @endif
    </div>
    <div class="flex items-center gap-3">
        <form method="GET" action="{{ Request::is('admin/*') ? url('/admin/dashboard') : url('/dashboard') }}" id="dash-filter-form">
            <input type="hidden" name="filter_date" id="dash-hidden-date" value="{{ $filterDate ?? '' }}">
        </form>
        <div class="dash-cal-wrap" id="dash-cal-wrap">
            <button id="dash-cal-trigger" type="button">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                <span id="dash-trigger-label">{{ $filterDate ? \Carbon\Carbon::parse($filterDate)->translatedFormat('d M Y') : '7 Hari Terakhir' }}</span>
                <svg class="w-3.5 h-3.5 opacity-60" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
            </button>
            <div id="dash-cal-popup" class="hidden">
                <div class="dash-cal-header">
                    <button class="dash-cal-nav" id="dash-prev" type="button">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                    </button>
                    <button class="dash-cal-title" id="dash-title-btn" type="button"></button>
                    <button class="dash-cal-nav" id="dash-next" type="button">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                    </button>
                </div>
                <div id="dash-cal-body"></div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-7">
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
        <h4 class="font-bold text-[1.15rem] mb-6 text-slate-800">Tren Penjualan</h4>
        <div class="h-[300px]">
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
// Dashboard Calendar (display-only, decorative for now)
(function() {
    const MONTHS_ID = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const MONTHS_SHORT = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    const DAYS_SHORT = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];

    let viewMode = 'days';
    const today = new Date();
    let curYear = today.getFullYear();
    let curMonth = today.getMonth();

    const trigger = document.getElementById('dash-cal-trigger');
    const popup   = document.getElementById('dash-cal-popup');
    const titleBtn = document.getElementById('dash-title-btn');
    const prevBtn  = document.getElementById('dash-prev');
    const nextBtn  = document.getElementById('dash-next');
    const body     = document.getElementById('dash-cal-body');
    const wrap     = document.getElementById('dash-cal-wrap');
    const trigLabel = document.getElementById('dash-trigger-label');
    const hiddenDate = document.getElementById('dash-hidden-date');
    const filterForm = document.getElementById('dash-filter-form');

    const presetDate = '{{ $filterDate ?? "" }}';
    let sel = null;
    if (presetDate) {
        const p = new Date(presetDate + 'T00:00:00');
        sel = {y: p.getFullYear(), m: p.getMonth(), d: p.getDate()};
        curYear = p.getFullYear(); curMonth = p.getMonth();
    }

    function render() {
        if (viewMode === 'days') renderDays();
        else if (viewMode === 'months') renderMonths();
        else renderYears();
    }

    function renderDays() {
        titleBtn.textContent = MONTHS_ID[curMonth] + ' ' + curYear;
        body.innerHTML = '';
        const wdRow = document.createElement('div');
        wdRow.className = 'dash-cal-weekdays';
        DAYS_SHORT.forEach(d => { const s = document.createElement('span'); s.textContent = d; wdRow.appendChild(s); });
        body.appendChild(wdRow);
        const grid = document.createElement('div');
        grid.className = 'dash-cal-days';
        const firstDay = new Date(curYear, curMonth, 1).getDay();
        const daysInMonth = new Date(curYear, curMonth + 1, 0).getDate();
        const daysInPrev = new Date(curYear, curMonth, 0).getDate();
        for (let i = firstDay - 1; i >= 0; i--) {
            const btn = document.createElement('button');
            btn.type = 'button'; btn.className = 'dash-cal-day other-month'; btn.textContent = daysInPrev - i;
            grid.appendChild(btn);
        }
        for (let d = 1; d <= daysInMonth; d++) {
            const isToday = d === today.getDate() && curMonth === today.getMonth() && curYear === today.getFullYear();
            const isSel = sel && sel.y === curYear && sel.m === curMonth && sel.d === d;
            const btn = document.createElement('button');
            btn.type = 'button'; btn.className = 'dash-cal-day' + (isToday ? ' today' : '') + (isSel ? ' selected' : '');
            btn.textContent = d;
            btn.onclick = () => {
                const dd = String(d).padStart(2,'0');
                const mm = String(curMonth + 1).padStart(2,'0');
                hiddenDate.value = `${curYear}-${mm}-${dd}`;
                trigLabel.textContent = `${dd} ${MONTHS_SHORT[curMonth]} ${curYear}`;
                popup.classList.add('hidden');
                filterForm.submit();
            };
            grid.appendChild(btn);
        }
        const trailing = (firstDay + daysInMonth) % 7 === 0 ? 0 : 7 - ((firstDay + daysInMonth) % 7);
        for (let i = 1; i <= trailing; i++) {
            const btn = document.createElement('button'); btn.type = 'button'; btn.className = 'dash-cal-day other-month'; btn.textContent = i; grid.appendChild(btn);
        }
        body.appendChild(grid);
    }

    function renderMonths() {
        titleBtn.textContent = String(curYear);
        body.innerHTML = '';
        const grid = document.createElement('div');
        grid.className = 'dash-cal-grid3';
        MONTHS_SHORT.forEach((m, idx) => {
            const btn = document.createElement('button');
            btn.type = 'button'; btn.textContent = m;
            btn.onclick = () => { curMonth = idx; viewMode = 'days'; render(); };
            grid.appendChild(btn);
        });
        body.appendChild(grid);
    }

    function renderYears() {
        const startYear = Math.floor(curYear / 12) * 12;
        titleBtn.textContent = `${startYear} – ${startYear + 11}`;
        body.innerHTML = '';
        const grid = document.createElement('div');
        grid.className = 'dash-cal-grid3';
        for (let y = startYear; y < startYear + 12; y++) {
            const btn = document.createElement('button');
            btn.type = 'button'; btn.textContent = y;
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
