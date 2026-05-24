<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tagihan Baru MySPP</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f5f7; margin: 0; padding: 20px;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0;">
        <tr>
            <td bgcolor="#10b981" style="padding: 30px 20px; text-align: center; color: white;">
                <h1 style="margin: 0; font-size: 24px;">Tagihan SPP Baru</h1>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">Aplikasi Manajemen Keuangan Sekolah MySPP</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 30px 20px; color: #334155;">
                <p>Halo <strong>{{ $invoice->student->user->name ?? 'Siswa' }}</strong>,</p>
                <p>Tagihan SPP baru Anda telah diterbitkan oleh pihak sekolah. Silakan lakukan pembayaran sebelum melewati batas waktu jatuh tempo.</p>

                <table width="100%" style="margin: 20px 0; border-collapse: collapse; background-color: #f8fafc; border-radius: 6px;">
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; font-weight: bold; width: 40%;">Nomor Invoice</td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">{{ $invoice->number }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; font-weight: bold;">Jurusan / Kelas</td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">{{ $invoice->department->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; font-weight: bold;">Nominal Tagihan</td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0; color: #10b981; font-weight: bold;">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; font-weight: bold;">Jatuh Tempo</td>
                        <td style="padding: 12px; color: #ef4444; font-weight: bold;">{{ \Carbon\Carbon::parse($invoice->due_date)->format('d F Y') }}</td>
                    </tr>
                </table>

                @if($invoice->notes)
                <div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 15px; margin-bottom: 25px; border-radius: 4px;">
                    <p style="margin: 0; font-weight: bold; color: #1e40af; font-size: 14px;">Catatan dari Sekolah:</p>
                    <p style="margin: 5px 0 0 0; color: #1e3a8a; font-style: italic; font-size: 14px;">"{{ $invoice->notes }}"</p>
                </div>
                @endif

                <table align="center" border="0" cellpadding="0" cellspacing="0" style="margin-top: 20px;">
                    <tr>
                        <td align="center" bgcolor="#10b981" style="border-radius: 6px;">
                            <a href="{{ url('/dashboard') }}" target="_blank" style="display: inline-block; padding: 14px 30px; color: white; text-decoration: none; font-weight: bold; font-size: 16px;">Bayar Sekarang</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td bgcolor="#f1f5f9" style="padding: 20px; text-align: center; font-size: 12px; color: #64748b;">
                <p style="margin: 0;">Email ini dikirim secara otomatis oleh sistem aplikasi MySPP.</p>
                <p style="margin: 5px 0 0 0;">&copy; {{ date('Y') }} MySPP School Management System. All rights reserved.</p>
            </td>
        </tr>
    </table>
</body>
</html>
