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
            ->renderHook(
                \Filament\View\PanelsRenderHook::TOPBAR_START,
                fn(): string => '
                    <div id="fi-search-left-placeholder" style="display: flex; align-items: center; flex: 1 1 auto; max-width: 300px; margin-right: 1rem; order: -1;"></div>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            setTimeout(function() {
                                const search = document.querySelector(".fi-global-search-field-wrapper");
                                const placeholder = document.getElementById("fi-search-left-placeholder");
                                if (search && placeholder) {
                                    placeholder.appendChild(search);
                                    search.style.display = "flex";
                                }
                            }, 150);
                        });
                    </script>
                ',
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::GLOBAL_SEARCH_AFTER,
                fn(): \Illuminate\Contracts\View\View => view('filament.topbar-profile'),
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
            ->authMiddleware([Authenticate::class, \App\Http\Middleware\AdminPanelAccess::class]);
    }
}
