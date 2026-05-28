<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil Diverifikasi - JoFresh</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f6f9; color: #333;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f6f9; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.06);">
                    
                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #16a34a, #15803d); padding: 32px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 900; letter-spacing: 4px;">JoFresh</h1>
                            <p style="margin: 6px 0 0 0; color: rgba(255,255,255,0.85); font-size: 13px;">Inventory System</p>
                        </td>
                    </tr>

                    {{-- Success Banner --}}
                    <tr>
                        <td style="padding: 24px 40px 0;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px;">
                                <tr>
                                    <td style="padding: 16px; text-align: center;">
                                        <p style="margin: 0; font-size: 40px;">✅</p>
                                        <p style="margin: 8px 0 0 0; color: #166534; font-weight: 700; font-size: 16px;">Pembayaran Berhasil Diverifikasi!</p>
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
                                Pembayaran untuk transaksi/invoice <strong style="color: #16a34a;">{{ $kodeInvoice }}</strong> telah berhasil diverifikasi dan diterima.
                            </p>
                            <p style="margin: 0 0 16px 0; font-size: 15px; line-height: 1.7; color: #334155;">
                                Invoice pembayaran dengan status <strong style="color: #16a34a;">LUNAS</strong> telah kami lampirkan pada email ini sebagai bukti pembayaran resmi.
                            </p>

                            {{-- Invoice Info Box --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; margin: 20px 0;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="font-size: 13px; color: #64748b; padding-bottom: 8px;">No. Invoice</td>
                                                <td style="font-size: 13px; font-weight: 700; color: #1e293b; padding-bottom: 8px; text-align: right;">{{ $kodeInvoice }}</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 13px; color: #64748b; padding-bottom: 8px;">Status</td>
                                                <td style="font-size: 13px; font-weight: 800; color: #16a34a; padding-bottom: 8px; text-align: right;">✓ LUNAS</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 13px; color: #64748b;">Lampiran</td>
                                                <td style="font-size: 13px; font-weight: 600; color: #1e293b; text-align: right;">📎 Invoice PDF LUNAS</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0; font-size: 15px; line-height: 1.7; color: #334155;">
                                Terima kasih telah melakukan pembayaran tepat waktu. Transaksi Anda kini telah dinyatakan selesai.
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
