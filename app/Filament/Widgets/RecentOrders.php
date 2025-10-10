<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentOrders extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('최근 주문')
            ->query(
                Order::query()->latest('ordered_at')->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('wb_order_id')
                    ->label('주문번호')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_name_translated')
                    ->label('고객명')
                    ->searchable()
                    ->limit(20),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('상태')
                    ->colors([
                        'secondary' => 'pending',
                        'info' => 'translated',
                        'primary' => 'wms_sent',
                        'warning' => 'shipped',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => '대기중',
                        'translated' => '번역완료',
                        'wms_sent' => 'WMS 전송완료',
                        'shipped' => '배송중',
                        'completed' => '완료',
                        'cancelled' => '취소',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('주문 금액')
                    ->money('RUB')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ordered_at')
                    ->label('주문일시')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('상세보기')
                    ->url(fn (Order $record): string => OrderResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
            ]);
    }
}
