<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SoilProfileResource\Pages;
use App\Filament\Resources\SoilProfileResource\RelationManagers;
use App\Models\SoilProfile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SoilProfileResource extends Resource
{
    protected static ?string $model = SoilProfile::class;
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('maximum_depth')
                    ->integer()
                    ->columnSpan(1),
                Forms\Components\TextInput::make('water_table_depth')
                    ->integer()
                    ->columnSpan(1),
                Forms\Components\Select::make('soil_type')
                    ->options([
                        'cohesive' => 'Cohesive',
                        'non-cohesive' => 'NonCohesive',
                    ])
                    ->columnSpan(1),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListSoilProfiles::route('/'),
            'create' => Pages\CreateSoilProfile::route('/create'),
            'edit' => Pages\EditSoilProfile::route('/{record}/edit'),
        ];
    }
}
