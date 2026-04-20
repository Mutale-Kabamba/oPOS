<?php

namespace App\Filament\Accounting\Widgets;

use App\Services\LedgerSummaryService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AccountingStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $service = app(LedgerSummaryService::class);
        $from = now()->startOfMonth()->toDateString();
        $to = now()->endOfMonth()->toDateString();
        $userId = auth()->id();

        $totals = $service->dashboardTotals($from, $to, $userId);

        return [
            Stat::make('Monthly Income', 'K ' . number_format($totals['income'], 2))
                ->icon('heroicon-o-arrow-trending-up')
                ->color('success'),
            Stat::make('Direct Costs (COGS)', 'K ' . number_format($totals['directCosts'], 2))
                ->icon('heroicon-o-arrow-trending-down')
                ->color('danger'),
            Stat::make('Operating Profit', 'K ' . number_format($totals['operatingProfit'], 2))
                ->icon('heroicon-o-banknotes')
                ->color($totals['operatingProfit'] >= 0 ? 'success' : 'danger'),
        ];
    }
}
