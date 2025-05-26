<?php

namespace App\Filament\Resources\SoilProfileResource\Pages;

use App\Filament\Resources\SoilProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSoilProfiles extends ListRecords
{
    protected static string $resource = SoilProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
