<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getBreadcrumbs(): array
    {
        $breadcrumbs = parent::getBreadcrumbs();
        $projectName = $this->record->project_name;

        $newBreadcrumbs = array_slice( $breadcrumbs, 0, 1 ) + [ 0 => "{$projectName}" ] + $breadcrumbs;

        return $newBreadcrumbs;
    }
}
