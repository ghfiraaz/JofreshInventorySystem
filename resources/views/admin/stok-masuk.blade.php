@extends('layouts.admin')

@section('title', 'Stok Masuk')

@section('content')

<div class="card" style="display:block; padding: 2rem;">
    <h3 class="fw-bold-700 mb-4">Catat Stok Masuk</h3>
    <form id="form-stok-masuk">
        <div class="form-grid-2">
            <div class="form-group">
                <label>Produk *</label>
                <select id="stok-produk" class="form-control" style="appearance:auto" required>
                    <option value="" disabled selected>Pilih produk</option>
                    @foreach(\App\Models\Produk::orderBy('nama')->get() as $p)
                        <option value="{{ $p->nama }}">{{ $p->nama }}</option>
                    @endforeach
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
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Catatan</label>
                <textarea id="stok-catatan" class="form-control" placeholder="Catatan tambahan..." rows="3" style="resize:vertical;"></textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Catat Stok Masuk</button>
    </form>
</div>

@endsection
