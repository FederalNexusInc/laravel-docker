<?php

namespace App\Filament\Resources\HelixResource\Pages;

use App\Filament\Resources\HelixResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHelix extends EditRecord
{
    protected static string $resource = HelixResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
