<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\ProjectSpecialistResource;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getRedirectUrl(): string
    {
        return ProjectResource::getUrl('edit', ['record' => $this->record->project_id]);
    }

        protected function handleRecordCreation(array $data): Model
    {
        $project = static::getModel()::create($data);
        session(['last_created_project_id' => $project->id]);
        return $project;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }
    
    protected function getSavedNotificationTitle(): ?string
{
    return 'Project created';
}

}
