<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockProducts extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('재고 부족 상품 (10개 이하)')
            ->query(
                Product::query()
                    ->where('is_active', true)
                    ->where('stock_quantity', '<=', 10)
                    ->orderBy('stock_quantity', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('상품명')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('카테고리')
                    ->badge(),
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('재고')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($state) => $state === 0 ? 'danger' : ($state <= 5 ? 'warning' : 'secondary'))
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('price')
                    ->label('가격')
                    ->money('RUB'),
                Tables\Columns\TextColumn::make('last_synced_at')
                    ->label('마지막 동기화')
                    ->dateTime('Y-m-d H:i')
                    ->since(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('상세보기')
                    ->url(fn (Product $record): string => ProductResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
            ])
            ->emptyStateHeading('재고 부족 상품 없음')
            ->emptyStateDescription('모든 상품의 재고가 충분합니다.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
