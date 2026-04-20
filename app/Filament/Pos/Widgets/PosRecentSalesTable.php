<?php

namespace App\Filament\Pos\Widgets;

use App\Models\PosSale;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PosRecentSalesTable extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'My Recent Sales';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PosSale::query()
                    ->where('user_id', auth()->id())
                    ->with('items.product')
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y H:i')->label('Date'),
                Tables\Columns\TextColumn::make('items_count')->counts('items')->label('Items'),
                Tables\Columns\TextColumn::make('total')->money('MWK'),
                Tables\Columns\TextColumn::make('payment_method')->badge(),
            ])
            ->defaultPaginationPageOption(10);
    }
}
