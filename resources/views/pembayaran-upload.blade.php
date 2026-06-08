<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Tagihan - JoFresh</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body { background: linear-gradient(135deg, #FAF8F5 0%, #F5F0EB 50%, #FFF8F0 100%); min-height: 100vh; font-family: 'Inter', sans-serif; }
        .upload-zone { border: 2px dashed #E0C4A8; border-radius: 16px; padding: 40px 20px; text-align: center; cursor: pointer; transition: all 0.3s; background: #FAF8F5; }
        .upload-zone:hover, .upload-zone.dragover { border-color: #7B3911; background: #FAF5EF; }
        .upload-zone.has-file { border-color: #22c55e; background: #f0fdf4; }
    </style>
</head>
<body class="antialiased">
<div class="max-w-2xl mx-auto py-10 px-4">

    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="flex justify-center mb-3">
            <img src="{{ asset('images/logo-jofresh.png') }}" alt="JoFresh Logo" class="h-16 w-auto object-contain">
        </div>
        <p class="text-sm text-gray-500 font-medium">JoFresh Inventory System</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-2xl p-8 text-center mb-8">
            <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-8 h-8 text-white"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
            </div>
            <h2 class="text-xl font-bold text-green-800 mb-2">Bukti Pembayaran Berhasil Diupload!</h2>
            <p class="text-sm text-green-600">Terima kasih, bukti pembayaran Anda sedang diverifikasi oleh tim kami.</p>
        </div>
    @else
        {{-- Rejected Payment Notice --}}
        @if(isset($hasDitolak) && $hasDitolak)
            <div class="bg-red-50 border border-red-200 rounded-2xl p-6 mb-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-white"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-red-800">Bukti Pembayaran Ditolak</h3>
                        <p class="text-sm text-red-600">Bukti pembayaran sebelumnya tidak valid. Silakan upload ulang bukti pembayaran yang benar.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Check if there are any outstanding tagihan --}}
        @if($transaksiUnpaid->isEmpty())
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 text-center mb-6">
                <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-8 h-8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                </div>
                <h2 class="text-xl font-bold text-slate-800 mb-2">Tagihan Anda Lunas!</h2>
                <p class="text-sm text-slate-500">Semua tagihan Anda telah terbayar lunas. Terima kasih atas kerja samanya!</p>
            </div>
        @else
            {{-- Mitra Info --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-[#7B3911] to-[#A1511E] p-6 text-white">
                    <h2 class="text-xl font-bold mb-1">Tagihan Pembayaran</h2>
                    <p class="text-white/80 text-sm">{{ $mitra->nama }}</p>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Total Tagihan</span>
                        <span class="text-2xl font-extrabold text-gray-800">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</span>
                    </div>
                    <div class="text-sm text-gray-500 mb-1">Jumlah Transaksi: <span class="font-bold text-gray-800">{{ $transaksiUnpaid->count() }}</span></div>
                </div>
            </div>

            {{-- Invoice List --}}
            @if($transaksiUnpaid->count() > 0)
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Daftar Invoice</h3>
                <div class="space-y-3">
                    @foreach($transaksiUnpaid as $tx)
                    <div class="flex justify-between items-center p-3 rounded-lg bg-gray-50 border border-gray-100">
                        <div>
                            <div class="text-sm font-bold text-[#7B3911]">{{ $tx->no_transaksi }}</div>
                            <div class="text-xs text-gray-400">{{ $tx->created_at->format('d/m/Y') }} • {{ $tx->total_item }} item</div>
                            @if($tx->status_pembayaran === 'Menunggu Validasi')
                                <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-700">Menunggu Validasi</span>
                            @elseif($tx->status_pembayaran === 'Ditolak')
                                <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-700">Ditolak (Tidak Valid)</span>
                            @else
                                <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600">Belum Dibayar</span>
                            @endif
                        </div>
                        <div class="text-sm font-bold text-gray-800">Rp {{ number_format($tx->total_harga, 0, ',', '.') }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Payment Info --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Informasi Pembayaran</h3>
                
                {{-- Bank Info --}}
                <div class="p-4 rounded-xl bg-[#FAF5EF] border border-[#E0D5CA] mb-4">
                    <div class="text-xs font-bold text-[#8B4513] uppercase tracking-wider mb-2">Transfer Bank</div>
                    <div class="text-sm text-gray-700 mb-1">Bank: <span class="font-bold">BCA</span></div>
                    <div class="text-sm text-gray-700 mb-1">No. Rekening: <span class="font-bold">7380582030</span></div>
                    <div class="text-sm text-gray-700">Atas Nama: <span class="font-bold">JoFresh</span></div>
                </div>

                {{-- QRIS --}}
                <div class="text-center p-4 rounded-xl bg-gray-50 border border-gray-200">
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">QRIS Pembayaran</div>
                    <div class="bg-white p-3 rounded-xl inline-block border border-gray-200 shadow-sm">
                        <img src="{{ asset('images/qris-jofresh.jpg') }}" alt="QRIS JoFresh" class="w-48 h-48 object-contain">
                    </div>
                </div>
            </div>

            {{-- Upload Bukti Pembayaran --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Upload Bukti Pembayaran</h3>
                
                @if($mitra->payment_upload_locked || $transaksiUnpaid->contains('status_pembayaran', 'Menunggu Validasi'))
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-center">
                        <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                        </div>
                        <h4 class="text-sm font-bold text-amber-800 mb-1">Upload Terkunci</h4>
                        <p class="text-xs text-amber-600 leading-relaxed mb-4">Anda telah mengunggah bukti pembayaran. Tombol upload terkunci sementara menunggu proses verifikasi/validasi oleh tim Jofresh.</p>
                        
                        <div class="border border-dashed border-gray-200 rounded-xl p-4 bg-gray-50 text-gray-400 text-xs">
                            Kirim Bukti Pembayaran dinonaktifkan
                        </div>
                    </div>
                @else
                    <form action="{{ route('pembayaran.store', $token) }}" method="POST" enctype="multipart/form-data" id="form-upload">
                        @csrf
                        <div class="upload-zone mb-4" id="upload-zone" onclick="document.getElementById('file-input').click()">
                            <input type="file" name="bukti_pembayaran" id="file-input" accept=".jpg,.jpeg,.png,.pdf" class="hidden" required>
                            <div id="upload-placeholder">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-[#E0C4A8] mx-auto mb-3"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" /></svg>
                                <p class="text-sm font-semibold text-gray-600 mb-1">Klik atau drag file ke sini</p>
                                <p class="text-xs text-gray-400">Format: JPG, PNG, PDF (maks. 5MB)</p>
                            </div>
                            <div id="upload-preview" class="hidden">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-green-500 mx-auto mb-2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                <p class="text-sm font-bold text-green-700" id="file-name">file.jpg</p>
                                <p class="text-xs text-gray-400 mt-1">Klik untuk ganti file</p>
                            </div>
                        </div>

                        @if($errors->has('bukti_pembayaran'))
                            <p class="text-sm text-red-500 mb-4">{{ $errors->first('bukti_pembayaran') }}</p>
                        @endif

                        <button type="submit" id="btn-submit" disabled class="w-full py-3.5 bg-[#7B3911] hover:bg-[#5A270B] text-white font-bold rounded-xl transition-all shadow-lg shadow-[#7B3911]/20 flex items-center justify-center gap-2 cursor-pointer border-none disabled:opacity-40 disabled:cursor-not-allowed">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                            <span>Kirim Bukti Pembayaran</span>
                        </button>
                    </form>
                @endif
            </div>
        @endif
    @endif

    <div class="text-center mt-8 text-xs text-gray-400">
        &copy; {{ date('Y') }} JoFresh Inventory System
    </div>
</div>

<script>
const fileInput = document.getElementById('file-input');
const zone = document.getElementById('upload-zone');
const placeholder = document.getElementById('upload-placeholder');
const preview = document.getElementById('upload-preview');
const fileName = document.getElementById('file-name');
const btnSubmit = document.getElementById('btn-submit');

if (fileInput) {
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            placeholder.classList.add('hidden');
            preview.classList.remove('hidden');
            fileName.textContent = this.files[0].name;
            zone.classList.add('has-file');
            btnSubmit.disabled = false;
        }
    });

    // Drag and drop
    zone.addEventListener('dragover', function(e) { e.preventDefault(); this.classList.add('dragover'); });
    zone.addEventListener('dragleave', function() { this.classList.remove('dragover'); });
    zone.addEventListener('drop', function(e) {
        e.preventDefault(); this.classList.remove('dragover');
        fileInput.files = e.dataTransfer.files;
        fileInput.dispatchEvent(new Event('change'));
    });
}
</script>
</body>
</html>
