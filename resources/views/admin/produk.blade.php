@extends('layouts.admin')

@section('title', 'Produk')

@section('content')

@if($stokRendahCount > 0 || $stokHabisCount > 0)
<div class="flex items-center gap-3 bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-yellow-800 mb-6 shadow-sm">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 flex-shrink-0 text-yellow-600">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
    </svg>
    <div>
        <strong class="font-bold">Peringatan Stok</strong><br>
        <span class="text-[0.85rem]">{{ $stokRendahCount }} produk stok rendah dan {{ $stokHabisCount }} produk habis.</span>
    </div>
</div>
@endif

<div class="flex justify-between items-center mb-6">
    <div class="relative w-1/3 min-w-[250px]">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
        </svg>
        <input type="text" id="search-produk" class="w-full pl-11 pr-4 py-2.5 bg-white border border-blue-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-300 focus:border-blue-900 transition-all text-sm" placeholder="Cari produk...">
    </div>
</div>

<div class="table-container">
    <table id="produkTable">
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th>Stok Saat Ini</th>
                <th>Stok Minimal</th>
                <th>Harga (per ekor)</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($produk as $p)
            <tr data-id="{{ $p->id }}">
                <td class="font-bold row-nama">{{ $p->nama }}</td>
                <td class="row-stok">{{ $p->stok }}</td>
                <td class="row-minimal">{{ $p->stok_minimal }}</td>
                <td class="row-harga">{{ $p->harga_format }}</td>
                <td><span class="px-3 py-1.5 rounded-full text-xs font-semibold {{ $p->status === 'Tersedia' ? 'bg-green-100 text-green-700' : ($p->status === 'Stok Rendah' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">{{ $p->status }}</span></td>
                <td>
                    <button class="bg-blue-50 text-blue-700 hover:bg-blue-100 hover:text-blue-800 px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors cursor-pointer border border-blue-200 btn-tambah-stok">
                        + Tambah Stok
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center; color: #718096; padding: 3rem;">
                    Belum ada produk. Klik "Tambah Produk" untuk memulai.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Modal Tambah Stok --}}
<div class="fixed inset-0 bg-slate-900/50 z-50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 [&.active]:opacity-100 [&.active]:pointer-events-auto modal-overlay" id="modal-tambah-stok">
    <div class="bg-white rounded-2xl w-full max-w-sm p-8 shadow-2xl relative transform scale-95 transition-transform duration-300 [&.active]:scale-100">
        <button class="absolute top-6 right-6 text-slate-400 hover:text-slate-700 text-2xl font-bold cursor-pointer bg-transparent border-none" data-close-modal>&times;</button>
        <div class="mb-6">
            <h3 class="text-xl font-bold text-slate-800">Tambah Stok</h3>
            <p class="text-sm text-slate-600 mt-1">Stok baru untuk <strong id="stok-nama-produk" class="text-blue-700"></strong></p>
        </div>
        <form id="form-tambah-stok">
            <input type="hidden" id="stok-produk-id" value="">
            <div class="flex flex-col gap-4">
                <div class="form-group mb-0">
                    <label>Jumlah (ekor) *</label>
                    <input type="number" id="stok-jumlah-input" class="form-control" placeholder="Contoh: 100" required min="1">
                </div>
                <div class="form-group mb-0">
                    <label>Tanggal Pencatatan</label>
                    <input type="date" id="stok-tanggal-input" class="form-control bg-slate-100 text-slate-600 cursor-not-allowed" value="{{ date('Y-m-d') }}" readonly>
                    <small class="text-slate-500 mt-1 block">Tanggal pencatatan stok dikunci pada hari ini.</small>
                </div>
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" class="btn btn-outline" data-close-modal>Batal</button>
                <button type="submit" class="btn btn-primary" id="btn-submit-stok">Simpan Stok</button>
            </div>
        </form>
    </div>
</div>

@endsection
