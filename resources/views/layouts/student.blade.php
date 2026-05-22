<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Siswa') — MySPP</title>

    {{-- Tailwind CSS via CDN untuk portal siswa --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50:  '#ecfdf5',
                            100: '#d1fae5',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                        },
                        slate: {
                            750: '#1a2744',
                            850: '#0f1a2e',
                            950: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>

    {{-- Alpine.js untuk interaktivitas ringan --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('head')
</head>
<body class="bg-slate-950 text-white min-h-screen antialiased">

    {{-- ── NAVBAR ─────────────────────────────────────────────── --}}
    <nav class="bg-slate-900 border-b border-slate-800 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Brand --}}
                <a href="{{ route('student.dashboard') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-primary-500 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                    </div>
                    <span class="font-bold text-white">
                        <span class="text-primary-500">My</span>SPP
                    </span>
                </a>

                {{-- Desktop Nav --}}
                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('student.dashboard') }}"
                       class="px-3 py-2 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('student.dashboard')
                                 ? 'bg-primary-500/10 text-primary-400'
                                 : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('student.transactions') }}"
                       class="px-3 py-2 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('student.transactions*')
                                 ? 'bg-primary-500/10 text-primary-400'
                                 : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        Pembayaran
                    </a>
                    <a href="{{ route('student.profile') }}"
                       class="px-3 py-2 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('student.profile*')
                                 ? 'bg-primary-500/10 text-primary-400'
                                 : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        Profil
                    </a>
                </div>

                {{-- User menu --}}
                <div class="flex items-center gap-3" x-data="{ open: false }">
                    <div class="hidden md:flex flex-col items-end">
                        <span class="text-sm font-medium text-white">{{ auth()->user()->name }}</span>
                        <span class="text-xs text-slate-400">{{ auth()->user()->student?->nis ?? 'Siswa' }}</span>
                    </div>

                    {{-- Avatar --}}
                    <button @click="open = !open" class="relative">
                        @if(auth()->user()->image)
                            <img src="{{ Storage::url(auth()->user()->image) }}"
                                 class="w-9 h-9 rounded-full object-cover ring-2 ring-slate-700">
                        @else
                            <div class="w-9 h-9 rounded-full bg-primary-500 flex items-center justify-center text-sm font-bold text-white">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif

                        {{-- Dropdown --}}
                        <div x-show="open" @click.outside="open = false"
                             x-transition
                             class="absolute right-0 mt-2 w-44 bg-slate-800 border border-slate-700 rounded-xl shadow-xl py-1 z-50">
                            <a href="{{ route('student.profile') }}"
                               class="block px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-white">
                                Edit Profil
                            </a>
                            <div class="border-t border-slate-700 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full text-left px-4 py-2 text-sm text-rose-400 hover:bg-slate-700">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </button>

                    {{-- Mobile menu button --}}
                    <button class="md:hidden p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800"
                            x-data @click="$dispatch('toggle-mobile-menu')">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Nav --}}
        <div class="md:hidden border-t border-slate-800"
             x-data="{ open: false }" @toggle-mobile-menu.window="open = !open"
             x-show="open" x-transition>
            <div class="px-4 py-2 space-y-1">
                <a href="{{ route('student.dashboard') }}"
                   class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('student.dashboard') ? 'bg-primary-500/10 text-primary-400' : 'text-slate-400' }}">
                    Dashboard
                </a>
                <a href="{{ route('student.transactions') }}"
                   class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('student.transactions*') ? 'bg-primary-500/10 text-primary-400' : 'text-slate-400' }}">
                    Pembayaran
                </a>
                <a href="{{ route('student.profile') }}"
                   class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('student.profile*') ? 'bg-primary-500/10 text-primary-400' : 'text-slate-400' }}">
                    Profil
                </a>
            </div>
        </div>
    </nav>

    {{-- ── MAIN CONTENT ──────────────────────────────────────────── --}}
    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mb-6 flex items-center gap-3 rounded-xl bg-primary-500/10 border border-primary-500/20 px-4 py-3 text-sm text-primary-400">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 flex items-center gap-3 rounded-xl bg-rose-500/10 border border-rose-500/20 px-4 py-3 text-sm text-rose-400">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    {{-- ── FOOTER ──────────────────────────────────────────────── --}}
    <footer class="mt-16 border-t border-slate-800 py-6 text-center text-xs text-slate-600">
        MySPP School Management System &copy; {{ date('Y') }}
    </footer>

    @stack('scripts')
</body>
</html>
