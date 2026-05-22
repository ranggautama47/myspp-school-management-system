<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — MySPP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' }
    </script>
</head>
<body class="bg-slate-950 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">

        {{-- Brand --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-500 rounded-2xl mb-4 shadow-lg shadow-emerald-500/25">
                <svg class="w-9 h-9 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 14l9-5-9-5-9 5 9 5zM12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">
                <span class="text-emerald-500">My</span>SPP
            </h1>
            <p class="text-slate-400 text-sm mt-1">Portal Siswa</p>
        </div>

        {{-- Card --}}
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-8 shadow-xl">
            <h2 class="text-lg font-semibold text-white mb-6">Masuk ke Akun</h2>

            {{-- Error --}}
            @if($errors->any())
                <div class="mb-4 rounded-xl bg-rose-500/10 border border-rose-500/20 px-4 py-3 text-sm text-rose-400">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">
                        Email
                    </label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="w-full rounded-xl bg-slate-800 border border-slate-700 text-white placeholder-slate-500
                               px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent
                               @error('email') border-rose-500 @enderror"
                        placeholder="email@siswa.com"
                    >
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">
                        Password
                    </label>
                    <input
                        type="password"
                        name="password"
                        required
                        class="w-full rounded-xl bg-slate-800 border border-slate-700 text-white placeholder-slate-500
                               px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                        placeholder="••••••••"
                    >
                </div>

                {{-- Remember --}}
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember"
                           class="w-4 h-4 rounded bg-slate-800 border-slate-600 text-emerald-500 focus:ring-emerald-500">
                    <label for="remember" class="text-sm text-slate-400">Ingat saya</label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700
                               text-white font-semibold py-2.5 rounded-xl transition-colors duration-150 text-sm mt-2">
                    Masuk
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-slate-600 mt-6">
            Hubungi admin sekolah jika lupa password atau belum punya akun.
        </p>
    </div>

</body>
</html>
