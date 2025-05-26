<?php

namespace App\Filament\Resources\AnchorResource\Pages;

use App\Filament\Resources\AnchorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAnchor extends EditRecord
{
    protected static string $resource = AnchorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
