<!DOCTYPE html>
<html
    lang="id"
    xmlns="http://www.w3.org/1999/xhtml"
    xmlns:v="urn:schemas-microsoft-com:vml"
    xmlns:o="urn:schemas-microsoft-com:office:office"
>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta
        name="format-detection"
        content="telephone=no,date=no,address=no,email=no"
    />
    <title>Pembayaran Berhasil</title>
    <!--[if mso]>
        <noscript
            ><xml
                ><o:OfficeDocumentSettings
                    ><o:PixelsPerInch
                        >96</o:PixelsPerInch
                    ></o:OfficeDocumentSettings
                ></xml
            ></noscript
        >
    <![endif]-->
    <style>
        body,
        table,
        td,
        a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }
        body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }

        @media screen and (max-width: 600px) {
            .wrap {
                width: 100% !important;
                min-width: 100% !important;
            }
            .mobile-pad {
                padding: 24px 18px !important;
            }
            .mobile-head {
                padding: 22px 18px !important;
            }
            .mobile-foot {
                padding: 16px 18px !important;
            }
            .school-name {
                font-size: 14px !important;
            }
            .hero-title {
                font-size: 20px !important;
            }
            .hero-sub {
                font-size: 12px !important;
            }
            .row-label,
            .row-value {
                display: block !important;
                width: 100% !important;
            }
            .row-label {
                border-bottom: none !important;
                border-right: none !important;
                padding-bottom: 4px !important;
                padding-top: 10px !important;
            }
            .row-value {
                border-bottom: 1px solid #e2e8f0 !important;
                padding-top: 0 !important;
                padding-bottom: 10px !important;
            }
            .amount-val {
                font-size: 18px !important;
            }
            .contact-box {
                padding: 14px 18px !important;
            }
            .total-box {
                padding: 16px 20px !important;
            }
        }
    </style>
</head>

{{-- Semua variable (schoolName, schoolEmail, dll) sudah di-inject oleh PaymentSuccessMail --}}
@php
    $studentName = optional($transaction->user)->name ?? "Siswa";
    $amount = "Rp " . number_format((float) $transaction->amount, 0, ",", ".");
    $payMethod = strtoupper(
        str_replace("_", " ", $transaction->payment_method ?? "Transfer Manual"),
    );
    $paidAt = $transaction->paid_at
        ? \Carbon\Carbon::parse($transaction->paid_at)->translatedFormat(
                "d F Y, H:i",
            ) . " WIB"
        : "-";
    $invoiceNum = optional($transaction->invoice)->number ?? null;
@endphp

<body
    style="
        margin: 0;
        padding: 0;
        background-color: #eef2f7;
        font-family: Arial, Helvetica, sans-serif;
    "
>
    {{-- PREHEADER --}}
    <div
        style="
            display: none;
            max-height: 0;
            overflow: hidden;
            mso-hide: all;
            font-size: 1px;
            line-height: 1px;
            color: #eef2f7;
        "
    >
        Pembayaran SPP {{ $amount }} telah dikonfirmasi ✅ — {{ $transaction->code }}
    </div>

    <table
        role="presentation"
        width="100%"
        cellpadding="0"
        cellspacing="0"
        border="0"
        style="background-color: #eef2f7"
    >
        <tr>
            <td align="center" style="padding: 32px 12px">
                {{-- CARD --}}
                <table
                    class="wrap"
                    role="presentation"
                    width="600"
                    cellpadding="0"
                    cellspacing="0"
                    border="0"
                    style="
                        background-color: #ffffff;
                        border-radius: 16px;
                        overflow: hidden;
                        border: 1px solid #dde3ed;
                        box-shadow: 0 8px 32px rgba(15, 23, 42, 0.1);
                    "
                >
                    {{-- ═══ KOP SEKOLAH ═══ --}}
                    <tr>
                        <td
                            class="mobile-head"
                            bgcolor="#0f172a"
                            style="padding: 26px 36px 22px"
                        >
                            <table
                                role="presentation"
                                width="100%"
                                cellpadding="0"
                                cellspacing="0"
                                border="0"
                            >
                                <tr>
                                    <td
                                        width="52"
                                        valign="middle"
                                        style="padding-right: 14px"
                                    >
                                        <table
                                            role="presentation"
                                            cellpadding="0"
                                            cellspacing="0"
                                            border="0"
                                        >
                                            <tr>
                                                <td
                                                    width="52"
                                                    height="52"
                                                    bgcolor="#10b981"
                                                    style="
                                                        border-radius: 14px;
                                                        text-align: center;
                                                        vertical-align: middle;
                                                        font-size: 0;
                                                    "
                                                >
                                                    <span
                                                        style="
                                                            display: inline-block;
                                                            font-size: 24px;
                                                            font-weight: 900;
                                                            color: #ffffff;
                                                            line-height: 52px;
                                                        "
                                                        >M</span
                                                    >
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td valign="middle">
                                        <p class="school-name" style="
                                                margin: 0;
                                                font-size: 15px;
                                                font-weight: 700;
                                                color: #f1f5f9;
                                                line-height: 1.35;
                                                letter-spacing: -0.2px;
                                            ">{{ $schoolName }}</p>
                                        @if ($academicYear)
                                            <p style="
                                                    margin: 3px 0 0;
                                                    font-size: 10.5px;
                                                    color: #64748b;
                                                ">Tahun Ajaran {{ $academicYear }}</p>
                                        @endif
                                        @if ($schoolAddress)
                                            <p style="
                                                    margin: 2px 0 0;
                                                    font-size: 10px;
                                                    color: #475569;
                                                    line-height: 1.5;
                                                ">{{
                                                Str::limit(
                                                    $schoolAddress,
                                                    70,
                                                )
                                            }}</p>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- ═══ HERO SUCCESS ═══ --}}
                    <tr>
                        <td
                            bgcolor="#10b981"
                            style="padding: 28px 36px 24px; text-align: center"
                        >
                            {{-- checkmark circle --}}
                            <table
                                role="presentation"
                                cellpadding="0"
                                cellspacing="0"
                                border="0"
                                align="center"
                                style="margin: 0 auto 14px"
                            >
                                <tr>
                                    <td
                                        width="64"
                                        height="64"
                                        bgcolor="rgba(255,255,255,0.20)"
                                        style="
                                            border-radius: 50%;
                                            text-align: center;
                                            vertical-align: middle;
                                        "
                                    >
                                        <span
                                            style="
                                                font-size: 30px;
                                                line-height: 64px;
                                                display: block;
                                            "
                                            >✅</span
                                        >
                                    </td>
                                </tr>
                            </table>
                            <p class="hero-title" style="
                                    margin: 0;
                                    font-size: 22px;
                                    font-weight: 700;
                                    color: #ffffff;
                                    letter-spacing: -0.4px;
                                ">Pembayaran Berhasil!</p>
                            <p class="hero-sub" style="margin: 7px 0 0; font-size: 13px; color: rgba(255, 255, 255, 0.82); line-height: 1.5">SPP Anda telah dikonfirmasi &amp; tercatat dalam sistem sekolah</p>
                        </td>
                    </tr>

                    {{-- ═══ TOTAL BOX ═══ --}}
                    <tr>
                        <td style="padding: 0 36px">
                            <table
                                role="presentation"
                                width="100%"
                                cellpadding="0"
                                cellspacing="0"
                                border="0"
                                style="
                                    background: linear-gradient(
                                        135deg,
                                        #0f172a 0%,
                                        #1e293b 100%
                                    );
                                    border-radius: 12px;
                                    margin-top: -12px;
                                    position: relative;
                                    overflow: hidden;
                                "
                            >
                                <tr>
                                    <td
                                        class="total-box"
                                        style="
                                            padding: 18px 24px;
                                            text-align: center;
                                        "
                                    >
                                        <p style="
                                                margin: 0 0 4px;
                                                font-size: 10px;
                                                font-weight: 700;
                                                text-transform: uppercase;
                                                letter-spacing: 1.2px;
                                                color: #64748b;
                                            ">Total Terbayar</p>
                                        <p class="amount-val" style="
                                                margin: 0;
                                                font-size: 26px;
                                                font-weight: 700;
                                                color: #10b981;
                                                font-family: monospace;
                                                letter-spacing: -0.5px;
                                            ">{{ $amount }}</p>
                                        <p style="
                                                margin: 5px 0 0;
                                                font-size: 11px;
                                                color: #475569;
                                                font-family: monospace;
                                            ">{{ $transaction->code }}</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- ═══ BODY ═══ --}}
                    <tr>
                        <td class="mobile-pad" style="padding: 26px 36px 24px">
                            <p style="
                                    margin: 0 0 18px;
                                    font-size: 14px;
                                    color: #334155;
                                    line-height: 1.7;
                                ">Halo <strong style="color: #0f172a;">{{ $studentName }}</strong>,<br />
                            Pembayaran SPP Anda telah berhasil diverifikasi dan tercatat dalam sistem keuangan sekolah.</p>

                            {{-- INFO TABLE --}}
                            <table
                                role="presentation"
                                width="100%"
                                cellpadding="0"
                                cellspacing="0"
                                border="0"
                                style="
                                    border: 1px solid #e2e8f0;
                                    border-radius: 10px;
                                    overflow: hidden;
                                    margin-bottom: 22px;
                                "
                            >
                                <tr>
                                    <td
                                        colspan="2"
                                        bgcolor="#f8fafc"
                                        style="
                                            padding: 10px 16px;
                                            border-bottom: 1px solid #e2e8f0;
                                        "
                                    >
                                        <p style="
                                                margin: 0;
                                                font-size: 10px;
                                                font-weight: 700;
                                                text-transform: uppercase;
                                                letter-spacing: 1px;
                                                color: #94a3b8;
                                            ">Detail Pembayaran</p>
                                    </td>
                                </tr>

                                {{-- Kode Transaksi --}}
                                <tr>
                                    <td
                                        class="row-label"
                                        width="42%"
                                        bgcolor="#f8fafc"
                                        style="
                                            padding: 11px 16px;
                                            font-size: 12px;
                                            font-weight: 700;
                                            color: #64748b;
                                            border-bottom: 1px solid #e2e8f0;
                                            border-right: 1px solid #e2e8f0;
                                            text-transform: uppercase;
                                            letter-spacing: 0.4px;
                                        "
                                    >
                                        Kode Transaksi
                                    </td>
                                    <td
                                        class="row-value"
                                        style="
                                            padding: 11px 16px;
                                            font-size: 13px;
                                            color: #1e293b;
                                            font-family: monospace;
                                            font-weight: 700;
                                            border-bottom: 1px solid #e2e8f0;
                                        "
                                    >
                                        {{ $transaction->code }}
                                    </td>
                                </tr>

                                {{-- Invoice --}}
                                @if ($invoiceNum)
                                    <tr>
                                        <td
                                            class="row-label"
                                            width="42%"
                                            bgcolor="#f8fafc"
                                            style="
                                                padding: 11px 16px;
                                                font-size: 12px;
                                                font-weight: 700;
                                                color: #64748b;
                                                border-bottom: 1px solid #e2e8f0;
                                                border-right: 1px solid #e2e8f0;
                                                text-transform: uppercase;
                                                letter-spacing: 0.4px;
                                            "
                                        >
                                            No. Invoice
                                        </td>
                                        <td
                                            class="row-value"
                                            style="
                                                padding: 11px 16px;
                                                font-size: 13px;
                                                color: #1e293b;
                                                font-family: monospace;
                                                border-bottom: 1px solid #e2e8f0;
                                            "
                                        >
                                            {{ $invoiceNum }}
                                        </td>
                                    </tr>
                                @endif

                                {{-- Nominal --}}
                                <tr>
                                    <td
                                        class="row-label"
                                        width="42%"
                                        bgcolor="#f8fafc"
                                        style="
                                            padding: 11px 16px;
                                            font-size: 12px;
                                            font-weight: 700;
                                            color: #64748b;
                                            border-bottom: 1px solid #e2e8f0;
                                            border-right: 1px solid #e2e8f0;
                                            text-transform: uppercase;
                                            letter-spacing: 0.4px;
                                        "
                                    >
                                        Nominal
                                    </td>
                                    <td
                                        class="row-value"
                                        style="
                                            padding: 11px 16px;
                                            font-size: 15px;
                                            font-weight: 700;
                                            color: #10b981;
                                            border-bottom: 1px solid #e2e8f0;
                                        "
                                    >
                                        {{ $amount }}
                                    </td>
                                </tr>

                                {{-- Metode --}}
                                <tr>
                                    <td
                                        class="row-label"
                                        width="42%"
                                        bgcolor="#f8fafc"
                                        style="
                                            padding: 11px 16px;
                                            font-size: 12px;
                                            font-weight: 700;
                                            color: #64748b;
                                            border-bottom: 1px solid #e2e8f0;
                                            border-right: 1px solid #e2e8f0;
                                            text-transform: uppercase;
                                            letter-spacing: 0.4px;
                                        "
                                    >
                                        Metode
                                    </td>
                                    <td
                                        class="row-value"
                                        style="
                                            padding: 11px 16px;
                                            font-size: 13px;
                                            color: #1e293b;
                                            border-bottom: 1px solid #e2e8f0;
                                        "
                                    >
                                        {{ $payMethod }}
                                    </td>
                                </tr>

                                {{-- Tanggal Lunas --}}
                                <tr>
                                    <td
                                        class="row-label"
                                        width="42%"
                                        bgcolor="#f8fafc"
                                        style="
                                            padding: 11px 16px;
                                            font-size: 12px;
                                            font-weight: 700;
                                            color: #64748b;
                                            border-right: 1px solid #e2e8f0;
                                            text-transform: uppercase;
                                            letter-spacing: 0.4px;
                                        "
                                    >
                                        Tanggal Lunas
                                    </td>
                                    <td
                                        class="row-value"
                                        style="
                                            padding: 11px 16px;
                                            font-size: 13px;
                                            color: #1e293b;
                                            font-weight: 600;
                                        "
                                    >
                                        {{ $paidAt }}
                                    </td>
                                </tr>
                            </table>

                            {{-- REMINDER BOX --}}
                            <table
                                role="presentation"
                                width="100%"
                                cellpadding="0"
                                cellspacing="0"
                                border="0"
                                style="margin-bottom: 8px"
                            >
                                <tr>
                                    <td
                                        style="
                                            background-color: #fffbeb;
                                            border: 1px solid #fde68a;
                                            border-radius: 8px;
                                            padding: 13px 16px;
                                            text-align: center;
                                        "
                                    >
                                        <p style="
                                                margin: 0;
                                                font-size: 12.5px;
                                                color: #92400e;
                                                line-height: 1.6;
                                            ">📌 Simpan email ini sebagai <strong>bukti pembayaran elektronik resmi</strong> yang sah.</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- ═══ KONTAK BOX ═══ --}}
                    @if ($schoolPhone || $schoolEmail)
                        <tr>
                            <td style="padding: 0 36px 24px">
                                <table
                                    role="presentation"
                                    width="100%"
                                    cellpadding="0"
                                    cellspacing="0"
                                    border="0"
                                    style="
                                        background-color: #f8fafc;
                                        border: 1px solid #e2e8f0;
                                        border-radius: 10px;
                                    "
                                >
                                    <tr>
                                        <td
                                            class="contact-box"
                                            style="
                                                padding: 14px 20px;
                                                text-align: center;
                                            "
                                        >
                                            <p style="
                                                    margin: 0 0 4px;
                                                    font-size: 10px;
                                                    font-weight: 700;
                                                    text-transform: uppercase;
                                                    letter-spacing: 1px;
                                                    color: #94a3b8;
                                                ">Ada Pertanyaan?</p>
                                            <p style="
                                                    margin: 0;
                                                    font-size: 12.5px;
                                                    color: #475569;
                                                    line-height: 1.7;
                                                ">
                                                @if ($schoolPhone) 📞{{ $schoolPhone }}@endif
                                                @if ($schoolPhone && $schoolEmail)
                                                    <span
                                                        style="color: #cbd5e1"
                                                    >
                                                        &nbsp;|&nbsp;
                                                    </span>
                                                @endif
                                                @if ($schoolEmail)
                                                    ✉
                                                    <a
                                                        href="mailto:{{ $schoolEmail }}"
                                                        style="
                                                            color: #10b981;
                                                            text-decoration: none;
                                                        "
                                                        >{{ $schoolEmail }}</a
                                                    >
                                                @endif
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif

                    {{-- ═══ FOOTER ═══ --}}
                    <tr>
                        <td
                            class="mobile-foot"
                            bgcolor="#f8fafc"
                            style="
                                padding: 18px 36px;
                                text-align: center;
                                border-top: 1px solid #e2e8f0;
                                border-radius: 0 0 16px 16px;
                            "
                        >
                            <p style="
                                    margin: 0;
                                    font-size: 11px;
                                    color: #94a3b8;
                                    line-height: 1.7;
                                ">Email ini dikirim secara otomatis oleh sistem <strong>{{ $schoolName }}</strong>.<br />
                            Mohon tidak membalas email ini langsung.</p>
                            <p style="
                                    margin: 8px 0 0;
                                    font-size: 10.5px;
                                    color: #cbd5e1;
                                ">
                                &copy; {{ date("Y") }} {{ $schoolName }} &middot;
                                Powered by <strong>MySPP</strong>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
