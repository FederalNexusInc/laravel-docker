<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SoilLayerResource\Pages;
use App\Filament\Resources\SoilLayerResource\RelationManagers;
use App\Models\SoilLayer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SoilLayerResource extends Resource
{
    protected static ?string $model = SoilLayer::class;
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
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
            'index' => Pages\ListSoilLayers::route('/'),
            'create' => Pages\CreateSoilLayer::route('/create'),
            'edit' => Pages\EditSoilLayer::route('/{record}/edit'),
        ];
    }
}
