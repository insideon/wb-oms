<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = '주문';

    protected static ?string $modelLabel = '주문';

    protected static ?string $pluralModelLabel = '주문';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('주문 정보')
                    ->schema([
                        Forms\Components\TextInput::make('wb_order_id')
                            ->label('와일드베리스 주문 ID')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('status')
                            ->label('주문 상태')
                            ->options([
                                'pending' => '대기중',
                                'translated' => '번역완료',
                                'wms_sent' => 'WMS 전송완료',
                                'shipped' => '배송중',
                                'completed' => '완료',
                                'cancelled' => '취소',
                            ])
                            ->required()
                            ->default('pending'),
                        Forms\Components\DateTimePicker::make('ordered_at')
                            ->label('주문 일시'),
                    ])->columns(3),

                Forms\Components\Section::make('고객 정보')
                    ->schema([
                        Forms\Components\TextInput::make('customer_name')
                            ->label('고객명 (러시아어)')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('customer_name_translated')
                            ->label('고객명 (번역)')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('customer_phone')
                            ->label('전화번호')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('customer_address')
                            ->label('배송 주소 (러시아어)')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('customer_address_translated')
                            ->label('배송 주소 (번역)')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(3),

                Forms\Components\Section::make('결제 정보')
                    ->schema([
                        Forms\Components\TextInput::make('total_amount')
                            ->label('주문 금액')
                            ->required()
                            ->numeric()
                            ->prefix('₽'),
                        Forms\Components\TextInput::make('currency')
                            ->label('통화')
                            ->required()
                            ->maxLength(3)
                            ->default('RUB'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('wb_order_id')
                    ->label('주문번호')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('customer_name_translated')
                    ->label('고객명')
                    ->searchable()
                    ->description(fn (Order $record): string => $record->customer_name),
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
                Tables\Columns\TextColumn::make('created_at')
                    ->label('생성일시')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('상태')
                    ->options([
                        'pending' => '대기중',
                        'translated' => '번역완료',
                        'wms_sent' => 'WMS 전송완료',
                        'shipped' => '배송중',
                        'completed' => '완료',
                        'cancelled' => '취소',
                    ]),
                Tables\Filters\Filter::make('ordered_at')
                    ->form([
                        Forms\Components\DatePicker::make('ordered_from')
                            ->label('주문일 (시작)'),
                        Forms\Components\DatePicker::make('ordered_until')
                            ->label('주문일 (종료)'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['ordered_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('ordered_at', '>=', $date),
                            )
                            ->when(
                                $data['ordered_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('ordered_at', '<=', $date),
                            );
                    }),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('ordered_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
