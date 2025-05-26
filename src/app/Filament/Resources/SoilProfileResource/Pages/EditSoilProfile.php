<?php

namespace App\Filament\Resources\SoilProfileResource\Pages;

use App\Filament\Resources\SoilProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSoilProfile extends EditRecord
{
    protected static string $resource = SoilProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
