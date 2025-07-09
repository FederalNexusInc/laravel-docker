<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Project;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\ProjectSpecialist;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('created_by')
                    ->default(fn () => Auth::id()),
                Forms\Components\TextInput::make('project_name')->columnSpan(4)
                    ->required(),
                Forms\Components\TextInput::make('project_number')->columnSpan(4),
                Forms\Components\TextInput::make('run_id')->columnSpan(4),
                Forms\Components\TextInput::make('soil_reporter')->label('Soils Report Performed by')->columnSpan(3),
                Forms\Components\TextInput::make('soil_report_number')->columnSpan(3),
                Forms\Components\DatePicker::make('soil_report_date')->columnSpan(3),
                Forms\Components\Select::make('pile_type')
                    ->options([
                        'guy_anchor' => 'Guy Anchor',
                        'new_construction_pile' => 'New Construction Pile',
                        'slab_pile' => 'Slab Pile',
                        'tie_back_anchor' => 'Tie Back Anchor',
                        'underpinning_pile' => 'Underpinning Pile',
                    ])
                    ->required()
                    ->columnSpan(3),
                Forms\Components\TextInput::make('boring_number')->columnSpan(4),
                Forms\Components\DatePicker::make('boring_log_date')->columnSpan(4),
                Forms\Components\TextInput::make('termination_depth')->integer()->columnSpan(4),
                Forms\Components\TextInput::make('project_address')->columnSpan(3),
                Forms\Components\TextInput::make('project_city')->columnSpan(3),
                Forms\Components\Select::make('project_state')
                    ->options(self::getStates())
                    ->searchable()
                    ->columnSpan(3),
                Forms\Components\TextInput::make('project_zip_code')->columnSpan(3),
                Forms\Components\Textarea::make('remarks')->label('Field Notes')->columnSpan(12),
                Forms\Components\Select::make('project_specialist_id')
                    ->label('Project Specialist')
                    ->relationship('projectSpecialist', 'name')
                    ->columnSpan(12)
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->columnSpan(3),
                        Forms\Components\TextInput::make('specialist_email')
                            ->email()
                            ->label('Email')
                            ->columnSpan(3),
                        Forms\Components\TextInput::make('company_name')
                            ->columnSpan(3),
                        Forms\Components\TextInput::make('address')
                            ->columnSpan(3),
                        Forms\Components\TextInput::make('city')
                            ->columnSpan(3),
                        Forms\Components\Select::make('state')
                                ->options(self::getStates())
                                ->searchable()
                                ->columnSpan(3),
                        Forms\Components\TextInput::make('zip')
                            ->columnSpan(3),
                        Forms\Components\Textarea::make('remarks')
                            ->columnSpan(12),
                    ]),
            ])
            ->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->modifyQueryUsing(function (Builder $query) {
                if (!Auth::user()->hasRole('super_admin')) {
                    $query->where('created_by', Auth::id());
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('project_name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('project_location')
                    ->label('Location')
                    ->getStateUsing(function (Project $record): string {
                        $city = $record->project_city ?? 'N/A';
                        $state = $record->project_state ?? 'N/A';
                        $zip = $record->project_zip_code ?? 'N/A';
                        return "{$city}, {$state} {$zip}";
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created Date')
                    ->dateTime('m-d-Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('project_number')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('run_id')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('soil_reporter')
                    ->label('Soils Report By')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('soil_report_number')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('soil_report_date')
                    ->date('m-d-Y')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('pile_type')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('boring_number')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('boring_log_date')
                    ->date('m-d-Y')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('termination_depth')
                    ->label('Term. Depth')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('project_address')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('project_city')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('project_state')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('project_zip_code')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('remarks')
                    ->label('Field Notes')
                    ->limit(50)
                    ->tooltip(function (Project $record): ?string {
                        return strlen($record->remarks) > 50 ? $record->remarks : null;
                    })
                    ->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('projectSpecialist.name')
                    ->label('Specialist')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('project_state')
                    ->options(self::getStates()),
                Tables\Filters\TernaryFilter::make('soil_report_provided')
                    ->nullable()
                    ->label('Soil Report Provided')
                    ->attribute('soil_reporter')
                    ->boolean()
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('soil_reporter'),
                        false: fn (Builder $query) => $query->whereNull('soil_reporter'),
                    ),
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
            // 'project-specialist' => Pages\ManageProjectSpecialist::route('/{record}/project-specialist'),
            // 'soil-profile' => Pages\ManageSoilProfile::route('/{record}/soil-profile'),
            // 'soil-layers' => Pages\ManageSoilLayer::route('/{record}/soil-layers'),
            'soil-data' => Pages\ManageSoilData::route('{record}/soil-data'),
            // 'anchors' => Pages\ManageAnchor::route('/{record}/anchors'),
            // 'helixes' => Pages\ManageHelix::route('/{record}/helixes'),
            'piles' => Pages\ManageAnchorData::route('/{record}/piles'),
            'calc' => Pages\CalculationResults::route('/{record}/calc'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\EditProject::class,
            // Pages\ManageProjectSpecialist::class,
            Pages\ManageSoilData::class,
            // Pages\ManageSoilProfile::class,
            // Pages\ManageSoilLayer::class,
            // Pages\ManageAnchor::class,
            // Pages\ManageHelix::class,
            Pages\ManageAnchorData::class,
            Pages\CalculationResults::class,
        ]);
    }

    private static function getStates(): array
    {
        return [
            'AL' => 'Alabama',
            'AK' => 'Alaska',
            'AZ' => 'Arizona',
            'AR' => 'Arkansas',
            'CA' => 'California',
            'CO' => 'Colorado',
            'CT' => 'Connecticut',
            'DE' => 'Delaware',
            'FL' => 'Florida',
            'GA' => 'Georgia',
            'HI' => 'Hawaii',
            'ID' => 'Idaho',
            'IL' => 'Illinois',
            'IN' => 'Indiana',
            'IA' => 'Iowa',
            'KS' => 'Kansas',
            'KY' => 'Kentucky',
            'LA' => 'Louisiana',
            'ME' => 'Maine',
            'MD' => 'Maryland',
            'MA' => 'Massachusetts',
            'MI' => 'Michigan',
            'MN' => 'Minnesota',
            'MS' => 'Mississippi',
            'MO' => 'Missouri',
            'MT' => 'Montana',
            'NE' => 'Nebraska',
            'NV' => 'Nevada',
            'NH' => 'New Hampshire',
            'NJ' => 'New Jersey',
            'NM' => 'New Mexico',
            'NY' => 'New York',
            'NC' => 'North Carolina',
            'ND' => 'North Dakota',
            'OH' => 'Ohio',
            'OK' => 'Oklahoma',
            'OR' => 'Oregon',
            'PA' => 'Pennsylvania',
            'RI' => 'Rhode Island',
            'SC' => 'South Carolina',
            'SD' => 'South Dakota',
            'TN' => 'Tennessee',
            'TX' => 'Texas',
            'UT' => 'Utah',
            'VT' => 'Vermont',
            'VA' => 'Virginia',
            'WA' => 'Washington',
            'WV' => 'West Virginia',
            'WI' => 'Wisconsin',
            'WY' => 'Wyoming',
        ];
    }
}
