<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Diterima</title>
</head>
<body style="margin:0;padding:0;font-family:'Inter',Arial,sans-serif;background-color:#FAF8F5;">
<table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 16px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 4px 24px rgba(123,57,17,0.08);border:1px solid #E0D5CA;">
    {{-- Header --}}
    <tr>
        <td style="background:linear-gradient(135deg,#7B3911,#A1511E);padding:36px 40px;text-align:center;">
            <img src="{{ $message->embed(public_path('images/logo-jofresh-white.png')) }}" alt="JoFresh" style="width:220px;height:auto;margin-bottom:16px;">
            <h1 style="margin:0;color:#fff;font-size:22px;font-weight:700;letter-spacing:0.5px;">Pembayaran Diterima ✓</h1>
            <p style="margin:8px 0 0;color:rgba(255,255,255,0.8);font-size:14px;">Terima kasih atas pembayaran Anda</p>
        </td>
    </tr>

    {{-- Body --}}
    <tr>
        <td style="padding:36px 40px;">
            <p style="margin:0 0 20px;color:#3D1B07;font-size:16px;line-height:1.7;">
                Yth. <strong>{{ $mitra->nama }}</strong>,
            </p>
            <p style="margin:0 0 24px;color:#6B5B4E;font-size:15px;line-height:1.7;">
                Kami mengkonfirmasi bahwa pembayaran Anda telah <strong style="color:#16a34a;">diterima dan diverifikasi</strong>.
            </p>

            {{-- Info Box --}}
            <table width="100%" cellpadding="0" cellspacing="0" style="background:#FAF0E6;border-radius:14px;border:1px solid #E0D5CA;margin-bottom:28px;">
                <tr>
                    <td style="padding:24px;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="padding:6px 0;color:#6B5B4E;font-size:14px;">Periode</td>
                                <td style="padding:6px 0;color:#3D1B07;font-size:14px;font-weight:600;text-align:right;">{{ $periodeAwal }} — {{ $periodeAkhir }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="border-bottom:1px solid #E0D5CA;padding:4px 0;"></td>
                            </tr>
                            <tr>
                                <td style="padding:6px 0;color:#6B5B4E;font-size:14px;">Jumlah Transaksi</td>
                                <td style="padding:6px 0;color:#3D1B07;font-size:14px;font-weight:600;text-align:right;">{{ $transaksiList->count() }} transaksi</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="border-bottom:1px solid #E0D5CA;padding:4px 0;"></td>
                            </tr>
                            <tr>
                                <td style="padding:8px 0;color:#6B5B4E;font-size:14px;">Total Dibayar</td>
                                <td style="padding:8px 0;color:#16a34a;font-size:18px;font-weight:800;text-align:right;">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <p style="margin:0 0 8px;color:#6B5B4E;font-size:14px;line-height:1.7;">
                Invoice lengkap telah dilampirkan pada email ini dalam format PDF.
            </p>
        </td>
    </tr>

    {{-- Footer --}}
    <tr>
        <td style="background:#FAF5EF;padding:28px 40px;border-top:1px solid #E0D5CA;">
            <p style="margin:0 0 4px;color:#6B5B4E;font-size:12px;text-align:center;">
                Email ini dikirim kepada <strong>{{ $mitra->nama }}</strong> sebagai mitra terdaftar di JoFresh.
            </p>
            <p style="margin:0;color:#9C8B7E;font-size:11px;text-align:center;">
                JoFresh — Jl. Elang Mutiara, Periuk Jaya, Kota Tangerang, Banten 15131
            </p>
        </td>
    </tr>
</table>
</td></tr>
</table>
</body>
</html>
