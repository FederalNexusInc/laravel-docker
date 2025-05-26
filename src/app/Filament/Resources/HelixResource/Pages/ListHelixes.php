<?php

namespace App\Filament\Resources\HelixResource\Pages;

use App\Filament\Resources\HelixResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHelixes extends ListRecords
{
    protected static string $resource = HelixResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
