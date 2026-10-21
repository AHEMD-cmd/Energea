<?php

namespace App\Filament\Pages;

use App\Filament\Support\Bilingual;
use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageSiteSettings extends Page
{
    protected string $view = 'filament.pages.manage-site-settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'Site settings';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->getSettings()->attributesToArray());
    }

    public static function getNavigationLabel(): string
    {
        return 'Site settings';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Company')
                    ->schema([
                        TextInput::make('company_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('general_email')
                            ->email()
                            ->required(),
                        FileUpload::make('og_image')
                            ->label('Social share image')
                            ->image()
                            ->disk('public')
                            ->directory('images')
                            ->visibility('public')
                            ->maxSize(5120),
                    ])
                    ->columns(2),
                Bilingual::tabs([
                    'footer_text' => Bilingual::textarea('footer_text', 'Footer text', required: true, rows: 3),
                ]),
                Section::make('Quote notifications')
                    ->schema([
                        TagsInput::make('quote_recipients')
                            ->label('Recipients')
                            ->helperText('Email addresses that receive every quote request.')
                            ->nestedRecursiveRules(['email'])
                            ->required(),
                    ]),
                Section::make('Departments')
                    ->schema([
                        Repeater::make('departments')
                            ->schema([
                                TextInput::make('name.en')
                                    ->label('Name (English)')
                                    ->required(),
                                TextInput::make('name.ar')
                                    ->label('Name (Arabic)')
                                    ->required()
                                    ->extraAttributes(['dir' => 'rtl']),
                                TextInput::make('phone')
                                    ->tel(),
                                TextInput::make('email')
                                    ->email(),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Add department')
                            ->columnSpanFull(),
                    ]),
                Section::make('Offices')
                    ->schema([
                        Repeater::make('offices')
                            ->schema([
                                TextInput::make('name.en')
                                    ->label('Name (English)')
                                    ->required(),
                                TextInput::make('name.ar')
                                    ->label('Name (Arabic)')
                                    ->required()
                                    ->extraAttributes(['dir' => 'rtl']),
                                TextInput::make('map_url')
                                    ->label('Google Maps URL')
                                    ->url()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Add office')
                            ->columnSpanFull(),
                    ]),
                Section::make('Analytics')
                    ->schema([
                        TextInput::make('ga4_measurement_id')
                            ->label('GA4 measurement ID'),
                        TextInput::make('search_console_token')
                            ->label('Search Console verification token'),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ])
            ->statePath('data')
            ->model($this->getSettings());
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
                                ->label('Save settings')
                                ->submit('save'),
                        ])->key('form-actions'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $this->getSettings()->update($this->form->getState());

        Notification::make()
            ->success()
            ->title('Settings saved')
            ->send();
    }

    private function getSettings(): SiteSetting
    {
        return SiteSetting::query()->firstOrCreate(['id' => 1], SiteSetting::defaults());
    }
}
