<?php

namespace App\Filament\Resources\SoilLayerResource\Pages;

use App\Filament\Resources\SoilLayerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSoilLayer extends EditRecord
{
    protected static string $resource = SoilLayerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
