<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WmsShipmentResource\Pages;
use App\Models\WmsShipment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WmsShipmentResource extends Resource
{
    protected static ?string $model = WmsShipment::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = '배송';

    protected static ?string $modelLabel = '배송';

    protected static ?string $pluralModelLabel = '배송';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('주문 정보')
                    ->schema([
                        Forms\Components\Select::make('order_id')
                            ->label('주문')
                            ->relationship('order', 'wb_order_id')
                            ->required()
                            ->searchable(),
                    ]),
                Forms\Components\Section::make('배송 정보')
                    ->schema([
                        Forms\Components\TextInput::make('wms_shipment_id')
                            ->label('WMS 배송 ID')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('tracking_number')
                            ->label('송장 번호')
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->label('배송 상태')
                            ->options([
                                'pending' => '대기중',
                                'processing' => '처리중',
                                'shipped' => '배송중',
                                'delivered' => '배송완료',
                                'failed' => '실패',
                            ])
                            ->required()
                            ->default('pending'),
                        Forms\Components\TextInput::make('carrier')
                            ->label('배송업체')
                            ->maxLength(255),
                    ])->columns(2),
                Forms\Components\Section::make('일정')
                    ->schema([
                        Forms\Components\DateTimePicker::make('requested_at')
                            ->label('요청 일시'),
                        Forms\Components\DateTimePicker::make('shipped_at')
                            ->label('발송 일시'),
                        Forms\Components\DateTimePicker::make('delivered_at')
                            ->label('배송 완료 일시'),
                    ])->columns(3),
                Forms\Components\Section::make('비고')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('메모')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.wb_order_id')
                    ->label('주문번호')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tracking_number')
                    ->label('송장번호')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('배송 상태')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'processing' => 'info',
                        'shipped' => 'warning',
                        'delivered' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => '대기중',
                        'processing' => '처리중',
                        'shipped' => '배송중',
                        'delivered' => '배송완료',
                        'failed' => '실패',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('carrier')
                    ->label('배송업체')
                    ->searchable(),
                Tables\Columns\TextColumn::make('requested_at')
                    ->label('요청일시')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('shipped_at')
                    ->label('발송일시')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('delivered_at')
                    ->label('배송완료일시')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('생성일시')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('배송 상태')
                    ->options([
                        'pending' => '대기중',
                        'processing' => '처리중',
                        'shipped' => '배송중',
                        'delivered' => '배송완료',
                        'failed' => '실패',
                    ]),
                Tables\Filters\SelectFilter::make('carrier')
                    ->label('배송업체'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('requested_at', 'desc');
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
            'index' => Pages\ListWmsShipments::route('/'),
            'create' => Pages\CreateWmsShipment::route('/create'),
            'edit' => Pages\EditWmsShipment::route('/{record}/edit'),
        ];
    }
}
