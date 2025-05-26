<?php

namespace App\Filament\Resources\ProjectSpecialistResource\Pages;

use App\Filament\Resources\ProjectSpecialistResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProjectSpecialists extends ListRecords
{
    protected static string $resource = ProjectSpecialistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
