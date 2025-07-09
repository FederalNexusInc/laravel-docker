<?php

namespace App\Filament\Resources\ProjectResource\Widgets;

use App\Models\SoilLayer;
use App\Models\SoilLayerType;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use Filament\Widgets\TableWidget as BaseWidget;

class SoilLayersTable extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public ?int $soilProfileId;

    protected function getTableRecordClassesUsing(): ?\Closure
    {
        return fn (SoilLayer $record) => ['cursor-pointer'];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SoilLayer::query()
                    ->where('soil_profile_id', $this->soilProfileId)
                    ->orderBy('start_depth')
            )
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('start_depth')
                    ->sortable()
                    ->label('Depth'),
                Tables\Columns\TextColumn::make('soilLayerType.name')
                    ->label('Type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('blow_count')
                    ->label('N'),
                Tables\Columns\TextColumn::make('cohesion')
                    ->label('c'),
                Tables\Columns\TextColumn::make('coefficient_of_adhesion')
                    ->label('α'),
                Tables\Columns\TextColumn::make('angle_of_internal_friction')
                    ->label('Ø'),
                Tables\Columns\TextColumn::make('coefficient_of_external_friction')
                    ->label('Β'),
                Tables\Columns\TextColumn::make('moist_unit_weight')
                    ->label('γm'),
                Tables\Columns\TextColumn::make('saturated_unit_weight')
                    ->label('γsat'),
                Tables\Columns\TextColumn::make('nc'),
                Tables\Columns\TextColumn::make('nq'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form($this->getFormSchema()),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->form($this->getFormSchema())
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['soil_profile_id'] = $this->soilProfileId;
                        return $data;
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('soil_layer_type_id')
                    ->label('Soil Layer Type')
                    ->relationship('soilLayerType', 'name')
                    ->searchable(),
            ])
            ->recordAction('edit');
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Grid::make(12)
            ->schema([
                Forms\Components\Hidden::make('soil_profile_id')
                    ->default($this->soilProfileId)
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
                ]),
        ];
    }
}
