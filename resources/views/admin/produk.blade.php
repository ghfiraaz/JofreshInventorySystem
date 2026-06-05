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
    <div class="flex items-center gap-3">
        <button id="btn-buka-penyesuaian" class="flex items-center gap-2 px-5 py-2.5 text-white rounded-xl font-semibold text-sm cursor-pointer border-none transition-all shadow-sm" style="background:#eab308;" onmouseover="this.style.background='#ca8a04'" onmouseout="this.style.background='#eab308'">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" /></svg>
            Penyesuaian Stok
        </button>
        <button id="btn-tambah-produk" class="flex items-center gap-2 px-5 py-2.5 text-white rounded-xl font-semibold text-sm cursor-pointer border-none transition-all" style="background:#1e3a5f;" onmouseover="this.style.background='#162d4a'" onmouseout="this.style.background='#1e3a5f'">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Tambah Produk
        </button>
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
                <th class="text-center">Aksi</th>
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
                    <div class="flex items-center justify-center gap-1.5">
                        <button class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors cursor-pointer border-none bg-transparent btn-edit-produk" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"/></svg>
                        </button>
                        <button class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors cursor-pointer border-none bg-transparent btn-delete-produk" title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                        </button>
                        <button class="bg-blue-50 text-blue-700 hover:bg-blue-100 hover:text-blue-800 px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors cursor-pointer border border-blue-200 btn-tambah-stok">
                            + Tambah Stok
                        </button>
                    </div>
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

{{-- Modal Penyesuaian Stok --}}
<div class="fixed inset-0 bg-slate-900/50 z-50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 [&.active]:opacity-100 [&.active]:pointer-events-auto modal-overlay" id="modal-penyesuaian-stok">
    <div class="bg-white rounded-2xl w-full max-w-lg p-8 shadow-2xl relative transform scale-95 transition-transform duration-300 [.active_&]:scale-100" style="max-width: 500px;">
        <button class="absolute top-6 right-6 text-slate-400 hover:text-slate-700 text-2xl font-bold cursor-pointer bg-transparent border-none shadow-none focus:outline-none" onclick="document.getElementById('modal-penyesuaian-stok').classList.remove('active')">&times;</button>
        
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-100 to-amber-200 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#d97706" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" /></svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-800">Penyesuaian Stok</h3>
                <p class="text-xs text-slate-500 mt-0.5">Koreksi jumlah stok jika terjadi kesalahan input</p>
            </div>
        </div>

        <form id="form-adjustment" class="flex flex-col gap-4" onsubmit="return false;">
            <div class="form-group mb-0">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Pilih Produk <span class="text-red-500">*</span></label>
                <select id="adj-produk" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-300 focus:border-amber-500 transition-all bg-slate-50" required>
                    <option value="">— Pilih Produk —</option>
                    @foreach($produk as $p)
                    <option value="{{ $p->id }}" data-stok="{{ $p->stok }}">{{ $p->nama }} (Stok: {{ intval($p->stok) }})</option>
                    @endforeach
                </select>
            </div>

            <div id="adj-stok-info" class="flex items-center gap-2 p-3 bg-blue-50 border border-blue-100 rounded-xl text-xs text-blue-700 font-medium hidden">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" /></svg>
                <span>Stok saat ini: <strong id="adj-stok-value">0</strong></span>
            </div>

            <div class="form-group mb-0">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tipe Penyesuaian <span class="text-red-500">*</span></label>
                <div class="adj-type-selector flex gap-3">
                    <button type="button" class="adj-type-btn btn-tambah flex-1 py-3 px-4 rounded-xl border-2 border-slate-200 bg-slate-50 text-slate-500 font-bold text-sm cursor-pointer transition-all hover:bg-green-50 hover:border-green-300 hover:text-green-600 flex items-center justify-center gap-2" id="adj-type-masuk">
                        <span class="check-icon hidden"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg></span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Tambah Stok
                    </button>
                    <button type="button" class="adj-type-btn btn-kurang flex-1 py-3 px-4 rounded-xl border-2 border-slate-200 bg-slate-50 text-slate-500 font-bold text-sm cursor-pointer transition-all hover:bg-red-50 hover:border-red-300 hover:text-red-600 flex items-center justify-center gap-2" id="adj-type-keluar">
                        <span class="check-icon hidden"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg></span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" /></svg>
                        Kurangi Stok
                    </button>
                </div>
                <input type="hidden" id="adj-tipe" value="">
            </div>

            <div class="form-group mb-0">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Jumlah <span class="text-red-500">*</span></label>
                <input type="number" id="adj-jumlah" min="1" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-300 focus:border-amber-500 transition-all bg-slate-50" placeholder="Masukkan jumlah penyesuaian" required>
            </div>

            <div class="form-group mb-0">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Keterangan / Alasan <span class="text-red-500">*</span></label>
                <textarea id="adj-keterangan" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-300 focus:border-amber-500 transition-all bg-slate-50 resize-y min-h-[80px]" placeholder="Wajib diisi. Contoh: Koreksi salah input kelebihan 10 ekor" required></textarea>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-penyesuaian-stok').classList.remove('active')" class="px-6 py-2.5 rounded-xl font-semibold text-sm text-slate-600 bg-slate-100 hover:bg-slate-200 transition-all cursor-pointer border-none">Batal</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl font-semibold text-sm text-white border-none cursor-pointer transition-all bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 shadow-sm disabled:opacity-50" id="btn-submit-adj">
                    <span id="adj-btn-text">Simpan Penyesuaian</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .adj-type-btn {
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .adj-type-btn .check-icon { display: none; }
    
    .adj-type-btn.btn-tambah { border-color: #a7f3d0; background: #f0fdf4; color: #059669; }
    .adj-type-btn.btn-tambah:hover { border-color: #6ee7b7; background: #dcfce7; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(16,185,129,0.15); }
    .adj-type-btn.btn-tambah.active-masuk {
        border-color: #059669;
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
        box-shadow: 0 0 0 4px rgba(16,185,129,0.2), 0 6px 20px rgba(16,185,129,0.25);
        transform: translateY(-3px) scale(1.02);
    }
    .adj-type-btn.btn-tambah.active-masuk .check-icon { display: inline-flex; }
    
    .adj-type-btn.btn-kurang { border-color: #fecaca; background: #fef2f2; color: #dc2626; }
    .adj-type-btn.btn-kurang:hover { border-color: #fca5a5; background: #fee2e2; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(239,68,68,0.15); }
    .adj-type-btn.btn-kurang.active-keluar {
        border-color: #dc2626;
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #991b1b;
        box-shadow: 0 0 0 4px rgba(239,68,68,0.2), 0 6px 20px rgba(239,68,68,0.25);
        transform: translateY(-3px) scale(1.02);
    }
    .adj-type-btn.btn-kurang.active-keluar .check-icon { display: inline-flex; }
</style>

@endsection
