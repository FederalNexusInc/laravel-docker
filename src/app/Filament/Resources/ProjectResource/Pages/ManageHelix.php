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

class ManageHelix extends ManageRelatedRecords
{
    protected static string $resource = ProjectResource::class;
    protected static string $relationship = 'helixes';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Helixes';
    }

    public function form(Form $form): Form
    {
        $projectId = $this->getOwnerRecord()->project_id;
        $anchors = Anchor::where('project_id', $projectId)
            ->orderBy('anchor_id', 'asc')
            ->pluck('lead_shaft_od', 'anchor_id')
            ->toArray();
        
        $defaultAnchorId = count($anchors) > 0 ? array_key_first($anchors) : null;

        return $form
            ->schema([
                Forms\Components\Select::make('anchor_id')
                    ->label('Anchor')
                    ->options($anchors)
                    ->default($defaultAnchorId)
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('description')
                    ->options([
                        'Size 8 Thickness 3/8' => 'Size 8 Thickness 3/8',
                        'Size 8 Thickness 1/2' => 'Size 8 Thickness 1/2',
                        'Size 10 Thickness 3/8' => 'Size 10 Thickness 3/8',
                        'Size 10 Thickness 1/2' => 'Size 10 Thickness 1/2',
                        'Size 12 Thickness 3/8' => 'Size 12 Thickness 3/8',
                        'Size 12 Thickness 1/2' => 'Size 12 Thickness 1/2',
                        'Size 14 Thickness 1/2' => 'Size 14 Thickness 1/2',
                        'Size 16 Thickness 1/2' => 'Size 16 Thickness 1/2',
                    ])
                    ->reactive()
                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                        if (preg_match('/Size (\d+)/', $state, $matches)) {
                            $size = $matches[1];
                            $set('size', $size);
                        } else {
                            $set('size', null);
                        }
                        if (preg_match('/Thickness (\d+\/\d+)/', $state, $matches)) {
                            $thicknessFraction = $matches[1];
                            $thicknessDecimal = $this->fractionToDecimal($thicknessFraction);
                            $set('thickness', $thicknessDecimal);
                        } else {
                            $set('thickness', null);
                        }
                        $rating = $this->getRatingFromDescription($state);
                        $set('rating', $rating);
                    }),
                Forms\Components\TextInput::make('size')
                    ->disabled()
                    ->dehydrated()
                    ->numeric()
                    ->label('Size'),
                Forms\Components\TextInput::make('thickness')
                    ->disabled()
                    ->dehydrated()
                    ->numeric()
                    ->label('Thickness'),
                Forms\Components\TextInput::make('rating')
                    ->numeric()
                    ->label('Rating'),
                Forms\Components\TextInput::make('helix_count')
                    ->label('Helix Count')
                    ->integer()
                    ->default(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('helix_id')
            ->columns([
                Tables\Columns\TextColumn::make('anchor.lead_shaft_od')
                    ->label('Anchor OD')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('helix_count')
                    ->label('Helix Count')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('size')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('thickness')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('rating')
                    ->numeric()
                    ->sortable()
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
                Tables\Filters\SelectFilter::make('description')
                    ->options([
                        'Size 8 Thickness 3/8' => 'Size 8 Thickness 3/8',
                        'Size 8 Thickness 1/2' => 'Size 8 Thickness 1/2',
                        'Size 10 Thickness 3/8' => 'Size 10 Thickness 3/8',
                        'Size 10 Thickness 1/2' => 'Size 10 Thickness 1/2',
                        'Size 12 Thickness 3/8' => 'Size 12 Thickness 3/8',
                        'Size 12 Thickness 1/2' => 'Size 12 Thickness 1/2',
                        'Size 14 Thickness 1/2' => 'Size 14 Thickness 1/2',
                        'Size 16 Thickness 1/2' => 'Size 16 Thickness 1/2',
                    ])
                    ->label('Helix Type')
                    ->searchable(),
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

    private function fractionToDecimal(string $fraction): ?string
    {
        if (strpos($fraction, '/') !== false) {
            list($numerator, $denominator) = explode('/', $fraction);
            if (is_numeric($numerator) && is_numeric($denominator) && $denominator != 0) {
                return number_format((float) $numerator / (float) $denominator, 4, '.', '');
            }
        }
        return null;
    }

    private function getRatingFromDescription(string $description): ?int
    {
        $ratings = [
            'Size 8 Thickness 3/8' => 54000,
            'Size 8 Thickness 1/2' => 96500,
            'Size 10 Thickness 3/8' => 45500,
            'Size 10 Thickness 1/2' => 82000,
            'Size 12 Thickness 3/8' => 41500,
            'Size 12 Thickness 1/2' => 70500,
            'Size 14 Thickness 1/2' => 58500,
            'Size 16 Thickness 1/2' => 50000,
        ];

        return $ratings[$description] ?? null;
    }
}
