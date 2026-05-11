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
    <button id="btn-tambah-produk" class="flex items-center gap-2 px-5 py-2.5 text-white rounded-xl font-semibold text-sm cursor-pointer border-none transition-all" style="background:#1e3a5f;" onmouseover="this.style.background='#162d4a'" onmouseout="this.style.background='#1e3a5f'">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Produk
    </button>
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
                <td class="row-stok">{{ intval($p->stok) }}</td>
                <td class="row-minimal">{{ intval($p->stok_minimal) }}</td>
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

{{-- Modal Tambah / Edit Produk --}}
<div class="fixed inset-0 bg-slate-900/50 z-50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 [&.active]:opacity-100 [&.active]:pointer-events-auto modal-overlay" id="modal-produk">
    <div class="bg-white rounded-2xl w-full max-w-md p-8 shadow-2xl relative transform scale-95 transition-transform duration-300 [.active_&]:scale-100">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-900 to-indigo-600"></div>
        <button class="absolute top-6 right-6 text-slate-400 hover:text-slate-700 text-2xl font-bold cursor-pointer bg-transparent border-none" onclick="document.getElementById('modal-produk').classList.remove('active')">&times;</button>
        <div class="mb-6">
            <h3 id="modal-produk-title" class="text-xl font-bold text-slate-800">Tambah Produk</h3>
            <p id="modal-produk-desc" class="text-sm text-slate-500 mt-1">Tambahkan produk baru ke inventaris.</p>
        </div>
        <form id="form-produk">
            <input type="hidden" id="produk-edit-id" value="">
            <div class="flex flex-col gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama Produk <span class="text-red-500">*</span></label>
                    <input type="text" id="produk-nama" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-500 transition-all" placeholder="Contoh: Ayam Broiler" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Harga (per ekor) <span class="text-red-500">*</span></label>
                    <input type="number" id="produk-harga" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-500 transition-all" placeholder="Contoh: 45000" required min="0">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Stok Minimal</label>
                    <input type="number" id="produk-minimal" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-500 transition-all" placeholder="Contoh: 50" min="0" value="0">
                    <small class="text-slate-400 mt-1 block">Sistem akan memberi peringatan jika stok di bawah nilai ini.</small>
                </div>
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-produk').classList.remove('active')" class="px-6 py-2.5 rounded-xl font-semibold text-sm text-slate-600 bg-slate-100 hover:bg-slate-200 transition-all cursor-pointer border-none">Batal</button>
                <button type="submit" id="btn-submit-produk" class="px-6 py-2.5 rounded-xl font-semibold text-sm text-white border-none cursor-pointer transition-all" style="background:#1e3a5f;" onmouseover="this.style.background='#162d4a'" onmouseout="this.style.background='#1e3a5f'">Simpan Produk</button>
            </div>
        </form>
    </div>
</div>

@endsection
