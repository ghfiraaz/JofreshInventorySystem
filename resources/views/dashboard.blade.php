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

<div class="flex justify-between items-center mb-6">
    <h3 class="font-bold text-xl text-slate-800">Analisis Penjualan</h3>
    <div class="flex items-center text-[0.95rem]">
        <label class="text-slate-600 font-medium">Periode:</label>
        <select class="ml-3 px-4 py-2 border border-blue-200 bg-white rounded-xl outline-none cursor-pointer shadow-sm text-[0.95rem] hover:border-blue-300 transition-colors">
            <option>1 Minggu</option>
            <option>1 Bulan</option>
        </select>
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
@endsection
