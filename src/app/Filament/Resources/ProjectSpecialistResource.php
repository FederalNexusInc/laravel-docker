<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectSpecialistResource\Pages;
use App\Filament\Resources\ProjectSpecialistResource\RelationManagers;
use App\Models\ProjectSpecialist;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Project;

class ProjectSpecialistResource extends Resource
{
    protected static ?string $model = ProjectSpecialist::class;
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
            'index' => Pages\ListProjectSpecialists::route('/'),
            'create' => Pages\CreateProjectSpecialist::route('/create'),
            'edit' => Pages\EditProjectSpecialist::route('/{record}/edit'),
        ];
    }
}
