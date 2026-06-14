@extends('layouts.admin')
@section('title', 'Mitra')
@section('content')

<div class="flex justify-end items-center mb-6">
    <button id="btn-tambah-mitra" class="flex items-center gap-2 px-5 py-2.5 text-white rounded-xl font-semibold text-sm cursor-pointer border-none transition-all" style="background:#7B3911;" onmouseover="this.style.background='#5A270B'" onmouseout="this.style.background='#7B3911'">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Mitra
    </button>
</div>

<div class="rounded-2xl overflow-hidden border border-slate-200 shadow-sm">
    <table class="w-full" id="mitraTable">
        <thead>
            <tr style="background:linear-gradient(135deg,#f8fafc,#f1f5f9);">
                <th class="py-3.5 px-5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Nama Mitra</th>
                <th class="py-3.5 px-5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Email</th>
                <th class="py-3.5 px-5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Kontak</th>
                <th class="py-3.5 px-5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Alamat</th>
                <th class="py-3.5 px-5 text-center text-xs font-bold uppercase tracking-wider text-slate-500">Jatuh Tempo</th>
                <th class="py-3.5 px-5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
            @forelse($mitra as $m)
            <tr data-id="{{ $m->id }}" class="hover:bg-slate-50/60 transition-colors">
                <td class="py-3.5 px-5 font-semibold text-slate-800 text-sm row-nama-mitra">{{ $m->nama }}</td>
                <td class="py-3.5 px-5 text-sm text-slate-600 row-email">{{ $m->email ?? '-' }}</td>
                <td class="py-3.5 px-5 text-sm text-slate-600 row-kontak">{{ $m->kontak ?? '-' }}</td>
                <td class="py-3.5 px-5 text-sm text-slate-600 row-alamat">{{ $m->alamat ?? '-' }}</td>
                <td class="py-3.5 px-5 text-sm text-center row-jatuh-tempo">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-[#FAF0E6] text-[#7B3911]">Tgl {{ $m->tanggal_jatuh_tempo }}</span>
                </td>
                <td class="py-3.5 px-5">
                    <div class="flex items-center gap-1">
                        <button class="p-2 text-slate-400 hover:text-[#7B3911] hover:bg-[#FAF5EF] rounded-lg transition-colors cursor-pointer border-none bg-transparent btn-edit-mitra" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"/></svg>
                        </button>
                        <button class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors cursor-pointer border-none bg-transparent btn-delete-mitra" title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="py-16 text-center text-slate-400 text-sm">Belum ada mitra terdaftar.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Modal --}}
<div class="fixed inset-0 bg-slate-900/50 z-50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 modal-overlay [&.active]:opacity-100 [&.active]:pointer-events-auto" id="modal-mitra">
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl relative overflow-hidden transform scale-95 transition-transform duration-300 [.active_&]:scale-100">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-[#7B3911] to-[#A1511E]"></div>
        <div class="px-8 pt-8 pb-2 flex justify-between items-start">
            <div>
                <h3 id="modal-mitra-title" class="text-xl font-bold text-slate-800">Tambah Mitra</h3>
                <p class="text-sm text-slate-500 mt-1">Tambahkan data mitra bisnis baru.</p>
            </div>
            <button type="button" onclick="document.getElementById('modal-mitra').classList.remove('active')" class="text-slate-400 hover:text-slate-700 text-2xl font-bold cursor-pointer bg-transparent border-none leading-none mt-1">&times;</button>
        </div>
        <form id="form-mitra" class="px-8 pb-8 pt-4">
            <input type="hidden" id="mitra-edit-id" value="">
            <div class="form-group">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama Mitra *</label>
                <input type="text" id="mitra-nama" placeholder="Nama restoran / warung" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#7B3911]/20 focus:border-[#7B3911] transition-all">
                <div class="error-msg text-red-500 text-xs mt-1.5 hidden" id="error-mitra-nama"></div>
            </div>
            <div class="form-group">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Email Mitra</label>
                <input type="email" id="mitra-email" placeholder="email@gmail.com" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#7B3911]/20 focus:border-[#7B3911] transition-all">
                <div class="error-msg text-red-500 text-xs mt-1.5 hidden" id="error-mitra-email"></div>
            </div>
            <div class="flex gap-4">
                <div class="form-group flex-1">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kontak / HP</label>
                    <input type="text" id="mitra-kontak" placeholder="0812-xxxx-xxxx" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#7B3911]/20 focus:border-[#7B3911] transition-all">
                    <div class="error-msg text-red-500 text-xs mt-1.5 hidden" id="error-mitra-kontak"></div>
                </div>
                <div class="form-group flex-1">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tanggal Jatuh Tempo *</label>
                    <select id="mitra-jatuh-tempo" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#7B3911]/20 focus:border-[#7B3911] transition-all bg-white cursor-pointer">
                        @for($i=1; $i<=31; $i++)
                            <option value="{{ $i }}">Tgl {{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Alamat Lengkap *</label>
                <input type="text" id="mitra-alamat" placeholder="Jl. ..." required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#7B3911]/20 focus:border-[#7B3911] transition-all">
                <div class="error-msg text-red-500 text-xs mt-1.5 hidden" id="error-mitra-alamat"></div>
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-mitra').classList.remove('active')" class="px-6 py-2.5 rounded-xl font-semibold text-sm text-slate-600 bg-slate-100 hover:bg-slate-200 transition-all cursor-pointer border-none">Batal</button>
                <button type="submit" id="btn-submit-mitra" class="px-6 py-2.5 rounded-xl font-semibold text-sm text-white border-none cursor-pointer transition-all" style="background:#7B3911;" onmouseover="this.style.background='#5A270B'" onmouseout="this.style.background='#7B3911'">Simpan Mitra</button>
            </div>
        </form>
    </div>
</div>

<script>
// Backdrop close
document.getElementById('modal-mitra').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('active');
});
</script>

@endsection
