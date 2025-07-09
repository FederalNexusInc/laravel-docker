<?php

namespace App\Filament\Resources\ProjectResource\Widgets;

use App\Models\Helix;
use App\Models\Anchor;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class HelixesTable extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public ?int $anchorId;

    protected function getTableRecordClassesUsing(): ?\Closure
    {
        return fn (Helix $record) => ['cursor-pointer'];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Helix::query()
                    ->where('anchor_id', $this->anchorId)
                    ->orderBy('helix_id')
            )
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('anchor.lead_shaft_od')
                    ->label('Anchor OD')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('helix_count')
                    ->label('Helix Count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('size')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('thickness')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('rating')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Actions\CreateAction::make()
                    ->form($this->getFormSchema())
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['anchor_id'] = $this->anchorId;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form($this->getFormSchema()),
                Tables\Actions\DeleteAction::make(),
            ])
            ->recordAction('edit');
    }

    protected function getFormSchema(): array
    {
        $anchors = Anchor::where('anchor_id', $this->anchorId)
            ->pluck('lead_shaft_od', 'anchor_id')
            ->toArray();

        return [
            Forms\Components\Select::make('anchor_id')
                ->label('Anchor')
                ->options($anchors)
                ->default($this->anchorId)
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
                        $set('size', $matches[1]);
                    }
                    if (preg_match('/Thickness (\d+\/\d+)/', $state, $matches)) {
                        $thicknessFraction = $matches[1];
                        $thicknessDecimal = $this->fractionToDecimal($thicknessFraction);
                        $set('thickness', $thicknessDecimal);
                    }
                    $rating = $this->getRatingFromDescription($state);
                    $set('rating', $rating);
                })
                ->required(),
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
                ->label('Rating')
                ->required(),
            Forms\Components\TextInput::make('helix_count')
                ->label('Helix Count')
                ->integer()
                ->default(1),
        ];
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
