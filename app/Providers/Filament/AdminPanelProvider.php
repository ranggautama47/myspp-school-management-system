<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\PaymentOverviewWidget;
use App\Filament\Widgets\PaymentTrendsWidget;
use App\Filament\Widgets\RecentTransactionsWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use Illuminate\Support\Facades\Auth;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()

            // =============================================
            // BRANDING
            // =============================================
            ->brandName('MySPP')
            ->brandLogo(fn() => view('filament.brand'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('images/android-chrome-512x512.png'))

            // =============================================
            // WARNA — Emerald primary sesuai blueprint
            // =============================================
            ->colors([
                'primary' => Color::Emerald,
                'gray' => Color::Slate,
                'info' => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
                'danger' => Color::Rose,
            ])

            // =============================================
            // DARK MODE — default dark, toggle tetap ada
            // =============================================
            ->darkMode(true)

            // =============================================
            // TOPBAR — search, notif, profile
            // =============================================
            ->globalSearch(true)
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])

            // =============================================
            // USER MENU — tampilkan nama + role
            // Filament v3 menggunakan getUserMenuItems untuk
            // customize menu, tapi untuk nama+role di topbar
            // kita inject via Blade view custom
            // =============================================
            ->userMenuItems([
                \Filament\Navigation\MenuItem::make()
                    ->label(fn() => Auth::user()?->name ?? 'Profile')
                    ->icon('heroicon-o-user-circle')
                    ->url(fn() => '#'),
            ])

            // =============================================
            // RENDER HOOK — inject role subtitle di user menu
            // Ini yang membuat "Super Administrator" muncul
            // di bawah nama user di topbar
            // =============================================
            ->renderHook(
                \Filament\View\PanelsRenderHook::USER_MENU_BEFORE,
                fn(): string => $this->getUserMenuTopbarHtml(),
            )


            // =============================================
            // MAX WIDTH
            // =============================================
            ->maxContentWidth(MaxWidth::Full)

            // =============================================
            // CUSTOM THEME CSS
            // =============================================
            ->viteTheme('resources/css/filament/admin/theme.css')

            // =============================================
            // NAVIGATION GROUPS — tanpa icon (items sudah punya icon)
            // Filament rule: group OR items can have icons, not both
            // =============================================
            ->navigationGroups([
                NavigationGroup::make('Academic')
                    ->collapsed(false),

                NavigationGroup::make('Finance')
                    ->collapsed(false),

                NavigationGroup::make('System')
                    ->collapsed(false),
            ])

            // =============================================
            // DISCOVERY
            // =============================================
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')

            ->pages([Dashboard::class])

            ->widgets([
                StatsOverviewWidget::class,
                PaymentTrendsWidget::class,
                PaymentOverviewWidget::class,
                RecentTransactionsWidget::class,
            ])

            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([Authenticate::class]);
    }
    /**
     * Inject HTML nama + role di atas user menu dropdown
     * Tampil: nama user + role (Super Administrator / Student)
     */
    private function getUserMenuTopbarHtml(): string
    {
        $user = Auth::user();
        if (!$user)
            return '';

        $role = $user->getRoleNames()->first() ?? 'user';

        $roleLabel = match ($role) {
            'admin' => 'Super Administrator',
            'student' => 'Student',
            default => ucfirst($role),
        };

        // Avatar — foto atau inisial
        $avatarUrl = $user->image
            ? \Illuminate\Support\Facades\Storage::url($user->image)
            : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=10B981&color=fff&size=64';

        return "
        <div style='
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-bottom: 1px solid rgba(51,65,85,0.5);
            margin-bottom: 4px;
        '>
            <img src='{$avatarUrl}'
                 style='width:36px;height:36px;border-radius:50%;object-fit:cover;flex-shrink:0;'
                 alt='{$user->name}' />
            <div style='min-width:0;'>
                <div style='font-size:0.85rem;font-weight:600;color:#F1F5F9;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;'>
                    {$user->name}
                </div>
                <div style='font-size:0.72rem;color:#64748B;margin-top:1px;'>
                    {$roleLabel}
                </div>
            </div>
        </div>
        ";
    }
}
