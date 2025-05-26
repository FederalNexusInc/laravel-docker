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
                Forms\Components\TextInput::make('project_name')->columnSpan(4),
                Forms\Components\TextInput::make('project_number')->columnSpan(4),
                Forms\Components\TextInput::make('run_id')->columnSpan(4),
                Forms\Components\TextInput::make('soil_reporter')->label('Soils Report Provided/Performed by')->columnSpan(3),
                Forms\Components\TextInput::make('soil_report_number')->integer()->columnSpan(3),
                Forms\Components\DatePicker::make('soil_report_date')->columnSpan(3),
                Forms\Components\Select::make('pile_type')
                    ->options([
                        'guy_anchor' => 'Guy Anchor',
                        'new_construction_pile' => 'New Construction Pile',
                        'slab_pile' => 'Slab Pile',
                        'tie_back_anchor' => 'Tie Back Anchor',
                        'underpinning_pile' => 'Underpinning Pile',
                    ])->columnSpan(3),
                Forms\Components\TextInput::make('boring_number')->integer()->columnSpan(4),
                Forms\Components\DatePicker::make('boring_log_date')->columnSpan(4),
                Forms\Components\TextInput::make('termination_depth')->integer()->columnSpan(4),
                Forms\Components\TextInput::make('project_address')->columnSpan(3),
                Forms\Components\TextInput::make('project_city')->columnSpan(3),
                Forms\Components\Select::make('project_state')
                    ->options(self::getStates())
                    ->searchable()
                    ->columnSpan(3),
                Forms\Components\TextInput::make('project_zip_code')->columnSpan(3),
                Forms\Components\Textarea::make('remarks')->columnSpan(12),
            ])
            ->columns(12);;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                if (!Auth::user()->hasRole('super_admin')) {
                    $query->where('created_by', Auth::id());
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('project_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('project_location')
                    ->label('Location')
                    ->getStateUsing(function (Project $record): string {
                        return "{$record->project_city}, {$record->project_state} {$record->project_zip_code}";
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created Date')
                    ->dateTime('m-d-Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('project_state')
                    ->options(self::getStates()),
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
            'project-specialist' => Pages\ManageProjectSpecialist::route('/{record}/project-specialist'),
            'soil-profile' => Pages\ManageSoilProfile::route('/{record}/soil-profile'),
            'soil-layers' => Pages\ManageSoilLayer::route('/{record}/soil-layers'),
            'anchors' => Pages\ManageAnchor::route('/{record}/anchors'),
            'helixes' => Pages\ManageHelix::route('/{record}/helixes'),
            'calc' => Pages\CalculationResults::route('/{record}/calc'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\EditProject::class,
            Pages\ManageProjectSpecialist::class,
            Pages\ManageSoilProfile::class,
            Pages\ManageSoilLayer::class,
            Pages\ManageAnchor::class,
            Pages\ManageHelix::class,
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
