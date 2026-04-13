@extends('layouts.app')

@section('title', 'Kelola Pengguna')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div class="relative w-1/3 min-w-[250px]">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
        </svg>
        <input type="text" class="w-full pl-11 pr-4 py-2.5 bg-white border border-blue-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-300 focus:border-blue-900 transition-all text-sm" placeholder="Cari pengguna...">
    </div>
    <button class="btn btn-primary flex items-center" id="btn-tambah-pengguna">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-[18px] h-[18px] mr-2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Tambah Pengguna
    </button>
</div>

<div class="table-container">
    <table id="usersTable">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr data-id="{{ $user->id }}">
                <td class="font-bold row-name">{{ $user->name }}</td>
                <td class="row-email">{{ $user->email }}</td>
                <td>
                    <span class="px-3 py-1.5 rounded-full text-xs font-semibold {{ $user->role === 'Admin' ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700' }} row-role">
                        {{ $user->role }}
                    </span>
                </td>
                <td class="row-date">{{ $user->created_at->format('d/m/Y') }}</td>
                <td>
                    <button class="p-2 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors cursor-pointer border-none bg-transparent btn-edit" title="Edit">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[18px] h-[18px]">
                          <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                    </button>
                    <button class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors cursor-pointer border-none bg-transparent ml-2 btn-delete" title="Hapus">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-[18px] h-[18px]">
                          <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </button>
                </td>
            </tr>
            @endforeach
            @if(count($users) == 0)
            <tr>
                <td colspan="5" style="text-align: center; color: #718096">Belum ada pengguna.</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>

<!-- Modal Form Pengguna -->
<div class="fixed inset-0 bg-slate-900/50 z-50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 [&.active]:opacity-100 [&.active]:pointer-events-auto" id="modal-tambah-pengguna">
    <div class="bg-white rounded-2xl w-full max-w-lg p-8 shadow-2xl relative transform scale-95 transition-transform duration-300 [&.active]:scale-100">
        <button class="absolute top-6 right-6 text-slate-400 hover:text-slate-700 text-2xl font-bold cursor-pointer bg-transparent border-none" data-close-modal>&times;</button>
        <div class="mb-6">
            <h3 id="modal-title" class="text-xl font-bold text-slate-800">Tambah Pengguna Baru</h3>
            <p id="modal-desc" class="text-sm text-slate-600 mt-1">Buat akun pengguna baru untuk sistem (Register).</p>
        </div>
        <form id="form-pengguna">
            <input type="hidden" id="edit-row-id" value="">
            <div class="form-group">
                <label>Nama *</label>
                <input type="text" id="user-name" class="form-control" placeholder="Nama Lengkap" required>
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" id="user-email" class="form-control" placeholder="user@jofresh.com" required>
            </div>
            <div class="form-group">
                <label>Password *</label>
                <input type="password" id="user-password" class="form-control" placeholder="Minimal 8 karakter" required>
                <small class="text-slate-500 mt-1" style="display:block; font-size: 0.8rem;" id="password-hint"></small>
            </div>
            <div class="form-group">
                <label>Role</label>
                <select id="user-role" class="form-control" required style="appearance: auto">
                    <option value="Kasir">Kasir</option>
                    <option value="Admin">Admin</option>
                    <option value="Superadmin" hidden>Superadmin</option>
                    <option value="Owner" hidden>Owner</option>
                </select>
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" class="btn btn-outline" data-close-modal>Batal</button>
                <button type="submit" class="btn btn-primary" id="btn-submit-modal">Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>
@endsection
