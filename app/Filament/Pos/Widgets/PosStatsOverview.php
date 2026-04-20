<?php

namespace App\Filament\Pos\Widgets;

use App\Models\PettyCashAllocation;
use App\Models\PettyCashExpense;
use App\Models\PosSale;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PosStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = auth()->user();
        $userId = $user->id;

        $todaySales = PosSale::where('user_id', $userId)->whereDate('created_at', today())->count();
        $todayRevenue = PosSale::where('user_id', $userId)->whereDate('created_at', today())->sum('total');
        $monthSales = PosSale::where('user_id', $userId)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $monthRevenue = PosSale::where('user_id', $userId)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total');

        $currentMonth = now()->format('Y-m');
        $pettyCashAllocated = PettyCashAllocation::where('user_id', $userId)->where('month', $currentMonth)->value('amount') ?? 0;
        $pettyCashSpent = PettyCashExpense::where('user_id', $userId)->whereRaw("strftime('%Y-%m', expense_date) = ?", [$currentMonth])->sum('amount');
        $pettyCashBalance = $pettyCashAllocated - $pettyCashSpent;

        return [
            Stat::make("Today's Sales", $todaySales)
                ->description('K ' . number_format($todayRevenue, 2) . ' revenue')
                ->icon('heroicon-o-shopping-cart'),
            Stat::make('Monthly Sales', $monthSales)
                ->description('K ' . number_format($monthRevenue, 2) . ' revenue')
                ->icon('heroicon-o-calendar'),
            Stat::make('Petty Cash', 'K ' . number_format($pettyCashBalance, 2))
                ->description('K ' . number_format($pettyCashAllocated, 2) . ' allocated / K ' . number_format($pettyCashSpent, 2) . ' spent')
                ->icon('heroicon-o-banknotes')
                ->color($pettyCashBalance > 0 ? 'success' : 'danger'),
        ];
    }
}
