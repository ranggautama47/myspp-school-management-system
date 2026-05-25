<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>
        Invoice {{ $transaction->code }} — {{
            $schoolName ??
                "MySPP"
        }}
    </title>
    <style>
        /* ── RESET ─────────────────────────────────────────── */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 12px;
            color: #1e293b;
            background: #ffffff;
            line-height: 1.5;
        }

        /* ── HEADER GELAP ──────────────────────────────────── */
        .header-wrap {
            background-color: #0f172a;
            padding: 26px 36px 22px 36px;
            width: 100%;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .school-name {
            font-size: 17px;
            font-weight: bold;
            color: #ffffff;
        }
        .school-meta {
            font-size: 9px;
            color: #94a3b8;
            margin-top: 4px;
            line-height: 1.7;
        }
        .badge-resmi {
            background-color: #10b981;
            color: #ffffff;
            font-size: 8px;
            font-weight: bold;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 2px 10px;
            border-radius: 20px;
        }
        .trx-code {
            font-family: "Courier New", monospace;
            font-size: 13px;
            color: #34d399;
            margin-top: 5px;
            font-weight: bold;
        }
        .ta-text {
            font-size: 9px;
            color: #64748b;
            margin-top: 2px;
        }

        /* ── ACCENT BAR ────────────────────────────────────── */
        .accent-bar {
            height: 4px;
            background-color: #10b981;
            width: 100%;
        }

        /* ── BODY ──────────────────────────────────────────── */
        .body-wrap {
            padding: 28px 36px;
        }

        /* ── META (tanggal + status) ───────────────────────── */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .lbl {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            margin-bottom: 3px;
        }
        .val {
            font-size: 12px;
            font-weight: bold;
            color: #1e293b;
        }

        /* ── STATUS BADGE ──────────────────────────────────── */
        .badge-paid {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-pending {
            background-color: #fef9c3;
            color: #92400e;
            border: 1px solid #fef08a;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-other {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
        }

        /* ── DIVIDER ───────────────────────────────────────── */
        .div-dash {
            border: none;
            border-top: 1.5px dashed #e2e8f0;
            margin: 18px 0;
        }
        .div-solid {
            border: none;
            border-top: 1.5px solid #e2e8f0;
        }

        /* ── PARTY TABLE ───────────────────────────────────── */
        .party-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .sec-lbl {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #10b981;
            margin-bottom: 5px;
        }
        .party-name {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
        }
        .party-sub {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }
        .party-mono {
            font-family: "Courier New", monospace;
            font-size: 10px;
            color: #64748b;
        }

        /* ── DETAIL TABLE ──────────────────────────────────── */
        .detail-table {
            width: 100%;
            border-collapse: collapse;
        }
        .detail-table thead tr {
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }
        .detail-table th {
            padding: 8px 12px;
            text-align: left;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            font-weight: bold;
        }
        .detail-table th.r {
            text-align: right;
        }
        .detail-table td {
            padding: 13px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }
        .item-name {
            font-size: 12px;
            font-weight: bold;
            color: #1e293b;
        }
        .item-code {
            font-family: "Courier New", monospace;
            font-size: 9px;
            color: #94a3b8;
            margin-top: 2px;
        }
        .item-ref {
            font-size: 9px;
            color: #10b981;
            margin-top: 2px;
        }
        .amt-cell {
            text-align: right;
            font-family: "Courier New", monospace;
            font-size: 12px;
            font-weight: bold;
            color: #1e293b;
        }

        /* ── TOTAL BOX ─────────────────────────────────────── */
        .total-outer {
            width: 100%;
            text-align: right;
            margin-top: 14px;
            margin-bottom: 20px;
        }
        .total-box {
            display: inline-block;
            background-color: #10b981;
            border-radius: 10px;
            padding: 12px 22px;
            text-align: right;
            min-width: 210px;
        }
        .total-lbl {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: rgba(255, 255, 255, 0.75);
            margin-bottom: 3px;
        }
        .total-amt {
            font-family: "Courier New", monospace;
            font-size: 20px;
            font-weight: bold;
            color: #ffffff;
        }

        /* ── PAYMENT DETAIL ────────────────────────────────── */
        .pay-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        /* ── PROOF BOX ─────────────────────────────────────── */
        .proof-box {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 7px 12px;
            font-size: 9px;
            color: #15803d;
            margin-bottom: 16px;
        }

        /* ── SIGNATURE ─────────────────────────────────────── */
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 28px;
        }
        .note-text {
            font-size: 8.5px;
            color: #94a3b8;
            line-height: 1.7;
            max-width: 270px;
        }
        .sig-title {
            text-align: center;
            font-size: 9px;
            color: #64748b;
            padding-bottom: 44px;
        }
        .sig-line {
            border-top: 1px solid #cbd5e1;
            text-align: center;
            font-size: 8.5px;
            color: #94a3b8;
            padding-top: 4px;
        }

        /* ── FOOTER ────────────────────────────────────────── */
        .footer-wrap {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 11px 36px;
            margin-top: 28px;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-l {
            font-size: 8.5px;
            color: #94a3b8;
        }
        .footer-r {
            text-align: right;
            font-family: "Courier New", monospace;
            font-size: 8.5px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    @php
        $schoolName = $schoolName ?? "Nama Sekolah";
        $schoolEmail = $schoolEmail ?? "";
        $schoolPhone = $schoolPhone ?? "";
        $schoolAddress = $schoolAddress ?? "";
        $academicYear = $academicYear ?? "";

        $statusVal = $transaction->payment_status->value;
        $isPaid = $statusVal === "paid";
        $isPending = str_contains($statusVal, "pending");
        $statusLbl = strtoupper($transaction->payment_status->label());

        $studentName = $transaction->user->name ?? "Siswa";
        $studentEmail = $transaction->user->email ?? "";
        $studentNis =
            optional(optional($transaction->user)->student)->nis ??
            (optional(optional($transaction->user)->student)->nisn ?? "");
        $classroom =
            optional(optional(optional($transaction->user)->student)->classroom)
                ->name ?? "—";
        $department =
            optional($transaction->department)->name ??
            (optional(optional(optional($transaction->user)->student)->department)
                ->name ??
                "—");
        $semester =
            optional($transaction->department)->semester ??
            (optional(optional(optional($transaction->user)->student)->department)
                ->semester ??
                "");
        $invoiceNum = optional($transaction->invoice)->number ?? null;
        $paidAt = $transaction->paid_at;
        $payMethod = strtoupper(
            str_replace("_", " ", $transaction->payment_method ?? ""),
        );
    @endphp

    {{-- ── HEADER ─────────────────────────────────────────────── --}}
    <div class="header-wrap">
        <table class="header-table">
            <tr>
                <td
                    style="width: 60%; vertical-align: top; padding-right: 15px; word-wrap: break-word; overflow-wrap: break-word;"
                >
                    <div
                        class="school-name"
                        style="font-size: 18px; letter-spacing: 0.3px"
                    >
                        {{ $schoolName }}
                    </div>
                    <div
                        class="school-meta"
                        style="
                            font-size: 9.5px;
                            line-height: 1.6;
                            word-wrap: break-word;
                            overflow-wrap: break-word;
                        "
                    >
                        @if ($schoolAddress)
                            {{ $schoolAddress }}<br
                             />
                        @endif
                        @if ($schoolPhone)
                            Telp: {{ $schoolPhone }}
                            @if ($schoolEmail) &nbsp;&#183;&nbsp; @endif
                            @endif
                            @if ($schoolEmail) {{ $schoolEmail }}@endif
                    </div>
                </td>

                <td
                    style="
                        width: 40%;
                        vertical-align: top;
                        text-align: right;
                        white-space: nowrap;
                    "
                >
                    <span class="badge-resmi">&#10003; Kwitansi Resmi</span>
                    <div
                        class="trx-code"
                        style="font-size: 14px; margin-top: 6px"
                    >
                        {{ $transaction->code }}
                    </div>
                    @if ($academicYear)
                        <div
                            class="ta-text"
                            style="
                                font-size: 10px;
                                color: #94a3b8;
                                margin-top: 4px;
                            "
                        >
                            T.A. {{ $academicYear }}
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>
    <div class="accent-bar"></div>

    {{-- ── BODY ────────────────────────────────────────────────── --}}
    <div class="body-wrap">
        {{-- Tanggal & Status --}}
        <table class="meta-table">
            <tr>
                <td style="width: 50%; vertical-align: top">
                    <div class="lbl">Tanggal Terbit</div>
                    <div class="val">
                        {{
                            $transaction->created_at->format(
                                "d F Y",
                            )
                        }}
                    </div>
                </td>
                <td style="width: 50%; text-align: right; vertical-align: top">
                    <div class="lbl" style="text-align: right">
                        Status Pembayaran
                    </div>
                    <div style="margin-top: 4px">
                        @if ($isPaid)
                            <span class="badge-paid">{{ $statusLbl }}</span>
                        @elseif ($isPending)
                            <span class="badge-pending">{{ $statusLbl }}</span>
                        @else
                            <span class="badge-other">{{ $statusLbl }}</span>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <hr class="div-dash" />

        {{-- Billed To / Dept --}}
        <table class="party-table">
            <tr>
                <td
                    style="width: 54%; vertical-align: top; padding-right: 18px"
                >
                    <div class="sec-lbl">Dibayarkan Oleh</div>
                    <div class="party-name">{{ $studentName }}</div>
                    @if ($studentNis)
                        <div class="party-mono">NIS: {{ $studentNis }}</div>
                    @endif
                    @if ($studentEmail)
                        <div class="party-sub">{{ $studentEmail }}</div>
                    @endif
                </td>
                <td
                    style="
                        width: 46%;
                        vertical-align: top;
                        padding-left: 18px;
                        border-left: 1.5px dashed #e2e8f0;
                    "
                >
                    <div class="sec-lbl">Jurusan / Kelas / Semester</div>
                    <div class="party-name" style="font-size: 13px">
                        {{ $department }}
                    </div>

                    @if ($classroom !== "—" || $semester)
                        <div class="party-sub">
                            {{
                                $classroom !== "—"
                                    ? "Kelas " . $classroom
                                    : ""
                            }}

                            {{-- Tambahkan titik pemisah jika kelas dan semester sama-sama ada --}}
                            @if ($classroom !== "—" && $semester)
                                &nbsp;&#183;&nbsp;
                            @endif

                            {{
                                $semester
                                    ? "Semester " . $semester
                                    : ""
                            }}
                        </div>
                    @endif
                </td>
            </tr>
        </table>

        <hr class="div-dash" />

        {{-- Detail Table --}}
        <table class="detail-table">
            <thead>
                <tr>
                    <th>Keterangan</th>
                    <th class="r" style="width: 160px">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="item-name">Pembayaran SPP</div>
                        <div class="item-code">{{ $transaction->code }}</div>
                        @if ($invoiceNum)
                            <div class="item-ref">
                                Ref. Invoice: {{ $invoiceNum }}
                            </div>
                        @endif
                    </td>
                    <td class="amt-cell">
                        Rp {{
                            number_format(
                                (float) $transaction->amount,
                                0,
                                ",",
                                ".",
                            )
                        }}
                    </td>
                </tr>
            </tbody>
        </table>
        <hr class="div-solid" />

        {{-- Total --}}
        <div class="total-outer">
            <table
                style="
                    width: auto;
                    margin-left: auto;
                    border-collapse: collapse;
                "
            >
                <tr>
                    <td>
                        <table
                            style="
                                background-color: #10b981;
                                border-radius: 10px;
                                border-collapse: collapse;
                            "
                        >
                            <tr>
                                <td
                                    style="
                                        padding: 12px 22px;
                                        text-align: right;
                                    "
                                >
                                    <div
                                        style="
                                            font-size: 8px;
                                            text-transform: uppercase;
                                            letter-spacing: 1.2px;
                                            color: rgba(255, 255, 255, 0.75);
                                            margin-bottom: 3px;
                                        "
                                    >
                                        Total Dibayar
                                    </div>
                                    <div
                                        style="
                                            font-family:
                                                &quot;Courier New&quot;,
                                                monospace;
                                            font-size: 20px;
                                            font-weight: bold;
                                            color: #ffffff;
                                        "
                                    >
                                        Rp {{
                                            number_format(
                                                (float) $transaction->amount,
                                                0,
                                                ",",
                                                ".",
                                            )
                                        }}
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <hr class="div-dash" />

        {{-- Payment Detail --}}
        <table class="pay-table">
            <tr>
                <td
                    style="width: 33%; vertical-align: top; padding-right: 10px"
                >
                    <div class="lbl">Metode Pembayaran</div>
                    <div
                        style="
                            font-size: 11px;
                            font-weight: bold;
                            color: #1e293b;
                            margin-top: 3px;
                        "
                    >
                        {{
                            $payMethod ?:
                                "&#8212;"
                        }}
                    </div>
                </td>
                <td style="width: 33%; vertical-align: top; padding: 0 10px">
                    <div class="lbl">Tanggal Lunas</div>
                    <div
                        style="
                            font-size: 11px;
                            font-weight: bold;
                            color: #1e293b;
                            margin-top: 3px;
                        "
                    >
                        {{
                            $paidAt
                                ? $paidAt->format("d F Y")
                                : "&#8212;"
                        }}
                    </div>
                </td>
                <td style="width: 33%; vertical-align: top; padding-left: 10px">
                    <div class="lbl">Jam Pembayaran</div>
                    <div
                        style="
                            font-family: &quot;Courier New&quot;, monospace;
                            font-size: 11px;
                            font-weight: bold;
                            color: #1e293b;
                            margin-top: 3px;
                        "
                    >
                        {{
                            $paidAt
                                ? $paidAt->format("H:i") . " WIB"
                                : "&#8212;"
                        }}
                    </div>
                </td>
            </tr>
        </table>

        @if ($transaction->proof_of_payment)
            <div class="proof-box">
                &#10003; Bukti pembayaran diunggah: {{
                    basename(
                        $transaction->proof_of_payment,
                    )
                }}
            </div>
        @endif

        {{-- Signature --}}
        <table class="sig-table">
            <tr>
                <td
                    style="
                        width: 55%;
                        vertical-align: bottom;
                        padding-bottom: 8px;
                    "
                >
                    <div class="note-text">
                        Dokumen ini dicetak secara otomatis oleh sistem
                        <strong>MySPP</strong>
                        dan merupakan bukti pembayaran resmi yang sah. Tidak
                        memerlukan tanda tangan basah.
                    </div>
                </td>
                <td
                    style="
                        width: 45%;
                        text-align: center;
                        vertical-align: bottom;
                    "
                >
                    <div class="sig-title">Bendahara Sekolah,</div>
                    <div class="sig-line">
                        (&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;&#160;)
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ── FOOTER ──────────────────────────────────────────────── --}}
    <div class="footer-wrap">
        <table class="footer-table">
            <tr>
                <td class="footer-l">
                    &copy; {{ date("Y") }} {{ $schoolName }} &nbsp;&#183;&nbsp;
                    Powered by MySPP
                </td>
                <td class="footer-r">
                    Dicetak: {{ now()->format("d/m/Y H:i") }} WIB
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
