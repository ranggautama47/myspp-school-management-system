{{--
Brand logo MySPP untuk Filament sidebar
- Icon: graduation cap hijau emerald
- "My" warna hijau emerald
- "SPP" warna putih (dark) / hitam (light)
- Subtitle: "School Management System" warna abu
--}}
<div style="display: flex; align-items: center; gap: 10px; padding: 2px 0;">
    {{-- Menggunakan file gambar PNG kamu --}}
    <img src="{{ asset('images/favicon.png') }}" alt="Logo" style="height: 40px; width: auto; border-radius: 12px;">

    {{-- Teks brand --}}
    <div style="line-height: 1.2;">
        <div
            style="font-size: 1.1rem; font-weight: 700; letter-spacing: -0.02em; display: flex; align-items: baseline; gap: 1px;">
            <span style="color: #10B981;">My</span><span class="dark:text-slate-100 text-slate-800"
                style="font-weight: 700;">SPP</span>
        </div>
        <div class="dark:text-slate-500 text-slate-400"
            style="font-size: 0.65rem; font-weight: 400; letter-spacing: 0.02em; white-space: nowrap;">
            School Management System
        </div>
    </div>

</div>