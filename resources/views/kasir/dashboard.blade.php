@extends('layouts.kasir')

@section('title', 'Dashboard Kasir')

@section('content')

{{-- Header & Welcome Section --}}
<div class="mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Halo, {{ Auth::user()->name }}! 👋</h1>
            <p class="text-slate-500 mt-1">Pantau aktivitas penjualan dan tagihan mitra hari ini.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm font-semibold text-indigo-600 bg-indigo-50 border border-indigo-100 px-4 py-2.5 rounded-xl shadow-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-indigo-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
                <span id="welcome-date-string">Memuat tanggal...</span>
            </span>
        </div>
    </div>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    {{-- Card 1 - Penjualan --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_2px_12px_rgba(0,0,0,0.02)] flex items-center justify-between group hover:border-slate-200 hover:shadow-md transition-all duration-300 min-h-[110px]">
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Penjualan Hari Ini</p>
            <h3 class="text-2xl font-bold text-slate-800 mt-1.5">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h3>
        </div>
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
            </svg>
        </div>
    </div>

    {{-- Card 2 - Transaksi --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_2px_12px_rgba(0,0,0,0.02)] flex items-center justify-between group hover:border-slate-200 hover:shadow-md transition-all duration-300 min-h-[110px]">
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Transaksi Hari Ini</p>
            <h3 class="text-2xl font-bold text-slate-800 mt-1.5">{{ $totalTransaksi }}</h3>
        </div>
        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
            </svg>
        </div>
    </div>

    {{-- Card 3 - Produk --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_2px_12px_rgba(0,0,0,0.02)] flex items-center justify-between group hover:border-slate-200 hover:shadow-md transition-all duration-300 min-h-[110px]">
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Produk Tersedia</p>
            <h3 class="text-2xl font-bold text-slate-800 mt-1.5">{{ $produkTersedia }}</h3>
        </div>
        <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
            </svg>
        </div>
    </div>

    {{-- Card 4 - Belum Dibayar --}}
    <a href="{{ url('/kasir/tagihan') }}" class="no-underline block">
        <div class="bg-white rounded-2xl p-6 border {{ $tagihanMendesak > 0 ? 'border-red-100 bg-red-50/10' : 'border-slate-100' }} shadow-[0_2px_12px_rgba(0,0,0,0.02)] flex items-center justify-between group hover:border-slate-200 hover:shadow-md transition-all duration-300 min-h-[110px] relative overflow-hidden">
            @if($tagihanMendesak > 0)
                <div class="absolute top-0 right-0 w-1 h-full bg-red-500 animate-pulse"></div>
            @endif
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Belum Dibayar</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1.5">{{ $belumBayar }}</h3>
                @if($tagihanMendesak > 0)
                    <span class="text-[10px] text-red-500 font-bold block mt-1 uppercase tracking-wider animate-pulse">{{ $tagihanMendesak }} Jatuh Tempo!</span>
                @endif
            </div>
            <div class="w-12 h-12 rounded-xl {{ $tagihanMendesak > 0 ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-600' }} flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
        </div>
    </a>
</div>

{{-- Main Layout Section: Chart & Quick Actions --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    {{-- Left: Chart Card --}}
    <div class="lg:col-span-2 bg-white border border-slate-100 rounded-2xl p-6 shadow-[0_2px_12px_rgba(0,0,0,0.02)] flex flex-col justify-between">
        <div>
            <h4 class="font-bold text-lg text-slate-800">Grafik Produk Terlaris</h4>
            <p class="text-xs text-slate-400 mt-1">Berdasarkan total volume kuantitas terjual (Ekor)</p>
        </div>
        <div class="h-[320px] mt-6 relative">
            <canvas id="chartProdukTerlaris"></canvas>
        </div>
    </div>

    {{-- Right: Quick Actions --}}
    <div class="lg:col-span-1 flex flex-col gap-6">
        <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-[0_2px_12px_rgba(0,0,0,0.02)] flex-grow">
            <h4 class="font-bold text-lg text-slate-800 mb-5">Aksi Cepat</h4>
            
            <div class="flex flex-col gap-4">
                <a href="{{ url('/kasir/transaksi') }}" class="group flex items-center gap-4 p-4 rounded-xl border border-slate-100 hover:border-blue-100 hover:bg-blue-50/10 transition-all duration-300 no-underline">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>
                    <div>
                        <h5 class="text-sm font-bold text-slate-800 group-hover:text-blue-600 transition-colors">Buat Transaksi Baru</h5>
                        <p class="text-xs text-slate-400 mt-0.5">Input penjualan mitra (POS)</p>
                    </div>
                </a>

                <a href="{{ url('/kasir/tagihan') }}" class="group flex items-center gap-4 p-4 rounded-xl border border-slate-100 hover:border-indigo-100 hover:bg-indigo-50/10 transition-all duration-300 no-underline">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <div>
                        <h5 class="text-sm font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">Daftar Tagihan Aktif</h5>
                        <p class="text-xs text-slate-400 mt-0.5">Kelola & tagih piutang mitra</p>
                    </div>
                </a>

                <a href="{{ url('/kasir/riwayat') }}" class="group flex items-center gap-4 p-4 rounded-xl border border-slate-100 hover:border-purple-100 hover:bg-purple-50/10 transition-all duration-300 no-underline">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    </div>
                    <div>
                        <h5 class="text-sm font-bold text-slate-800 group-hover:text-purple-600 transition-colors">Riwayat Transaksi</h5>
                        <p class="text-xs text-slate-400 mt-0.5">Lihat seluruh transaksi selesai</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // Realtime date greeting string
    const dStr = new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    document.getElementById('welcome-date-string').textContent = dStr;

    // Render chart
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('chartProdukTerlaris').getContext('2d');
        
        const labels = @json($chartLabels);
        const data = @json($chartData);

        if (!ctx) return;

        // Create elegant gradient fill
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.25)');
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0.00)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels.length ? labels : ['Belum Ada Data'],
                datasets: [{
                    label: 'Total Terjual',
                    data: data.length ? data : [0],
                    borderColor: '#4f46e5',
                    borderWidth: 3,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#4f46e5',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2.5,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#4f46e5',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleColor: '#ffffff',
                        bodyColor: '#e2e8f0',
                        padding: 12,
                        cornerRadius: 10,
                        displayColors: false,
                        titleFont: {
                            family: 'Inter',
                            size: 12,
                            weight: 'bold'
                        },
                        bodyFont: {
                            family: 'Inter',
                            size: 12
                        },
                        callbacks: {
                            label: function(context) {
                                return `Terjual: ${context.parsed.y} Ekor`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Inter',
                                size: 11,
                                weight: '500'
                            },
                            color: '#64748b'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9'
                        },
                        ticks: {
                            precision: 0,
                            font: {
                                family: 'Inter',
                                size: 11,
                                weight: '500'
                            },
                            color: '#64748b'
                        }
                    }
                }
            }
        });
    });
</script>

@endsection
