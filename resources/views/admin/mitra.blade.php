@extends('layouts.admin')

@section('title', 'Mitra')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="search-box">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
        </svg>
        <input type="text" id="search-mitra" class="form-control" placeholder="Cari mitra...">
    </div>
    <button class="btn btn-primary" id="btn-tambah-mitra">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px;margin-right:0.5rem">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Tambah Mitra
    </button>
</div>

<div class="table-container">
    <table id="mitraTable">
        <thead>
            <tr>
                <th>Nama Mitra</th>
                <th>Kontak</th>
                <th>Alamat</th>
                <th>Status</th>
                <th>Terdaftar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr data-id="m1">
                <td class="fw-bold row-nama-mitra">Restoran Padang Sederhana</td>
                <td class="row-kontak">0812-3456-7890</td>
                <td class="row-alamat">Jl. Sudirman No. 123, Jakarta</td>
                <td><span class="badge badge-aktif row-status-mitra">Aktif</span></td>
                <td class="row-terdaftar">15/1/2026</td>
                <td>
                    <button class="btn-icon btn-edit-mitra" title="Edit">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;">
                          <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                    </button>
                    <button class="btn-icon danger ms-2 btn-delete-mitra" title="Hapus">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;">
                          <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </button>
                </td>
            </tr>
            <tr data-id="m2">
                <td class="fw-bold row-nama-mitra">Warung Makan Ibu Haji</td>
                <td class="row-kontak">0821-9876-5432</td>
                <td class="row-alamat">Jl. Gatot Subroto No. 45, Jakarta</td>
                <td><span class="badge badge-aktif row-status-mitra">Aktif</span></td>
                <td class="row-terdaftar">1/2/2026</td>
                <td>
                    <button class="btn-icon btn-edit-mitra" title="Edit">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;">
                          <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                    </button>
                    <button class="btn-icon danger ms-2 btn-delete-mitra" title="Hapus">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;">
                          <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </button>
                </td>
            </tr>
            <tr data-id="m3">
                <td class="fw-bold row-nama-mitra">Rumah Makan Sunda</td>
                <td class="row-kontak">0856-1234-5678</td>
                <td class="row-alamat">Jl. Thamrin No. 78, Jakarta</td>
                <td><span class="badge badge-aktif row-status-mitra">Aktif</span></td>
                <td class="row-terdaftar">20/2/2026</td>
                <td>
                    <button class="btn-icon btn-edit-mitra" title="Edit">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;">
                          <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                    </button>
                    <button class="btn-icon danger ms-2 btn-delete-mitra" title="Hapus">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;">
                          <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- Modal Tambah/Edit Mitra --}}
<div class="modal-overlay" id="modal-mitra">
    <div class="modal-content">
        <button class="modal-close" data-close-modal>&times;</button>
        <div class="modal-header">
            <h3 id="modal-mitra-title">Tambah Mitra</h3>
            <p>Tambahkan data mitra bisnis baru.</p>
        </div>
        <form id="form-mitra">
            <input type="hidden" id="mitra-edit-id" value="">
            <div class="form-group">
                <label>Nama Mitra *</label>
                <input type="text" id="mitra-nama" class="form-control" placeholder="Nama restoran / warung" required>
            </div>
            <div class="form-group">
                <label>Kontak *</label>
                <input type="text" id="mitra-kontak" class="form-control" placeholder="0812-xxxx-xxxx" required>
            </div>
            <div class="form-group">
                <label>Alamat *</label>
                <input type="text" id="mitra-alamat" class="form-control" placeholder="Jl. ..." required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-close-modal>Batal</button>
                <button type="submit" class="btn btn-primary" id="btn-submit-mitra">Simpan Mitra</button>
            </div>
        </form>
    </div>
</div>

@endsection
