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

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('admin.navigation.groups.settings');
    }

    protected static ?int $navigationSort = 10;

    public function getTitle(): string
    {
        return __('admin.pages.site_settings');
    }

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
        return __('admin.pages.site_settings');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.company'))
                    ->schema([
                        TextInput::make('company_name')
                            ->label(__('admin.fields.company_name'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('general_email')
                            ->label(__('admin.fields.general_email'))
                            ->email()
                            ->required(),
                        FileUpload::make('og_image')
                            ->label(__('admin.fields.social_share_image'))
                            ->image()
                            ->disk('public')
                            ->directory('images')
                            ->visibility('public')
                            ->maxSize(5120),
                    ])
                    ->columns(2),
                Bilingual::tabs([
                    'footer_text' => Bilingual::textarea('footer_text', __('admin.fields.footer_text'), required: true, rows: 3),
                ]),
                Section::make(__('admin.sections.quote_notifications'))
                    ->schema([
                        TagsInput::make('quote_recipients')
                            ->label(__('admin.fields.recipients'))
                            ->helperText(__('admin.descriptions.quote_recipients_helper'))
                            ->nestedRecursiveRules(['email'])
                            ->required(),
                    ]),
                Section::make(__('admin.sections.departments'))
                    ->schema([
                        Repeater::make('departments')
                            ->hiddenLabel()
                            ->schema([
                                TextInput::make('name.en')
                                    ->label(__('admin.fields.name_en'))
                                    ->required(),
                                TextInput::make('name.ar')
                                    ->label(__('admin.fields.name_ar'))
                                    ->required()
                                    ->extraAttributes(['dir' => 'rtl']),
                                TextInput::make('phone')
                                    ->label(__('admin.fields.phone'))
                                    ->tel(),
                                TextInput::make('email')
                                    ->label(__('admin.fields.email'))
                                    ->email(),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel(__('admin.actions.add_department'))
                            ->columnSpanFull(),
                    ]),
                Section::make(__('admin.sections.offices'))
                    ->schema([
                        Repeater::make('offices')
                            ->hiddenLabel()
                            ->schema([
                                TextInput::make('name.en')
                                    ->label(__('admin.fields.name_en'))
                                    ->required(),
                                TextInput::make('name.ar')
                                    ->label(__('admin.fields.name_ar'))
                                    ->required()
                                    ->extraAttributes(['dir' => 'rtl']),
                                TextInput::make('map_url')
                                    ->label(__('admin.fields.map_url'))
                                    ->url()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel(__('admin.actions.add_office'))
                            ->columnSpanFull(),
                    ]),
                Section::make(__('admin.sections.analytics'))
                    ->schema([
                        TextInput::make('ga4_measurement_id')
                            ->label(__('admin.fields.ga4_measurement_id')),
                        TextInput::make('search_console_token')
                            ->label(__('admin.fields.search_console_token')),
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
                                ->label(__('admin.actions.save_settings'))
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
            ->title(__('admin.notifications.settings_saved'))
            ->send();
    }

    private function getSettings(): SiteSetting
    {
        return SiteSetting::query()->firstOrCreate(['id' => 1], SiteSetting::defaults());
    }
}
