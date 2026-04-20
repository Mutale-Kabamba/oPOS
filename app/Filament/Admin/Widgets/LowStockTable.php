<?php

namespace App\Filament\Admin\Widgets;

use App\Models\PosProduct;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockTable extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Low Stock Alert';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PosProduct::query()
                    ->where('is_active', true)
                    ->where('stock', '<=', 5)
                    ->orderBy('stock')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('sku')->label('SKU'),
                Tables\Columns\TextColumn::make('stock')
                    ->badge()
                    ->color(fn (int $state) => $state <= 0 ? 'danger' : 'warning'),
                Tables\Columns\TextColumn::make('price')->money('MWK')->label('Price'),
            ])
            ->paginated(false);
    }
}
