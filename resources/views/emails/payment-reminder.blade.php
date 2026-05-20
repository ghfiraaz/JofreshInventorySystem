<!DOCTYPE html>
<html lang="id" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Rekapitulasi Transaksi - JoFresh</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f4f6f9; padding: 40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">

                    {{-- Header --}}
                    <tr>
                        <td style="background-color: #1e3a5f; padding: 28px 40px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: 2px;">JoFresh</h1>
                            <p style="color: rgba(255,255,255,0.7); margin: 4px 0 0 0; font-size: 12px;">Supplier Unggas Segar dan Terpercaya</p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 32px 40px;">

                            <p style="color: #334155; font-size: 14px; line-height: 1.7; margin: 0 0 16px 0;">
                                Yth. Bapak/Ibu <strong>{{ $mitra->nama }}</strong>,
                            </p>

                            <p style="color: #475569; font-size: 14px; line-height: 1.7; margin: 0 0 20px 0;">
                                Bersama email ini, kami sampaikan rekapitulasi transaksi yang tercatat pada sistem kami untuk periode <strong>{{ $periodeAwal }}</strong> sampai dengan <strong>{{ $periodeAkhir }}</strong>.
                            </p>

                            {{-- Info Box --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 20px;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="padding: 4px 0; color: #64748b; font-size: 13px; width: 140px;">Periode</td>
                                                <td style="padding: 4px 0; color: #1e293b; font-size: 13px; font-weight: 600;">{{ $periodeAwal }} s/d {{ $periodeAkhir }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 4px 0; color: #64748b; font-size: 13px;">Batas Waktu</td>
                                                <td style="padding: 4px 0; color: #1e293b; font-size: 13px; font-weight: 600;">{{ $tanggalTempo }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 4px 0; color: #64748b; font-size: 13px;">Jumlah Transaksi</td>
                                                <td style="padding: 4px 0; color: #1e293b; font-size: 13px; font-weight: 600;">{{ $transaksiList->count() }} transaksi</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 4px 0; color: #64748b; font-size: 13px;">Total</td>
                                                <td style="padding: 4px 0; color: #1e293b; font-size: 15px; font-weight: 700;">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- Daftar Transaksi --}}
                            <p style="color: #475569; font-size: 12px; font-weight: 700; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 1px;">Rincian Transaksi</p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden; margin-bottom: 24px;">
                                <tr style="background-color: #f1f5f9;">
                                    <th style="padding: 8px 12px; text-align: left; font-size: 11px; color: #64748b; font-weight: 600; border-bottom: 1px solid #e2e8f0;">No. Transaksi</th>
                                    <th style="padding: 8px 12px; text-align: left; font-size: 11px; color: #64748b; font-weight: 600; border-bottom: 1px solid #e2e8f0;">Tanggal</th>
                                    <th style="padding: 8px 12px; text-align: right; font-size: 11px; color: #64748b; font-weight: 600; border-bottom: 1px solid #e2e8f0;">Total</th>
                                </tr>
                                @foreach($transaksiList as $tx)
                                <tr>
                                    <td style="padding: 8px 12px; font-size: 13px; color: #334155; border-bottom: 1px solid #f1f5f9;">{{ $tx->no_transaksi }}</td>
                                    <td style="padding: 8px 12px; font-size: 13px; color: #64748b; border-bottom: 1px solid #f1f5f9;">{{ $tx->created_at->format('d/m/Y') }}</td>
                                    <td style="padding: 8px 12px; font-size: 13px; color: #1e293b; font-weight: 600; text-align: right; border-bottom: 1px solid #f1f5f9;">Rp {{ number_format($tx->total_harga, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </table>

                            {{-- CTA Button --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin: 0 0 24px 0;">
                                <tr>
                                    <td align="center">
                                        <p style="color: #475569; font-size: 13px; margin: 0 0 12px 0;">Silakan klik tombol di bawah untuk mengunggah bukti pembayaran:</p>
                                        <a href="{{ $paymentLink }}" target="_blank" style="display: inline-block; background-color: #1e3a5f; color: #ffffff; text-decoration: none; padding: 12px 32px; border-radius: 8px; font-size: 14px; font-weight: 600;">
                                            Lihat Detail &amp; Upload Bukti
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #475569; font-size: 13px; line-height: 1.7; margin: 0 0 12px 0;">
                                Kami juga melampirkan dokumen rekapitulasi (PDF) pada email ini untuk arsip Anda. Dokumen tersebut berisi rincian lengkap beserta kode QRIS untuk kemudahan pembayaran.
                            </p>

                            <p style="color: #475569; font-size: 13px; line-height: 1.7; margin: 0 0 12px 0;">
                                Mohon kiranya dapat diselesaikan sebelum tanggal <strong>{{ $tanggalTempo }}</strong>.
                            </p>

                            <p style="color: #94a3b8; font-size: 12px; line-height: 1.6; margin: 20px 0 0 0;">
                                Jika pembayaran sudah dilakukan, Anda dapat mengabaikan email ini. Terima kasih atas kerja sama yang baik.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f8fafc; padding: 20px 40px; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0 0 4px 0; color: #1e3a5f; font-size: 13px; font-weight: 700;">Hormat kami,</p>
                            <p style="margin: 0 0 12px 0; color: #64748b; font-size: 12px;">Tim JoFresh</p>
                            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 0 0 10px 0;">
                            <p style="margin: 0; color: #94a3b8; font-size: 10px; text-align: center; line-height: 1.6;">
                                Email ini dikirim kepada {{ $mitra->nama }} sebagai mitra terdaftar di JoFresh Inventory System.<br>
                                JoFresh - Jl. Elang Mutiara, Periuk Jaya, Kota Tangerang, Banten 15131<br>
                                &copy; {{ date('Y') }} JoFresh. Hak cipta dilindungi undang-undang.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
