<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Portal Siswa — MySPP</title>

    <link
        rel="icon"
        type="image/png"
        href="{{ asset('images/favicon.png') }}"
    />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: "class" };
    </script>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap");
        body {
            font-family: "Inter", sans-serif;
            /* Tambahkan ini untuk mencegah scroll bar aneh */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Memastikan background tidak mengganggu flow konten */
        .bg-layer {
            position: fixed;
            inset: 0;
            z-index: -1;
        }

        /* Memperbaiki container agar responsive dan tidak pecah */
        .main-container {
            width: 100%;
            max-width: 1120px; /* 6xl */
            margin: auto;
            padding: 2rem 1rem;
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
            align-items: start;
        }

        @media (min-width: 1024px) {
            .main-container {
                grid-template-columns: 1fr 1fr;
                padding: 4rem 2rem;
            }
        }
    </style>
</head>
<body
    class="bg-slate-950 min-h-screen flex items-center justify-center p-4 lg:p-8 relative overflow-y-auto"
>
    {{-- Background Image (DIPERBAIKI: lebih terang & kelihatan) --}}
    <div class="fixed inset-0 w-full h-full -z-10">
        <img
            src="{{ asset('images/sekolah.png') }}"
            alt="School Background"
            class="w-full h-full object-cover opacity-40"
        />
    </div>
    {{-- Overlay tipis saja, biar gambar background kelihatan --}}
    <div class="fixed inset-0 bg-slate-950/60 -z-10"></div>

    {{-- Main Container --}}
    <div
        class="relative z-10 w-full max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-start my-8"
    >
        {{-- KOLOM KIRI --}}
        <div class="flex flex-col w-full max-w-lg mx-auto lg:mx-0">
            <div class="text-center mb-8 flex flex-col items-center">
                <div
                    class="mb-5 bg-emerald-500/10 p-4 rounded-2xl border border-emerald-500/30"
                >
                    <img
                        src="{{ asset('images/favicon.png') }}"
                        alt="Favicon"
                        class="w-20 h-20 object-contain"
                    />
                </div>
                <h2
                    class="text-2xl lg:text-3xl font-bold text-white tracking-tight leading-tight mb-3"
                >
                    Selamat Datang Kembali<br />
                    di Portal Siswa <span class="text-emerald-500">MySPP</span>
                </h2>
                <p class="text-slate-400 text-sm">Akses informasi akademik dan pembayaran Anda dengan aman dan mudah.</p>
            </div>

            {{-- Card Kebijakan Privasi --}}
            <div
                class="bg-slate-900/70 border border-slate-700/60 rounded-2xl p-5 mb-4 backdrop-blur-sm"
            >
                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center gap-3">
                        <div
                            class="p-1.5 bg-emerald-500/10 rounded-lg text-emerald-500"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-white font-semibold">
                            Kebijakan Privasi
                        </h3>
                    </div>
                    <span
                        class="px-2.5 py-0.5 text-xs bg-emerald-900/50 text-emerald-400 rounded-full font-medium border border-emerald-500/20"
                        >Wajib</span
                    >
                </div>
                <p class="text-slate-400 text-sm leading-relaxed mb-4">Kami berkomitmen melindungi data pribadi Anda. Semua informasi yang dikumpulkan hanya digunakan untuk keperluan administrasi sekolah dan tidak akan dibagikan kepada pihak ketiga tanpa izin.</p>
                <div
                    class="border-t border-slate-700/50 pt-4 flex items-center gap-3"
                >
                    <input
                        type="checkbox"
                        id="privacy_check"
                        class="w-4 h-4 rounded bg-slate-900 border-slate-600 text-emerald-500 focus:ring-emerald-500/30"
                    />
                    <span class="text-sm text-slate-400"
                        >Saya telah membaca dan memahami
                        <span class="text-emerald-500"
                            >Kebijakan Privasi</span
                        ></span
                    >
                </div>
            </div>

            {{-- Card Syarat & Ketentuan --}}
            <div
                class="bg-slate-900/70 border border-slate-700/60 rounded-2xl p-5 backdrop-blur-sm"
            >
                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center gap-3">
                        <div
                            class="p-1.5 bg-emerald-500/10 rounded-lg text-emerald-500"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-white font-semibold">
                            Syarat & Ketentuan
                        </h3>
                    </div>
                    <span
                        class="px-2.5 py-0.5 text-xs bg-emerald-900/50 text-emerald-400 rounded-full font-medium border border-emerald-500/20"
                        >Wajib</span
                    >
                </div>
                <p class="text-slate-400 text-sm leading-relaxed mb-4">Dengan menggunakan MySPP, Anda setuju untuk mematuhi semua syarat dan ketentuan yang berlaku dalam sistem ini. Pastikan Anda memahami seluruh ketentuan sebelum menggunakan layanan.</p>
                <div
                    class="border-t border-slate-700/50 pt-4 flex items-center gap-3"
                >
                    <input
                        type="checkbox"
                        id="terms_check"
                        class="w-4 h-4 rounded bg-slate-900 border-slate-600 text-emerald-500 focus:ring-emerald-500/30"
                    />
                    <span class="text-sm text-slate-400"
                        >Saya telah membaca dan memahami
                        <span class="text-emerald-500"
                            >Syarat & Ketentuan</span
                        ></span
                    >
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Login Card --}}
        <div
            class="w-full max-w-md mx-auto lg:ml-auto bg-slate-900/70 backdrop-blur-sm border border-slate-700/60 rounded-3xl p-8 shadow-2xl"
        >
            <div class="flex justify-center mb-6">
                <div
                    class="w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center shadow-inner border border-slate-700/50"
                >
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 12C14.21 12 16 10.21 16 8C16 5.79 14.21 4 12 4C9.79 4 8 5.79 8 8C8 10.21 9.79 12 12 12ZM12 14C9.33 14 4 15.34 4 18V20H20V18C20 15.34 14.67 14 12 14Z" fill="#10B981" />
                    </svg>
                </div>
            </div>

            <h3 class="text-2xl font-bold text-white text-center mb-2">
                Masuk ke Portal Siswa
            </h3>
            <p class="text-center text-sm text-slate-400 mb-8">Silahkan masuk untuk melanjutkan</p>

            <form
                method="POST"
                action="{{ route('login') }}"
                class="space-y-5"
                id="loginForm"
            >
                @csrf

                <div class="space-y-2">
                    <label class="text-sm font-medium text-white"
                        >Email Sekolah</label
                    >
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"
                        >
                            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input
                            type="email"
                            name="email"
                            required
                            value="{{ old('email') }}"
                            class="w-full pl-11 pr-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all text-sm"
                            placeholder="Silahkan masukan email dari sekolah"
                        />
                    </div>
                    @error ("email")
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <label class="text-sm font-medium text-white"
                            >Kata Sandi</label
                        >
                        @if (Route::has("password.request"))
                            <a
                                href="{{ route('password.request') }}"
                                class="text-xs text-emerald-500 hover:text-emerald-400"
                                >Lupa Password?</a
                            >
                        @endif
                    </div>
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"
                        >
                            <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            required
                            class="w-full pl-11 pr-11 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all text-sm"
                            placeholder="Masukkan kata sandi Anda"
                        />
                        <button
                            type="button"
                            onclick="togglePasswordVisibility()"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-300"
                        >
                            <svg id="password-toggle-icon" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    @error ("password")
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input
                            type="checkbox"
                            name="remember"
                            class="w-4 h-4 rounded bg-slate-800 border-slate-600 text-emerald-500 focus:ring-emerald-500/30"
                        />
                        <span
                            class="text-sm text-slate-300 group-hover:text-white transition"
                            >Ingat Saya</span
                        >
                    </label>

                    {{-- TOOLTIP Butuh bantuan? (bukan link) --}}
                    <div class="relative group">
                        <span
                            class="text-sm text-emerald-500 cursor-help hover:text-emerald-400 transition"
                            >Butuh bantuan?</span
                        >
                        <div
                            class="absolute right-0 bottom-full mb-2 hidden group-hover:block z-20 w-64"
                        >
                            <div
                                class="bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-xs text-slate-300 shadow-xl"
                            >
                                Silahkan hubungi bagian BAAK atau admin TI
                                sekolah
                                <div
                                    class="absolute right-3 -bottom-1.5 w-3 h-3 bg-slate-800 border-r border-b border-slate-700 rotate-45"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full mt-4 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-3.5 px-4 rounded-xl shadow-lg transition-all text-base flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Masuk
                </button>

                {{-- Card Butuh Bantuan (TETAP DI PERTAHANKAN) --}}
                <div
                    class="mt-6 border border-slate-700/60 rounded-xl p-4 bg-slate-800/50 transition-colors group"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="p-2.5 bg-emerald-500/10 rounded-lg text-emerald-500"
                        >
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-white font-medium text-sm">
                                Butuh bantuan?
                            </h4>
                            <p class="text-slate-400 text-xs mt-0.5 leading-relaxed">Hubungi Admin IT Sekolah atau<br />Panduan Aktivasi Akun</p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- COPYRIGHT DI SINI (Tengah Bawah Layar) --}}
    <p class="absolute bottom-4 left-1/2 transform -translate-x-1/2 text-center text-xs text-slate-500 z-10 w-full">&copy; {{ date("Y") }} MySPP. All Rights Reserved.</p>

    <script>
        function togglePasswordVisibility() {
            const input = document.getElementById("password");
            const icon = document.getElementById("password-toggle-icon");
            if (input.type === "password") {
                input.type = "text";
                icon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.024 10.024 0 014.138-4.137M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 21l-18-18"/>';
            } else {
                input.type = "password";
                icon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
            }
        }
    </script>
</body>
</html>
