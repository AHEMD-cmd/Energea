<?php

namespace App\Filament\Pages;

use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Validation\Rules\Password;

class ChangePassword extends Page
{
    protected string $view = 'filament.pages.change-password';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static ?int $navigationSort = 20;

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('admin.navigation.groups.settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.pages.change_password');
    }

    public function getTitle(): string
    {
        return __('admin.pages.change_password');
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.change_password'))
                    ->description(__('admin.descriptions.change_password'))
                    ->schema([
                        TextInput::make('current_password')
                            ->label(__('admin.fields.current_password'))
                            ->password()
                            ->revealable()
                            ->autocomplete('current-password')
                            ->currentPassword(guard: Filament::getAuthGuard())
                            ->required()
                            ->dehydrated(false),
                        TextInput::make('password')
                            ->label(__('admin.fields.new_password'))
                            ->password()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->rule(Password::default())
                            ->showAllValidationMessages()
                            ->same('password_confirmation')
                            ->required(),
                        TextInput::make('password_confirmation')
                            ->label(__('admin.fields.confirm_password'))
                            ->password()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->required()
                            ->dehydrated(false),
                    ]),
            ])
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label(__('admin.actions.change_password'))
                                ->submit('save'),
                        ])->key('form-actions'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $user = $this->getUser();

        $user->update(['password' => $data['password']]);

        if (request()->hasSession()) {
            request()->session()->put([
                'password_hash_'.Filament::getAuthGuard() => $user->getAuthPassword(),
            ]);
        }

        $this->form->fill();

        Notification::make()
            ->success()
            ->title(__('admin.notifications.password_changed'))
            ->send();
    }

    private function getUser(): User
    {
        $user = Filament::auth()->user();

        abort_unless($user instanceof User, 403);

        return $user;
    }
}
