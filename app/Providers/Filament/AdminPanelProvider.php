<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\Dashboard;
use App\Filament\Admin\Widgets\StatsOverviewWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
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

            /*----------------------------------------------------------------
            | Branding
            ----------------------------------------------------------------*/
            ->brandName('ArtCard')
            ->brandLogo(asset('images/logo.png'))
            ->favicon(asset('images/favicon.ico'))
            ->colors([
                'primary' => Color::Amber,
                'gray' => Color::Slate,
                'info' => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
                'danger' => Color::Rose,
            ])
            ->font('Inter')

            /*----------------------------------------------------------------
            | Auth
            ----------------------------------------------------------------*/
            ->login()
            ->profile()

            ->discoverResources(
                in: app_path('Filament/Admin/Resources'),
                for: 'App\\Filament\\Admin\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Admin/Pages'),
                for: 'App\\Filament\\Admin\\Pages'
            )
            ->discoverWidgets(
                in: app_path('Filament/Admin/Widgets'),
                for: 'App\\Filament\\Admin\\Widgets'
            )

            /*----------------------------------------------------------------
            | Dashboard
            ----------------------------------------------------------------*/
            ->pages([Dashboard::class])
            ->widgets([
                StatsOverviewWidget::class,
                // RevenueChartWidget::class,
                Widgets\AccountWidget::class,
            ])

            /*----------------------------------------------------------------
            | Navigation Groups
            ----------------------------------------------------------------*/
            ->navigationGroups([
                NavigationGroup::make('Sales')
                    ->icon('heroicon-o-shopping-bag'),

                NavigationGroup::make('Catalog')
                    ->icon('heroicon-o-photo'),
            ])

            /*----------------------------------------------------------------
            | Global Search
            ----------------------------------------------------------------*/
            ->globalSearch()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])

            /*----------------------------------------------------------------
            | Middleware
            ----------------------------------------------------------------*/
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
            ->authMiddleware([
                Authenticate::class,
            ])

            /*----------------------------------------------------------------
            | UX Polish
            ----------------------------------------------------------------*/
            ->sidebarCollapsibleOnDesktop()
            ->spa()
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s');
    }
}
