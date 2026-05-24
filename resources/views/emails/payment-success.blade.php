<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pembayaran Berhasil MySPP</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f5f7; margin: 0; padding: 20px;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0;">
        <tr>
            <td bgcolor="#10b981" style="padding: 30px 20px; text-align: center; color: white;">
                <div style="font-size: 40px; margin-bottom: 10px;">✅</div>
                <h1 style="margin: 0; font-size: 24px;">Pembayaran Berhasil!</h1>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">Terima kasih atas pembayaran Anda</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 30px 20px; color: #334155;">
                <p>Halo <strong>{{ $transaction->student->name ?? 'Siswa' }}</strong>,</p>
                <p>Pembayaran administrasi sekolah Anda telah berhasil diverifikasi dan tercatat ke dalam sistem database keuangan sekolah.</p>

                <table width="100%" style="margin: 20px 0; border-collapse: collapse; background-color: #f8fafc; border-radius: 6px;">
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; font-weight: bold; width: 40%;">Kode Transaksi</td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; font-family: monospace; font-size: 14px;">{{ $transaction->transaction_code }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; font-weight: bold;">Nominal Terbayar</td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; color: #10b981; font-weight: bold;">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; font-weight: bold;">Metode Pembayaran</td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; text-transform: uppercase;">{{ str_replace('_', ' ', $transaction->payment_method ?? 'Manual Transfer') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; font-weight: bold;">Tanggal Lunas</td>
                        <td style="padding: 12px;">{{ \Carbon\Carbon::parse($transaction->paid_at)->format('d F Y H:i') }} WIB</td>
                    </tr>
                </table>

                <p style="background-color: #fffbeb; border: 1px solid #fef3c7; color: #78350f; padding: 12px; border-radius: 6px; font-size: 13px; text-align: center;">
                    Silakan simpan notifikasi email ini sebagai bukti pembayaran elektronik yang sah.
                </p>
            </td>
        </tr>
        <tr>
            <td bgcolor="#f1f5f9" style="padding: 20px; text-align: center; font-size: 12px; color: #64748b;">
                <p style="margin: 0;">Email ini didistribusikan secara otomatis oleh modul keuangan MySPP.</p>
                <p style="margin: 5px 0 0 0;">&copy; {{ date('Y') }} MySPP Project. All rights reserved.</p>
            </td>
        </tr>
    </table>
</body>
</html>
