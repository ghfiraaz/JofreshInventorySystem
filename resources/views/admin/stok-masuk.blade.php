@extends('layouts.admin')

@section('title', 'Stok Masuk')

@section('content')

<div class="card mb-4" style="display:block; padding: 2rem;">
    <h3 class="fw-bold-700 mb-4">Catat Stok Masuk</h3>
    <form id="form-stok-masuk">
        <div class="form-grid-2">
            <div class="form-group">
                <label>Produk *</label>
                <select id="stok-produk" class="form-control" style="appearance:auto" required>
                    <option value="" disabled selected>Pilih produk</option>
                    <option>Ayam Broiler</option>
                    <option>Ayam Kampung</option>
                    <option>Bebek</option>
                </select>
            </div>
            <div class="form-group">
                <label>Tipe Pergerakan *</label>
                <select id="stok-tipe" class="form-control" style="appearance:auto" required>
                    <option>Stok Masuk</option>
                    <option>Stok Keluar</option>
                </select>
            </div>
            <div class="form-group">
                <label>Jumlah *</label>
                <input type="number" id="stok-jumlah" class="form-control" placeholder="0" min="1" required>
            </div>
            <div class="form-group">
                <label>Tanggal *</label>
                <input type="date" id="stok-tanggal" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Nomor Referensi</label>
                <input type="text" id="stok-referensi" class="form-control" placeholder="Contoh: SM-2024-001">
            </div>
            <div class="form-group">
                <label>Catatan</label>
                <textarea id="stok-catatan" class="form-control" placeholder="Catatan tambahan..." rows="3" style="resize:vertical;"></textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Catat Pergerakan</button>
    </form>
</div>

<div class="card" style="display:block; padding: 2rem;">
    <h3 class="fw-bold-700 mb-4">Pergerakan Stok Terbaru</h3>
    <table id="stokTable">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Produk</th>
                <th>Tipe</th>
                <th>Jumlah</th>
                <th>Referensi</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody id="stok-tbody">
            <tr>
                <td>10/3/2026</td>
                <td class="fw-bold">Ayam Broiler</td>
                <td><span class="badge badge-stok-masuk">↑ Stok Masuk</span></td>
                <td>100 kg</td>
                <td>SM-001</td>
                <td class="text-muted">Pengiriman mingguan dari supplier</td>
            </tr>
            <tr>
                <td>11/3/2026</td>
                <td class="fw-bold">Ayam Kampung</td>
                <td><span class="badge badge-stok-keluar">↓ Stok Keluar</span></td>
                <td>20 kg</td>
                <td>TXN-001</td>
                <td class="text-muted">Penjualan ke Restoran Padang Sederhana</td>
            </tr>
        </tbody>
    </table>
</div>

@endsection
