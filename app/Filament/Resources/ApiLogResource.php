<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiLogResource\Pages;
use App\Models\ApiLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ApiLogResource extends Resource
{
    protected static ?string $model = ApiLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'API 로그';

    protected static ?string $modelLabel = 'API 로그';

    protected static ?string $pluralModelLabel = 'API 로그';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('요청 정보')
                    ->schema([
                        Forms\Components\Select::make('service')
                            ->label('서비스')
                            ->options([
                                'wildberries' => 'Wildberries',
                                'wms' => 'WMS',
                                'translation' => 'Translation',
                            ])
                            ->required(),
                        Forms\Components\Select::make('method')
                            ->label('메서드')
                            ->options([
                                'GET' => 'GET',
                                'POST' => 'POST',
                                'PUT' => 'PUT',
                                'DELETE' => 'DELETE',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('endpoint')
                            ->label('엔드포인트')
                            ->required()
                            ->maxLength(255),
                    ])->columns(3),
                Forms\Components\Section::make('요청/응답 데이터')
                    ->schema([
                        Forms\Components\Textarea::make('request_data')
                            ->label('요청 데이터')
                            ->rows(5)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('response_data')
                            ->label('응답 데이터')
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('결과')
                    ->schema([
                        Forms\Components\TextInput::make('status_code')
                            ->label('상태 코드')
                            ->numeric(),
                        Forms\Components\Select::make('status')
                            ->label('상태')
                            ->options([
                                'success' => '성공',
                                'failed' => '실패',
                                'error' => '에러',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('duration_ms')
                            ->label('응답 시간 (ms)')
                            ->numeric()
                            ->suffix('ms'),
                        Forms\Components\Textarea::make('error_message')
                            ->label('에러 메시지')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('service')
                    ->label('서비스')
                    ->colors([
                        'primary' => 'wildberries',
                        'success' => 'wms',
                        'warning' => 'translation',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'wildberries' => 'Wildberries',
                        'wms' => 'WMS',
                        'translation' => 'Translation',
                        default => $state,
                    }),
                Tables\Columns\BadgeColumn::make('method')
                    ->label('메서드')
                    ->colors([
                        'info' => 'GET',
                        'success' => 'POST',
                        'warning' => 'PUT',
                        'danger' => 'DELETE',
                    ]),
                Tables\Columns\TextColumn::make('endpoint')
                    ->label('엔드포인트')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('status_code')
                    ->label('상태 코드')
                    ->badge()
                    ->color(fn ($state) => $state >= 200 && $state < 300 ? 'success' : 'danger')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('상태')
                    ->colors([
                        'success' => 'success',
                        'danger' => 'failed',
                        'warning' => 'error',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'success' => '성공',
                        'failed' => '실패',
                        'error' => '에러',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('duration_ms')
                    ->label('응답시간')
                    ->numeric()
                    ->sortable()
                    ->suffix(' ms')
                    ->color(fn ($state) => $state > 2000 ? 'danger' : ($state > 1000 ? 'warning' : 'success')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('기록일시')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('service')
                    ->label('서비스')
                    ->options([
                        'wildberries' => 'Wildberries',
                        'wms' => 'WMS',
                        'translation' => 'Translation',
                    ]),
                Tables\Filters\SelectFilter::make('method')
                    ->label('메서드')
                    ->options([
                        'GET' => 'GET',
                        'POST' => 'POST',
                        'PUT' => 'PUT',
                        'DELETE' => 'DELETE',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('상태')
                    ->options([
                        'success' => '성공',
                        'failed' => '실패',
                        'error' => '에러',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListApiLogs::route('/'),
            'create' => Pages\CreateApiLog::route('/create'),
            'edit' => Pages\EditApiLog::route('/{record}/edit'),
        ];
    }
}
