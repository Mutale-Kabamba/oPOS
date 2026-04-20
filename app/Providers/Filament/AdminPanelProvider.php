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

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('oPOS | By Ori')
            ->colors([
                'primary' => Color::hex('#0B4D2C'),
                'success' => Color::hex('#32CD32'),
                'warning' => Color::hex('#FF6B35'),
            ])
            ->navigationItems([
                NavigationItem::make('Inventory')
                    ->url(fn () => route('admin.pos-products.index'))
                    ->icon('heroicon-o-cube')
                    ->sort(1),
                NavigationItem::make('Suppliers')
                    ->url(fn () => route('admin.suppliers.index'))
                    ->icon('heroicon-o-truck')
                    ->sort(2),
                NavigationItem::make('Sales Report')
                    ->url(fn () => route('reports.sales'))
                    ->icon('heroicon-o-chart-bar')
                    ->sort(3),
                NavigationItem::make('Petty Cash')
                    ->url(fn () => route('admin.petty-cash.index'))
                    ->icon('heroicon-o-banknotes')
                    ->sort(4),
                NavigationItem::make('Settings')
                    ->url(fn () => route('admin.settings'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->sort(5),
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Back to App')
                    ->url('/')
                    ->icon('heroicon-o-arrow-left'),
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
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
