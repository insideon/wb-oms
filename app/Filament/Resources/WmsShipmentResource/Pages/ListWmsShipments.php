<?php

namespace App\Filament\Resources\WmsShipmentResource\Pages;

use App\Filament\Resources\WmsShipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWmsShipments extends ListRecords
{
    protected static string $resource = WmsShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
