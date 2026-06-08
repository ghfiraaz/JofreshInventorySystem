<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pembayaran Tidak Valid - JoFresh</title>
</head>
<body style="margin:0;padding:0;font-family:'Inter',Arial,sans-serif;background-color:#FAF8F5;color:#333;">
<table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 16px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 4px 24px rgba(123,57,17,0.08);border:1px solid #E0D5CA;">
    
    {{-- Header --}}
    <tr>
        <td style="background:linear-gradient(135deg,#7B3911,#A1511E);padding:36px 40px;text-align:center;">
            <img src="{{ $message->embed(public_path('images/logo-jofresh-white.png')) }}" alt="JoFresh" style="width:220px;height:auto;margin-bottom:16px;">
            <h1 style="margin:0;color:#fff;font-size:22px;font-weight:700;letter-spacing:0.5px;">Pembayaran Perlu Ditinjau</h1>
            <p style="margin:8px 0 0;color:rgba(255,255,255,0.8);font-size:14px;">Bukti pembayaran belum dapat divalidasi</p>
        </td>
    </tr>

    {{-- Alert Banner --}}
    <tr>
        <td style="padding:24px 40px 0;">
            <table width="100%" cellpadding="0" cellspacing="0" style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;">
                <tr>
                    <td style="padding:16px;">
                        <p style="margin:0;color:#991b1b;font-weight:700;font-size:15px;">⚠️ Bukti Pembayaran Tidak Valid</p>
                        <p style="margin:6px 0 0;color:#b91c1c;font-size:13px;">Bukti pembayaran yang Anda kirim belum dapat divalidasi.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Body --}}
    <tr>
        <td style="padding:28px 40px;">
            <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#3D1B07;">
                Halo <strong>{{ $mitra->nama }}</strong>,
            </p>
            <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#6B5B4E;">
                Bukti pembayaran yang Anda kirim untuk transaksi/invoice <strong style="color:#dc2626;">{{ $kodeInvoice }}</strong> belum dapat kami validasi karena data pembayaran tidak sesuai atau bukti kurang jelas.
            </p>
            <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#6B5B4E;">
                Silakan melakukan upload ulang bukti pembayaran yang valid melalui sistem JoFresh Inventory System agar pembayaran dapat segera diproses.
            </p>

            {{-- Upload Button --}}
            @if($mitra->payment_token)
            <table width="100%" cellpadding="0" cellspacing="0" style="margin:24px 0;">
                <tr>
                    <td align="center">
                        <a href="{{ url('/pembayaran/' . $mitra->payment_token) }}" 
                           style="display:inline-block;background:linear-gradient(135deg,#7B3911,#A1511E);color:#fff;text-decoration:none;padding:14px 40px;border-radius:12px;font-weight:700;font-size:14px;letter-spacing:0.5px;">
                            Upload Ulang Bukti Pembayaran
                        </a>
                    </td>
                </tr>
            </table>
            @endif

            <p style="margin:0;font-size:15px;line-height:1.7;color:#6B5B4E;">
                Terima kasih atas perhatian dan kerja samanya.
            </p>
        </td>
    </tr>

    {{-- Footer --}}
    <tr>
        <td style="background:#FAF5EF;padding:28px 40px;border-top:1px solid #E0D5CA;text-align:center;">
            <p style="margin:0 0 4px;color:#6B5B4E;font-size:13px;font-weight:600;">Salam,</p>
            <p style="margin:0 0 12px;color:#7B3911;font-size:15px;font-weight:800;">JoFresh Inventory System</p>
            <p style="margin:0;font-size:11px;color:#9C8B7E;">
                Email ini dikirim secara otomatis oleh sistem. Mohon tidak membalas email ini.
            </p>
        </td>
    </tr>

</table>
</td></tr>
</table>
</body>
</html>
