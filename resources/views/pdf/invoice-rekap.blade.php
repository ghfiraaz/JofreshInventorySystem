<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice Rekapitulasi - {{ $mitra->nama }}</title>
    <style>
        @page { margin: 30px 40px; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            position: relative;
        }

        /* ===== WATERMARK ===== */
        .watermark {
            position: fixed;
            top: 35%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 80px;
            font-weight: 900;
            letter-spacing: 8px;
            opacity: 0.06;
            z-index: 0;
            pointer-events: none;
            white-space: nowrap;
        }
        .watermark.belum { color: #dc2626; }
        .watermark.lunas { color: #16a34a; }

        /* ===== KOP SURAT ===== */
        .kop-surat {
            text-align: center;
            padding-bottom: 14px;
            border-bottom: 3px double #1e3a5f;
            margin-bottom: 24px;
        }
        .kop-nama {
            font-size: 28px;
            font-weight: 900;
            color: #1e3a5f;
            margin: 0;
            letter-spacing: 4px;
            text-transform: uppercase;
        }
        .kop-tagline {
            font-size: 12px;
            color: #64748b;
            margin: 2px 0 6px 0;
            font-style: italic;
        }
        .kop-detail {
            font-size: 11px;
            color: #475569;
            margin: 0;
            line-height: 1.6;
        }

        /* ===== PERIHAL & TUJUAN ===== */
        .surat-info {
            margin-bottom: 20px;
            font-size: 13px;
            color: #334155;
        }
        .surat-info .tanggal {
            text-align: right;
            margin-bottom: 12px;
            font-size: 12px;
            color: #64748b;
        }
        .surat-info .tujuan {
            margin-bottom: 0;
        }
        .surat-info .tujuan strong {
            color: #1e293b;
        }

        /* ===== BODY SURAT ===== */
        .surat-body {
            font-size: 13px;
            color: #334155;
            line-height: 1.7;
            margin-bottom: 16px;
            text-align: justify;
        }

        /* ===== STATUS BADGE ===== */
        .status-badge {
            display: inline-block;
            padding: 5px 18px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 16px;
        }
        .status-badge.belum {
            background: #fef2f2;
            color: #dc2626;
            border: 2px solid #fca5a5;
        }
        .status-badge.lunas {
            background: #f0fdf4;
            color: #16a34a;
            border: 2px solid #86efac;
        }

        /* ===== TABEL ===== */
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            font-size: 12px;
        }
        .invoice-table th {
            background: #1e3a5f;
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
            padding: 10px 10px;
            text-align: left;
            border: 1px solid #1e3a5f;
        }
        .invoice-table td {
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .invoice-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }

        /* ===== TOTAL ===== */
        .total-box {
            text-align: right;
            margin-bottom: 24px;
        }
        .total-box table {
            margin-left: auto;
            border-collapse: collapse;
        }
        .total-box td {
            padding: 6px 14px;
            font-size: 13px;
        }
        .total-box .total-label {
            font-weight: 700;
            color: #1e3a5f;
            text-align: right;
            border-top: 2px solid #1e3a5f;
        }
        .total-box .total-value {
            font-weight: 900;
            font-size: 16px;
            color: #1e3a5f;
            text-align: right;
            border-top: 2px solid #1e3a5f;
        }

        /* ===== QR CODE ===== */
        .qr-section {
            text-align: center;
            margin: 20px 0;
            padding: 16px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }
        .qr-section h4 {
            margin: 0 0 10px 0;
            font-size: 13px;
            color: #1e3a5f;
        }
        .qr-section p {
            margin: 8px 0 0 0;
            font-size: 10px;
            color: #94a3b8;
        }

        /* ===== PENUTUP ===== */
        .penutup {
            font-size: 13px;
            color: #334155;
            line-height: 1.7;
            margin-bottom: 30px;
            text-align: justify;
        }

        .ttd {
            margin-top: 30px;
            text-align: right;
            font-size: 13px;
            color: #334155;
        }
        .ttd .nama-perusahaan {
            font-weight: 800;
            color: #1e3a5f;
            margin-top: 50px;
        }

        /* ===== CATATAN ===== */
        .catatan {
            margin-top: 30px;
            padding: 12px 16px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 6px;
            font-size: 11px;
            color: #92400e;
            line-height: 1.6;
        }
        .catatan strong {
            display: block;
            margin-bottom: 4px;
            font-size: 12px;
        }

        /* ===== FOOTER ===== */
        .footer {
            margin-top: 24px;
            font-size: 10px;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 12px;
        }

        .separator {
            border: none;
            border-top: 1px solid #cbd5e1;
            margin: 16px 0;
        }
    </style>
</head>
<body>

    {{-- Watermark --}}
    @php
        $allPaid = $transaksiList->every(fn($t) => $t->status_pembayaran === 'Sudah Dibayar');
        $statusLabel = $allPaid ? 'LUNAS' : 'BELUM DIBAYAR';
        $statusClass = $allPaid ? 'lunas' : 'belum';
    @endphp
    <div class="watermark {{ $statusClass }}">{{ $statusLabel }}</div>

    {{-- ===== KOP SURAT ===== --}}
    <div class="kop-surat">
        <h1 class="kop-nama">JoFresh</h1>
        <p class="kop-tagline">Supplier Unggas Segar & Terpercaya</p>
        <p class="kop-detail">
            Jl. Raya JoFresh No. 88, Kota Bekasi, Jawa Barat 17112<br>
            Telp: (021) 8888-7777 &nbsp;|&nbsp; Email: jofreshinventorys@gmail.com
        </p>
    </div>

    {{-- ===== INFO TUJUAN ===== --}}
    <div class="surat-info">
        <div class="tanggal">
            {{ now()->translatedFormat('l, d F Y') }}
        </div>
        <div class="tujuan">
            Kepada Yth.<br>
            <strong>{{ $mitra->nama }}</strong><br>
            {{ $mitra->alamat ?? '-' }}<br>
            @if($mitra->kontak)
                Telp: {{ $mitra->kontak }}
            @endif
        </div>
    </div>

    <hr class="separator">

    {{-- ===== PERIHAL ===== --}}
    <p style="font-size:13px; color:#334155; margin-bottom:4px;">
        <strong>Perihal:</strong> Invoice Tagihan Pembelian Produk<br>
        <strong>Periode:</strong> {{ $periodeAwal }} — {{ $periodeAkhir }}
    </p>

    {{-- ===== BODY SURAT ===== --}}
    <div class="surat-body">
        <p>Dengan hormat,</p>
        <p>
            Bersama surat ini kami sampaikan rincian tagihan atas transaksi pembelian produk yang telah dilakukan
            selama periode <strong>{{ $periodeAwal }}</strong> sampai dengan <strong>{{ $periodeAkhir }}</strong>.
            Mohon untuk melakukan pembayaran sesuai dengan jumlah tagihan yang tertera pada invoice ini.
        </p>
    </div>

    {{-- ===== STATUS ===== --}}
    <p style="font-size:13px; margin-bottom:4px; color:#334155;">
        <strong>Status Pembayaran:</strong>
    </p>
    <div class="status-badge {{ $statusClass }}">{{ $statusLabel }}</div>

    {{-- ===== TABEL DETAIL ===== --}}
    <table class="invoice-table">
        <thead>
            <tr>
                <th class="text-center" style="width:5%;">No</th>
                <th style="width:28%;">Nama Produk</th>
                <th style="width:17%;">Tanggal</th>
                <th class="text-center" style="width:10%;">Jumlah</th>
                <th class="text-right" style="width:18%;">Harga</th>
                <th class="text-right" style="width:22%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($transaksiList as $tx)
                @foreach($tx->items as $item)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $item->nama_produk }}</td>
                    <td>{{ $tx->created_at->translatedFormat('d F Y') }}</td>
                    <td class="text-center">{{ intval($item->jumlah) }}</td>
                    <td class="text-right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                    <td class="text-right"><strong>Rp {{ number_format($item->harga_satuan * $item->jumlah, 0, ',', '.') }}</strong></td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    {{-- ===== TOTAL ===== --}}
    <div class="total-box">
        <table>
            <tr>
                <td class="total-label">Total Tagihan:</td>
                <td class="total-value">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <hr class="separator">

    {{-- ===== QR CODE ===== --}}
    <div class="qr-section">
        <h4>Scan QR Code untuk Pembayaran (QRIS)</h4>
        @if(isset($qrCodePath) && file_exists($qrCodePath))
            <img src="{{ $qrCodePath }}" alt="QRIS Pembayaran" width="160" height="160">
        @else
            <p style="color: #ef4444; font-style: italic;">QR Code tidak tersedia</p>
        @endif
        <p>QRIS Pembayaran JoFresh</p>
    </div>

    {{-- ===== PENUTUP ===== --}}
    <div class="penutup">
        <p>
            Pembayaran dapat dilakukan melalui metode pembayaran yang telah disediakan (QRIS di atas).
            Apabila pembayaran telah dilakukan, status invoice akan otomatis berubah menjadi <strong>"LUNAS"</strong>.
        </p>
        <p>
            Demikian invoice ini kami sampaikan. Atas perhatian dan kerja samanya, kami ucapkan terima kasih.
        </p>
    </div>

    {{-- ===== TTD ===== --}}
    <div class="ttd">
        <p>Hormat kami,</p>
        <p class="nama-perusahaan">JoFresh</p>
        <p style="font-size:11px; color:#64748b; margin-top:4px;">Sistem Inventaris & Penjualan Unggas</p>
    </div>

    {{-- ===== CATATAN ===== --}}
    <div class="catatan">
        <strong>Catatan:</strong>
        • Invoice ini sah dan diterbitkan secara otomatis oleh sistem.<br>
        • Mohon simpan invoice ini sebagai bukti transaksi pembayaran.<br>
        • Jika ada pertanyaan mengenai tagihan, silakan hubungi admin JoFresh.
    </div>

    {{-- ===== FOOTER ===== --}}
    <div class="footer">
        Dokumen ini dicetak secara otomatis oleh JoFresh Inventory System<br>
        &copy; {{ date('Y') }} JoFresh. Semua Hak Cipta Dilindungi.
    </div>

</body>
</html>
