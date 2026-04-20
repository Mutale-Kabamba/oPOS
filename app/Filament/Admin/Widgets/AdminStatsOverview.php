<?php

namespace App\Filament\Admin\Widgets;

use App\Models\PosProduct;
use App\Models\PosSale;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalProducts = PosProduct::where('is_active', true)->count();
        $lowStockCount = PosProduct::where('is_active', true)->where('stock', '<=', 5)->count();
        $todaySales = PosSale::whereDate('created_at', today())->count();
        $todayRevenue = PosSale::whereDate('created_at', today())->sum('total');
        $monthSales = PosSale::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $monthRevenue = PosSale::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total');

        return [
            Stat::make('Active Products', $totalProducts)
                ->icon('heroicon-o-cube'),
            Stat::make('Low Stock Items', $lowStockCount)
                ->color($lowStockCount > 0 ? 'danger' : 'success')
                ->icon('heroicon-o-exclamation-triangle'),
            Stat::make("Today's Sales", $todaySales)
                ->description('K ' . number_format($todayRevenue, 2) . ' revenue')
                ->icon('heroicon-o-shopping-cart'),
            Stat::make('Monthly Sales', $monthSales)
                ->description('K ' . number_format($monthRevenue, 2) . ' revenue')
                ->icon('heroicon-o-calendar'),
        ];
    }
}
