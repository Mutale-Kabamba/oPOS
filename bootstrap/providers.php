<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\AccountingPanelProvider;
use App\Providers\Filament\PosPanelProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    AccountingPanelProvider::class,
    PosPanelProvider::class,
];
