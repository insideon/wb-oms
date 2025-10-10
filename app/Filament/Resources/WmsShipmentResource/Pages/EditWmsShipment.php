<?php

namespace App\Filament\Resources\WmsShipmentResource\Pages;

use App\Filament\Resources\WmsShipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWmsShipment extends EditRecord
{
    protected static string $resource = WmsShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
