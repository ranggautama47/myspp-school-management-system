@extends ("layouts.student")

@section ("title", "Edit Profil")

@section ("content")
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">Profil Saya</h1>
        <p class="text-slate-400 text-sm mt-1">Kelola informasi akun dan keamanan</p>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- ── Info Siswa (read-only) ─────────────────────────────── --}}
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <div class="text-center mb-5">
                @if (auth()->user()->image)
                    <img
                        src="{{ Storage::url(auth()->user()->image) }}"
                        class="w-20 h-20 rounded-full object-cover ring-4 ring-slate-700 mx-auto mb-3"
                    />
                @else
                    <div
                        class="w-20 h-20 rounded-full bg-emerald-500 flex items-center justify-center text-2xl font-bold text-white mx-auto mb-3"
                    >
                        {{
                            strtoupper(
                                substr(auth()->user()->name, 0, 1),
                            )
                        }}
                    </div>
                @endif
                <p class="font-semibold text-white">{{
                    auth()->user()
                        ->name
                }}</p>
                <p class="text-xs text-slate-400 mt-0.5">{{
                    auth()->user()
                        ->email
                }}</p>
            </div>

            @if ($student)
                <div class="space-y-2 text-xs border-t border-slate-800 pt-4">
                    <div class="flex justify-between">
                        <span class="text-slate-500">NIS</span>
                        <span
                            class="font-mono text-slate-300"
                            >{{ $student->nis }}</span
                        >
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Kelas</span>
                        <span class="text-slate-300">{{
                            $student->classroom?->name ??
                                "-"
                        }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Jurusan</span>
                        <span class="text-slate-300">{{
                            $student->department?->name ??
                                "-"
                        }}</span>
                    </div>

                    {{-- ── Tambahan: Tahun Ajaran & Semester ── --}}
                    <div class="flex justify-between">
                        <span class="text-slate-500">Tahun Ajaran</span>
                        <span class="text-slate-300">{{
                            $student->academicYear?->name ??
                                "-"
                        }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Semester</span>
                        <span class="capitalize text-slate-300">{{
                            $student->department?->semester
                                ? 'Semester ' . $student->department->semester
                                : '-'
                        }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-slate-500">Status</span>
                        {{-- Sedikit sentuhan warna agar status 'aktif' lebih menonjol --}}
                        <span
                            class="capitalize font-medium {{ strtolower($student->status) === 'aktif' ? 'text-emerald-400' : 'text-slate-300' }}"
                        >
                            {{ $student->status }}
                        </span>
                    </div>
                </div>
            @endif
        </div>

        {{-- ── Form Edit ────────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-4">
            {{-- Edit Biodata --}}
            <div
                class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden"
            >
                <div class="px-5 py-4 border-b border-slate-800">
                    <h2 class="font-semibold text-white text-sm">
                        Informasi Pribadi
                    </h2>
                </div>
                <form
                    method="POST"
                    action="{{ route('student.profile.update') }}"
                    enctype="multipart/form-data"
                    class="px-5 py-4 space-y-4"
                >
                    @csrf
                    @method ("PUT")

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-xs font-medium text-slate-400 mb-1.5"
                                >Nama Lengkap</label
                            >
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name', $user->name) }}"
                                class="w-full rounded-xl bg-slate-800 border border-slate-700 text-white text-sm
                                          px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500
                                          @error('name') border-rose-500 @enderror"
                            />
                            @error ("name")
                                <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label
                                class="block text-xs font-medium text-slate-400 mb-1.5"
                                >Email</label
                            >
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email', $user->email) }}"
                                class="w-full rounded-xl bg-slate-800 border border-slate-700 text-white text-sm
                                          px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500
                                          @error('email') border-rose-500 @enderror"
                            />
                            @error ("email")
                                <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label
                                class="block text-xs font-medium text-slate-400 mb-1.5"
                                >Nomor HP</label
                            >
                            <input
                                type="tel"
                                name="phone"
                                value="{{ old('phone', $user->phone) }}"
                                placeholder="08xxxxxxxxxx"
                                class="w-full rounded-xl bg-slate-800 border border-slate-700 text-white text-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                        </div>

                        {{-- Field Foto Profil (Yang sudah ada, saya sertakan sebagai penanda posisi) --}}
                        <div>
                            <label
                                class="block text-xs font-medium text-slate-400 mb-1.5"
                                >Foto Profil</label
                            >
                            <input
                                type="file"
                                name="image"
                                accept=".jpg,.jpeg,.png"
                                class="w-full text-sm text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-slate-700 file:text-slate-300 hover:file:bg-slate-600 file:cursor-pointer cursor-pointer"
                            />
                            <p class="text-xs text-slate-600 mt-1">JPG/PNG, maks 2MB</p>
                        </div>
                    </div>
                    {{-- Tutup grid sm:grid-cols-2 sebelumnya --}}

                    {{-- ── Upload Scan Ijazah (Drag & Drop) ── --}}
                    <div
                        class="mt-4"
                        x-data="{
                        isDragging: false,
                        fileName: '{{ $user->scan_ijazah ? basename($user->scan_ijazah) : '' }}',
                        handleFileChange(event) {
                            const file = event.target.files[0];
                            if (file) {
                                this.fileName = file.name;
                            } else {
                                this.fileName = '';
                            }
                        },
                        handleDrop(event) {
                            this.isDragging = false;
                            const files = event.dataTransfer.files;
                            if (files.length > 0) {
                                this.$refs.fileInput.files = files;
                                this.fileName = files[0].name;
                            }
                        }
                    }"
                    >
                        <label
                            class="block text-xs font-medium text-slate-400 mb-1.5"
                            >Scan Ijazah
                        </label>

                        <div
                            class="relative flex flex-col items-center justify-center w-full h-32 rounded-xl border-2 border-dashed transition-colors"
                            :class="isDragging
                                ? 'border-emerald-500 bg-emerald-500/10'
                                : 'border-slate-700 bg-slate-800 hover:bg-slate-800/80 hover:border-slate-600'"
                            @dragover.prevent="isDragging = true"
                            @dragleave.prevent="isDragging = false"
                            @drop.prevent="handleDrop($event)"
                        >
                            {{-- Input File Asli (Disembunyikan) --}}
                            <input
                                type="file"
                                name="scan_ijazah"
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                x-ref="fileInput"
                                @change="handleFileChange"
                            />

                            {{-- State: Belum ada file atau sedang drag --}}
                            <div class="text-center" x-show="!fileName">
                                <svg class="w-8 h-8 text-slate-500 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                </svg>
                                <p class="text-sm font-medium text-slate-300">Klik untuk upload atau drag and drop</p>
                                <p class="text-xs text-slate-500 mt-1">PDF, JPG, PNG (Maks 5MB)</p>
                            </div>

                            {{-- State: File sudah dipilih/ada --}}
                            <div
                                class="text-center flex flex-col items-center justify-center"
                                x-show="fileName"
                                style="display: none"
                            >
                                <svg class="w-8 h-8 text-emerald-500 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p
                                    class="text-sm font-medium text-emerald-400"
                                    x-text="fileName"
                                ></p>
                                <p class="text-xs text-slate-500 mt-1">File siap diupload (Ganti: klik atau drag lagi)</p>
                            </div>
                        </div>
                        @error ("scan_ijazah")
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Diploma Preview --}}
                    <div class="mt-4 p-4 rounded-xl bg-slate-800/50 border border-slate-700/50">
                        <span class="block text-xs font-medium text-slate-400 mb-2">Preview Scan Ijazah</span>
                        
                        @if ($user->scan_ijazah)
                            @php
                                $filePath = $user->scan_ijazah;
                                $fileUrl = Storage::url($filePath);
                                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                $isPdf = $extension === 'pdf';
                                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                            @endphp

                            @if ($isPdf)
                                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-3 bg-slate-850 rounded-lg border border-slate-700">
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-red-500/10 text-red-500">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-semibold text-white">Dokumen PDF</span>
                                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-red-500/20 text-red-400 border border-red-500/30">PDF</span>
                                            </div>
                                            <p class="text-xs text-slate-400 mt-0.5 truncate max-w-xs sm:max-w-md">{{ basename($filePath) }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 w-full sm:w-auto">
                                        <a href="{{ $fileUrl }}" target="_blank" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg bg-slate-700 hover:bg-slate-600 text-white transition-colors">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                            Buka File
                                        </a>
                                        <a href="{{ $fileUrl }}" download class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white transition-colors">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            Unduh
                                        </a>
                                    </div>
                                </div>
                            @elseif ($isImage)
                                <div class="space-y-3">
                                    <div class="relative overflow-hidden rounded-lg border border-slate-700 bg-slate-900 group max-h-80 flex items-center justify-center">
                                        <img src="{{ $fileUrl }}" class="object-contain max-h-80 w-auto rounded-lg transition-transform duration-300 group-hover:scale-[1.02]" alt="Scan Ijazah" />
                                    </div>
                                    <div class="flex items-center justify-between gap-4 p-3 bg-slate-850 rounded-lg border border-slate-700">
                                        <div class="flex items-center gap-3">
                                            <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-emerald-500/10 text-emerald-400">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <p class="text-xs text-slate-400 truncate max-w-xs sm:max-w-md">{{ basename($filePath) }}</p>
                                        </div>
                                        <div class="flex gap-2">
                                            <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg bg-slate-700 hover:bg-slate-600 text-white transition-colors">
                                                Buka
                                            </a>
                                            <a href="{{ $fileUrl }}" download class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white transition-colors">
                                                Unduh
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center justify-between p-3 bg-slate-850 rounded-lg border border-slate-700">
                                    <span class="text-sm text-slate-300">File terunggah (Format tidak dikenal)</span>
                                    <a href="{{ $fileUrl }}" target="_blank" class="text-xs text-emerald-400 hover:underline">Unduh File</a>
                                </div>
                            @endif
                        @else
                            <div class="flex flex-col items-center justify-center py-6 text-center border border-dashed border-slate-700 rounded-lg bg-slate-900/40">
                                <svg class="w-8 h-8 text-slate-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                                <span class="text-xs text-slate-500">Belum ada scan ijazah yang diunggah</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-end pt-2">
                        <button
                            type="submit"
                            class="bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-colors"
                        >
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Ganti Password --}}
            <div
                class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden"
            >
                <div class="px-5 py-4 border-b border-slate-800">
                    <h2 class="font-semibold text-white text-sm">
                        Ganti Password
                    </h2>
                </div>
                <form
                    method="POST"
                    action="{{ route('student.profile.password') }}"
                    class="px-5 py-4 space-y-4"
                >
                    @csrf
                    @method ("PUT")

                    <div>
                        <label
                            class="block text-xs font-medium text-slate-400 mb-1.5"
                            >Password Sekarang</label
                        >
                        <input
                            type="password"
                            name="current_password"
                            class="w-full rounded-xl bg-slate-800 border border-slate-700 text-white text-sm
                                      px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500
                                      @error('current_password') border-rose-500 @enderror"
                        />
                        @error ("current_password")
                            <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-xs font-medium text-slate-400 mb-1.5"
                                >Password Baru</label
                            >
                            <input
                                type="password"
                                name="password"
                                class="w-full rounded-xl bg-slate-800 border border-slate-700 text-white text-sm
                                          px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500
                                          @error('password') border-rose-500 @enderror"
                            />
                            @error ("password")
                                <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label
                                class="block text-xs font-medium text-slate-400 mb-1.5"
                                >Konfirmasi Password</label
                            >
                            <input
                                type="password"
                                name="password_confirmation"
                                class="w-full rounded-xl bg-slate-800 border border-slate-700 text-white text-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button
                            type="submit"
                            class="bg-slate-700 hover:bg-slate-600 text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-colors"
                        >
                            Ganti Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
