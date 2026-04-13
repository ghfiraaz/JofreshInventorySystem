@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-7 mb-10">
    <div class="card group">
        <div>
            <span class="text-slate-600 text-[0.9rem]">Penjualan Hari Ini</span>
            <h3 class="font-bold text-[1.75rem] mt-2 text-slate-800">Rp {{ number_format($penjualanHariIni, 0, ',', '.') }}</h3>
        </div>
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center bg-blue-100 text-blue-900 group-hover:scale-110 transition-transform duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
            </svg>
        </div>
    </div>
    <div class="card group">
        <div>
            <span class="text-slate-600 text-[0.9rem]">Total Transaksi</span>
            <h3 class="font-bold text-[1.75rem] mt-2 text-slate-800">{{ $totalTransaksi }}</h3>
        </div>
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center bg-pink-100 text-pink-700 group-hover:scale-110 transition-transform duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
            </svg>
        </div>
    </div>
    <div class="card group">
        <div>
            <span class="text-slate-600 text-[0.9rem]">Total Mitra</span>
            <h3 class="font-bold text-[1.75rem] mt-2 text-slate-800">{{ $totalMitra }}</h3>
        </div>
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center bg-yellow-100 text-yellow-700 group-hover:scale-110 transition-transform duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
            </svg>
        </div>
    </div>
    <div class="card group {{ $isStokRendah ? '!border-red-300 !bg-red-50' : '' }}">
        <div>
            <span class="{{ $isStokRendah ? 'text-red-600' : 'text-slate-600' }} text-[0.9rem]">Total Stok</span>
            <div class="flex items-baseline gap-2">
                <h3 class="font-bold text-[1.75rem] mt-2 {{ $isStokRendah ? 'text-red-700' : 'text-slate-800' }}">{{ number_format($totalStok, 0, ',', '.') }}</h3>
                <span class="{{ $isStokRendah ? 'text-red-600' : 'text-slate-500' }} text-sm font-medium">ekor</span>
            </div>
            @if($isStokRendah)
                <div class="flex items-center gap-1 mt-1 text-red-600 animate-pulse">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-[0.75rem] font-bold uppercase tracking-tight">Stok Menipis! ({{ $stokRendahCount }})</span>
                </div>
            @endif
        </div>
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center {{ $isStokRendah ? 'bg-red-200 text-red-800' : 'bg-green-100 text-green-700' }} group-hover:scale-110 transition-transform duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
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
    <div class="bg-white border border-blue-200 rounded-2xl p-8 shadow-[0_2px_4px_rgba(30,58,138,0.05)] transition-all duration-300 hover:shadow-[0_4px_12px_rgba(30,58,138,0.08)]">
        <h4 class="font-bold text-[1.25rem] mb-6 text-slate-800">Tren Penjualan</h4>
        <div class="h-[300px]">
           <canvas id="chartTrend"></canvas>
        </div>
    </div>
    <div class="bg-white border border-blue-200 rounded-2xl p-8 shadow-[0_2px_4px_rgba(30,58,138,0.05)] transition-all duration-300 hover:shadow-[0_4px_12px_rgba(30,58,138,0.08)]">
        <h4 class="font-bold text-[1.25rem] mb-6 text-slate-800">Produk Terlaris</h4>
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
