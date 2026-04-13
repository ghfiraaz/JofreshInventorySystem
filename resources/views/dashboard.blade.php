@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="summary-cards">
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
            <span class="text-muted fs-sm">Total Transaksi</span>
            <h3 class="fw-bold-700">2</h3>
        </div>
        <div class="card-icon icon-cart">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:24px;height:24px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
            </svg>
        </div>
    </div>
    <div class="card">
        <div class="card-content">
            <span class="text-muted fs-sm">Total Mitra</span>
            <h3 class="fw-bold-700">3</h3>
        </div>
        <div class="card-icon icon-users">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:24px;height:24px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
            </svg>
        </div>
    </div>
    <div class="card">
        <div class="card-content">
            <span class="text-muted fs-sm">Total Stok</span>
            <h3 class="fw-bold-700">405 kg</h3>
        </div>
        <div class="card-icon icon-stock">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:24px;height:24px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
            </svg>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold-700">Analisis Penjualan</h3>
    <div class="header-actions">
        <label class="text-muted">Periode:</label>
        <select>
            <option>1 Minggu</option>
            <option>1 Bulan</option>
        </select>
    </div>
</div>

<div class="charts-grid">
    <div class="chart-card">
        <h4 class="fw-bold-700">Tren Penjualan (7 Hari Terakhir)</h4>
        <div style="height: 250px;">
           <canvas id="chartTrend"></canvas>
        </div>
    </div>
    <div class="chart-card">
        <h4 class="fw-bold-700">Distribusi Penjualan Produk (7 Hari Terakhir)</h4>
        <div style="height: 250px;">
           <canvas id="chartDist"></canvas>
        </div>
    </div>
</div>
@endsection
