<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ApiLogResource;
use App\Models\ApiLog;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentApiErrors extends BaseWidget
{
    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('최근 API 에러')
            ->query(
                ApiLog::query()
                    ->whereIn('status', ['failed', 'error'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('service')
                    ->label('서비스')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'wildberries' => 'primary',
                        'wms' => 'success',
                        'translation' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'wildberries' => 'Wildberries',
                        'wms' => 'WMS',
                        'translation' => 'Translation',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('method')
                    ->label('메서드')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'GET' => 'info',
                        'POST' => 'success',
                        'PUT' => 'warning',
                        'DELETE' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('endpoint')
                    ->label('엔드포인트')
                    ->limit(30),
                Tables\Columns\TextColumn::make('status_code')
                    ->label('상태 코드')
                    ->badge()
                    ->color('danger'),
                Tables\Columns\TextColumn::make('error_message')
                    ->label('에러 메시지')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->error_message),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('발생일시')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->since(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('상세보기')
                    ->url(fn (ApiLog $record): string => ApiLogResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
            ])
            ->emptyStateHeading('API 에러 없음')
            ->emptyStateDescription('모든 API 호출이 정상적으로 처리되고 있습니다.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
