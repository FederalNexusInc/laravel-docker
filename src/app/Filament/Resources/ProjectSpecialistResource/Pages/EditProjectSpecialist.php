<?php

namespace App\Filament\Resources\ProjectSpecialistResource\Pages;

use App\Filament\Resources\ProjectSpecialistResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProjectSpecialist extends EditRecord
{
    protected static string $resource = ProjectSpecialistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
