<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AccountingPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('accounting')
            ->path('accounting')
            ->login()
            ->brandName('oPOS | By Ori')
            ->colors([
                'primary' => Color::hex('#0B4D2C'),
                'success' => Color::hex('#32CD32'),
                'warning' => Color::hex('#FF6B35'),
            ])
            ->navigationItems([
                NavigationItem::make('Ledger')
                    ->url(fn () => route('reports.transactions'))
                    ->icon('heroicon-o-book-open')
                    ->sort(1),
                NavigationItem::make('Reports')
                    ->url(fn () => route('reports.hub'))
                    ->icon('heroicon-o-document-chart-bar')
                    ->sort(2),
                NavigationItem::make('Record Entry')
                    ->url(fn () => route('accounting.transactions.create'))
                    ->icon('heroicon-o-plus-circle')
                    ->sort(3),
                NavigationItem::make('Settings')
                    ->url(fn () => route('accounting.settings'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->sort(4),
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Back to App')
                    ->url('/')
                    ->icon('heroicon-o-arrow-left'),
            ])
            ->discoverWidgets(in: app_path('Filament/Accounting/Widgets'), for: 'App\\Filament\\Accounting\\Widgets')
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
            ->authGuard('web');
    }
}
