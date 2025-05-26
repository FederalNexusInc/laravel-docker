<?php

namespace App\Filament\Resources\SoilLayerTypeResource\Pages;

use App\Filament\Resources\SoilLayerTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSoilLayerType extends EditRecord
{
    protected static string $resource = SoilLayerTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
