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
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProjectResource;
use Filament\Resources\Pages\ManageRelatedRecords;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageSoilLayer extends ManageRelatedRecords
{
    protected static string $resource = ProjectResource::class;
    protected static string $relationship = 'soilLayers';
    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    public static function getNavigationLabel(): string
    {
        return 'Soil Layers';
    }

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
        $soilProfile = SoilProfile::where('project_id', $this->getOwnerRecord()->project_id)->first();

        return $form
            ->schema([
                Forms\Components\Hidden::make('soil_profile_id')
                    ->default($soilProfile->soil_profile_id)
                    ->required(),
                Forms\Components\TextInput::make('start_depth')
                    ->numeric()
                    ->required()
                    ->columnSpan(4),
                Forms\Components\TextInput::make('blow_count')
                    ->numeric()
                    ->columnSpan(4)
                    ->label('N')
                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Blow Count'),
                Forms\Components\Select::make('soil_layer_type_id')
                    ->label('Type')
                    ->options(SoilLayerType::all()->pluck('name', 'soil_layer_type_id'))
                    ->searchable()
                    ->required()
                    ->columnSpan(4),
                Forms\Components\TextInput::make('cohesion')
                    ->numeric()
                    ->columnSpan(3)
                    ->label('C')
                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Cohesion'),
                Forms\Components\TextInput::make('coefficient_of_adhesion')
                    ->numeric()
                    ->columnSpan(3)
                    ->label('α')
                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Coefficient Of Adhesion'),
                Forms\Components\TextInput::make('angle_of_internal_friction')
                    ->numeric()
                    ->columnSpan(3)
                    ->label('Ø')
                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Angle Of Internal Friction'),
                Forms\Components\TextInput::make('coefficient_of_external_friction')
                    ->numeric()
                    ->columnSpan(3)
                    ->label('Β')
                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Coefficient Of External Friction'),
                Forms\Components\TextInput::make('moist_unit_weight')
                    ->numeric()
                    ->columnSpan(3)
                    ->label('γm')
                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Moist Unit Weight'),
                Forms\Components\TextInput::make('saturated_unit_weight')
                    ->numeric()
                    ->columnSpan(3)
                    ->label('γsat')
                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Saturated Unit Weight'),
                Forms\Components\TextInput::make('nc')
                    ->numeric()
                    ->columnSpan(3),
                Forms\Components\TextInput::make('nq')
                    ->numeric()
                    ->columnSpan(3),
            ])
            ->columns(12);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('soil_profile_id')
            ->striped()
            ->columns([
                // Default Visible Columns
                Tables\Columns\TextColumn::make('start_depth')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('soilLayerType.name')
                    ->label('Type')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('blow_count')
                    ->sortable()
                    ->toggleable()
                    ->label('N'),
                Tables\Columns\TextColumn::make('cohesion')
                    ->sortable()
                    ->toggleable()
                    ->label('c'),
                Tables\Columns\TextColumn::make('coefficient_of_adhesion')
                    ->label('Coeff. Adhesion')
                    ->sortable()
                    ->toggleable()
                    ->label('α'),
                Tables\Columns\TextColumn::make('angle_of_internal_friction')
                    ->label('Int. Friction Angle')
                    ->sortable()
                    ->toggleable()
                    ->label('Ø'),
                Tables\Columns\TextColumn::make('coefficient_of_external_friction')
                    ->label('Ext. Friction Coeff.')
                    ->sortable()
                    ->toggleable()
                    ->label('Β'),
                Tables\Columns\TextColumn::make('moist_unit_weight')
                    ->label('Moist Unit Weight')
                    ->sortable()
                    ->toggleable()
                    ->label('γm'),
                Tables\Columns\TextColumn::make('saturated_unit_weight')
                    ->label('Sat. Unit Weight')
                    ->sortable()
                    ->toggleable()
                    ->label('γsat'),
                Tables\Columns\TextColumn::make('nc')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('nq')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('m-d-Y H:i A')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('m-d-Y H:i A')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('soil_layer_type_id')
                    ->label('Soil Layer Type')
                    ->relationship('soilLayerType', 'name')
                    ->searchable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $soilProfile = SoilProfile::where('project_id', $this->getOwnerRecord()->project_id)->first();
                        $data['soil_profile_id'] = $soilProfile->soil_profile_id;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getTableQuery(): Builder
    {
        $soilProfile = SoilProfile::where('project_id', $this->getOwnerRecord()->project_id)->first();
        return SoilLayer::query()->where('soil_profile_id', $soilProfile->soil_profile_id);
    }
}
