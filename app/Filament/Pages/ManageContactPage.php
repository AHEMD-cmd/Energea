<?php

namespace App\Filament\Pages;

use App\Filament\Support\Bilingual;
use App\Models\PageContent;
use App\Support\ContactContent;
use App\Support\Media;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;

class ManageContactPage extends Page
{
    protected string $view = 'filament.pages.manage-contact-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?int $navigationSort = 6;

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('admin.navigation.groups.content');
    }

    public function getTitle(): string
    {
        return __('admin.pages.contact');
    }

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(PageContent::for(ContactContent::PAGE));
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.pages.contact');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.seo'))
                    ->description(__('admin.descriptions.contact_seo'))
                    ->schema([
                        Bilingual::tabs([
                            'title' => Bilingual::text('title', __('admin.fields.title'), required: true),
                            'description' => Bilingual::textarea('description', __('admin.fields.description'), required: true, rows: 3),
                        ], 'seo'),
                    ])
                    ->collapsible(),
                Section::make(__('admin.sections.hero'))
                    ->description(__('admin.descriptions.contact_hero'))
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', __('admin.fields.section_label'), required: true),
                            'title' => Bilingual::text('title', __('admin.fields.title'), required: true),
                            'body' => Bilingual::textarea('body', __('admin.fields.body'), required: true),
                            'alt' => Bilingual::text('alt', __('admin.fields.image_alt'), required: true),
                        ], 'hero'),
                        Placeholder::make('current_hero_image')
                            ->label(__('admin.fields.current_image'))
                            ->content(function () {
                                $image = PageContent::for(ContactContent::PAGE)['hero']['image'];

                                return view('filament.forms.image-preview', [
                                    'url' => Media::url($image, 'assets/images/field-electrical-testing.jpeg'),
                                    'isCustom' => is_string($image) && $image !== '',
                                    'title' => __('admin.titles.hero_image'),
                                    'hint' => __('admin.descriptions.image_hint_about_snapshot'),
                                    'variant' => 'landscape',
                                ]);
                            })
                            ->columnSpanFull(),
                        FileUpload::make('hero_image_upload')
                            ->label(__('admin.fields.replace_image'))
                            ->helperText(__('admin.descriptions.upload_replace'))
                            ->image()
                            ->disk('public')
                            ->directory('images/contact')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                Section::make(__('admin.sections.contact_card'))
                    ->description(__('admin.descriptions.contact_card'))
                    ->schema([
                        Bilingual::tabs([
                            'direct_label' => Bilingual::text('direct_label', __('admin.fields.direct_contact_label'), required: true),
                            'general_email' => Bilingual::text('general_email', __('admin.fields.general_email_label'), required: true),
                            'offices' => Bilingual::text('offices', __('admin.fields.offices_heading'), required: true),
                        ], 'card'),
                    ])
                    ->collapsible(),
                Section::make(__('admin.sections.form_heading'))
                    ->description(__('admin.descriptions.contact_form_heading'))
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', __('admin.fields.section_label'), required: true),
                            'title' => Bilingual::text('title', __('admin.fields.title'), required: true),
                        ], 'form'),
                    ])
                    ->collapsible(),
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
                                ->label(__('admin.actions.save_contact'))
                                ->submit('save'),
                        ])->key('form-actions'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $current = PageContent::for(ContactContent::PAGE);

        $upload = $state['hero_image_upload'] ?? null;

        unset($state['hero_image_upload']);

        $currentImage = $current['hero']['image'];

        $state['hero']['image'] = is_string($upload) && $upload !== ''
            ? $upload
            : $currentImage;

        if (is_string($currentImage) && str_starts_with($currentImage, 'images/contact/') && $currentImage !== $state['hero']['image']) {
            Storage::disk('public')->delete($currentImage);
        }

        PageContent::store(ContactContent::PAGE, $state);

        $this->form->fill(PageContent::for(ContactContent::PAGE));

        Notification::make()
            ->success()
            ->title(__('admin.notifications.contact_saved'))
            ->send();
    }
}
