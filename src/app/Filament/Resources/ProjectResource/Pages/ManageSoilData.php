<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Filament\Forms;
use Filament\Tables;
use Filament\Actions;
use Filament\Forms\Form;
use App\Models\SoilLayer;
use Filament\Tables\Table;
use App\Models\SoilProfile;
use App\Models\SoilLayerType;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ManageRelatedRecords;
use App\Filament\Resources\ProjectResource;

class ManageSoilData extends ManageRelatedRecords
{
    protected static string $resource = ProjectResource::class;
    protected static string $relationship = 'soilProfile';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Soil Data';

        public function getBreadcrumbs(): array
    {
        $breadcrumbs = parent::getBreadcrumbs();

        $project = $this->getOwnerRecord();

        $projectId = $project->getKey();

        $newBreadcrumbs = array_slice( $breadcrumbs, 0, 1 ) + [ 0 => "Project {$projectId}" ] + $breadcrumbs;

        return $newBreadcrumbs;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Soil Profile')
                    ->schema([
                        Forms\Components\Select::make('project_id')
                            ->relationship('project', 'project_name')
                            ->disabled()
                            ->required()
                            ->default($this->getOwnerRecord()->project_id),
                        Forms\Components\TextInput::make('maximum_depth')
                            ->numeric()
                            ->required()
                            ->suffix('m'),
                        Forms\Components\TextInput::make('water_table_depth')
                            ->numeric()
                            ->suffix('m'),
                        Forms\Components\Select::make('soil_type')
                            ->options([
                                'cohesive' => 'Cohesive',
                                'non-cohesive' => 'NonCohesive',
                            ])
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('project_id')
            ->striped()
            ->columns([
                TextColumn::make('maximum_depth')
                    ->label('Max Depth')
                    ->suffix(' m'),
                TextColumn::make('water_table_depth')
                    ->label('Water Table')
                    ->suffix(' m'),
                TextColumn::make('soil_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cohesive' => 'success',
                        'non-cohesive' => 'warning',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('soil_type')
                    ->options([
                        'cohesive' => 'Cohesive',
                        'non-cohesive' => 'NonCohesive',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->hidden(fn (): bool => $this->getOwnerRecord()->soilProfile()->exists()),
            ]);
    }

    protected function getFooterWidgets(): array
    {
        $soilProfile = $this->getOwnerRecord()->soilProfile;

        if (!$soilProfile) {
            return [];
        }

        return [
            ProjectResource\Widgets\SoilLayersTable::make([
                'soilProfileId' => $soilProfile->getKey(),
            ]),
        ];
    }
}
