@extends('layouts.admin')

@section('title', 'Pembayaran Mitra')

@section('content')

{{-- Summary Cards --}}
<div class="summary-cards mb-4" style="grid-template-columns: repeat(3, 1fr);">
    <div class="card">
        <div class="card-content">
            <span class="text-muted fs-sm">Total Piutang</span>
            <h3 class="fw-bold-700" style="color: #dc2626;">Rp 750.000</h3>
        </div>
    </div>
    <div class="card">
        <div class="card-content">
            <span class="text-muted fs-sm">Total Lunas</span>
            <h3 class="fw-bold-700" style="color: var(--primary-color);">Rp 2.750.000</h3>
        </div>
    </div>
    <div class="card">
        <div class="card-content">
            <span class="text-muted fs-sm">Total Transaksi</span>
            <h3 class="fw-bold-700">2</h3>
        </div>
    </div>
</div>

<div class="search-box mb-4" style="max-width:440px;">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
    </svg>
    <input type="text" class="form-control" placeholder="Cari mitra atau nomor transaksi...">
</div>

<div class="table-container">
    <table id="pembayaranTable">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>No. Transaksi</th>
                <th>Nama Mitra</th>
                <th>Total Transaksi</th>
                <th>Jatuh Tempo</th>
                <th>Status Bayar</th>
                <th>Metode</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>12/3/2026</td>
                <td class="fw-bold">TXN-001</td>
                <td>Restoran Padang Sederhana</td>
                <td class="fw-bold">Rp 2.750.000</td>
                <td class="text-muted">-</td>
                <td><span class="badge badge-lunas">Lunas</span></td>
                <td>Transfer Bank</td>
            </tr>
            <tr>
                <td>11/3/2026</td>
                <td class="fw-bold">TXN-002</td>
                <td>Warung Makan Ibu Haji</td>
                <td class="fw-bold">Rp 750.000</td>
                <td style="color: #dc2626; font-weight:600;">18/3/2026</td>
                <td><span class="badge badge-belum-lunas">Belum Lunas</span></td>
                <td>Tempo</td>
            </tr>
        </tbody>
    </table>
</div>

@endsection
