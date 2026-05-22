@extends('layouts.student')

@section('title', 'Edit Profil')

@section('content')

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">Profil Saya</h1>
        <p class="text-slate-400 text-sm mt-1">Kelola informasi akun dan keamanan</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Info Siswa (read-only) ─────────────────────────────── --}}
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <div class="text-center mb-5">
                @if(auth()->user()->image)
                    <img src="{{ Storage::url(auth()->user()->image) }}"
                         class="w-20 h-20 rounded-full object-cover ring-4 ring-slate-700 mx-auto mb-3">
                @else
                    <div class="w-20 h-20 rounded-full bg-emerald-500 flex items-center justify-center text-2xl font-bold text-white mx-auto mb-3">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif
                <p class="font-semibold text-white">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ auth()->user()->email }}</p>
            </div>

            @if($student)
                <div class="space-y-2 text-xs border-t border-slate-800 pt-4">
                    <div class="flex justify-between">
                        <span class="text-slate-500">NIS</span>
                        <span class="font-mono text-slate-300">{{ $student->nis }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Kelas</span>
                        <span class="text-slate-300">{{ $student->classroom?->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Jurusan</span>
                        <span class="text-slate-300">{{ $student->department?->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Status</span>
                        <span class="capitalize text-slate-300">{{ $student->status }}</span>
                    </div>
                </div>
            @endif
        </div>

        {{-- ── Form Edit ────────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Edit Biodata --}}
            <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-800">
                    <h2 class="font-semibold text-white text-sm">Informasi Pribadi</h2>
                </div>
                <form method="POST" action="{{ route('student.profile.update') }}"
                      enctype="multipart/form-data" class="px-5 py-4 space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                   class="w-full rounded-xl bg-slate-800 border border-slate-700 text-white text-sm
                                          px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500
                                          @error('name') border-rose-500 @enderror">
                            @error('name') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                   class="w-full rounded-xl bg-slate-800 border border-slate-700 text-white text-sm
                                          px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500
                                          @error('email') border-rose-500 @enderror">
                            @error('email') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Nomor HP</label>
                            <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                                   placeholder="08xxxxxxxxxx"
                                   class="w-full rounded-xl bg-slate-800 border border-slate-700 text-white text-sm
                                          px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Foto Profil</label>
                            <input type="file" name="image" accept=".jpg,.jpeg,.png"
                                   class="w-full text-sm text-slate-400 file:mr-3 file:py-1.5 file:px-3
                                          file:rounded-lg file:border-0 file:text-xs file:font-medium
                                          file:bg-slate-700 file:text-slate-300 hover:file:bg-slate-600
                                          file:cursor-pointer cursor-pointer">
                            <p class="text-xs text-slate-600 mt-1">JPG/PNG, maks 2MB</p>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit"
                                class="bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold
                                       px-6 py-2.5 rounded-xl transition-colors">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Ganti Password --}}
            <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-800">
                    <h2 class="font-semibold text-white text-sm">Ganti Password</h2>
                </div>
                <form method="POST" action="{{ route('student.profile.password') }}"
                      class="px-5 py-4 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1.5">Password Sekarang</label>
                        <input type="password" name="current_password"
                               class="w-full rounded-xl bg-slate-800 border border-slate-700 text-white text-sm
                                      px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500
                                      @error('current_password') border-rose-500 @enderror">
                        @error('current_password') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Password Baru</label>
                            <input type="password" name="password"
                                   class="w-full rounded-xl bg-slate-800 border border-slate-700 text-white text-sm
                                          px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500
                                          @error('password') border-rose-500 @enderror">
                            @error('password') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1.5">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation"
                                   class="w-full rounded-xl bg-slate-800 border border-slate-700 text-white text-sm
                                          px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit"
                                class="bg-slate-700 hover:bg-slate-600 text-white text-sm font-semibold
                                       px-6 py-2.5 rounded-xl transition-colors">
                            Ganti Password
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

@endsection
