<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pembayaran Tidak Valid - JoFresh</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f6f9; color: #333;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f6f9; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.06);">
                    
                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #dc2626, #b91c1c); padding: 32px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 900; letter-spacing: 4px;">JoFresh</h1>
                            <p style="margin: 6px 0 0 0; color: rgba(255,255,255,0.85); font-size: 13px;">Inventory System</p>
                        </td>
                    </tr>

                    {{-- Alert Banner --}}
                    <tr>
                        <td style="padding: 24px 40px 0;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 16px;">
                                <tr>
                                    <td style="padding: 16px;">
                                        <p style="margin: 0; color: #991b1b; font-weight: 700; font-size: 15px;">⚠️ Bukti Pembayaran Tidak Valid</p>
                                        <p style="margin: 6px 0 0 0; color: #b91c1c; font-size: 13px;">Bukti pembayaran yang Anda kirim belum dapat divalidasi.</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 28px 40px;">
                            <p style="margin: 0 0 16px 0; font-size: 15px; line-height: 1.7; color: #334155;">
                                Halo <strong>{{ $mitra->nama }}</strong>,
                            </p>
                            <p style="margin: 0 0 16px 0; font-size: 15px; line-height: 1.7; color: #334155;">
                                Bukti pembayaran yang Anda kirim untuk transaksi/invoice <strong style="color: #dc2626;">{{ $kodeInvoice }}</strong> belum dapat kami validasi karena data pembayaran tidak sesuai atau bukti kurang jelas.
                            </p>
                            <p style="margin: 0 0 16px 0; font-size: 15px; line-height: 1.7; color: #334155;">
                                Silakan melakukan upload ulang bukti pembayaran yang valid melalui sistem JoFresh Inventory System agar pembayaran dapat segera diproses.
                            </p>

                            {{-- Upload Button --}}
                            @if($mitra->payment_token)
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 24px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ url('/pembayaran/' . $mitra->payment_token) }}" 
                                           style="display: inline-block; background: linear-gradient(135deg, #dc2626, #b91c1c); color: #ffffff; text-decoration: none; padding: 14px 40px; border-radius: 12px; font-weight: 700; font-size: 14px; letter-spacing: 0.5px;">
                                            Upload Ulang Bukti Pembayaran
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <p style="margin: 0; font-size: 15px; line-height: 1.7; color: #334155;">
                                Terima kasih atas perhatian dan kerja samanya.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding: 24px 40px; background-color: #f8fafc; border-top: 1px solid #e2e8f0; text-align: center;">
                            <p style="margin: 0 0 4px 0; font-size: 13px; color: #64748b; font-weight: 600;">Salam,</p>
                            <p style="margin: 0 0 12px 0; font-size: 15px; color: #1e3a5f; font-weight: 800;">JoFresh Inventory System</p>
                            <p style="margin: 0; font-size: 11px; color: #94a3b8;">
                                Email ini dikirim secara otomatis oleh sistem. Mohon tidak membalas email ini.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
