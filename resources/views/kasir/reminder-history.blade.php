@extends('layouts.kasir')
@section('title', 'Histori Reminder')
@section('content')

{{-- ===== HEADING ===== --}}
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-800 mb-1">Histori Reminder Pembayaran</h2>
    <p class="text-sm text-gray-400">Riwayat pengiriman email reminder ke mitra</p>
</div>

{{-- ===== FILTER PERIODE ===== --}}
<div class="bg-white rounded-xl border border-gray-200 p-5 mb-6">
    <form method="GET" action="{{ url('/kasir/reminder-history') }}" class="flex items-end gap-4 flex-wrap">
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Mulai Tanggal</label>
            <input type="date" name="dari" value="{{ request('dari', now()->startOfMonth()->format('Y-m-d')) }}"
                   class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 transition-all">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Sampai Tanggal</label>
            <input type="date" name="sampai" value="{{ request('sampai', now()->format('Y-m-d')) }}"
                   class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 transition-all">
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Status</label>
            <select name="status" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 transition-all bg-white cursor-pointer">
                <option value="">Semua</option>
                <option value="berhasil" {{ request('status') == 'berhasil' ? 'selected' : '' }}>Berhasil</option>
                <option value="gagal" {{ request('status') == 'gagal' ? 'selected' : '' }}>Gagal</option>
            </select>
        </div>
        <button type="submit" class="px-6 py-2.5 rounded-xl font-semibold text-sm text-white border-none cursor-pointer transition-all" style="background:#1e3a5f;" onmouseover="this.style.background='#162d4a'" onmouseout="this.style.background='#1e3a5f'">
            <span class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" /></svg>
                Filter
            </span>
        </button>
        @if(request('dari') || request('sampai') || request('status'))
            <a href="{{ url('/kasir/reminder-history') }}" class="px-4 py-2.5 rounded-xl text-sm font-medium text-gray-500 bg-gray-100 hover:bg-gray-200 transition-all no-underline">Reset</a>
        @endif
    </form>
</div>

{{-- ===== SUMMARY CARDS ===== --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center justify-between">
        <div>
            <div class="text-xs text-gray-400 font-medium mb-1">Total Reminder</div>
            <div class="text-2xl font-extrabold text-gray-800">{{ $totalReminder }}</div>
        </div>
        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-blue-600"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center justify-between">
        <div>
            <div class="text-xs text-gray-400 font-medium mb-1">Berhasil</div>
            <div class="text-2xl font-extrabold text-emerald-600">{{ $totalBerhasil }}</div>
        </div>
        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-emerald-600"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center justify-between">
        <div>
            <div class="text-xs text-gray-400 font-medium mb-1">Gagal</div>
            <div class="text-2xl font-extrabold text-red-500">{{ $totalGagal }}</div>
        </div>
        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-red-500"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        </div>
    </div>
</div>

{{-- ===== HISTORI TABLE ===== --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-base font-bold text-gray-800">Riwayat Pengiriman</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr style="background:linear-gradient(135deg,#f8fafc,#f1f5f9);">
                    <th class="py-3.5 px-5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Tanggal</th>
                    <th class="py-3.5 px-5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Mitra</th>
                    <th class="py-3.5 px-5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Email Penerima</th>
                    <th class="py-3.5 px-5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Periode</th>
                    <th class="py-3.5 px-5 text-right text-xs font-bold uppercase tracking-wider text-gray-500">Total Tagihan</th>
                    <th class="py-3.5 px-5 text-center text-xs font-bold uppercase tracking-wider text-gray-500">Status</th>
                    <th class="py-3.5 px-5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Invoice</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($histories as $h)
                <tr class="hover:bg-gray-50/60 transition-colors">
                    <td class="py-3.5 px-5 text-sm text-gray-600">
                        {{ $h->tanggal_pengiriman->format('d/m/Y H:i') }}
                    </td>
                    <td class="py-3.5 px-5">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                {{ strtoupper(substr($h->mitra->nama ?? '-', 0, 1)) }}
                            </div>
                            <span class="text-sm font-semibold text-gray-800">{{ $h->mitra->nama ?? '-' }}</span>
                        </div>
                    </td>
                    <td class="py-3.5 px-5 text-sm text-gray-600">{{ $h->email_penerima }}</td>
                    <td class="py-3.5 px-5 text-sm text-gray-500">
                        {{ $h->periode_awal->format('d/m/Y') }} — {{ $h->periode_akhir->format('d/m/Y') }}
                    </td>
                    <td class="py-3.5 px-5 text-sm font-semibold text-gray-800 text-right">
                        Rp {{ number_format($h->total_tagihan, 0, ',', '.') }}
                    </td>
                    <td class="py-3.5 px-5 text-center">
                        @if($h->status === 'berhasil')
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">✓ Berhasil</span>
                        @else
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-600" title="{{ $h->error_message }}">✗ Gagal</span>
                        @endif
                    </td>
                    <td class="py-3.5 px-5 text-sm text-gray-500">
                        @if($h->invoice_filename)
                            <span class="text-xs text-blue-600 font-medium" title="{{ $h->invoice_filename }}">
                                📄 {{ \Illuminate\Support\Str::limit($h->invoice_filename, 25) }}
                            </span>
                        @else
                            <span class="text-xs text-gray-400">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-16 text-center text-gray-400 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-12 h-12 mx-auto mb-3 text-gray-300"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                        Belum ada histori reminder pada periode ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
