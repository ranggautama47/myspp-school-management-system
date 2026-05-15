{{--
resources/views/filament/topbar-profile.blade.php

Inject nama + role di topbar, di sebelah KIRI avatar Filament default.
Hook: GLOBAL_SEARCH_AFTER — render tepat setelah search, sebelum user menu.
Hasil tampilan topbar kanan:
[Search] [nama user] [Super Administrator] [avatar] ▼
--}}

@auth
    @php
        $user = auth()->user();
        $role = $user->getRoleNames()->first() ?? 'user';
        $roleLabel = match ($role) {
            'admin' => 'Super Administrator',
            'student' => 'Student',
            default => ucfirst($role),
        };
    @endphp

    {{-- Nama + role — muncul di KIRI avatar, dengan contrast dinamis --}}
    <div class="fi-user-menu-text flex flex-col items-end justify-center leading-[1.3] me-2">
        <span class="text-sm font-semibold text-gray-900 dark:text-white whitespace-nowrap">
            {{ $user->name }}
        </span>
        <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
            {{ $roleLabel }}
        </span>
    </div>
@endauth