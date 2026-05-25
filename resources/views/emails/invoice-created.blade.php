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
    <title>Tagihan SPP Baru</title>
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
        /* CLIENT RESET */
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

        /* MOBILE */
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
            .mobile-hide {
                display: none !important;
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
                padding-bottom: 4px !important;
                padding-top: 10px !important;
            }
            .row-value {
                border-bottom: 1px solid #e2e8f0 !important;
                padding-top: 0 !important;
                padding-bottom: 10px !important;
            }
            .btn-full {
                width: 100% !important;
                display: block !important;
            }
            .contact-box {
                padding: 14px 18px !important;
            }
            .amount-text {
                font-size: 17px !important;
            }
        }
    </style>
</head>

{{-- Semua variable (schoolName, schoolEmail, dll) sudah di-inject oleh InvoiceCreatedMail --}}
@php
    $studentName = optional(optional($invoice->student)->user)->name ?? "Siswa";
    $deptName = optional($invoice->department)->name ?? "-";
    $dueDate = \Carbon\Carbon::parse($invoice->due_date)->translatedFormat("d F Y");
    $amount = "Rp " . number_format($invoice->amount, 0, ",", ".");
@endphp

<body
    style="
        margin: 0;
        padding: 0;
        background-color: #eef2f7;
        font-family: Arial, Helvetica, sans-serif;
    "
>
    {{-- PREHEADER hidden --}}
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
        Tagihan SPP baru telah diterbitkan • {{ $amount }} • Jatuh tempo: {{ $dueDate }}
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
                    {{-- ═══ KOP SEKOLAH (dark) ═══ --}}
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
                                    {{-- Logo bulat emerald --}}
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
                                    {{-- Identitas --}}
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

                    {{-- ═══ HERO BANNER (emerald) ═══ --}}
                    <tr>
                        <td
                            bgcolor="#10b981"
                            style="padding: 26px 36px 22px; text-align: center"
                        >
                            {{-- Icon --}}
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
                                        width="60"
                                        height="60"
                                        bgcolor="rgba(255,255,255,0.18)"
                                        style="
                                            border-radius: 50%;
                                            text-align: center;
                                            vertical-align: middle;
                                        "
                                    >
                                        <span
                                            style="
                                                font-size: 28px;
                                                line-height: 60px;
                                                display: block;
                                            "
                                            >📋</span
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
                                ">Tagihan SPP Baru</p>
                            <p class="hero-sub" style="margin: 7px 0 0; font-size: 13px; color: rgba(255, 255, 255, 0.82); line-height: 1.5">Harap selesaikan pembayaran sebelum batas jatuh tempo</p>
                        </td>
                    </tr>

                    {{-- ═══ BODY ═══ --}}
                    <tr>
                        <td class="mobile-pad" style="padding: 32px 36px 24px">
                            {{-- Sapaan --}}
                            <p style="
                                    margin: 0 0 18px;
                                    font-size: 14px;
                                    color: #334155;
                                    line-height: 1.7;
                                ">Halo <strong style="color: #0f172a;">{{ $studentName }}</strong>,<br />
                            Tagihan SPP baru telah diterbitkan oleh pihak sekolah. Silakan selesaikan pembayaran sebelum tanggal jatuh tempo.</p>

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
                                {{-- header row --}}
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
                                            ">Rincian Tagihan</p>
                                    </td>
                                </tr>

                                {{-- Nomor Invoice --}}
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
                                            font-weight: 700;
                                            border-bottom: 1px solid #e2e8f0;
                                        "
                                    >
                                        {{ $invoice->number }}
                                    </td>
                                </tr>

                                {{-- Jurusan --}}
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
                                        Jurusan
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
                                        {{ $deptName }}
                                    </td>
                                </tr>

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
                                            border-bottom: 1px solid #e2e8f0;
                                        "
                                    >
                                        <span
                                            class="amount-text"
                                            style="
                                                font-size: 16px;
                                                font-weight: 700;
                                                color: #10b981;
                                            "
                                            >{{ $amount }}</span
                                        >
                                    </td>
                                </tr>

                                {{-- Jatuh Tempo --}}
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
                                        Jatuh Tempo
                                    </td>
                                    <td
                                        class="row-value"
                                        style="padding: 11px 16px"
                                    >
                                        <span
                                            style="
                                                font-size: 13px;
                                                font-weight: 700;
                                                color: #ef4444;
                                            "
                                            >⚠ {{ $dueDate }}</span
                                        >
                                    </td>
                                </tr>
                            </table>

                            {{-- Catatan sekolah --}}
                            @if (!empty($invoice->notes))
                                <table
                                    role="presentation"
                                    width="100%"
                                    cellpadding="0"
                                    cellspacing="0"
                                    border="0"
                                    style="margin-bottom: 22px"
                                >
                                    <tr>
                                        <td
                                            style="
                                                background-color: #eff6ff;
                                                border-left: 4px solid #3b82f6;
                                                border-radius: 0 8px 8px 0;
                                                padding: 14px 16px;
                                            "
                                        >
                                            <p style="
                                                    margin: 0 0 4px;
                                                    font-size: 11.5px;
                                                    font-weight: 700;
                                                    color: #1e40af;
                                                    text-transform: uppercase;
                                                    letter-spacing: 0.5px;
                                                ">📌 Catatan dari Sekolah</p>
                                            <p style="
                                                    margin: 0;
                                                    font-size: 13px;
                                                    color: #1e3a8a;
                                                    font-style: italic;
                                                    line-height: 1.6;
                                                ">"{{ $invoice->notes }}"</p>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            {{-- CTA BUTTON --}}
                            <table
                                role="presentation"
                                cellpadding="0"
                                cellspacing="0"
                                border="0"
                                align="center"
                                style="margin: 0 auto"
                            >
                                <tr>
                                    <td
                                        align="center"
                                        bgcolor="#10b981"
                                        style="
                                            border-radius: 10px;
                                            box-shadow: 0 4px 14px
                                                rgba(16, 185, 129, 0.35);
                                        "
                                    >
                                        <a
                                            class="btn-full"
                                            href="{{ url('/dashboard') }}"
                                            target="_blank"
                                            style="
                                                display: inline-block;
                                                padding: 14px 40px;
                                                color: #ffffff;
                                                text-decoration: none;
                                                font-weight: 700;
                                                font-size: 14px;
                                                letter-spacing: 0.3px;
                                                white-space: nowrap;
                                            "
                                        >
                                            Bayar Sekarang &rarr;
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="
                                    margin: 18px 0 0;
                                    text-align: center;
                                    font-size: 11.5px;
                                    color: #94a3b8;
                                ">
                                Atau login ke portal:
                                <a
                                    href="{{ url('/dashboard') }}"
                                    style="
                                        color: #10b981;
                                        text-decoration: none;
                                    "
                                    >{{
                                        url(
                                            "/dashboard",
                                        )
                                    }}</a
                                >
                            </p>
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
                                                ">Butuh Bantuan?</p>
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
                {{-- /CARD --}}
            </td>
        </tr>
    </table>
</body>
</html>
