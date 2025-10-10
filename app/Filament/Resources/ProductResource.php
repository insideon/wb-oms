<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = '상품';

    protected static ?string $modelLabel = '상품';

    protected static ?string $pluralModelLabel = '상품';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('기본 정보')
                    ->schema([
                        Forms\Components\TextInput::make('wb_product_id')
                            ->label('와일드베리스 상품 ID')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('name')
                            ->label('상품명')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('category')
                            ->label('카테고리')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('barcode')
                            ->label('바코드')
                            ->maxLength(255),
                    ])->columns(2),
                Forms\Components\Section::make('상품 설명')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('설명')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('재고 및 가격')
                    ->schema([
                        Forms\Components\TextInput::make('stock_quantity')
                            ->label('재고 수량')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        Forms\Components\TextInput::make('price')
                            ->label('가격')
                            ->required()
                            ->numeric()
                            ->prefix('₽')
                            ->minValue(0),
                        Forms\Components\Toggle::make('is_active')
                            ->label('활성 상태')
                            ->required()
                            ->default(true),
                        Forms\Components\DateTimePicker::make('last_synced_at')
                            ->label('마지막 동기화'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('wb_product_id')
                    ->label('상품 ID')
                    ->searchable()
                    ->copyable(),
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
                    ->searchable()
                    ->badge(),
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('재고')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($state) => $state < 10 ? 'danger' : ($state < 50 ? 'warning' : 'success')),
                Tables\Columns\TextColumn::make('price')
                    ->label('가격')
                    ->money('RUB')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('활성')
                    ->boolean(),
                Tables\Columns\TextColumn::make('last_synced_at')
                    ->label('마지막 동기화')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('생성일시')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('수정일시')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('카테고리'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('활성 상태')
                    ->boolean()
                    ->trueLabel('활성')
                    ->falseLabel('비활성'),
                Tables\Filters\Filter::make('low_stock')
                    ->label('재고 부족')
                    ->query(fn ($query) => $query->where('stock_quantity', '<', 10)),
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
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
