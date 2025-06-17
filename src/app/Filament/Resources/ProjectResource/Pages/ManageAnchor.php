<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Filament\Forms;
use Filament\Tables;
use Filament\Actions;
use App\Models\Anchor;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProjectResource;
use Filament\Resources\Pages\ManageRelatedRecords;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageAnchor extends ManageRelatedRecords
{
    protected static string $resource = ProjectResource::class;
    protected static string $relationship = 'anchors';
    protected static ?string $navigationIcon = 'zondicon-anchor';

    public static function getNavigationLabel(): string
    {
        return 'Anchors';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('project_id')
                    ->default($this->getOwnerRecord()->project_id)
                    ->required(),
                Forms\Components\Select::make('lead_shaft_od')
                    ->label('Lead Shaft Od')
                    ->options([
                        '2 3/8' => '2 3/8',
                        '2 7/8' => '2 7/8',
                        '3 1/2' => '3 1/2',
                        '4 1/2' => '4 1/2',
                        '5 1/2' => '5 1/2',
                        '6 5/8' => '6 5/8',
                        '8 5/8'=> '8 5/8',
                    ])
                    ->columnSpan(4)
                    ->required(),
                Forms\Components\TextInput::make('lead_shaft_length')
                    ->label('Lead Shaft Length')
                    ->numeric()
                    ->nullable()
                    ->required()
                    ->columnSpan(4),
                Forms\Components\Select::make('extension_shaft_od')
                    ->label('Extension Shaft Od')
                    ->options([
                        '2 3/8' => '2 3/8',
                        '2 7/8' => '2 7/8',
                        '3 1/2' => '3 1/2',
                        '4 1/2' => '4 1/2',
                        '5 1/2' => '5 1/2',
                        '6 5/8' => '6 5/8',
                        '8 5/8'=> '8 5/8',
                    ])
                    ->required()
                    ->columnSpan(4),
                Forms\Components\TextInput::make('wall_thickness')
                    ->label('Wall Thickness')
                    ->numeric()
                    ->nullable()
                    ->required()
                    ->columnSpan(4),
                Forms\Components\TextInput::make('yield_strength')
                    ->label('Yield Strength')
                    ->numeric()
                    ->nullable()
                    ->required()
                    ->columnSpan(4),
                Forms\Components\TextInput::make('tensile_strength')
                    ->label('Tensile Strength')
                    ->numeric()
                    ->nullable()
                    ->required()
                    ->columnSpan(4),
                Forms\Components\TextInput::make('empirical_torque_factor')
                    ->label('Empirical Torque Factor')
                    ->numeric()
                    ->nullable()
                    ->required()
                    ->columnSpan(4),
                Forms\Components\TextInput::make('required_allowable_capacity')
                    ->label('Required Allowable Capacity')
                    ->numeric()
                    ->nullable()
                    ->required()
                    ->columnSpan(4),
                Forms\Components\Radio::make('anchor_type')
                    ->label('Anchor Type')
                    ->options([
                        1 => 'Compression',
                        2 => 'Tension',
                    ])
                    ->default(1)
                    ->inline()
                    ->inlineLabel(false)
                    ->nullable()
                    ->required()
                    ->columnSpan(4),
                Forms\Components\TextInput::make('required_safety_factor')
                    ->label('Required Safety Factor')
                    ->numeric()
                    ->nullable()
                    ->required()
                    ->columnSpan(4),
                Forms\Components\TextInput::make('anchor_declination_degree')
                    ->label('Anchor Declination Degree')
                    ->numeric()
                    ->nullable()
                    ->required()
                    ->columnSpan(4),
                Forms\Components\TextInput::make('pile_head_position')
                    ->label('Pile Head Position')
                    ->numeric()
                    ->nullable()
                    ->required()
                    ->columnSpan(4),
                Forms\Components\Grid::make(1)
                    ->schema([
                        Forms\Components\Grid::make(5)
                            ->schema([
                                Forms\Components\TextInput::make('x1')
                                    ->hiddenLabel()
                                    ->numeric()
                                    ->placeholder('X1')
                                    ->columnSpan(1)
                                    ->required(),
                                Forms\Components\TextInput::make('x2')
                                    ->hiddenLabel()
                                    ->numeric()
                                    ->placeholder('X2')
                                    ->columnSpan(1)
                                    ->required(),
                                Forms\Components\TextInput::make('x3')
                                    ->hiddenLabel()
                                    ->numeric()
                                    ->placeholder('X3')
                                    ->columnSpan(1)
                                    ->required(),
                                Forms\Components\TextInput::make('x4')
                                ->hiddenLabel()
                                    ->numeric()
                                    ->placeholder('X4')
                                    ->columnSpan(1)
                                    ->required(),
                                Forms\Components\TextInput::make('x5')
                                    ->hiddenLabel()
                                    ->numeric()
                                    ->placeholder('X5')
                                    ->columnSpan(1)
                                    ->required(),
                            ])
                            ->columns(5)
                            ->columnSpanFull(),
                        Forms\Components\Grid::make(5)
                            ->schema([
                                Forms\Components\TextInput::make('y1')
                                    ->hiddenLabel()
                                    ->placeholder('Y1')
                                    ->numeric()
                                    ->columnSpan(1)
                                    ->required(),
                                Forms\Components\TextInput::make('y2')
                                    ->hiddenLabel()
                                    ->numeric()
                                    ->placeholder('Y2')
                                    ->columnSpan(1)
                                    ->required(),
                                Forms\Components\TextInput::make('y3')
                                    ->hiddenLabel()
                                    ->numeric()
                                    ->placeholder('Y3')
                                    ->columnSpan(1)
                                    ->required(),
                                Forms\Components\TextInput::make('y4')
                                    ->hiddenLabel()
                                    ->numeric()
                                    ->placeholder('Y4')
                                    ->columnSpan(1)
                                    ->required(),
                                Forms\Components\TextInput::make('y5')
                                    ->hiddenLabel()
                                    ->numeric()
                                    ->placeholder('Y5')
                                    ->columnSpan(1)
                                    ->required(),
                            ])
                            ->columns(5)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Forms\Components\Checkbox::make('omit_shaft_resistance')
                    ->label('Omit Shaft Resistance')
                    ->default(false)
                    ->columnSpanFull(),
                Forms\Components\Checkbox::make('omit_helix_mechanical_strength_check')
                    ->label('Omit Helix Mechanical Strength Check')
                    ->default(false)
                    ->columnSpanFull(),
                Forms\Components\Checkbox::make('omit_shaft_mechanical_strength_check')
                    ->label('Omit Shaft Mechanical Strength Check')
                    ->default(false)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('field_notes')
                    ->label('Field Notes')
                    ->nullable()
                    ->columnSpanFull(),
            ])
            ->columns(12);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('anchor_id')
            ->columns([
                Tables\Columns\TextColumn::make('lead_shaft_od')
                    ->label('Lead Shaft OD')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('lead_shaft_length')
                    ->label('Lead Shaft Length')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('extension_shaft_od')
                    ->label('Ext. Shaft OD')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('wall_thickness')
                    ->label('Wall Thickness')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('yield_strength')
                    ->label('Yield Strength')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('tensile_strength')
                    ->label('Tensile Strength')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('empirical_torque_factor')
                    ->label('Empirical Torque Factor')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('required_allowable_capacity')
                    ->label('Req. Allow. Capacity')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('anchor_type')
                    ->label('Anchor Type')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        '1' => 'Compression',
                        '2' => 'Tension',
                        default => 'N/A',
                    })
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('required_safety_factor')
                    ->label('Req. Safety Factor')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('anchor_declination_degree')
                    ->label('Declination Degree')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('pile_head_position')
                    ->label('Pile Head Position')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('x1')->label('X1')->numeric()->sortable()->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('x2')->label('X2')->numeric()->sortable()->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('x3')->label('X3')->numeric()->sortable()->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('x4')->label('X4')->numeric()->sortable()->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('x5')->label('X5')->numeric()->sortable()->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('y1')->label('Y1')->numeric()->sortable()->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('y2')->label('Y2')->numeric()->sortable()->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('y3')->label('Y3')->numeric()->sortable()->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('y4')->label('Y4')->numeric()->sortable()->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('y5')->label('Y5')->numeric()->sortable()->toggleable()->toggledHiddenByDefault(),
                Tables\Columns\IconColumn::make('omit_shaft_resistance')
                    ->label('Omit Shaft Resistance')
                    ->boolean()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\IconColumn::make('omit_helix_mechanical_strength_check')
                    ->label('Omit Helix Mech. Strength Check')
                    ->boolean()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\IconColumn::make('omit_shaft_mechanical_strength_check')
                    ->label('Omit Shaft Mech. Strength Check')
                    ->boolean()
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                Tables\Columns\TextColumn::make('field_notes')
                    ->label('Field Notes')
                    ->limit(50)
                    ->tooltip(fn (Anchor $record): ?string => strlen($record->field_notes) > 50 ? $record->field_notes : null)
                    ->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
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
                Tables\Filters\SelectFilter::make('anchor_type')
                    ->options([
                        1 => 'Compression',
                        2 => 'Tension',
                    ])
                    ->label('Anchor Type'),
                Tables\Filters\SelectFilter::make('lead_shaft_od')
                    ->options([
                        '2 3/8' => '2 3/8',
                        '2 7/8' => '2 7/8',
                        '3 1/2' => '3 1/2',
                        '4 1/2' => '4 1/2',
                        '5 1/2' => '5 1/2',
                        '6 5/8' => '6 5/8',
                        '8 5/8'=> '8 5/8',
                    ])
                    ->label('Lead Shaft OD'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
