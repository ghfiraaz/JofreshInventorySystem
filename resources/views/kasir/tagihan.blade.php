@extends('layouts.kasir')
@section('title', 'Belum Dibayar')
@section('content')

{{-- ===== HEADING ===== --}}
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-800 mb-1">Tagihan Mitra</h2>
    <p class="text-sm text-gray-400">Kelola tagihan mitra, kirim reminder via email, dan validasi pembayaran</p>
</div>

{{-- ===== SUMMARY CARDS ===== --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center justify-between">
        <div>
            <div class="text-xs text-gray-400 font-medium mb-1">Total Mitra</div>
            <div class="text-2xl font-extrabold text-gray-800">{{ $totalMitra }}</div>
        </div>
        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-blue-600"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" /></svg>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center justify-between">
        <div>
            <div class="text-xs text-gray-400 font-medium mb-1">Total Tagihan</div>
            <div class="text-2xl font-extrabold text-gray-800">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</div>
        </div>
        <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-amber-600"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center justify-between">
        <div>
            <div class="text-xs text-gray-400 font-medium mb-1">Menunggu Validasi</div>
            <div class="text-2xl font-extrabold text-gray-800">{{ $menungguValidasi }}</div>
        </div>
        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-indigo-600"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
        </div>
    </div>
</div>

{{-- ===== TAGIHAN CONTENT ===== --}}
<div class="bg-white rounded-xl border border-gray-200 p-6">
    <h3 class="text-base font-bold text-gray-800 mb-5">Daftar Tagihan Mitra</h3>

    @if(count($mitraTagihan) === 0)
        <div class="flex flex-col items-center justify-center py-16 text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-16 h-16 mb-3 text-gray-300"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
            <span class="text-sm">Semua tagihan sudah lunas! 🎉</span>
        </div>
    @else
        <div class="flex flex-col gap-3">
            @foreach($mitraTagihan as $mt)
                @php
                    $borderColor = 'border-gray-200';
                    $bgColor = '';
                    $tempoLabel = '';
                    $tempoClass = 'text-gray-500';

                    if ($mt['sisaHari'] !== null) {
                        if ($mt['sisaHari'] < 0) {
                            $borderColor = 'border-red-300';
                            $bgColor = 'bg-red-50/50';
                            $tempoLabel = 'Lewat ' . abs($mt['sisaHari']) . ' hari!';
                            $tempoClass = 'text-red-600 font-bold';
                        } elseif ($mt['sisaHari'] == 0) {
                            $borderColor = 'border-red-300';
                            $bgColor = 'bg-red-50/50';
                            $tempoLabel = 'Jatuh tempo hari ini!';
                            $tempoClass = 'text-red-600 font-bold';
                        } elseif ($mt['sisaHari'] == 1) {
                            $borderColor = 'border-red-200';
                            $bgColor = 'bg-red-50/30';
                            $tempoLabel = 'Jatuh tempo besok';
                            $tempoClass = 'text-red-500 font-bold';
                        } elseif ($mt['sisaHari'] <= 3) {
                            $borderColor = 'border-amber-200';
                            $bgColor = 'bg-amber-50/30';
                            $tempoLabel = 'Sisa ' . $mt['sisaHari'] . ' hari';
                            $tempoClass = 'text-amber-600 font-semibold';
                        } else {
                            $tempoLabel = 'Sisa ' . $mt['sisaHari'] . ' hari';
                            $tempoClass = 'text-gray-500';
                        }
                    }

                    // Check if any transaction has "Menunggu Validasi" status
                    $hasWaitingValidation = $mt['transaksi']->contains('status_pembayaran', 'Menunggu Validasi');

                    // H-3 logic: reminder hanya boleh dikirim jika sisa hari <= 3 (termasuk lewat tempo)
                    $canSendReminder = $mt['sisaHari'] !== null && $mt['sisaHari'] <= 3;
                @endphp

                <div class="border {{ $borderColor }} {{ $bgColor }} rounded-lg overflow-hidden">
                    {{-- Mitra Header --}}
                    <div class="tagihan-mitra-header flex items-center justify-between px-5 py-4 cursor-pointer hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full {{ $mt['isTempoMerah'] ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }} flex items-center justify-center font-bold text-sm flex-shrink-0">
                                {{ strtoupper(substr($mt['mitra']->nama, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-sm text-gray-800">{{ $mt['mitra']->nama }}</div>
                                <div class="text-xs text-gray-400">
                                    {{ $mt['count'] }} transaksi
                                    @if($mt['closestTempo'])
                                        • Tempo: {{ \Carbon\Carbon::parse($mt['closestTempo'])->format('d/m/Y') }}
                                    @endif
                                </div>
                                @if($tempoLabel)
                                    <div class="text-xs {{ $tempoClass }} mt-0.5 flex items-center gap-1">
                                        @if($mt['sisaHari'] !== null && $mt['sisaHari'] <= 3)
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                        @endif
                                        {{ $tempoLabel }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-bold text-gray-800">Rp {{ number_format($mt['total'], 0, ',', '.') }}</span>

                            {{-- Terima/Tolak buttons (if waiting validation) --}}
                            @if($hasWaitingValidation)
                                <button type="button" class="btn-validasi-mitra px-3 py-1.5 bg-emerald-100 text-emerald-700 hover:bg-emerald-600 hover:text-white rounded-lg text-xs font-bold transition-colors shadow-sm cursor-pointer border-none"
                                    data-mitra="{{ $mt['mitra']->id }}"
                                    data-action="terima"
                                    onclick="event.stopPropagation(); validasiMitra(this)"
                                    title="Terima pembayaran">
                                    ✓ Terima
                                </button>
                                <button type="button" class="btn-tolak-mitra px-3 py-1.5 bg-red-100 text-red-700 hover:bg-red-600 hover:text-white rounded-lg text-xs font-bold transition-colors shadow-sm cursor-pointer border-none"
                                    data-mitra="{{ $mt['mitra']->id }}"
                                    data-action="tolak"
                                    onclick="event.stopPropagation(); validasiMitra(this)"
                                    title="Tolak pembayaran">
                                    ✗ Tolak
                                </button>
                            @endif

                            {{-- Reminder button - available for all mitra with email --}}
                            @if($mt['mitra']->email)
                            @php
                                $reminderDisabled = $mt['reminderSentToday'] ?? false;
                            @endphp
                                <button type="button" class="btn-send-reminder px-3 py-1.5 rounded-lg text-xs font-bold transition-colors shadow-sm cursor-pointer border-none
                                    {{ $reminderDisabled ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-amber-100 text-amber-700 hover:bg-amber-600 hover:text-white' }}"
                                    data-mitra="{{ $mt['mitra']->id }}"
                                    data-nama="{{ $mt['mitra']->nama }}"
                                    data-email="{{ $mt['mitra']->email }}"
                                    {{ $mt['reminderSentToday'] ? 'disabled' : '' }}
                                    onclick="event.stopPropagation(); sendReminder(this)"
                                    title="{{ $mt['reminderSentToday'] ? 'Reminder sudah dikirim hari ini' : 'Kirim reminder via email ke ' . $mt['mitra']->email }}">
                                    <span class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                                        {{ $mt['reminderSentToday'] ? 'Terkirim' : ($canSendReminder ? 'Reminder' : 'H-'.($mt['sisaHari'] ?? '?')) }}
                                    </span>
                                </button>
                            @else
                                <span class="text-xs text-gray-400 italic">Email belum diisi</span>
                            @endif

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-400 tagihan-expand-icon transition-transform duration-200"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                        </div>
                    </div>

                    {{-- Mitra Detail (hidden) --}}
                    <div class="tagihan-mitra-detail hidden border-t border-gray-100 px-5 py-4 bg-gray-50">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="text-left pb-2 text-xs text-gray-400 font-medium">Tanggal</th>
                                    <th class="text-left pb-2 text-xs text-gray-400 font-medium">No. Transaksi</th>
                                    <th class="text-center pb-2 text-xs text-gray-400 font-medium">Item</th>
                                    <th class="text-center pb-2 text-xs text-gray-400 font-medium">Status</th>
                                    <th class="text-right pb-2 text-xs text-gray-400 font-medium">Total</th>
                                    <th class="text-center pb-2 text-xs text-gray-400 font-medium">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mt['transaksi'] as $tx)
                                    <tr>
                                        <td class="py-1.5 text-sm text-gray-600">{{ $tx->created_at->format('d/m/Y') }}</td>
                                        <td class="py-1.5 text-sm font-medium text-gray-700">{{ $tx->no_transaksi }}</td>
                                        <td class="py-1.5 text-sm text-gray-600 text-center">{{ $tx->total_item }}</td>
                                        <td class="py-1.5 text-center">
                                            @if($tx->status_pembayaran === 'Menunggu Validasi')
                                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700">Menunggu Validasi</span>
                                            @else
                                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700">Belum Dibayar</span>
                                            @endif
                                        </td>
                                        <td class="py-1.5 text-sm font-semibold text-gray-800 text-right">Rp {{ number_format($tx->total_harga, 0, ',', '.') }}</td>
                                        <td class="py-1.5 text-center">
                                            <a href="{{ url('/kasir/transaksi/'.$tx->id.'/invoice') }}" target="_blank" class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all" title="Lihat Detail">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @if($hasWaitingValidation)
                            @php
                                $buktiPath = $mt['transaksi']->first(fn($t) => $t->bukti_pembayaran)?->bukti_pembayaran;
                            @endphp
                            @if($buktiPath)
                                @php
                                    $buktiFilename = basename($buktiPath);
                                    $buktiUrl = url('/kasir/bukti-pembayaran/' . $buktiFilename);
                                    $isImage = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $buktiPath);
                                @endphp
                                <div class="mt-4 p-4 bg-indigo-50 border border-indigo-100 rounded-lg">
                                    <div class="text-xs font-bold text-indigo-600 mb-3">Bukti Pembayaran:</div>
                                    @if($isImage)
                                        <div class="mb-3">
                                            <img src="{{ $buktiUrl }}" alt="Bukti Pembayaran" class="max-w-xs max-h-48 rounded-lg border border-indigo-200 shadow-sm cursor-pointer hover:opacity-90 transition-opacity" onclick="window.open('{{ $buktiUrl }}', '_blank')">
                                        </div>
                                    @endif
                                    <a href="{{ $buktiUrl }}" target="_blank" class="inline-flex items-center gap-1.5 text-sm text-indigo-700 font-semibold hover:text-indigo-900 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                                        Buka Bukti Pembayaran
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ===== TOAST NOTIFICATION (CENTERED) ===== --}}
<div id="toast-notification" class="fixed inset-0 z-50 flex items-center justify-center pointer-events-none" style="display:none;">
    <div class="rounded-2xl shadow-2xl border overflow-hidden max-w-sm w-full mx-4 pointer-events-auto transform scale-95 opacity-0 transition-all duration-300" id="toast-inner">
        <div class="px-6 py-5 flex flex-col items-center text-center gap-3">
            <div id="toast-icon" class="flex-shrink-0"></div>
            <div>
                <p id="toast-title" class="font-bold text-base mb-1"></p>
                <p id="toast-message" class="text-sm opacity-80"></p>
            </div>
            <button onclick="hideToast()" class="mt-2 px-6 py-2 rounded-xl font-semibold text-sm cursor-pointer border-none transition-all text-white" style="background:#1e3a5f;" onmouseover="this.style.background='#162d4a'" onmouseout="this.style.background='#1e3a5f'">OK</button>
        </div>
    </div>
</div>

{{-- ===== CONFIRMATION MODAL (CENTERED) ===== --}}
<div id="modal-confirm-tagihan" class="fixed inset-0 bg-slate-900/50 z-50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 [&.active]:opacity-100 [&.active]:pointer-events-auto">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl relative overflow-hidden transform scale-95 transition-transform duration-300 [.active_&]:scale-100">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-900 to-indigo-600"></div>
        <div class="px-8 pt-8 pb-2 flex justify-between items-start">
            <div>
                <h3 id="confirm-tagihan-title" class="text-lg font-bold text-slate-800"></h3>
            </div>
            <button type="button" onclick="closeConfirmModal()" class="text-slate-400 hover:text-slate-700 text-2xl font-bold cursor-pointer bg-transparent border-none leading-none mt-1">&times;</button>
        </div>
        <div class="px-8 pb-3">
            <p id="confirm-tagihan-message" class="text-sm text-slate-600 leading-relaxed"></p>
            <div id="confirm-tagihan-detail" class="mt-3 p-3 bg-slate-50 rounded-xl text-sm text-slate-700 hidden"></div>
        </div>
        <div class="px-8 pb-8 pt-4 flex justify-end gap-3">
            <button type="button" onclick="closeConfirmModal()" class="px-6 py-2.5 rounded-xl font-semibold text-sm text-slate-600 bg-slate-100 hover:bg-slate-200 transition-all cursor-pointer border-none">Batal</button>
            <button type="button" id="confirm-tagihan-yes" class="px-6 py-2.5 rounded-xl font-semibold text-sm text-white border-none cursor-pointer transition-all" style="background:#1e3a5f;" onmouseover="this.style.background='#162d4a'" onmouseout="this.style.background='#1e3a5f'">Ya, Kirim</button>
        </div>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

// ========== Toast (centered) ==========
function showToast(type, title, message) {
    const toast = document.getElementById('toast-notification');
    const inner = document.getElementById('toast-inner');
    const iconEl = document.getElementById('toast-icon');
    const titleEl = document.getElementById('toast-title');
    const msgEl = document.getElementById('toast-message');

    titleEl.textContent = title;
    msgEl.textContent = message;

    if (type === 'success') {
        inner.className = 'rounded-2xl shadow-2xl border overflow-hidden max-w-sm w-full mx-4 pointer-events-auto transform transition-all duration-300 bg-white border-emerald-200 text-emerald-800';
        iconEl.innerHTML = '<div class="w-14 h-14 rounded-full bg-emerald-100 flex items-center justify-center"><svg class="w-7 h-7 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg></div>';
    } else {
        inner.className = 'rounded-2xl shadow-2xl border overflow-hidden max-w-sm w-full mx-4 pointer-events-auto transform transition-all duration-300 bg-white border-red-200 text-red-800';
        iconEl.innerHTML = '<div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center"><svg class="w-7 h-7 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg></div>';
    }

    toast.style.display = 'flex';
    requestAnimationFrame(() => {
        inner.classList.remove('scale-95', 'opacity-0');
        inner.classList.add('scale-100', 'opacity-100');
    });

    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(hideToast, 5000);
}

function hideToast() {
    const toast = document.getElementById('toast-notification');
    const inner = document.getElementById('toast-inner');
    inner.classList.add('scale-95', 'opacity-0');
    inner.classList.remove('scale-100', 'opacity-100');
    setTimeout(() => { toast.style.display = 'none'; }, 300);
}

// ========== Confirm Modal ==========
let _confirmCallback = null;

function showConfirmTagihan(title, message, detail, yesLabel, onYes) {
    const modal = document.getElementById('modal-confirm-tagihan');
    document.getElementById('confirm-tagihan-title').textContent = title;
    document.getElementById('confirm-tagihan-message').textContent = message;
    const detailEl = document.getElementById('confirm-tagihan-detail');
    const yesBtn = document.getElementById('confirm-tagihan-yes');
    yesBtn.textContent = yesLabel || 'Ya, Lanjutkan';
    if (detail) {
        detailEl.innerHTML = detail;
        detailEl.classList.remove('hidden');
    } else {
        detailEl.classList.add('hidden');
    }
    _confirmCallback = onYes;
    modal.classList.add('active');
}

function closeConfirmModal() {
    document.getElementById('modal-confirm-tagihan').classList.remove('active');
    _confirmCallback = null;
}

document.getElementById('confirm-tagihan-yes')?.addEventListener('click', () => {
    if (_confirmCallback) _confirmCallback();
    closeConfirmModal();
});

document.getElementById('modal-confirm-tagihan')?.addEventListener('click', (e) => {
    if (e.target === e.currentTarget) closeConfirmModal();
});

// ========== Send Reminder ==========
function sendReminder(btn) {
    const mitraId = btn.getAttribute('data-mitra');
    const mitraName = btn.getAttribute('data-nama');
    const mitraEmail = btn.getAttribute('data-email');

    showConfirmTagihan(
        'Kirim Reminder Pembayaran',
        `Kirim email reminder pembayaran ke ${mitraName} (${mitraEmail})?`,
        '<div style="line-height:1.8;">Email akan berisi:<br>• Rekapitulasi tagihan 1 bulan<br>• PDF Invoice sebagai lampiran<br>• QR Code pembayaran</div>',
        'Ya, Kirim Email',
        () => {
            btn.disabled = true;
            const originalContent = btn.querySelector('span').innerHTML;
            btn.querySelector('span').innerHTML = `<svg class="animate-spin w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Mengirim...`;

            fetch('/kasir/tagihan/send-reminder', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ mitra_id: mitraId })
            })
            .then(r => r.json().then(data => ({ ok: r.ok, data })))
            .then(({ ok, data }) => {
                if (ok) {
                    btn.classList.remove('bg-amber-100', 'text-amber-700', 'hover:bg-amber-600', 'hover:text-white');
                    btn.classList.add('bg-emerald-100', 'text-emerald-700', 'cursor-not-allowed');
                    btn.querySelector('span').innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg> Terkirim`;
                    showToast('success', 'Reminder Terkirim!', data.message);
                } else {
                    throw data;
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.querySelector('span').innerHTML = originalContent;
                showToast('error', 'Gagal Mengirim', err.message || 'Terjadi kesalahan saat mengirim reminder');
            });
        }
    );
}

// ========== Validasi Mitra ==========
function validasiMitra(btn) {
    const mitraId = btn.getAttribute('data-mitra');

    showConfirmTagihan(
        'Validasi Pembayaran',
        'Validasi semua bukti pembayaran mitra ini?',
        null,
        'Ya, Validasi',
        () => {
            btn.disabled = true;
            btn.textContent = 'Memproses...';

            fetch('/kasir/tagihan/validasi-mitra', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ mitra_id: mitraId })
            })
            .then(r => r.ok ? r.json() : r.json().then(e => { throw e; }))
            .then(data => {
                window.location.reload();
            })
            .catch(err => {
                showToast('error', 'Gagal Memvalidasi', err.message || 'Terjadi kesalahan');
                btn.disabled = false;
                btn.textContent = '✓ Validasi';
            });
        }
    );
}
</script>

@endsection
