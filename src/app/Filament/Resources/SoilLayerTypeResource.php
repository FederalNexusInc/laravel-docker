<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Configurations;
use App\Filament\Resources\SoilLayerTypeResource\Pages;
use App\Filament\Resources\SoilLayerTypeResource\RelationManagers;
use App\Models\SoilLayerType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SoilLayerTypeResource extends Resource
{
    protected static ?string $model = SoilLayerType::class;
    protected static ?string $cluster = Configurations::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->columnSpan(4),
                Forms\Components\TextInput::make('description')
                    ->label('Description')
                    ->columnSpan(4),
                Forms\Components\TextInput::make('default_cohesion')
                    ->label('Default Cohesion')
                    ->numeric()
                    ->columnSpan(4),
                Forms\Components\TextInput::make('default_coefficient_of_adhesion')
                    ->label('Default Coefficient Of Adhesion')
                    ->numeric()
                    ->columnSpan(4),
                Forms\Components\TextInput::make('default_angle_of_internal_friction')
                    ->label('Default Angle Of Internal Friction')
                    ->numeric()
                    ->columnSpan(4),
                Forms\Components\TextInput::make('default_coefficient_of_external_friction')
                    ->label('Default Coefficient Of External Friction')
                    ->numeric()
                    ->columnSpan(4),
                Forms\Components\TextInput::make('default_moist_unit_weight')
                    ->label('Default Moist Unit Weight')
                    ->numeric()
                    ->columnSpan(4),
                Forms\Components\TextInput::make('default_saturated_unit_weight')
                    ->label('Default Saturated Unit Weight')
                    ->numeric()
                    ->columnSpan(4),
                Forms\Components\TextInput::make('default_nc')
                    ->label('Default Nc')
                    ->numeric()
                    ->columnSpan(4),
                Forms\Components\TextInput::make('default_nq')
                    ->label('Default Nq')
                    ->numeric()
                    ->columnSpan(4),
            ])
            ->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('description'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSoilLayerTypes::route('/'),
            'create' => Pages\CreateSoilLayerType::route('/create'),
            'edit' => Pages\EditSoilLayerType::route('/{record}/edit'),
        ];
    }
}
