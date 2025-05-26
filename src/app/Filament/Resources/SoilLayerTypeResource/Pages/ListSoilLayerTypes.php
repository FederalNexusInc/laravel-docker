<?php

namespace App\Filament\Resources\SoilLayerTypeResource\Pages;

use App\Filament\Resources\SoilLayerTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSoilLayerTypes extends ListRecords
{
    protected static string $resource = SoilLayerTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
