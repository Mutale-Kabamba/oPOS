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

class PosPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('pos')
            ->path('pos')
            ->login()
            ->brandName('oPOS | By Ori')
            ->colors([
                'primary' => Color::hex('#0B4D2C'),
                'success' => Color::hex('#32CD32'),
                'warning' => Color::hex('#FF6B35'),
            ])
            ->pages([
                \App\Filament\Pos\Pages\PosDashboard::class,
            ])
            ->navigationItems([
                NavigationItem::make('My Sales')
                    ->url(fn () => route('pos.sales.index'))
                    ->icon('heroicon-o-clipboard-document-list')
                    ->sort(2),
                NavigationItem::make('Petty Cash')
                    ->url(fn () => route('pos.petty-cash.index'))
                    ->icon('heroicon-o-banknotes')
                    ->sort(3),
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Back to App')
                    ->url('/')
                    ->icon('heroicon-o-arrow-left'),
            ])
            ->discoverWidgets(in: app_path('Filament/Pos/Widgets'), for: 'App\\Filament\\Pos\\Widgets')
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
