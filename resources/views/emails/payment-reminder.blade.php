<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reminder Pembayaran - JoFresh</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f1f5f9; padding: 40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.06);">

                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e3a5f 0%, #2d5a8e 100%); padding: 36px 40px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 32px; font-weight: 800; letter-spacing: 3px;">JoFresh</h1>
                            <p style="color: rgba(255,255,255,0.75); margin: 6px 0 0 0; font-size: 13px; letter-spacing: 1px;">Sistem Inventaris & Penjualan Unggas Terpercaya</p>
                        </td>
                    </tr>

                    {{-- Title --}}
                    <tr>
                        <td style="padding: 32px 40px 8px 40px;">
                            <div style="background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 0 8px 8px 0; padding: 14px 20px;">
                                <p style="margin: 0; color: #92400e; font-weight: 700; font-size: 15px;">⏰ Reminder Tagihan Pembayaran</p>
                            </div>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 24px 40px;">
                            <p style="color: #334155; font-size: 15px; line-height: 1.7; margin: 0 0 18px 0;">
                                Yth. Bapak/Ibu <strong>{{ $mitra->nama }}</strong>,
                            </p>
                            <p style="color: #475569; font-size: 14px; line-height: 1.7; margin: 0 0 18px 0;">
                                Semoga Bapak/Ibu dalam keadaan baik. Melalui email ini kami ingin mengingatkan mengenai tagihan pembayaran berikut:
                            </p>

                            {{-- Info Box --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px 24px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="padding: 4px 0; color: #64748b; font-size: 13px; width: 140px;">Periode Tagihan</td>
                                                <td style="padding: 4px 0; color: #1e293b; font-size: 13px; font-weight: 600;">{{ $periodeAwal }} — {{ $periodeAkhir }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 4px 0; color: #64748b; font-size: 13px;">Jatuh Tempo</td>
                                                <td style="padding: 4px 0; color: #dc2626; font-size: 13px; font-weight: 700;">{{ $tanggalTempo }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 4px 0; color: #64748b; font-size: 13px;">Jumlah Transaksi</td>
                                                <td style="padding: 4px 0; color: #1e293b; font-size: 13px; font-weight: 600;">{{ $transaksiList->count() }} transaksi</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- Total Tagihan Box --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background: linear-gradient(135deg, #1e3a5f 0%, #2d5a8e 100%); border-radius: 12px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 24px 28px; text-align: center;">
                                        <p style="margin: 0 0 6px 0; color: rgba(255,255,255,0.7); font-size: 12px; text-transform: uppercase; letter-spacing: 2px;">Total Tagihan</p>
                                        <p style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 800;">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</p>
                                    </td>
                                </tr>
                            </table>

                            {{-- Daftar Transaksi --}}
                            <p style="color: #475569; font-size: 13px; font-weight: 700; margin: 0 0 10px 0; text-transform: uppercase; letter-spacing: 1px;">Rincian Transaksi:</p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; margin-bottom: 24px;">
                                <tr style="background-color: #f1f5f9;">
                                    <th style="padding: 10px 14px; text-align: left; font-size: 11px; color: #64748b; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">No. Transaksi</th>
                                    <th style="padding: 10px 14px; text-align: left; font-size: 11px; color: #64748b; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">Tanggal</th>
                                    <th style="padding: 10px 14px; text-align: right; font-size: 11px; color: #64748b; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">Total</th>
                                </tr>
                                @foreach($transaksiList as $tx)
                                <tr>
                                    <td style="padding: 10px 14px; font-size: 13px; color: #334155; border-bottom: 1px solid #f1f5f9;">{{ $tx->no_transaksi }}</td>
                                    <td style="padding: 10px 14px; font-size: 13px; color: #64748b; border-bottom: 1px solid #f1f5f9;">{{ $tx->created_at->format('d/m/Y') }}</td>
                                    <td style="padding: 10px 14px; font-size: 13px; color: #1e293b; font-weight: 600; text-align: right; border-bottom: 1px solid #f1f5f9;">Rp {{ number_format($tx->total_harga, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </table>

                            {{-- QR Code Section --}}
                            <div style="text-align: center; margin: 28px 0;">
                                <p style="color: #475569; font-size: 13px; font-weight: 700; margin: 0 0 12px 0; text-transform: uppercase; letter-spacing: 1px;">Scan QR untuk Pembayaran:</p>
                                <div style="display: inline-block; padding: 16px; background: #ffffff; border: 2px solid #e2e8f0; border-radius: 12px;">
                                    <img src="{{ $message->embed(public_path('images/qris-jofresh.png')) }}" alt="QRIS Pembayaran JoFresh" width="200" height="200" style="display: block;">
                                </div>
                                <p style="color: #94a3b8; font-size: 11px; margin: 8px 0 0 0;">QRIS Pembayaran JoFresh</p>
                            </div>

                            {{-- CTA Button --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin: 28px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $paymentLink }}" target="_blank" style="display: inline-block; background: linear-gradient(135deg, #1e3a5f 0%, #2d5a8e 100%); color: #ffffff; text-decoration: none; padding: 14px 36px; border-radius: 10px; font-size: 14px; font-weight: 700; letter-spacing: 0.5px;">
                                            Upload Bukti Pembayaran →
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #475569; font-size: 14px; line-height: 1.7; margin: 0 0 12px 0;">
                                Kami juga melampirkan <strong>PDF Invoice Rekapitulasi</strong> dan <strong>QR Code pembayaran</strong> pada email ini.
                            </p>

                            <p style="color: #475569; font-size: 14px; line-height: 1.7; margin: 0 0 12px 0;">
                                Kami mohon kesediaannya untuk segera melakukan pembayaran sesuai jadwal yang telah disepakati.
                            </p>

                            <p style="color: #94a3b8; font-size: 13px; line-height: 1.6; margin: 24px 0 0 0; font-style: italic;">
                                Apabila pembayaran sudah dilakukan, mohon abaikan pesan ini. Terima kasih atas perhatian dan kerja sama yang baik.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f8fafc; padding: 24px 40px; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0 0 4px 0; color: #1e3a5f; font-size: 14px; font-weight: 700;">Hormat kami,</p>
                            <p style="margin: 0 0 16px 0; color: #64748b; font-size: 13px;">Tim JoFresh Inventory</p>
                            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 0 0 12px 0;">
                            <p style="margin: 0; color: #94a3b8; font-size: 11px; text-align: center;">
                                Email ini dikirim secara otomatis oleh JoFresh Inventory System.<br>
                                &copy; {{ date('Y') }} JoFresh. Semua Hak Cipta Dilindungi.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
