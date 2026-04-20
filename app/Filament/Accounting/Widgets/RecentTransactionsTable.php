<?php

namespace App\Filament\Accounting\Widgets;

use App\Models\Transaction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentTransactionsTable extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Recent Transactions';

    public function table(Table $table): Table
    {
        $userId = auth()->id();

        return $table
            ->query(
                Transaction::query()
                    ->with('account')
                    ->withSum('paymentTransactions as paid_amount', 'amount')
                    ->leftJoin('transactions as parent_transactions', 'parent_transactions.id', '=', 'transactions.parent_transaction_id')
                    ->whereRaw('COALESCE(parent_transactions.user_id, transactions.user_id) = ?', [$userId])
                    ->select('transactions.*')
                    ->latest('transactions.date')
                    ->latest('transactions.id')
            )
            ->columns([
                Tables\Columns\TextColumn::make('date')->date('d M Y'),
                Tables\Columns\TextColumn::make('description')->limit(50)->searchable(),
                Tables\Columns\TextColumn::make('account.name')->label('Account'),
                Tables\Columns\TextColumn::make('amount')->money('MWK'),
                Tables\Columns\TextColumn::make('type')->badge(),
            ])
            ->defaultPaginationPageOption(25);
    }
}
