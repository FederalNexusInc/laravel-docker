<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Filament\Forms;
use Filament\Tables;
use Filament\Actions;
use App\Models\Project;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Filament\Resources\ProjectResource;
use Filament\Resources\Pages\ManageRelatedRecords;

class ManageProjectSpecialist extends ManageRelatedRecords
{
    protected static string $resource = ProjectResource::class;

    protected static string $relationship = 'projectSpecialist';

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    public static function getNavigationLabel(): string
    {
        return 'Project Specialist';
    }

    public function getBreadcrumbs(): array
    {
        $breadcrumbs = parent::getBreadcrumbs();

        $project = $this->getOwnerRecord();

        $projectId = $project->getKey();

        $newBreadcrumbs = array_slice( $breadcrumbs, 0, 1 ) + [ 0 => "Project {$projectId}" ] + $breadcrumbs;

        return $newBreadcrumbs;
    }

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Select::make('project_id')
                ->label('Project')
                ->relationship('project', 'project_name')
                ->disabled()
                ->required()
                ->default($this->getOwnerRecord()->project_id)
                ->columnSpan(3),
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
        ])->columns(12);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('company_name')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('state')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('specialist_email')
                    ->label('Email')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('address')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('city')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('zip')
                    ->label('Zip Code')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('remarks')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->remarks)
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
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->hidden(fn (ManageProjectSpecialist $livewire): bool => $livewire->getOwnerRecord()->projectSpecialist()->exists())
                    ->createAnother(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->paginated(false);
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
