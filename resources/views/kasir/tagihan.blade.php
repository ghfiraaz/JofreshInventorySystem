@extends('layouts.kasir')
@section('title', 'Tagihan Bulanan')
@section('content')

{{-- ===== HEADING ===== --}}
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-800 mb-1">Tagihan Bulanan Mitra</h2>
    <p class="text-sm text-gray-400">Kelola dan tagih transaksi bulanan mitra dengan QR Code Midtrans</p>
</div>

{{-- ===== PERIODE FILTER ===== --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
    <form method="GET" action="{{ url('/kasir/tagihan') }}" class="flex items-center gap-4">
        <div class="flex items-center gap-2 text-sm font-medium text-gray-600">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
            </svg>
            Periode:
        </div>
        <select name="bulan" class="px-4 py-2 border border-gray-200 rounded-lg text-sm bg-white outline-none focus:border-blue-500 transition-all cursor-pointer appearance-none" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 20 20%27%3E%3Cpath stroke=%27%236b7280%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%271.5%27 d=%27m6 8 4 4 4-4%27/%3E%3C/svg%3E'); background-position: right 10px center; background-repeat: no-repeat; background-size: 14px; padding-right: 32px;">
            @foreach($namaBulan as $num => $nama)
                <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>{{ $nama }}</option>
            @endforeach
        </select>
        <select name="tahun" class="px-4 py-2 border border-gray-200 rounded-lg text-sm bg-white outline-none focus:border-blue-500 transition-all cursor-pointer appearance-none" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 20 20%27%3E%3Cpath stroke=%27%236b7280%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%271.5%27 d=%27m6 8 4 4 4-4%27/%3E%3C/svg%3E'); background-position: right 10px center; background-repeat: no-repeat; background-size: 14px; padding-right: 32px;">
            @for($y = date('Y'); $y >= 2024; $y--)
                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg border-none cursor-pointer hover:bg-blue-700 transition-all">Terapkan</button>
    </form>
</div>

{{-- ===== SUMMARY CARDS ===== --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center justify-between">
        <div>
            <div class="text-xs text-gray-400 font-medium mb-1">Total Mitra</div>
            <div class="text-2xl font-extrabold text-gray-800">{{ $totalMitra }}</div>
        </div>
        <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-yellow-600">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center justify-between">
        <div>
            <div class="text-xs text-gray-400 font-medium mb-1">Total Tagihan</div>
            <div class="text-2xl font-extrabold text-gray-800">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</div>
        </div>
        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-blue-600">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center justify-between">
        <div>
            <div class="text-xs text-gray-400 font-medium mb-1">Sudah Lunas</div>
            <div class="text-2xl font-extrabold text-gray-800">{{ $sudahLunas }}</div>
        </div>
        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-green-600">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center justify-between">
        <div>
            <div class="text-xs text-gray-400 font-medium mb-1">Belum Lunas</div>
            <div class="text-2xl font-extrabold text-gray-800">{{ $belumLunas }}</div>
        </div>
        <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-yellow-600">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </div>
    </div>
</div>

{{-- ===== TAGIHAN CONTENT ===== --}}
<div class="bg-white rounded-xl border border-gray-200 p-6">
    <h3 class="text-base font-bold text-gray-800 mb-5">Tagihan {{ $namaBulan[$bulan] ?? '' }} {{ $tahun }}</h3>

    @if(count($mitraTagihan) === 0)
        {{-- Empty State --}}
        <div class="flex flex-col items-center justify-center py-16 text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-16 h-16 mb-3 text-gray-300">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
            </svg>
            <span class="text-sm">Tidak ada transaksi untuk periode ini</span>
        </div>
    @else
        {{-- Mitra Tagihan List --}}
        <div class="flex flex-col gap-3">
            @foreach($mitraTagihan as $mt)
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    {{-- Mitra Header (clickable) --}}
                    <div class="tagihan-mitra-header flex items-center justify-between px-5 py-4 cursor-pointer hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm flex-shrink-0">{{ strtoupper(substr($mt['mitra']->nama, 0, 1)) }}</div>
                            <div>
                                <div class="font-semibold text-sm text-gray-800">{{ $mt['mitra']->nama }}</div>
                                <div class="text-xs text-gray-400">{{ $mt['count'] }} transaksi</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-bold text-gray-800">Rp {{ number_format($mt['total'], 0, ',', '.') }}</span>
                            @php
                                $allItems = [];
                                foreach($mt['transaksi'] as $tx) {
                                    foreach($tx->items as $item) {
                                        $name = $item->nama_produk;
                                        if(!isset($allItems[$name])) {
                                            $allItems[$name] = [
                                                'nama' => $name,
                                                'qty' => 0,
                                                'subtotal' => 0
                                            ];
                                        }
                                        $allItems[$name]['qty'] += $item->jumlah;
                                        $allItems[$name]['subtotal'] += $item->subtotal;
                                    }
                                }
                                $itemsJson = json_encode(array_values($allItems));
                            @endphp
                            <button type="button" class="btn-bayar-qr px-3 py-1.5 bg-green-100 text-green-700 hover:bg-green-600 hover:text-white rounded-lg text-xs font-bold transition-colors shadow-sm cursor-pointer border-none" data-mitra="{{ $mt['mitra']->id }}" data-nama="{{ $mt['mitra']->nama }}" data-total="Rp {{ number_format($mt['total'], 0, ',', '.') }}" data-items="{{ $itemsJson }}" onclick="event.stopPropagation(); openQrModal(this)">Bayar & QR</button>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-gray-400 tagihan-expand-icon transition-transform duration-200">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
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
                                    <th class="text-right pb-2 text-xs text-gray-400 font-medium">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mt['transaksi'] as $tx)
                                    <tr>
                                        <td class="py-1.5 text-sm text-gray-600">{{ $tx->created_at->format('d/m/Y') }}</td>
                                        <td class="py-1.5 text-sm font-medium text-gray-700">{{ $tx->no_transaksi }}</td>
                                        <td class="py-1.5 text-sm text-gray-600 text-center">{{ $tx->total_item }}</td>
                                        <td class="py-1.5 text-sm font-semibold text-gray-800 text-right">Rp {{ number_format($tx->total_harga, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Modal QR Code --}}
<div id="modal-qr" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/50 backdrop-blur-sm transition-opacity opacity-0">
    <div class="bg-white rounded-2xl w-[400px] max-w-[90%] shadow-2xl transform scale-95 transition-transform overflow-hidden">
        <div class="bg-blue-600 p-4 flex justify-between items-center">
            <div class="text-white font-bold text-lg flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                </svg>
                Midtrans QRIS
            </div>
            <button type="button" onclick="closeQrModal()" class="text-white/80 hover:text-white transition-colors cursor-pointer border-none bg-transparent">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <div class="p-6 text-center flex flex-col max-h-[85vh]">
            <div class="flex-shrink-0">
                <h4 class="font-bold text-gray-800 text-lg mb-1" id="qr-mitra-nama">Nama Mitra</h4>
                <p class="text-sm text-gray-500 mb-4">Total Tagihan: <span id="qr-total" class="font-bold text-gray-800">Rp 0</span></p>
            </div>
            
            <div class="text-left bg-gray-50 border border-gray-100 rounded-lg p-3 mb-4 flex-1 overflow-y-auto hidden scrollbar-hide" id="qr-items-container">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 sticky top-0 bg-gray-50 pb-1">Rincian Pembelian</div>
                <div id="qr-items-list" class="space-y-2"></div>
            </div>

            <div class="flex-shrink-0">
                <div class="bg-gray-50 p-4 rounded-xl inline-block mb-6 border border-gray-200">
                    <img id="qr-img" src="" alt="QR Code" class="w-48 h-48 object-contain">
                </div>

                <form action="{{ url('/kasir/tagihan/bayar') }}" method="POST" id="form-bayar-tagihan" onsubmit="handlePayment(event)">
                @csrf
                <input type="hidden" name="mitra_id" id="input-mitra-id">
                <input type="hidden" name="bulan" value="{{ $bulan }}">
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <button type="submit" id="btn-konfirmasi-qr" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-colors shadow-lg shadow-blue-200 flex items-center justify-center gap-2 cursor-pointer border-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    <span>Konfirmasi Pembayaran</span>
                </button>
            </form>
            <p class="text-xs text-gray-400 mt-3">*Tombol ini adalah simulasi webhook Midtrans (Sudah Dibayar)</p>

            {{-- Success Overlay --}}
            <div id="payment-success-overlay" class="absolute inset-0 bg-white/95 z-10 flex flex-col items-center justify-center hidden opacity-0 transition-opacity duration-300">
                <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mb-4 shadow-lg shadow-green-200 scale-0 transition-transform duration-500 delay-100" id="payment-success-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-8 h-8 text-white"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-1 translate-y-4 opacity-0 transition-all duration-500 delay-200" id="payment-success-text">Pembayaran Berhasil!</h3>
                <p class="text-sm text-gray-500 translate-y-4 opacity-0 transition-all duration-500 delay-300" id="payment-success-sub">Mengalihkan ke riwayat transaksi...</p>
            </div>
        </div>
    </div>
</div>

<script>
    function openQrModal(btn) {
        const mitraId = btn.getAttribute('data-mitra');
        const nama = btn.getAttribute('data-nama');
        const total = btn.getAttribute('data-total');
        const items = JSON.parse(btn.getAttribute('data-items') || '[]');
        
        document.getElementById('qr-mitra-nama').textContent = nama;
        document.getElementById('qr-total').textContent = total;
        document.getElementById('input-mitra-id').value = mitraId;

        const container = document.getElementById('qr-items-container');
        const list = document.getElementById('qr-items-list');
        list.innerHTML = '';
        
        if(items.length > 0) {
            items.forEach(item => {
                list.innerHTML += `
                    <div class="flex items-start justify-between text-xs border-b border-gray-200/50 pb-1.5 last:border-0 last:pb-0">
                        <div class="text-gray-600 font-medium pr-2 leading-tight">${item.nama} <span class="text-gray-400">x${item.qty}</span></div>
                        <div class="font-bold text-gray-800 whitespace-nowrap">Rp ${parseInt(item.subtotal).toLocaleString('id-ID')}</div>
                    </div>
                `;
            });
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
        
        // Generate a random QR Code from API for visual demo
        document.getElementById('qr-img').src = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=MIDTRANS_DEMO_${mitraId}_${Date.now()}`;
        
        const modal = document.getElementById('modal-qr');
        modal.classList.remove('hidden');
        // trigger animation
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('div').classList.remove('scale-95');
        }, 10);
    }

    function closeQrModal() {
        const modal = document.getElementById('modal-qr');
        modal.classList.add('opacity-0');
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function handlePayment(e) {
        e.preventDefault();
        const form = e.target;
        const btn = document.getElementById('btn-konfirmasi-qr');
        const span = btn.querySelector('span');
        
        btn.disabled = true;
        span.textContent = 'Memproses...';
        
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if(response.ok) {
                showSuccessAnimation();
            } else {
                alert('Gagal mengkonfirmasi pembayaran.');
                btn.disabled = false;
                span.textContent = 'Konfirmasi Pembayaran';
            }
        })
        .catch(err => {
            alert('Terjadi kesalahan jaringan.');
            btn.disabled = false;
            span.textContent = 'Konfirmasi Pembayaran';
        });
    }

    function showSuccessAnimation() {
        const overlay = document.getElementById('payment-success-overlay');
        const icon = document.getElementById('payment-success-icon');
        const text = document.getElementById('payment-success-text');
        const sub = document.getElementById('payment-success-sub');

        overlay.classList.remove('hidden');
        
        // Trigger animations
        setTimeout(() => {
            overlay.classList.remove('opacity-0');
            
            setTimeout(() => {
                icon.classList.remove('scale-0');
                text.classList.remove('translate-y-4', 'opacity-0');
                sub.classList.remove('translate-y-4', 'opacity-0');
            }, 150);
            
            // Redirect after delay
            setTimeout(() => {
                window.location.href = "{{ url('/kasir/riwayat') }}";
            }, 2000);
            
        }, 10);
    }
</script>

@endsection
