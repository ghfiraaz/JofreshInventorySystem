@php
    $role = Auth::user()->role;
    $layout = match($role) {
        'Admin' => 'layouts.admin',
        'Kasir' => 'layouts.kasir',
        default => 'layouts.app',
    };
@endphp

@extends($layout)

@section('title', 'Log Stok')

@section('content')
<style>
    .badge-masuk { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46; border: 1px solid #6ee7b7; }
    .badge-keluar { background: linear-gradient(135deg, #fee2e2, #fecaca); color: #991b1b; border: 1px solid #fca5a5; }
    .badge-adj-masuk { background: linear-gradient(135deg, #FAF0E6, #E8E0D8); color: #7B3911; border: 1px solid #C8702A; }
    .badge-adj-keluar { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; border: 1px solid #fbbf24; }
    .badge-tipe { display: inline-flex; align-items: center; gap: 5px; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.025em; white-space: nowrap; }

    .stat-card { background: white; border-radius: 16px; padding: 20px 24px; border: 1px solid #e2e8f0; transition: all 0.3s ease; position: relative; overflow: hidden; }
    .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; border-radius: 16px 16px 0 0; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.06); }
    .stat-card.card-total::before { background: linear-gradient(90deg, #7B3911, #D2691E); }
    .stat-card.card-masuk::before { background: linear-gradient(90deg, #10b981, #34d399); }
    .stat-card.card-keluar::before { background: linear-gradient(90deg, #ef4444, #f87171); }
    .stat-card.card-adj::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

    .log-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .log-table thead th { background: #f8fafc; padding: 12px 16px; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; text-align: left; border-bottom: 2px solid #e2e8f0; white-space: nowrap; }
    .log-table tbody td { padding: 14px 16px; font-size: 0.875rem; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .log-table tbody tr { transition: background 0.15s ease; }
    .log-table tbody tr:hover { background: #f8fafc; }

    .stok-change { display: inline-flex; align-items: center; gap: 6px; font-weight: 600; font-size: 0.8rem; font-variant-numeric: tabular-nums; }
    .stok-change .arrow { display: inline-flex; align-items: center; justify-content: center; width: 20px; height: 20px; border-radius: 50%; font-size: 0.65rem; }
    .stok-change.up .arrow { background: #d1fae5; color: #065f46; }
    .stok-change.down .arrow { background: #fee2e2; color: #991b1b; }

    .filter-bar { background: white; border-radius: 16px; padding: 20px 24px; border: 1px solid #e2e8f0; display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end; }
    .filter-bar label { font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 6px; }
    .filter-bar select, .filter-bar input[type="date"] { appearance: none; -webkit-appearance: none; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-size: 0.875rem; font-family: 'Inter', sans-serif; background: #f8fafc; color: #334155; outline: none; transition: all 0.2s; min-width: 160px; }
    .filter-bar select:focus, .filter-bar input[type="date"]:focus { border-color: #A1511E; box-shadow: 0 0 0 3px rgba(123, 57, 17, 0.1); background: white; }

    .btn-action { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 12px; font-weight: 600; font-size: 0.875rem; border: none; cursor: pointer; transition: all 0.2s ease; font-family: 'Inter', sans-serif; white-space: nowrap; text-decoration: none; }
    .btn-action:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .btn-filter { background: linear-gradient(135deg, #7B3911, #A1511E); color: white; padding: 10px 18px; }
    .btn-reset { background: #f1f5f9; color: #64748b; padding: 10px 18px; }
    .btn-reset:hover { background: #e2e8f0; color: #334155; }

    .empty-state { text-align: center; padding: 60px 20px; color: #94a3b8; }
    .empty-state svg { width: 64px; height: 64px; margin-bottom: 16px; opacity: 0.5; }
    .empty-state p { font-size: 0.95rem; font-weight: 500; }

    .oleh-badge { display: inline-flex; align-items: center; gap: 6px; font-size: 0.8rem; }
    .oleh-badge .avatar-sm { width: 26px; height: 26px; border-radius: 50%; background: #e2e8f0; color: #475569; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.65rem; flex-shrink: 0; }
    .oleh-badge .role-tag { font-size: 0.65rem; padding: 2px 6px; border-radius: 4px; font-weight: 600; margin-left: 2px; }
    .role-admin { background: #FAF0E6; color: #7B3911; }
    .role-kasir { background: #fce7f3; color: #9d174d; }
    .role-superadmin { background: #FFF8F0; color: #C8702A; }
</style>

{{-- ===== STAT CARDS ===== --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="stat-card card-total">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Log Hari Ini</p>
                <p class="text-2xl font-bold text-slate-800">{{ $totalLogHariIni }}</p>
            </div>
            <div class="stat-icon" style="background: linear-gradient(135deg, #FAF5EF, #F0E0D0);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#7B3911" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" /></svg>
            </div>
        </div>
    </div>
    <div class="stat-card card-masuk">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Stok Masuk</p>
                <p class="text-2xl font-bold text-emerald-600">+{{ $totalMasuk }}</p>
            </div>
            <div class="stat-icon" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#059669" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0 6.75-6.75M12 19.5l-6.75-6.75" /></svg>
            </div>
        </div>
    </div>
    <div class="stat-card card-keluar">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Stok Keluar</p>
                <p class="text-2xl font-bold text-red-600">-{{ $totalKeluar }}</p>
            </div>
            <div class="stat-icon" style="background: linear-gradient(135deg, #fee2e2, #fecaca);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#dc2626" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m0 0-6.75 6.75M12 4.5l6.75 6.75" /></svg>
            </div>
        </div>
    </div>
    <div class="stat-card card-adj">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Adjustment</p>
                <p class="text-2xl font-bold text-amber-600">{{ $totalAdjustment }}</p>
            </div>
            <div class="stat-icon" style="background: linear-gradient(135deg, #fef3c7, #fde68a);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#d97706" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" /></svg>
            </div>
        </div>
    </div>
</div>

{{-- ===== FILTER BAR ===== --}}
<div class="mb-6">
    <form method="GET" action="{{ url('/log-stok') }}" class="filter-bar" id="filter-form">
        <div>
            <label>Tipe Transaksi</label>
            <select name="tipe" id="filter-tipe">
                <option value="">Semua Tipe</option>
                <option value="Masuk" {{ $filterTipe === 'Masuk' ? 'selected' : '' }}>Masuk</option>
                <option value="Keluar" {{ $filterTipe === 'Keluar' ? 'selected' : '' }}>Keluar</option>
                <option value="Adjustment Masuk" {{ $filterTipe === 'Adjustment Masuk' ? 'selected' : '' }}>Adjustment Masuk</option>
                <option value="Adjustment Keluar" {{ $filterTipe === 'Adjustment Keluar' ? 'selected' : '' }}>Adjustment Keluar</option>
            </select>
        </div>
        <div>
            <label>Dari Tanggal</label>
            <input type="date" name="tanggal_dari" value="{{ $filterTanggalDari }}" id="filter-dari">
        </div>
        <div>
            <label>Sampai Tanggal</label>
            <input type="date" name="tanggal_sampai" value="{{ $filterTanggalSampai }}" id="filter-sampai">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn-action btn-filter" id="btn-filter">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" /></svg>
                Filter
            </button>
            @if($filterTipe || $filterTanggalDari || $filterTanggalSampai)
            <a href="{{ url('/log-stok') }}" class="btn-action btn-reset" id="btn-reset-filter">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                Reset
            </a>
            @endif
        </div>
    </form>
</div>

{{-- ===== LOG TABLE ===== --}}
<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-bold text-slate-800 text-base">Riwayat Perubahan Stok</h3>
        <span class="text-sm text-slate-400 font-medium">{{ $logs->count() }} catatan</span>
    </div>

    @if($logs->isEmpty())
    <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="mx-auto"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" /></svg>
        <p>Belum ada log stok yang tercatat</p>
    </div>
    @else
    <div style="overflow-x: auto;">
        <table class="log-table" id="log-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Waktu</th>
                    <th>Tipe</th>
                    <th>Produk</th>
                    <th>Qty</th>
                    <th>Perubahan Stok</th>
                    <th>Oleh</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $index => $log)
                <tr>
                    <td class="text-slate-400 font-medium text-xs">{{ $index + 1 }}</td>
                    <td>
                        <div class="text-sm font-semibold text-slate-700">{{ $log->created_at->format('d/m/Y') }}</div>
                        <div class="text-xs text-slate-400">{{ $log->created_at->format('H:i:s') }}</div>
                    </td>
                    <td>
                        <span class="badge-tipe {{ $log->tipe_badge }}">
                            @if($log->tipe === 'Masuk')
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l6.75-6.75M12 19.5l-6.75-6.75" /></svg>
                            @elseif($log->tipe === 'Keluar')
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m0 0l-6.75 6.75M12 4.5l6.75 6.75" /></svg>
                            @elseif($log->tipe === 'Adjustment Masuk')
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" /></svg>
                            @endif
                            {{ $log->tipe }}
                        </span>
                    </td>
                    <td><span class="font-semibold text-slate-700">{{ $log->produk ? $log->produk->nama : '-' }}</span></td>
                    <td>
                        <span class="font-bold {{ in_array($log->tipe, ['Masuk', 'Adjustment Masuk']) ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ in_array($log->tipe, ['Masuk', 'Adjustment Masuk']) ? '+' : '-' }}{{ $log->jumlah }}
                        </span>
                    </td>
                    <td>
                        <div class="stok-change {{ $log->stok_sesudah >= $log->stok_sebelum ? 'up' : 'down' }}">
                            {{ $log->stok_sebelum }}
                            <span class="arrow">→</span>
                            {{ $log->stok_sesudah }}
                        </div>
                    </td>
                    <td>
                        @if($log->user)
                        <div class="oleh-badge">
                            <span class="avatar-sm">{{ strtoupper(substr($log->user->name, 0, 1)) }}</span>
                            <div>
                                <span class="text-slate-700 font-medium">{{ $log->user->name }}</span>
                                <span class="role-tag role-{{ strtolower($log->user->role) }}">{{ $log->user->role }}</span>
                            </div>
                        </div>
                        @else
                        <span class="text-slate-400">-</span>
                        @endif
                    </td>
                    <td>
                        <span class="text-slate-500 text-sm" style="max-width: 200px; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $log->keterangan }}">
                            {{ $log->keterangan ?: '-' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
