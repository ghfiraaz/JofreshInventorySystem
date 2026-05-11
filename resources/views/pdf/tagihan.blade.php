<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tagihan JoFresh - {{ $mitra->nama }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #1e3a5f; padding-bottom: 20px; }
        .logo { font-size: 32px; font-weight: bold; color: #1e3a5f; margin: 0; letter-spacing: 2px; }
        .sublogo { font-size: 14px; color: #666; margin: 5px 0 0 0; }
        .title { font-size: 20px; font-weight: bold; margin-top: 20px; color: #333; text-transform: uppercase; }
        .info-table { width: 100%; margin-bottom: 30px; }
        .info-table td { vertical-align: top; }
        .label { font-weight: bold; color: #555; width: 120px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 30px; font-size: 14px; }
        .table th, .table td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .table th { background-color: #f8fafc; color: #1e3a5f; font-weight: bold; text-transform: uppercase; font-size: 12px; }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .total-row th { background-color: #e0e7ff; color: #1e3a5f; font-size: 16px; }
        .total-row td { background-color: #e0e7ff; color: #1e3a5f; font-size: 16px; font-weight: bold; }
        .footer { margin-top: 50px; font-size: 12px; color: #777; text-align: center; border-top: 1px solid #ddd; padding-top: 20px; }
    </style>
</head>
<body>

    <div class="header">
        <h1 class="logo">JoFresh</h1>
        <p class="sublogo">Sistem Inventaris & Penjualan Unggas Terpercaya</p>
        <div class="title">Rincian Tagihan Pembayaran</div>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Nama Mitra:</td>
            <td><strong>{{ $mitra->nama }}</strong></td>
            <td class="label">Tanggal Cetak:</td>
            <td>{{ now()->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Kontak/Email:</td>
            <td>{{ $mitra->kontak ?? '-' }} / {{ $mitra->email ?? '-' }}</td>
            <td class="label">Jatuh Tempo:</td>
            @php
                $closestTempo = $transaksiUnpaid->whereNotNull('jatuh_tempo')->pluck('jatuh_tempo')->sort()->first();
                $tanggalTempo = $closestTempo ? \Carbon\Carbon::parse($closestTempo)->translatedFormat('d F Y') : '-';
            @endphp
            <td><strong style="color: #e11d48;">{{ $tanggalTempo }}</strong></td>
        </tr>
        <tr>
            <td class="label">Alamat:</td>
            <td colspan="3">{{ $mitra->alamat ?? '-' }}</td>
        </tr>
    </table>

    <p style="margin-bottom: 15px; font-size: 14px;">Berikut adalah daftar transaksi yang belum dibayar dan perlu segera dilunasi:</p>

    <table class="table">
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="20%">No. Transaksi</th>
                <th width="35%">Rincian Item</th>
                <th class="text-right" width="25%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksiUnpaid as $index => $tx)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $tx->no_transaksi }}</td>
                <td>
                    <ul style="margin: 0; padding-left: 15px; font-size: 13px;">
                        @foreach($tx->items as $item)
                        <li>{{ $item->nama_produk }} ({{ intval($item->jumlah) }} ekor)</li>
                        @endforeach
                    </ul>
                </td>
                <td class="text-right">Rp {{ number_format($tx->total_harga, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <th colspan="4" class="text-right">TOTAL TAGIHAN:</th>
                <td class="text-right">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="background: #f8fafc; padding: 15px; border-radius: 8px; font-size: 13px; border: 1px solid #e2e8f0; margin-top: 30px;">
        <h4 style="margin-top: 0; color: #1e3a5f;">Instruksi Pembayaran:</h4>
        <p style="margin-bottom: 5px;">Silakan lakukan pembayaran sesuai dengan <strong>TOTAL TAGIHAN</strong> di atas. Bukti pembayaran dapat diunggah melalui link yang telah kami kirimkan ke email Anda.</p>
        <p style="margin: 0;">Jika ada pertanyaan mengenai tagihan ini, silakan hubungi kasir atau admin JoFresh.</p>
    </div>

    <div class="footer">
        Dicetak secara otomatis oleh JoFresh Inventory System (JIS)<br>
        &copy; {{ date('Y') }} JoFresh. Semua Hak Cipta Dilindungi.
    </div>

</body>
</html>
