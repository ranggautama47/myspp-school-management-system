<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Dashboard — MySPP</title>

    <!-- Favicon / Browser Tab Icon (PWA tap) -->
    <link
        rel="icon"
        type="image/png"
        sizes="512x512"
        href="{{ asset('images/android-chrome-512x512.png') }}"
    />
    <link
        rel="shortcut icon"
        href="{{ asset('images/favicon.png') }}"
        type="image/png"
    />
    <link
        rel="apple-touch-icon"
        href="{{ asset('images/android-chrome-512x512.png') }}"
    />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        emerald: { 500: "#10b981", 600: "#059669" },
                        slate: {
                            750: "#1a2744",
                            800: "#1e293b",
                            850: "#131f35",
                            900: "#0f172a",
                            950: "#0d1525",
                        },
                    },
                },
            },
        };
    </script>
    <script
        defer
        src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"
    ></script>
    @stack ("head")
</head>
<body class="bg-slate-900 text-white min-h-screen antialiased">
    <div class="flex min-h-screen">
        {{-- ── SIDEBAR ─────────────────────────────────────────────────── --}}
        <aside
            class="w-56 bg-slate-950 border-r border-slate-800 flex flex-col flex-shrink-0 sticky top-0 h-screen"
        >
            {{-- Brand --}}
            <div class="px-4 py-5 border-b border-slate-800">
                <a
                    href="{{ route('student.dashboard') }}"
                    class="flex items-center gap-2.5"
                >
                    <div
                        class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center flex-shrink-0"
                    >
                        <div
                            class="w-8 h-8 flex items-center justify-center flex-shrink-0"
                        >
                            <img
                                src="{{ asset('images/favicon.png') }}"
                                alt="MySPP Logo"
                                class="w-8 h-8 object-contain"
                            />
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white leading-none">
                            <span class="text-emerald-500">My</span>SPP
                        </p>
                        <p class="text-[10px] text-slate-500 mt-0.5">Student Portal</p>
                    </div>
                </a>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 px-2 py-4 space-y-0.5">
                @php
                    $navItems = [
                        [
                            "route" => "student.dashboard",
                            "label" => "Dashboard",
                            "icon" =>
                                "M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z",
                        ],
                        [
                            "route" => "student.transactions",
                            "label" => "Payment History",
                            "icon" =>
                                "M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2",
                        ],
                        [
                            "route" => "student.profile",
                            "label" => "My Profile",
                            "icon" =>
                                "M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z",
                        ],
                    ];
                @endphp

                @foreach ($navItems as $item)
                    @php $isActive = request()->routeIs($item["route"] . "*"); @endphp
                    <a
                        href="{{ route($item['route']) }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ $isActive ? 'bg-emerald-500/10 text-emerald-400' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800' }}"
                    >
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path
                                d="{{ $item['icon'] }}"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                        {{ $item["label"] }}
                    </a>
                @endforeach
            </nav>

            {{-- Help box --}}
            <div
                class="m-3 p-3 bg-slate-800 rounded-xl border border-slate-700"
            >
                <p class="text-[11px] text-slate-400 leading-relaxed">If you have any questions or need support, feel free to contact us.</p>
                <a
                    href="mailto:admin@myspp.com"
                    class="mt-2 block text-center text-[11px] text-emerald-400 border border-emerald-500/30 rounded-lg py-1.5 hover:bg-emerald-500/10 transition-colors"
                >
                    Contact Support
                </a>
            </div>
        </aside>

        {{-- ── MAIN ────────────────────────────────────────────────────── --}}
        <div class="flex-1 flex flex-col min-w-0">
            {{-- Topbar --}}
            <header
                class="bg-slate-950 border-b border-slate-800 px-6 py-3 flex items-center justify-between sticky top-0 z-30"
            >
                <div>
                    <h1 class="text-sm font-medium text-white">
                        Welcome back, {{ auth()->user()->name }}!
                    </h1>
                    <p class="text-xs text-slate-500 mt-0.5">Here's an overview of your academic payment status.</p>
                </div>

                <div class="flex items-center gap-3" x-data="{ open: false }">
                    {{-- Notification Bell Wrapper --}}
                    <div class="relative" x-data="{ openNotifications: false }">
                        {{-- Tombol Lonceng --}}
                        <button
                            @click="openNotifications = !openNotifications"
                            class="relative w-8 h-8 bg-slate-800 border border-slate-700 rounded-lg flex items-center justify-center hover:bg-slate-700 transition-colors cursor-pointer"
                        >
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            @php $dashNotifCount = $pendingInvoices->count() + $totalPending; @endphp
                            @if ($dashNotifCount > 0)
                                <span
                                    class="absolute -top-1 -right-1 min-w-[16px] h-4 bg-rose-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center px-0.5 border-2 border-slate-950"
                                >
                                    {{
                                        $dashNotifCount > 9
                                            ? "9+"
                                            : $dashNotifCount
                                    }}
                                </span>
                            @endif
                        </button>

                        {{-- Popover Panel Notifikasi --}}
                        <div
                            x-show="openNotifications"
                            @click.outside="openNotifications = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-[-10px]"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 translate-y-[-10px]"
                            class="absolute right-0 mt-2 w-80 bg-slate-800 border border-slate-700 rounded-xl shadow-2xl shadow-black/40 z-50 overflow-hidden"
                            style="display: none"
                        >
                            {{-- Header Popover --}}
                            <div
                                class="px-4 py-3 border-b border-slate-700 flex items-center justify-between"
                            >
                                <div class="flex items-center gap-2">
                                    <span
                                        class="text-xs font-semibold text-slate-200"
                                        >Notifikasi</span
                                    >
                                    @if ($dashNotifCount > 0)
                                        <span
                                            class="bg-rose-500/20 text-rose-400 text-[10px] font-semibold rounded-full px-1.5 py-0.5"
                                        >
                                            {{
                                                $dashNotifCount > 9
                                                    ? "9+"
                                                    : $dashNotifCount
                                            }}
                                        </span>
                                    @endif
                                </div>
                                <button
                                    @click="openNotifications = false"
                                    class="text-slate-400 hover:text-white transition-colors"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            {{-- List Notifikasi Dinamis --}}
                            <div
                                class="max-h-72 overflow-y-auto divide-y divide-slate-700/60"
                            >
                                @php $hasNotif = false; @endphp

                                {{-- Tagihan belum dibayar --}}
                                @foreach ($pendingInvoices as $inv)
                                    @php $hasNotif = true; @endphp
                                    <div
                                        class="p-3.5 hover:bg-slate-700/40 transition-colors flex gap-3 items-start group"
                                    >
                                        <div
                                            class="w-2 h-2 {{ $inv->status->value === 'overdue' ? 'bg-rose-400' : 'bg-amber-400' }} rounded-full mt-1.5 flex-shrink-0"
                                        ></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold text-slate-200 group-hover:text-emerald-400 transition-colors leading-none">
                                                {{
                                                    $inv->status->value === "overdue"
                                                        ? "Tagihan Lewat Jatuh Tempo"
                                                        : "Tagihan Belum Dibayar"
                                                }}
                                            </p>
                                            <p class="text-[11px] text-slate-400 mt-1 truncate">
                                                {{ $inv->number }} · Rp {{
                                                    number_format(
                                                        (float) $inv->amount,
                                                        0,
                                                        ",",
                                                        ".",
                                                    )
                                                }}
                                            </p>
                                            <p class="text-[10px] text-slate-600 mt-0.5">
                                                Jatuh tempo: {{
                                                    $inv->due_date?->format(
                                                        "d M Y",
                                                    )
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Transaksi pending menunggu verifikasi --}}
                                @foreach ($recentTransactions as $trx)
                                    @if ($trx->isPending())
                                        @php $hasNotif = true; @endphp
                                        <div
                                            class="p-3.5 hover:bg-slate-700/40 transition-colors flex gap-3 items-start group"
                                        >
                                            <div
                                                class="w-2 h-2 bg-blue-400 rounded-full mt-1.5 flex-shrink-0"
                                            ></div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-semibold text-slate-200 group-hover:text-emerald-400 transition-colors leading-none">Menunggu Verifikasi Admin</p>
                                                <p class="text-[11px] text-slate-400 mt-1 truncate">
                                                    {{ $trx->code }} · Rp {{
                                                        number_format(
                                                            (float) $trx->amount,
                                                            0,
                                                            ",",
                                                            ".",
                                                        )
                                                    }}
                                                </p>
                                                <p class="text-[10px] text-slate-600 mt-0.5">{{ $trx->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach

                                @if (!$hasNotif)
                                    <div class="py-10 text-center">
                                        <svg class="w-8 h-8 text-slate-700 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                        <p class="text-xs text-slate-600 font-medium">Tidak ada notifikasi</p>
                                        <p class="text-[11px] text-slate-700 mt-0.5">Semua tagihan sudah beres!</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Footer Popover --}}
                            <div
                                class="border-t border-slate-700 bg-slate-900/50"
                            >
                                <a
                                    href="{{ route('student.transactions') }}"
                                    class="block text-center py-2.5 text-[11px] font-medium text-emerald-400 hover:text-emerald-300 hover:bg-emerald-500/5 transition-colors"
                                >
                                    Lihat Semua Transaksi →
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Avatar dropdown --}}
                    <div class="relative">
                        <button
                            @click="open = !open"
                            class="flex items-center gap-2.5 cursor-pointer"
                        >
                            @if (auth()->user()->image)
                                <img
                                    src="{{ Storage::url(auth()->user()->image) }}"
                                    class="w-8 h-8 rounded-full object-cover ring-2 ring-slate-700"
                                />
                            @else
                                <div
                                    class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center text-xs font-semibold text-white"
                                >
                                    {{
                                        strtoupper(
                                            substr(auth()->user()->name, 0, 2),
                                        )
                                    }}
                                </div>
                            @endif
                            <div class="text-right hidden sm:block">
                                <p class="text-xs font-medium text-slate-200 leading-none">{{
                                    auth()->user()
                                        ->name
                                }}</p>
                                <p class="text-[10px] text-slate-500 mt-0.5">{{
                                    $student?->nis ??
                                        "Student"
                                }}</p>
                            </div>
                        </button>

                        <div
                            x-show="open"
                            @click.outside="open = false"
                            x-transition
                            class="absolute right-0 mt-2 w-40 bg-slate-800 border border-slate-700 rounded-xl shadow-xl py-1 z-50"
                        >
                            <a
                                href="{{ route('student.profile') }}"
                                class="block px-4 py-2 text-xs text-slate-300 hover:bg-slate-700 hover:text-white transition-colors"
                            >
                                Edit Profile
                            </a>
                            <div class="border-t border-slate-700 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="w-full text-left px-4 py-2 text-xs text-rose-400 hover:bg-slate-700 transition-colors"
                                >
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1 p-6">
                {{-- Flash --}}
                @if (session("success"))
                    <div
                        class="mb-5 flex items-center gap-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 px-4 py-3 text-xs text-emerald-400"
                    >
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{
                            session(
                                "success",
                            )
                        }}
                    </div>
                @endif

                @if (session("error"))
                    <div
                        class="mb-5 flex items-center gap-2.5 rounded-xl bg-rose-500/10 border border-rose-500/20 px-4 py-3 text-xs text-rose-400"
                    >
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        {{
                            session(
                                "error",
                            )
                        }}
                    </div>
                @endif

                <div class="grid grid-cols-1 xl:grid-cols-[1fr_272px] gap-5">
                    {{-- ── LEFT COLUMN ──────────────────────────────────── --}}
                    <div class="space-y-4">
                        {{-- Overall Status --}}
                        <div
                            class="bg-emerald-900/20 border border-emerald-500/20 rounded-xl p-4 flex items-center gap-4"
                        >
                            <div
                                class="w-10 h-10 bg-emerald-500/15 rounded-xl flex items-center justify-center flex-shrink-0"
                            >
                                <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] text-emerald-400 uppercase tracking-wider font-medium">Overall Status</p>
                                <p class="text-base font-semibold text-white mt-0.5">
                                    @if ($pendingInvoices->where("status", "overdue")->count() > 0)
                                        Overdue Payment
                                    @elseif ($pendingInvoices->count() > 0)
                                        Pending Payment
                                    @else
                                        Good Standing
                                    @endif
                                </p>
                                <p class="text-xs text-emerald-400/70 mt-0.5">
                                    @if ($pendingInvoices->count() === 0)
                                        You have no overdue payments
                                    @else
                                        {{ $pendingInvoices->count() }} tagihan menunggu pembayaran
                                    @endif
                                </p>
                            </div>
                            <div
                                class="ml-auto flex items-center gap-1.5 bg-emerald-500/10 border border-emerald-500/20 rounded-full px-3 py-1"
                            >
                                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span
                                    class="text-xs text-emerald-400 font-medium"
                                    >Active</span
                                >
                            </div>
                        </div>

                        {{-- Active Bill Cards --}}
                        @forelse ($pendingInvoices as $invoice)
                            <div
                                class="bg-slate-900 border {{ $invoice->status->value === 'overdue' ? 'border-rose-500/30' : 'border-slate-700/50' }} rounded-xl p-5"
                            >
                                {{-- WRAPPER UTAMA BAGIAN ATAS (Membagi Info Kiri & Icon Kanan) --}}
                                <div
                                    class="flex justify-between items-start gap-4 mb-5 pb-5 border-b border-slate-800"
                                >
                                    {{-- BAGIAN KIRI: Semua Info Tagihan --}}
                                    <div class="flex-1 min-w-0">
                                        {{-- min-w-0 penting agar teks panjang bisa ter-wrap dengan sempurna --}}

                                        {{-- Header: Icon Profil + Judul --}}
                                        <div
                                            class="flex items-start gap-4 mb-4"
                                        >
                                            <div
                                                class="w-16 h-16 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center flex-shrink-0 overflow-hidden"
                                            >
                                                <img
                                                    src="{{ asset('images/icon_dasboad_siswa.png') }}"
                                                    alt="Payment Icon"
                                                    class="w-full h-full object-cover scale-125"
                                                />
                                            </div>

                                            <div class="flex-1 min-w-0">
                                                <span
                                                    class="text-[10px] font-medium text-blue-400 bg-blue-500/10 border border-blue-500/20 rounded px-2 py-0.5 uppercase tracking-wider"
                                                >
                                                    Active Bill
                                                </span>
                                                <h2
                                                    class="text-base font-semibold text-white mt-1"
                                                >
                                                    Current Semester Fee
                                                </h2>
                                                {{-- Teks Angkatan Tahun Ajaran dibiarkan wrap normal --}}
                                                <p class="text-xs text-slate-400 mt-0.5 leading-relaxed break-words">
                                                    {{
                                                        $student?->academicYear?->name ??
                                                            "Academic Year"
                                                    }} · {{
                                                        $invoice->department?->name ??
                                                            "-"
                                                    }} Semester {{
                                                        $invoice->department
                                                            ?->semester
                                                    }}
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Nominal Tagihan (Tetap di posisinya, tidak bergeser) --}}
                                        <div class="space-y-6">
                                            {{-- Section Nominal Tagihan --}}
                                            <div>
                                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Amount Due</p>
                                                <p class="text-3xl md:text-4xl font-extrabold text-emerald-400 tracking-tight">
                                                    Rp {{
                                                        number_format(
                                                            (float) $invoice->amount,
                                                            2,
                                                            ",",
                                                            ".",
                                                        )
                                                    }}
                                                </p>
                                            </div>

                                            {{-- Section Due Date & Bill Status --}}
                                            <div
                                                class="flex flex-wrap items-center gap-6 md:gap-8 pt-2"
                                            >
                                                {{-- Due Date --}}
                                                <div class="min-w-fit">
                                                    <p class="text-xs font-semibold text-slate-400 mb-2 flex items-center gap-1.5 uppercase tracking-wider">
                                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <rect x="3" y="4" width="18" height="18" rx="2" />
                                                            <path d="M16 2v4M8 2v4M3 10h18" />
                                                        </svg>
                                                        Due Date
                                                    </p>
                                                    <p class="text-base font-bold text-slate-200 whitespace-nowrap">
                                                        {{
                                                            $invoice->due_date?->format(
                                                                "F d, Y",
                                                            )
                                                        }}
                                                    </p>
                                                </div>

                                                {{-- Separator Garis Vertikal --}}
                                                <div
                                                    class="hidden md:block w-px h-10 bg-slate-800 self-end mb-1"
                                                ></div>

                                                {{-- Bill Status --}}
                                                <div class="min-w-fit">
                                                    <p class="text-xs font-semibold text-slate-400 mb-2 uppercase tracking-wider">Bill Status</p>
                                                    @if ($invoice->status->value === "overdue")
                                                        <span
                                                            class="inline-flex items-center text-xs font-semibold text-rose-400 bg-rose-500/10 border border-rose-500/20 rounded-lg px-3 py-1.5 whitespace-nowrap"
                                                        >
                                                            Overdue
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center text-xs font-semibold text-amber-400 bg-amber-500/10 border border-amber-500/20 rounded-lg px-3 py-1.5 whitespace-nowrap"
                                                        >
                                                            Pending Payment
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- BAGIAN KANAN: Icon Payment (Ditarik keluar sejajar dengan judul Kiri) --}}
                                    <div
                                        class="hidden md:flex w-24 h-24 md:w-28 md:h-28 rounded-3xl bg-emerald-500/5 border border-emerald-500/10 items-center justify-center overflow-hidden flex-shrink-0 mt-2"
                                    >
                                        <img
                                            src="{{ asset('images/iconPayment.png') }}"
                                            alt="Payment"
                                            class="w-16 h-16 md:w-20 md:h-20 object-contain scale-125 opacity-90"
                                        />
                                    </div>
                                </div>

                                <div class="payment-action-wrapper">
                                    {{-- Bayar Button --}}
                                    @if ($invoice->transaction_id)
                                        @php
                                            $pendingTx = \App\Models\Transaction::find($invoice->transaction_id);
                                        @endphp
                                        @if ($pendingTx && $pendingTx->isPending())
                                            <a
                                                href="{{ route('student.transactions.show', $pendingTx) }}"
                                                class="relative w-full flex items-center justify-center bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold py-4 rounded-2xl transition-all duration-300 mb-3 shadow-[0_0_25px_rgba(245,158,11,0.25)] hover:shadow-[0_0_35px_rgba(245,158,11,0.45)]"
                                            >
                                                {{-- Left Icon --}}
                                                <svg
                                                    class="w-5 h-5 absolute left-5"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                >
                                                    <circle cx="12" cy="12" r="10" />
                                                    <path d="M12 8v4l3 3" />
                                                </svg>

                                                {{-- Center Text --}}
                                                <span
                                                    class="text-center tracking-wide"
                                                >
                                                    Menunggu Pembayaran
                                                </span>

                                                {{-- Right Arrow --}}
                                                <svg
                                                    class="w-5 h-5 absolute right-5"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                >
                                                    <path d="M5 12h14M12 5l7 7-7 7" />
                                                </svg>
                                            </a>

                                        @endif

                                    @else
                                        <form
                                            method="POST"
                                            action="{{ route('student.invoices.checkout', $invoice) }}"
                                        >
                                            @csrf

                                            <button
                                                type="submit"
                                                class="relative w-full flex items-center justify-center bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-white text-sm font-semibold py-4 rounded-2xl transition-all duration-300 mb-3 shadow-[0_0_30px_rgba(16,185,129,0.30)] hover:shadow-[0_0_45px_rgba(16,185,129,0.50)]"
                                            >
                                                {{-- Left Icon --}}
                                                <svg
                                                    class="w-5 h-5 absolute left-5"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
                                                    />
                                                </svg>

                                                {{-- Center Text --}}
                                                <span
                                                    class="text-center tracking-wide"
                                                >
                                                    Bayar Sekarang (Pay Now)
                                                </span>

                                                {{-- Right Arrow --}}
                                                <svg
                                                    class="w-5 h-5 absolute right-5"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                >
                                                    <path d="M5 12h14M12 5l7 7-7 7" />
                                                </svg>
                                            </button>
                                        </form>

                                    @endif

                                    {{-- Footer --}}
                                    <p class="text-[10px] text-slate-400 text-center flex items-center justify-center gap-1">
                                        <svg
                                            class="w-3 h-3"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <rect x="3" y="11" width="18" height="11" rx="2" />
                                            <path d="M7 11V7a5 5 0 0110 0v4" />
                                        </svg>
                                        Secure payment powered by Midtrans
                                    </p>
                                </div>

                                {{-- Admin notes --}}
                                @if ($invoice->notes)
                                    <div
                                        class="mt-3 flex items-start gap-2 rounded-lg bg-slate-800 px-3 py-2"
                                    >
                                        <svg class="w-3.5 h-3.5 text-slate-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10" />
                                            <path d="M12 8v4m0 4h.01" />
                                        </svg>
                                        <p class="text-[11px] text-slate-400">{{ $invoice->notes }}</p>
                                    </div>
                                @endif
                            </div>

                        @empty
                            <div
                                class="bg-emerald-900/10 border border-emerald-500/15 rounded-xl p-6 text-center"
                            >
                                <svg class="w-10 h-10 text-emerald-500/40 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-sm font-medium text-emerald-400">Tidak ada tagihan aktif</p>
                                <p class="text-xs text-slate-500 mt-1">Semua pembayaran sudah lunas.</p>
                            </div>
                        @endforelse

                        {{-- Payment History Table --}}
                        <div
                            class="bg-slate-950 border border-slate-800 rounded-xl overflow-hidden"
                        >
                            <div
                                class="px-5 py-3.5 border-b border-slate-800 flex items-center justify-between"
                            >
                                <h3 class="text-sm font-medium text-slate-200">
                                    Payment History
                                </h3>
                                <select
                                    class="bg-slate-800 border border-slate-700 text-slate-400 text-xs rounded-lg px-2 py-1.5 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                                >
                                    <option>All Semesters</option>
                                </select>
                            </div>

                            @if ($recentTransactions->isEmpty())
                                <div class="py-10 text-center">
                                    <p class="text-xs text-slate-500">Belum ada riwayat transaksi.</p>
                                </div>
                            @else
                                <table
                                    class="w-full text-xs"
                                    style="table-layout: fixed"
                                >
                                    <thead>
                                        <tr class="bg-slate-900">
                                            <th
                                                class="text-left px-5 py-2.5 text-[10px] font-medium text-slate-500 uppercase tracking-wider w-2/5"
                                            >
                                                Invoice ID
                                            </th>
                                            <th
                                                class="text-left px-3 py-2.5 text-[10px] font-medium text-slate-500 uppercase tracking-wider"
                                            >
                                                Date
                                            </th>
                                            <th
                                                class="text-right px-3 py-2.5 text-[10px] font-medium text-slate-500 uppercase tracking-wider"
                                            >
                                                Amount
                                            </th>
                                            <th
                                                class="text-center px-3 py-2.5 text-[10px] font-medium text-slate-500 uppercase tracking-wider"
                                            >
                                                Status
                                            </th>
                                            <th
                                                class="text-center px-3 py-2.5 text-[10px] font-medium text-slate-500 uppercase tracking-wider w-16"
                                            >
                                                DL
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800">
                                        @foreach ($recentTransactions as $trx)
                                            <tr
                                                class="hover:bg-slate-800/30 transition-colors"
                                            >
                                                <td class="px-5 py-3">
                                                    <a
                                                        href="{{ route('student.transactions.show', $trx) }}"
                                                        class="flex items-center gap-2 text-slate-300 hover:text-emerald-400 transition-colors font-mono"
                                                    >
                                                        <svg class="w-3 h-3 text-slate-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                                                        </svg>
                                                        <span
                                                            class="truncate"
                                                            >{{ $trx->code }}</span
                                                        >
                                                    </a>
                                                </td>
                                                <td
                                                    class="px-3 py-3 text-slate-400"
                                                >
                                                    {{
                                                        $trx->created_at->format(
                                                            "M d, Y",
                                                        )
                                                    }}
                                                </td>
                                                <td
                                                    class="px-3 py-3 text-right text-slate-300 font-medium"
                                                >
                                                    Rp {{
                                                        number_format(
                                                            (float) $trx->amount,
                                                            2,
                                                            ",",
                                                            ".",
                                                        )
                                                    }}
                                                </td>
                                                <td
                                                    class="px-3 py-3 text-center"
                                                >
                                                    @php $s = $trx->payment_status; @endphp
                                                    <span
                                                        class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium
                                                    {{ match($s->color()) {
                                                        'success' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
                                                        'warning' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
                                                        'danger'  => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
                                                        default   => 'bg-slate-700 text-slate-400',
                                                    } }}"
                                                    >
                                                        {{ $s->label() }}
                                                    </span>
                                                </td>
                                                <td
                                                    class="px-3 py-3 text-center"
                                                >
                                                    <a
                                                        href="{{ route('student.transactions.download', $trx) }}"
                                                        class="text-slate-600 hover:text-emerald-400 transition-colors p-1 inline-block"
                                                        title="Download Invoice"
                                                    >
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                        </svg>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div
                                    class="px-5 py-3 border-t border-slate-800 flex justify-end"
                                >
                                    <a
                                        href="{{ route('student.transactions') }}"
                                        class="text-xs text-emerald-400 hover:text-emerald-300 transition-colors"
                                    >
                                        View all payments →
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- ── RIGHT COLUMN — Payment Summary ────────────────── --}}
                    <div>
                        <div
                            class="bg-slate-950 border border-slate-800 rounded-xl overflow-hidden"
                        >
                            <div class="px-5 py-3.5 border-b border-slate-800">
                                <h3 class="text-sm font-medium text-slate-200">
                                    Payment Summary
                                </h3>
                            </div>

                            <a
                                href="{{ route('student.transactions') }}?status=paid"
                                class="flex items-center gap-3 px-4 py-3.5 hover:bg-slate-800/30 transition-colors group"
                            >
                                <div
                                    class="w-8 h-8 bg-emerald-500/10 rounded-lg flex items-center justify-center flex-shrink-0"
                                >
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[11px] text-slate-500">Total Paid</p>
                                    <p class="text-sm font-semibold text-slate-200 mt-0.5">
                                        Rp {{
                                            number_format(
                                                (float) $totalPaid,
                                                2,
                                                ",",
                                                ".",
                                            )
                                        }}
                                    </p>
                                </div>
                                <svg class="w-3.5 h-3.5 text-slate-600 group-hover:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path d="M9 18l6-6-6-6" />
                                </svg>
                            </a>

                            <a
                                href="{{ route('student.transactions') }}?status=pending"
                                class="flex items-center gap-3 px-4 py-3.5 border-t border-slate-800 hover:bg-slate-800/30 transition-colors group"
                            >
                                <div
                                    class="w-8 h-8 bg-amber-500/10 rounded-lg flex items-center justify-center flex-shrink-0"
                                >
                                    <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[11px] text-slate-500">Pending</p>
                                    @php
                                        $pendingAmount = \App\Models\Transaction::where("user_id", auth()->id())
                                            ->pending()
                                            ->sum("amount");
                                    @endphp
                                    <p class="text-sm font-semibold text-slate-200 mt-0.5">
                                        Rp {{
                                            number_format(
                                                (float) $pendingAmount,
                                                2,
                                                ",",
                                                ".",
                                            )
                                        }}
                                    </p>
                                </div>
                                <svg class="w-3.5 h-3.5 text-slate-600 group-hover:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path d="M9 18l6-6-6-6" />
                                </svg>
                            </a>

                            <a
                                href="{{ route('student.transactions') }}?status=failed"
                                class="flex items-center gap-3 px-4 py-3.5 border-t border-slate-800 hover:bg-slate-800/30 transition-colors group"
                            >
                                <div
                                    class="w-8 h-8 bg-rose-500/10 rounded-lg flex items-center justify-center flex-shrink-0"
                                >
                                    <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[11px] text-slate-500">Failed</p>
                                    @php
                                        $failedAmount = \App\Models\Transaction::where("user_id", auth()->id())
                                            ->where("payment_status", \App\Enums\TransactionStatus::Failed)
                                            ->sum("amount");
                                    @endphp
                                    <p class="text-sm font-semibold text-slate-200 mt-0.5">
                                        Rp {{
                                            number_format(
                                                (float) $failedAmount,
                                                2,
                                                ",",
                                                ".",
                                            )
                                        }}
                                    </p>
                                </div>
                                <svg class="w-3.5 h-3.5 text-slate-600 group-hover:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path d="M9 18l6-6-6-6" />
                                </svg>
                            </a>

                            <a
                                href="{{ route('student.transactions') }}"
                                class="flex items-center gap-2 px-4 py-3 border-t border-slate-800 hover:bg-slate-800/20 transition-colors"
                            >
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                                </svg>
                                <span class="text-xs text-slate-500"
                                    >View Payment History</span
                                >
                                <svg class="w-3.5 h-3.5 text-slate-600 ml-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path d="M9 18l6-6-6-6" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    @stack ("scripts")
</body>
</html>
