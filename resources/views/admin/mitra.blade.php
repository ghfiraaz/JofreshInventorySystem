@extends('layouts.admin')

@section('title', 'Mitra')

@section('content')

<div class="flex justify-between items-center mb-6">
    <div class="relative w-1/3 min-w-[250px]">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
        </svg>
        <input type="text" id="search-mitra" class="w-full pl-11 pr-4 py-2.5 bg-white border border-blue-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-300 focus:border-blue-900 transition-all text-sm" placeholder="Cari mitra...">
    </div>
    <button class="btn btn-primary flex items-center" id="btn-tambah-mitra">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-[18px] h-[18px] mr-2">
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
            @forelse($mitra as $m)
            <tr data-id="{{ $m->id }}">
                <td class="font-bold row-nama-mitra">{{ $m->nama }}</td>
                <td class="row-kontak">{{ $m->kontak ?? '-' }}</td>
                <td class="row-alamat">{{ $m->alamat ?? '-' }}</td>
                <td><span class="bg-blue-100 text-blue-800 px-3 py-1.5 rounded-full text-xs font-semibold row-status-mitra">{{ $m->status }}</span></td>
                <td class="row-terdaftar">{{ $m->created_at->format('d/m/Y') }}</td>
                <td>
                    <button class="p-2 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors cursor-pointer border-none bg-transparent btn-edit-mitra" title="Edit">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[18px] h-[18px]">
                          <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                    </button>
                    <button class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors cursor-pointer border-none bg-transparent ml-2 btn-delete-mitra" title="Hapus">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[18px] h-[18px]">
                          <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center; color: #718096; padding: 3rem;">
                    Belum ada mitra. Klik "Tambah Mitra" untuk memulai.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Modal Tambah/Edit Mitra --}}
<div class="fixed inset-0 bg-slate-900/50 z-50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 [&.active]:opacity-100 [&.active]:pointer-events-auto" id="modal-mitra">
    <div class="bg-white rounded-2xl w-full max-w-lg p-8 shadow-2xl relative transform scale-95 transition-transform duration-300 [&.active]:scale-100">
        <button class="absolute top-6 right-6 text-slate-400 hover:text-slate-700 text-2xl font-bold cursor-pointer bg-transparent border-none" data-close-modal>&times;</button>
        <div class="mb-6">
            <h3 id="modal-mitra-title" class="text-xl font-bold text-slate-800">Tambah Mitra</h3>
            <p class="text-sm text-slate-600 mt-1">Tambahkan data mitra bisnis baru.</p>
        </div>
        <form id="form-mitra">
            <input type="hidden" id="mitra-edit-id" value="">
            <div class="form-group">
                <label>Nama Mitra *</label>
                <input type="text" id="mitra-nama" class="form-control" placeholder="Nama restoran / warung" required>
            </div>
            <div class="form-group">
                <label>Kontak</label>
                <input type="text" id="mitra-kontak" class="form-control" placeholder="0812-xxxx-xxxx">
            </div>
            <div class="form-group">
                <label>Alamat</label>
                <input type="text" id="mitra-alamat" class="form-control" placeholder="Jl. ...">
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" class="btn btn-outline" data-close-modal>Batal</button>
                <button type="submit" class="btn btn-primary" id="btn-submit-mitra">Simpan Mitra</button>
            </div>
        </form>
    </div>
</div>

@endsection
