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

class ManageAnchor extends ManageRelatedRecords
{
    protected static string $resource = ProjectResource::class;
    protected static string $relationship = 'anchors';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                    ->columnSpan(4),
                Forms\Components\TextInput::make('lead_shaft_length')
                    ->label('Lead Shaft Length')
                    ->numeric()
                    ->nullable()
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
                    ->columnSpan(4),
                Forms\Components\TextInput::make('wall_thickness')
                    ->label('Wall Thickness')
                    ->numeric()
                    ->nullable()
                    ->columnSpan(4),
                Forms\Components\TextInput::make('yield_strength')
                    ->label('Yield Strength')
                    ->numeric()
                    ->nullable()
                    ->columnSpan(4),
                Forms\Components\TextInput::make('tensile_strength')
                    ->label('Tensile Strength')
                    ->numeric()
                    ->nullable()
                    ->columnSpan(4),
                Forms\Components\TextInput::make('empirical_torque_factor')
                    ->label('Empirical Torque Factor')
                    ->numeric()
                    ->nullable()
                    ->columnSpan(4),
                Forms\Components\TextInput::make('required_allowable_capacity')
                    ->label('Required Allowable Capacity')
                    ->numeric()
                    ->nullable()
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
                    ->columnSpan(4),
                Forms\Components\TextInput::make('required_safety_factor')
                    ->label('Required Safety Factor')
                    ->numeric()
                    ->nullable()
                    ->columnSpan(4),
                Forms\Components\TextInput::make('anchor_declination_degree')
                    ->label('Anchor Declination Degree')
                    ->numeric()
                    ->nullable()
                    ->columnSpan(4),
                Forms\Components\TextInput::make('pile_head_position')
                    ->label('Pile Head Position')
                    ->numeric()
                    ->nullable()
                    ->columnSpan(4),
                Forms\Components\Grid::make(1)
                    ->schema([
                        Forms\Components\Grid::make(5)
                            ->schema([
                                Forms\Components\TextInput::make('x1')
                                    ->hiddenLabel()
                                    ->numeric()
                                    ->placeholder('X1')
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('x2')
                                    ->hiddenLabel()
                                    ->numeric()
                                    ->placeholder('X2')
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('x3')
                                    ->hiddenLabel()
                                    ->numeric()
                                    ->placeholder('X3')
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('x4')
                                ->hiddenLabel()

                                    ->numeric()
                                    ->placeholder('X4')
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('x5')
                                    ->hiddenLabel()
                                    ->numeric()
                                    ->placeholder('X5')
                                    ->columnSpan(1),
                            ])
                            ->columns(5)
                            ->columnSpanFull(),
                        Forms\Components\Grid::make(5)
                            ->schema([
                                Forms\Components\TextInput::make('y1')
                                    ->hiddenLabel()
                                    ->placeholder('Y1')
                                    ->numeric()
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('y2')
                                    ->hiddenLabel()
                                    ->numeric()
                                    ->placeholder('Y2')
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('y3')
                                    ->hiddenLabel()
                                    ->numeric()
                                    ->placeholder('Y3')
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('y4')
                                    ->hiddenLabel()
                                    ->numeric()
                                    ->placeholder('Y4')
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('y5')
                                    ->hiddenLabel()
                                    ->numeric()
                                    ->placeholder('Y5')
                                    ->columnSpan(1),
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
                    ->label('Size'),
            ])
            ->filters([
                //
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
