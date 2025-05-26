<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;

class MyProfile extends Page implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.pages.my-profile';
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Auth::user()->attributesToArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('company_name')
                            ->maxLength(255),
                    ])->columns(2),
            ])
            ->statePath('data')
            ->model(Auth::user());
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('Update')
                ->color('primary')
                ->action('update'),
        ];
    }

    public function update(): void
    {
        Auth::user()->update(
            $this->form->getState()
        );

        Notification::make()
            ->title('Profile updated successfully')
            ->success()
            ->send();
    }
}