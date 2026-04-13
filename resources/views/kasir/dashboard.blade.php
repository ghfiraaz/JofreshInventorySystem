@extends('layouts.kasir')

@section('title', 'Dashboard Kasir')

@section('content')

<div class="summary-cards" style="grid-template-columns: repeat(3, 1fr);">
    <div class="card">
        <div class="card-content">
            <span class="text-muted fs-sm">Penjualan Hari Ini</span>
            <h3 class="fw-bold-700">Rp 0</h3>
        </div>
        <div class="card-icon icon-trend">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:24px;height:24px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
            </svg>
        </div>
    </div>
    <div class="card">
        <div class="card-content">
            <span class="text-muted fs-sm">Transaksi Hari Ini</span>
            <h3 class="fw-bold-700">0</h3>
        </div>
        <div class="card-icon icon-cart">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:24px;height:24px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
            </svg>
        </div>
    </div>
    <div class="card">
        <div class="card-content">
            <span class="text-muted fs-sm">Produk Tersedia</span>
            <h3 class="fw-bold-700">3</h3>
        </div>
        <div class="card-icon icon-stock">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:24px;height:24px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
            </svg>
        </div>
    </div>
</div>

<div class="mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold-700">Aksi Cepat</h3>
    </div>
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        <a href="{{ url('/kasir/transaksi') }}" class="card" style="text-decoration:none; cursor:pointer; display:flex; align-items:center; gap:1rem; padding:2rem; transition: all 0.3s;">
            <div class="card-icon icon-cart" style="width:60px; height:60px; border-radius:14px; flex-shrink:0;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:28px;height:28px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </div>
            <div>
                <div class="fw-bold" style="font-size:1.1rem; color: var(--text-main);">Buat Transaksi Baru</div>
                <div class="text-muted fs-sm">Catat penjualan ke mitra</div>
            </div>
        </a>
        <a href="{{ url('/kasir/riwayat') }}" class="card" style="text-decoration:none; cursor:pointer; display:flex; align-items:center; gap:1rem; padding:2rem; transition: all 0.3s;">
            <div class="card-icon icon-trend" style="width:60px; height:60px; border-radius:14px; flex-shrink:0;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:28px;height:28px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <div>
                <div class="fw-bold" style="font-size:1.1rem; color: var(--text-main);">Riwayat Transaksi</div>
                <div class="text-muted fs-sm">Lihat semua transaksi Anda</div>
            </div>
        </a>
    </div>
</div>

@endsection
