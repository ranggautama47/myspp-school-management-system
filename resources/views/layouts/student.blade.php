<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>
        @yield ("title", "Portal Siswa")
        — MySPP
    </title>

    <!-- Favicon / Browser Tab Icon -->
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
                    colors: { emerald: { 500: "#10b981", 600: "#059669" } },
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
        {{-- ── SIDEBAR ─────────────────────────────────────────────── --}}
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
                        class="w-8 h-8 flex items-center justify-center flex-shrink-0"
                    >
                        <img
                            src="{{ asset('images/favicon.png') }}"
                            alt="MySPP Logo"
                            class="w-8 h-8 object-contain"
                        />
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white leading-none"><span class="text-emerald-500">My</span>SPP</p>
                        <p class="text-[10px] text-slate-500 mt-0.5">Student Portal</p>
                    </div>
                </a>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 px-2 py-4 space-y-0.5">
                @php
                    $nav = [
                        [
                            "route" => "student.dashboard",
                            "label" => "Dashboard",
                            "match" => "student.dashboard",
                            "icon" =>
                                "M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z",
                        ],
                        [
                            "route" => "student.transactions",
                            "label" => "Payment History",
                            "match" => "student.transactions*",
                            "icon" =>
                                "M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2",
                        ],
                        [
                            "route" => "student.profile",
                            "label" => "My Profile",
                            "match" => "student.profile*",
                            "icon" =>
                                "M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z",
                        ],
                    ];
                @endphp

                @foreach ($nav as $item)
                    @php $active = request()->routeIs($item["match"]); @endphp
                    <a
                        href="{{ route($item['route']) }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                          {{ $active ? 'bg-emerald-500/10 text-emerald-400 font-medium' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/70' }}"
                    >
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="{{ $item['icon'] }}"
                            />
                        </svg>
                        {{ $item["label"] }}
                        @if ($active)
                            <span
                                class="ml-auto w-1.5 h-1.5 rounded-full bg-emerald-500"
                            ></span>
                        @endif
                    </a>
                @endforeach
            </nav>

            {{-- Help --}}
            <div
                class="m-3 p-3 bg-slate-800/60 rounded-xl border border-slate-700/50"
            >
                <p class="text-[11px] text-slate-400 leading-relaxed">Need help? Contact our support team.</p>
                <a
                    href="mailto:admin@myspp.com"
                    class="mt-2 block text-center text-[11px] font-medium text-emerald-400 border border-emerald-500/25 rounded-lg py-1.5 hover:bg-emerald-500/10 transition-colors"
                >
                    Contact Support
                </a>
            </div>
        </aside>

        {{-- ── MAIN ─────────────────────────────────────────────────── --}}
        <div class="flex-1 flex flex-col min-w-0">
            {{-- Topbar --}}
            <header
                class="bg-slate-950 border-b border-slate-800 px-6 py-3 flex items-center justify-between sticky top-0 z-30"
            >
                <div>
                    <h1 class="text-sm font-medium text-white">
                        @yield ("page-title", auth()->user()->name)
                    </h1>
                    <p class="text-[11px] text-slate-500 mt-0.5">@yield ("page-subtitle", "Portal Siswa MySPP")</p>
                </div>

                <div class="flex items-center gap-3" x-data="{ open: false }">
                    {{-- ── Notification Bell ─────────────────────────────── --}}
                    <div class="relative" x-data="{ openNotifications: false }">
                        <button
                            @click="openNotifications = !openNotifications"
                            class="relative w-8 h-8 bg-slate-800 border border-slate-700/60 rounded-lg flex items-center justify-center hover:bg-slate-700 transition-colors cursor-pointer"
                        >
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            @if ($notifCount > 0)
                                <span class="absolute -top-1 -right-1 min-w-[16px] h-4 bg-rose-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center px-0.5 border-2 border-slate-950">
                                    {{ $notifCount > 9 ? '9+' : $notifCount }}
                                </span>
                            @endif
                        </button>

                        {{-- Popover --}}
                        <div
                            x-show="openNotifications"
                            @click.outside="openNotifications = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-[-8px]"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 translate-y-[-8px]"
                            class="absolute right-0 mt-2 w-80 bg-slate-800 border border-slate-700 rounded-xl shadow-2xl shadow-black/40 z-50 overflow-hidden"
                            style="display: none"
                        >
                            {{-- Header --}}
                            <div class="px-4 py-3 border-b border-slate-700 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-semibold text-slate-200">Notifikasi</span>
                                    @if ($notifCount > 0)
                                        <span class="bg-rose-500/20 text-rose-400 text-[10px] font-semibold rounded-full px-1.5 py-0.5">
                                            {{ $notifCount }}
                                        </span>
                                    @endif
                                </div>
                                <button @click="openNotifications = false" class="text-slate-500 hover:text-slate-300 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Body --}}
                            <div class="max-h-72 overflow-y-auto divide-y divide-slate-700/50">
                                @forelse ($navNotifications as $notif)
                                    <div class="p-3.5 hover:bg-slate-700/40 transition-colors flex gap-3 items-start group">
                                        <div class="w-2 h-2 {{ $notif['dot'] }} rounded-full mt-1.5 flex-shrink-0 shadow-[0_0_6px_currentColor]"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold text-slate-200 group-hover:text-emerald-400 transition-colors leading-none">
                                                {{ $notif['title'] }}
                                            </p>
                                            <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">{{ $notif['message'] }}</p>
                                            <p class="text-[10px] text-slate-600 mt-1">{{ $notif['time'] }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="py-10 text-center">
                                        <svg class="w-8 h-8 text-slate-700 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                        <p class="text-xs text-slate-600 font-medium">Tidak ada notifikasi</p>
                                        <p class="text-[11px] text-slate-700 mt-0.5">Semua tagihan sudah beres!</p>
                                    </div>
                                @endforelse
                            </div>

                            {{-- Footer --}}
                            <div class="border-t border-slate-700 bg-slate-900/50">
                                <a
                                    href="{{ route('student.transactions') }}"
                                    class="block text-center py-2.5 text-[11px] font-medium text-emerald-400 hover:text-emerald-300 hover:bg-emerald-500/5 transition-colors"
                                >
                                    Lihat Semua Transaksi →
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <button
                            @click="open = !open"
                            class="flex items-center gap-2.5 cursor-pointer group"
                        >
                            @if (auth()->user()->image)
                                <img
                                    src="{{ Storage::url(auth()->user()->image) }}"
                                    class="w-8 h-8 rounded-full object-cover ring-2 ring-slate-700 group-hover:ring-emerald-500/40 transition-all"
                                />
                            @else
                                <div
                                    class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center text-xs font-semibold text-white ring-2 ring-slate-700 group-hover:ring-emerald-500/40 transition-all"
                                >
                                    {{
                                        strtoupper(
                                            substr(auth()->user()->name, 0, 2),
                                        )
                                    }}
                                </div>
                            @endif
                            <div class="hidden sm:block text-right">
                                <p class="text-xs font-medium text-slate-200 leading-none">{{
                                    auth()->user()
                                        ->name
                                }}</p>
                                <p class="text-[10px] text-slate-500 mt-0.5">{{
                                    auth()->user()->student?->nis ??
                                        "Student"
                                }}</p>
                            </div>
                        </button>

                        <div
                            x-show="open"
                            @click.outside="open = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            class="absolute right-0 mt-2 w-44 bg-slate-800 border border-slate-700/60 rounded-xl shadow-xl shadow-black/30 py-1 z-50"
                        >
                            <a
                                href="{{ route('student.profile') }}"
                                class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-300 hover:bg-slate-700/60 hover:text-white transition-colors"
                            >
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Edit Profile
                            </a>
                            <div
                                class="border-t border-slate-700/50 my-1"
                            ></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-xs text-rose-400 hover:bg-slate-700/60 transition-colors"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Flash --}}
            <div class="px-6 pt-4">
                @if (session("success"))
                    <div
                        class="flex items-center gap-2.5 rounded-xl bg-emerald-500/8 border border-emerald-500/15 px-4 py-2.5 text-xs text-emerald-400 mb-0"
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
                        class="flex items-center gap-2.5 rounded-xl bg-rose-500/8 border border-rose-500/15 px-4 py-2.5 text-xs text-rose-400 mb-0"
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
            </div>

            {{-- Content --}}
            <main class="flex-1 p-6">
                @yield ("content")
            </main>
        </div>
    </div>

    @stack ("scripts")
</body>
</html>
