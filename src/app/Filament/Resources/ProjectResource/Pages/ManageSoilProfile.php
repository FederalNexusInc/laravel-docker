<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageSoilProfile extends ManageRelatedRecords
{
    protected static string $resource = ProjectResource::class;

    protected static string $relationship = 'soilProfile';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Soil Profile';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('project_id')
                    ->label('Project')
                    ->relationship('project', 'project_name')
                    ->disabled()
                    ->required()
                    ->default($this->getOwnerRecord()->project_id)
                    ->columnSpan(1),
                Forms\Components\TextInput::make('maximum_depth')
                    ->integer()
                    ->columnSpan(1)
                    ->required(),
                Forms\Components\TextInput::make('water_table_depth')
                    ->integer()
                    ->columnSpan(1),
                Forms\Components\Select::make('soil_type')
                    ->options([
                        'cohesive' => 'Cohesive',
                        'non-cohesive' => 'NonCohesive',
                    ])
                    ->required()
                    ->columnSpan(1),
            ])
            ->columns(4);;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('project_id')
            ->columns([
                Tables\Columns\TextColumn::make('maximum_depth')
                ->toggleable(),
                Tables\Columns\TextColumn::make('water_table_depth')
                ->toggleable(),
                Tables\Columns\TextColumn::make('soil_type')
                ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->hidden(fn (ManageSoilProfile $livewire): bool => $livewire->getOwnerRecord()->soilProfile()->exists())
                    ->createAnother(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->paginated(false);
    }
}
