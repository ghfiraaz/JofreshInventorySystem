Rekapitulasi Transaksi - JoFresh

Yth. Bapak/Ibu {{ $mitra->nama }},

Bersama email ini, kami sampaikan rekapitulasi transaksi yang tercatat pada sistem kami untuk periode {{ $periodeAwal }} sampai dengan {{ $periodeAkhir }}.

Detail Rekapitulasi:
- Periode      : {{ $periodeAwal }} s/d {{ $periodeAkhir }}
- Batas Waktu   : {{ $tanggalTempo }}
- Jumlah        : {{ $transaksiList->count() }} transaksi
- Total         : Rp {{ number_format($totalTagihan, 0, ',', '.') }}

Rincian Transaksi:
@foreach($transaksiList as $tx)
  {{ $tx->no_transaksi }} | {{ $tx->created_at->format('d/m/Y') }} | Rp {{ number_format($tx->total_harga, 0, ',', '.') }}
@endforeach

Silakan kunjungi halaman berikut untuk mengunggah bukti pembayaran:
{{ $paymentLink }}

Kami juga melampirkan dokumen rekapitulasi (PDF) pada email ini untuk arsip Anda. 
Dokumen tersebut berisi rincian lengkap beserta kode QRIS untuk kemudahan pembayaran.

Mohon kiranya dapat diselesaikan sebelum tanggal {{ $tanggalTempo }}.

Jika pembayaran sudah dilakukan, Anda dapat mengabaikan email ini.

Hormat kami,
Tim JoFresh

---
Email ini dikirim kepada {{ $mitra->nama }} sebagai mitra terdaftar di JoFresh Inventory System.
JoFresh - Jl. Elang Mutiara, Periuk Jaya, Kota Tangerang, Banten 15131
