<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $isLunas ? 'LUNAS' : '' }} - {{ $transaksi->no_transaksi }}</title>
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

        /* ===== WATERMARK LUNAS ===== */
        .watermark-lunas {
            position: fixed;
            top: 35%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 100px;
            font-weight: 900;
            letter-spacing: 12px;
            color: #16a34a;
            opacity: 0.08;
            z-index: 0;
            pointer-events: none;
            white-space: nowrap;
            text-transform: uppercase;
        }

        /* ===== KOP SURAT ===== */
        .kop-nama {
            font-size: 28px;
            font-weight: 900;
            color: #7B3911;
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

        /* ===== INFO ===== */
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
        .surat-info .tujuan strong {
            color: #1e293b;
        }

        /* ===== STATUS BADGE ===== */
        .status-badge {
            display: inline-block;
            padding: 6px 20px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 16px;
        }
        .status-badge.lunas {
            background: #f0fdf4;
            color: #16a34a;
            border: 2px solid #86efac;
        }
        .status-badge.belum {
            background: #fef2f2;
            color: #dc2626;
            border: 2px solid #fca5a5;
        }

        /* ===== TABEL ===== */
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            font-size: 12px;
        }
        .invoice-table th {
            background: #7B3911;
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
            padding: 10px 10px;
            text-align: left;
            border: 1px solid #7B3911;
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
            color: #7B3911;
            text-align: right;
            border-top: 2px solid #7B3911;
        }
        .total-box .total-value {
            font-weight: 900;
            font-size: 16px;
            color: #7B3911;
            text-align: right;
            border-top: 2px solid #7B3911;
        }

        /* ===== INVOICE INFO ===== */
        .invoice-info {
            margin-bottom: 20px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 14px 18px;
        }
        .invoice-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-info td {
            padding: 4px 0;
            font-size: 12px;
            color: #475569;
        }
        .invoice-info .label {
            font-weight: 700;
            color: #1e293b;
            width: 140px;
        }

        /* ===== TTD ===== */
        .ttd {
            margin-top: 30px;
            text-align: right;
            font-size: 13px;
            color: #334155;
        }
        .ttd .nama-perusahaan {
            font-weight: 800;
            color: #7B3911;
            margin-top: 50px;
        }

        /* ===== CATATAN ===== */
        .catatan {
            margin-top: 30px;
            padding: 12px 16px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            font-size: 11px;
            color: #166534;
            line-height: 1.6;
        }
        .catatan strong {
            display: block;
            margin-bottom: 4px;
            font-size: 12px;
        }

        .catatan.pending {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
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

    {{-- Watermark LUNAS (hanya jika sudah dibayar) --}}
    @if($isLunas)
        <div class="watermark-lunas">LUNAS</div>
    @endif

    {{-- ===== KOP SURAT ===== --}}
    <table style="width: 100%; border-bottom: 3px double #7B3911; padding-bottom: 14px; margin-bottom: 24px; border-collapse: collapse;">
        <tr>
            <td style="width: 25%; vertical-align: middle; padding: 0;">
                <img src="{{ public_path('images/logo-jofresh.png') }}" style="height: 14mm; width: auto; display: block;">
            </td>
            <td style="width: 50%; text-align: center; padding: 0; vertical-align: middle;">
                <h1 class="kop-nama">JoFresh</h1>
                <p class="kop-tagline">Elevating Freshness Every Day</p>
                <p class="kop-detail">
                    Jl. Elang Mutiara, Periuk Jaya, Kota Tangerang, Banten 15131<br>
                    Telp: (0857-1871-9077) &nbsp;|&nbsp; Email: jofreshinventorys@gmail.com
                </p>
            </td>
            <td style="width: 25%; padding: 0;"></td>
        </tr>
    </table>

    {{-- ===== TANGGAL & TUJUAN ===== --}}
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

    {{-- ===== INVOICE INFO ===== --}}
    <div class="invoice-info">
        <table>
            <tr>
                <td class="label">No. Invoice</td>
                <td>: {{ $transaksi->no_transaksi }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Transaksi</td>
                <td>: {{ $transaksi->created_at->translatedFormat('d F Y, H:i') }} WIB</td>
            </tr>
            <tr>
                <td class="label">Metode Pembayaran</td>
                <td>: {{ $transaksi->metode_pembayaran }}</td>
            </tr>
            @if($transaksi->jatuh_tempo)
            <tr>
                <td class="label">Jatuh Tempo</td>
                <td>: {{ $transaksi->jatuh_tempo->translatedFormat('d F Y') }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- ===== STATUS ===== --}}
    <p style="font-size:13px; margin-bottom:4px; color:#334155;">
        <strong>Status Pembayaran:</strong>
    </p>
    @if($isLunas)
        <div class="status-badge lunas">LUNAS</div>
    @else
        <div class="status-badge belum">BELUM DIBAYAR</div>
    @endif

    {{-- ===== TABEL DETAIL ITEM ===== --}}
    <table class="invoice-table">
        <thead>
            <tr>
                <th class="text-center" style="width:5%;">No</th>
                <th style="width:35%;">Nama Produk</th>
                <th class="text-center" style="width:12%;">Jumlah</th>
                <th class="text-right" style="width:22%;">Harga Satuan</th>
                <th class="text-right" style="width:26%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksi->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->nama_produk }}</td>
                <td class="text-center">{{ intval($item->jumlah) }} ekor</td>
                <td class="text-right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                <td class="text-right"><strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ===== TOTAL ===== --}}
    <div class="total-box">
        <table>
            <tr>
                <td class="total-label">Grand Total:</td>
                <td class="total-value">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <hr class="separator">

    {{-- ===== TTD ===== --}}
    <div class="ttd">
        <p>Hormat kami,</p>
        <p class="nama-perusahaan">JoFresh</p>
        <p style="font-size:11px; color:#64748b; margin-top:4px;">Sistem Inventaris & Penjualan Unggas</p>
    </div>

    {{-- ===== CATATAN ===== --}}
    @if($isLunas)
        <div class="catatan">
            <strong>Catatan:</strong>
            - Invoice ini telah dinyatakan <strong style="display:inline;">LUNAS</strong> dan sah secara resmi.<br>
            - Dokumen ini diterbitkan secara otomatis oleh sistem sebagai bukti pembayaran resmi.<br>
            - Mohon simpan invoice ini sebagai arsip transaksi Anda.
        </div>
    @else
        <div class="catatan pending">
            <strong>Catatan:</strong>
            - Invoice ini sah dan diterbitkan secara otomatis oleh sistem.<br>
            - Mohon segera lakukan pembayaran sebelum jatuh tempo.<br>
            - Jika ada pertanyaan mengenai tagihan, silakan hubungi admin JoFresh.
        </div>
    @endif

    {{-- ===== FOOTER ===== --}}
    <div class="footer">
        Dokumen ini dicetak secara otomatis oleh JoFresh Inventory System<br>
        &copy; {{ date('Y') }} JoFresh. Semua Hak Cipta Dilindungi.
    </div>

</body>
</html>
