@extends('layouts.admin')

@section('title', 'Produk')

@section('content')

{{-- Stock Warning Banner --}}
<div class="alert-warning mb-4">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px;height:20px;flex-shrink:0;">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
    </svg>
    <div>
        <strong>Peringatan Stok</strong><br>
        <span class="fs-sm">1 produk stok rendah dan 0 produk habis.</span>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="search-box">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
        </svg>
        <input type="text" id="search-produk" class="form-control" placeholder="Cari produk...">
    </div>
    <button class="btn btn-primary" id="btn-tambah-produk">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px;margin-right:0.5rem">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Tambah Produk
    </button>
</div>

<div class="table-container">
    <table id="produkTable">
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Stok Saat Ini</th>
                <th>Stok Minimal</th>
                <th>Satuan</th>
                <th>Harga/Satuan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr data-id="1" data-stok="250" data-minimal="50">
                <td class="fw-bold row-nama">Ayam Broiler</td>
                <td class="row-kategori">Unggas</td>
                <td class="row-stok">250</td>
                <td class="row-minimal">50</td>
                <td class="row-satuan">KG</td>
                <td class="row-harga">Rp 45.000</td>
                <td><span class="badge badge-tersedia">Tersedia</span></td>
                <td>
                    <button class="btn-icon btn-edit-produk" title="Edit">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;">
                          <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                    </button>
                    <button class="btn-icon danger ms-2 btn-delete-produk" title="Hapus">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;">
                          <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </button>
                </td>
            </tr>
            <tr data-id="2" data-stok="120" data-minimal="30">
                <td class="fw-bold row-nama">Ayam Kampung</td>
                <td class="row-kategori">Unggas</td>
                <td class="row-stok">120</td>
                <td class="row-minimal">30</td>
                <td class="row-satuan">KG</td>
                <td class="row-harga">Rp 70.000</td>
                <td><span class="badge badge-tersedia">Tersedia</span></td>
                <td>
                    <button class="btn-icon btn-edit-produk" title="Edit">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;">
                          <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                    </button>
                    <button class="btn-icon danger ms-2 btn-delete-produk" title="Hapus">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;">
                          <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </button>
                </td>
            </tr>
            <tr data-id="3" data-stok="35" data-minimal="40">
                <td class="fw-bold row-nama">Bebek</td>
                <td class="row-kategori">Unggas</td>
                <td class="row-stok">35</td>
                <td class="row-minimal">40</td>
                <td class="row-satuan">KG</td>
                <td class="row-harga">Rp 50.000</td>
                <td><span class="badge badge-stok-rendah">Stok Rendah</span></td>
                <td>
                    <button class="btn-icon btn-edit-produk" title="Edit">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;">
                          <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                    </button>
                    <button class="btn-icon danger ms-2 btn-delete-produk" title="Hapus">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;">
                          <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- Modal Tambah/Edit Produk --}}
<div class="modal-overlay" id="modal-produk">
    <div class="modal-content">
        <button class="modal-close" data-close-modal>&times;</button>
        <div class="modal-header">
            <h3 id="modal-produk-title">Tambah Produk</h3>
            <p id="modal-produk-desc">Tambahkan produk baru ke dalam inventaris.</p>
        </div>
        <form id="form-produk">
            <input type="hidden" id="produk-edit-id" value="">
            <div class="form-grid-2">
                <div class="form-group">
                    <label>Nama Produk *</label>
                    <input type="text" id="produk-nama" class="form-control" placeholder="Nama produk" required>
                </div>
                <div class="form-group">
                    <label>Kategori *</label>
                    <input type="text" id="produk-kategori" class="form-control" placeholder="Misal: Unggas" required>
                </div>
                <div class="form-group">
                    <label>Stok Saat Ini *</label>
                    <input type="number" id="produk-stok" class="form-control" placeholder="0" required min="0">
                </div>
                <div class="form-group">
                    <label>Stok Minimal *</label>
                    <input type="number" id="produk-minimal" class="form-control" placeholder="0" required min="0">
                </div>
                <div class="form-group">
                    <label>Satuan *</label>
                    <select id="produk-satuan" class="form-control" style="appearance:auto">
                        <option>KG</option>
                        <option>Ekor</option>
                        <option>Gram</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Harga/Satuan (Rp) *</label>
                    <input type="number" id="produk-harga" class="form-control" placeholder="0" required min="0">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-close-modal>Batal</button>
                <button type="submit" class="btn btn-primary" id="btn-submit-produk">Simpan Produk</button>
            </div>
        </form>
    </div>
</div>

@endsection
