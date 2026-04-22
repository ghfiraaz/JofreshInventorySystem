@extends('layouts.app')

@section('title', 'Laporan Transaksi')

@section('content')
<div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-[0_2px_10px_rgba(0,0,0,0.02)] mb-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h3 class="font-bold text-xl text-slate-800">Riwayat Laporan Harian</h3>
            <p class="text-sm text-slate-500 mt-1">Daftar rekapitulasi transaksi lunas dikelompokkan per hari.</p>
        </div>
        
        <form method="GET" action="{{ url('/owner/laporan-transaksi') }}" class="flex items-center gap-3">
            <select name="bulan" class="px-4 py-2 border border-slate-200 rounded-xl outline-none text-sm text-slate-700 hover:border-slate-300 focus:border-indigo-500 transition-colors shadow-sm bg-white cursor-pointer" onchange="this.form.submit()">
                <option value="all" {{ empty($bulan) || $bulan === 'all' ? 'selected' : '' }}>Semua Bulan</option>
                @php
                    $months = [
                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                        '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                    ];
                @endphp
                @foreach($months as $num => $name)
                    <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            
            <select name="tahun" class="px-4 py-2 border border-slate-200 rounded-xl outline-none text-sm text-slate-700 hover:border-slate-300 focus:border-indigo-500 transition-colors shadow-sm bg-white cursor-pointer" onchange="this.form.submit()">
                @for($y = date('Y'); $y >= 2023; $y--)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </form>
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
                @php $grandTotal = 0; @endphp
                @foreach($grouped as $dateKey => $data)
                    @php $grandTotal += $data['total_harga']; @endphp
                    <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors">
                        <td class="py-3 px-4 text-sm font-semibold text-slate-800">{{ $data['date']->translatedFormat('d F Y') }}</td>
                        <td class="py-3 px-4 text-sm text-slate-600 text-center">{{ $data['total_transaksi'] }}</td>
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
@endsection
