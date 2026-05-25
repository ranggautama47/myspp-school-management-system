<x-filament-panels::page.simple>

    {{-- ══════════════════════════════════════════════════════════
         ROOT CAUSE FIX:
         1. @apply tidak bisa di <style> tag biasa → ganti ke plain CSS
         2. fi-simple-main perlu disembunyikan dengan plain CSS
         3. Div fullscreen kita harus escape dari wrapper Filament
    ══════════════════════════════════════════════════════════ --}}

    <style>
        html, body {
            background-color: #0B121E !important;
            margin: 0 !important;
            padding: 0 !important;
            height: 100% !important;
            overflow: hidden !important;
        }

        .fi-simple-layout,
        .fi-simple-page,
        .fi-simple-main-ctn,
        .fi-simple-main {
            background: transparent !important;
            padding: 0 !important;
            margin: 0 !important;
            box-shadow: none !important;
            border: none !important;
            max-width: 100% !important;
            width: 100% !important;
            min-height: 0 !important;
        }

        .fi-simple-header,
        .fi-simple-header-ctn,
        .fi-simple-logo {
            display: none !important;
        }

        .fi-body {
            background-color: #0B121E !important;
            min-height: 100vh !important;
        }

        /* ── Form Inputs ── */
        .fi-input-wrp {
            background-color: rgba(19, 27, 42, 0.9) !important;
            border-color: #1e293b !important;
            border-radius: 12px !important;
            transition: all 0.2s !important;
            box-shadow: none !important;
        }
        .fi-input-wrp:focus-within {
            border-color: rgba(16, 185, 129, 0.5) !important;
            box-shadow: 0 0 0 1px rgba(16, 185, 129, 0.3) !important;
        }
        .fi-input {
            color: #f1f5f9 !important;
            background: transparent !important;
        }
        .fi-input::placeholder {
            color: #64748b !important;
        }

        /* ── Form Labels ── */
        .fi-fo-field-wrp-label label,
        .fi-fo-field-wrp-label span {
            color: #cbd5e1 !important;
            font-size: 13px !important;
        }

        /* ── Submit / Sign In Button ── */
        .fi-btn-color-primary,
        .fi-form-actions .fi-btn {
            background-color: #10b981 !important;
            border: none !important;
            border-radius: 12px !important;
            padding-top: 12px !important;
            padding-bottom: 12px !important;
            font-size: 15px !important;
            font-weight: 700 !important;
            color: #ffffff !important;
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.3) !important;
            transition: all 0.2s !important;
            width: 100% !important;
        }
        .fi-btn-color-primary:hover,
        .fi-form-actions .fi-btn:hover {
            background-color: #059669 !important;
            box-shadow: 0 0 28px rgba(16, 185, 129, 0.45) !important;
        }

        /* ── Remember me checkbox & link ── */
        .fi-checkbox-input {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            accent-color: #10b981 !important;
        }
        .fi-link { color: #10b981 !important; }
        .fi-link:hover { color: #34d399 !important; }

        /* ── Custom Scrollbar ── */
        .myspp-scrollbar::-webkit-scrollbar { width: 5px; }
        .myspp-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .myspp-scrollbar::-webkit-scrollbar-thumb { background-color: #1e293b; border-radius: 20px; }

        /* ── Policy card highlight on error ── */
        .policy-card-error { border-color: #ef4444 !important; }

        /* ── Toast notif custom ── */
        #myspp-toast {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%) translateY(80px);
            background: #1e293b;
            border: 1px solid #ef4444;
            border-radius: 12px;
            padding: 12px 20px;
            color: #fca5a5;
            font-size: 13px;
            font-weight: 500;
            z-index: 9999;
            transition: transform 0.3s ease, opacity 0.3s ease;
            opacity: 0;
            white-space: nowrap;
        }
        #myspp-toast.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }
    </style>

    {{-- ══════════════════════════════════════════════════════════
         FULLSCREEN LAYOUT — di dalam slot Filament
         fi-simple-main disembunyikan via CSS di atas,
         tapi form Filament tetap perlu di-render di sini
         supaya wire:submit bisa jalan
    ══════════════════════════════════════════════════════════ --}}

    {{-- FULLSCREEN WRAPPER --}}
    <div style="position:fixed;inset:0;z-index:9999;display:flex;flex-direction:row;background-color:#0B121E;font-family:ui-sans-serif,system-ui,sans-serif;overflow:hidden;"
         id="myspp-login-root">

        {{-- ══════ KOLOM KIRI — Privasi & Kebijakan ══════ --}}
        <div class="myspp-scrollbar"
             style="width:40%;min-width:340px;height:100%;background-color:#0B121E;overflow-y:auto;display:flex;flex-direction:column;justify-content:center;padding:40px 48px;border-right:1px solid #1e293b;">

            <div style="max-width:380px;margin:0 auto;width:100%;">

                {{-- Header kiri --}}
                <div style="text-align:center;margin-bottom:36px;">
                    <div style="width:64px;height:64px;background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2);border-radius:18px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;box-shadow:0 0 30px rgba(16,185,129,0.12);">
                        <svg style="width:30px;height:30px;color:#10b981;" fill="none" viewBox="0 0 24 24" stroke="#10b981">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h1 style="font-size:26px;font-weight:700;color:#f1f5f9;margin:0 0 6px;letter-spacing:-0.5px;">Privasi & Kebijakan</h1>
                    <p style="font-size:13px;color:#64748b;margin:0;">Harap baca dengan seksama sebelum melanjutkan</p>
                </div>

                {{-- Card Kebijakan Privasi --}}
                <div id="card-privacy"
                     style="background:#0f1929;border:1px solid #1e293b;border-radius:14px;padding:18px 20px;margin-bottom:14px;transition:border-color 0.2s;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="padding:8px;background:rgba(16,185,129,0.08);border-radius:10px;">
                                <svg style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="#10b981">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <span style="font-size:14px;font-weight:600;color:#f1f5f9;">Kebijakan Privasi</span>
                        </div>
                        <span style="font-size:11px;font-weight:600;color:#10b981;background:rgba(16,185,129,0.1);padding:3px 10px;border-radius:20px;">Wajib</span>
                    </div>
                    <p style="font-size:12.5px;color:#64748b;line-height:1.7;margin:0 0 14px;padding-left:36px;">
                        Kami berkomitmen melindungi data pribadi Anda. Semua informasi hanya digunakan untuk keperluan administrasi sekolah dan tidak dibagikan kepada pihak ketiga tanpa izin.
                    </p>
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;border-top:1px solid #1e293b;padding-top:14px;">
                        <input type="checkbox" id="privacy_check"
                               style="width:18px;height:18px;accent-color:#10b981;background:#1e293b;border:1px solid #334155;border-radius:5px;cursor:pointer;flex-shrink:0;">
                        <span style="font-size:12.5px;color:#94a3b8;line-height:1.5;">Saya telah membaca dan memahami
                            <a href="#" style="color:#10b981;text-decoration:none;font-weight:600;">Kebijakan Privasi</a>
                        </span>
                    </label>
                </div>

                {{-- Card Syarat & Ketentuan --}}
                <div id="card-terms"
                     style="background:#0f1929;border:1px solid #1e293b;border-radius:14px;padding:18px 20px;margin-bottom:28px;transition:border-color 0.2s;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="padding:8px;background:rgba(16,185,129,0.08);border-radius:10px;">
                                <svg style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="#10b981">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <span style="font-size:14px;font-weight:600;color:#f1f5f9;">Syarat & Ketentuan</span>
                        </div>
                        <span style="font-size:11px;font-weight:600;color:#10b981;background:rgba(16,185,129,0.1);padding:3px 10px;border-radius:20px;">Wajib</span>
                    </div>
                    <p style="font-size:12.5px;color:#64748b;line-height:1.7;margin:0 0 14px;padding-left:36px;">
                        Dengan menggunakan MySPP, Anda setuju mematuhi semua syarat dan ketentuan yang berlaku. Pastikan Anda memahami seluruh ketentuan sebelum menggunakan layanan.
                    </p>
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;border-top:1px solid #1e293b;padding-top:14px;">
                        <input type="checkbox" id="terms_check"
                               style="width:18px;height:18px;accent-color:#10b981;background:#1e293b;border:1px solid #334155;border-radius:5px;cursor:pointer;flex-shrink:0;">
                        <span style="font-size:12.5px;color:#94a3b8;line-height:1.5;">Saya telah membaca dan memahami
                            <a href="#" style="color:#10b981;text-decoration:none;font-weight:600;">Syarat & Ketentuan</a>
                        </span>
                    </label>
                </div>

                {{-- Status keamanan --}}
                <div style="display:flex;align-items:center;justify-content:center;gap:8px;color:#64748b;font-size:12px;">
                    <svg style="width:14px;height:14px;flex-shrink:0;" fill="#10b981" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>Keamanan data Anda adalah prioritas kami</span>
                </div>

            </div>
        </div>

        {{-- ══════ KOLOM KANAN — Form Login ══════ --}}
        <div style="flex:1;height:100%;position:relative;display:flex;align-items:center;justify-content:center;padding:40px 32px;">

            {{-- Background image + overlay --}}
            <div style="position:absolute;inset:0;z-index:0;">
                <img src="{{ asset('images/sekolah.png') }}" alt="School"
                     style="width:100%;height:100%;object-fit:cover;object-position:center;" />
                <div style="position:absolute;inset:0;background:rgba(11,18,30,0.78);backdrop-filter:blur(2px);"></div>
                <div style="position:absolute;inset:0;background:linear-gradient(to bottom, transparent 20%, rgba(11,18,30,0.5) 60%, #0B121E 100%);"></div>
            </div>

            {{-- Form container --}}
            <div style="position:relative;z-index:10;width:100%;max-width:420px;">

                {{-- Logo & Heading --}}
                <div style="text-align:center;margin-bottom:32px;">
                    <div style="width:76px;height:76px;margin:0 auto 16px;background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.25);border-radius:20px;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(8px);">
                        <img src="{{ asset('images/favicon.png') }}" alt="MySPP Logo"
                             style="width:46px;height:46px;object-fit:contain;"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='block';">
                        <span style="display:none;font-size:28px;font-weight:900;color:#10b981;">M</span>
                    </div>
                    <div style="font-size:30px;font-weight:700;color:#f1f5f9;letter-spacing:-1px;margin-bottom:2px;">
                        <span style="color:#10b981;">My</span>SPP
                    </div>
                    <div style="font-size:12px;color:#64748b;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:20px;">School Management System</div>
                    <h2 style="font-size:22px;font-weight:700;color:#f1f5f9;margin:0 0 4px;">
                        Selamat Datang, <span style="color:#10b981;">Admin</span>
                    </h2>
                    <p style="font-size:13px;color:#64748b;margin:0;">Masuk untuk mengakses dashboard admin</p>
                </div>

                {{-- ═══ FILAMENT FORM — render di sini ═══ --}}
                {{-- Filament butuh ini untuk wire:submit & validasi --}}
                <div style="width:100%;">
                    <x-filament-panels::form
                        id="loginForm"
                        wire:submit="authenticate"
                        style="display:flex;flex-direction:column;gap:18px;">
                        {{ $this->form }}
                        <x-filament-panels::form.actions
                            :actions="$this->getCachedFormActions()"
                            :full-width="true"
                        />
                    </x-filament-panels::form>
                </div>

                {{-- Security badge --}}
                <div style="margin-top:24px;background:rgba(11,18,30,0.6);backdrop-filter:blur(8px);border:1px solid #1e293b;border-radius:14px;padding:14px 18px;display:flex;gap:12px;align-items:flex-start;">
                    <div style="padding:6px;background:rgba(16,185,129,0.15);border-radius:50%;flex-shrink:0;margin-top:1px;">
                        <svg style="width:14px;height:14px;" fill="#10b981" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <p style="font-size:13px;font-weight:600;color:#f1f5f9;margin:0 0 3px;">Sistem Aman & Terlindungi</p>
                        <p style="font-size:11.5px;color:#64748b;margin:0;line-height:1.6;">MySPP menggunakan enkripsi data tingkat tinggi untuk melindungi informasi sekolah dan pengguna dari akses tidak sah.</p>
                    </div>
                </div>

                {{-- Footer --}}
                <p style="text-align:center;font-size:11px;color:#334155;margin-top:20px;">
                    &copy; {{ date('Y') }} MySPP. All rights reserved.
                </p>

            </div>
        </div>

    </div>

    {{-- Toast notifikasi --}}
    <div id="myspp-toast">⚠ Harap centang Kebijakan Privasi dan Syarat & Ketentuan terlebih dahulu</div>

    {{-- ══════ SCRIPT VALIDASI ══════ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const toast    = document.getElementById('myspp-toast');
            const cardP    = document.getElementById('card-privacy');
            const cardT    = document.getElementById('card-terms');
            const privacyCb = document.getElementById('privacy_check');
            const termsCb   = document.getElementById('terms_check');

            function showToast(msg) {
                toast.textContent = '⚠ ' + msg;
                toast.classList.add('show');
                setTimeout(() => toast.classList.remove('show'), 3500);
            }

            function flashCard(el) {
                el.style.borderColor = '#ef4444';
                el.style.boxShadow   = '0 0 0 2px rgba(239,68,68,0.2)';
                setTimeout(() => {
                    el.style.borderColor = '';
                    el.style.boxShadow   = '';
                }, 3000);
            }

            // Intercept semua event submit / Livewire authenticate
            // Filament v3 gunakan wire:submit, kita intercept di form element
            const form = document.getElementById('loginForm');
            if (form) {
                // Intercept native submit (fallback)
                form.addEventListener('submit', function (e) {
                    if (!privacyCb.checked || !termsCb.checked) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        handleValidationFail();
                        return false;
                    }
                }, true);
            }

            // Intercept tombol submit (click level) — lebih andal untuk Livewire
            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.fi-form-actions .fi-btn, button[type="submit"]');
                if (!btn) return;
                if (!privacyCb.checked || !termsCb.checked) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    handleValidationFail();
                }
            }, true);

            function handleValidationFail() {
                if (!privacyCb.checked) flashCard(cardP);
                if (!termsCb.checked)   flashCard(cardT);
                showToast('Harap centang Kebijakan Privasi dan Syarat & Ketentuan terlebih dahulu');

                // Scroll kolom kiri ke atas supaya card terlihat
                const leftCol = document.querySelector('.myspp-scrollbar');
                if (leftCol) leftCol.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    </script>

</x-filament-panels::page.simple>