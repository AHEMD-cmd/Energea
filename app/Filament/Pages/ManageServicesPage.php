<?php

namespace App\Filament\Pages;

use App\Filament\Support\Bilingual;
use App\Models\PageContent;
use App\Support\Media;
use App\Support\ServicesContent;
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

class ManageServicesPage extends Page
{
    protected string $view = 'filament.pages.manage-services-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('admin.navigation.groups.content');
    }

    public function getTitle(): string
    {
        return __('admin.pages.services');
    }

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(PageContent::for(ServicesContent::PAGE));
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.pages.services');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.seo'))
                    ->description(__('admin.descriptions.services_seo'))
                    ->schema([
                        Bilingual::tabs([
                            'title' => Bilingual::text('title', __('admin.fields.title'), required: true),
                            'description' => Bilingual::textarea('description', __('admin.fields.description'), required: true, rows: 3),
                        ], 'seo'),
                    ])
                    ->collapsible(),
                Section::make(__('admin.sections.hero'))
                    ->description(__('admin.descriptions.services_hero'))
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
                                $image = PageContent::for(ServicesContent::PAGE)['hero']['image'];

                                return view('filament.forms.image-preview', [
                                    'url' => Media::url($image, 'assets/images/services-banner.jpeg'),
                                    'isCustom' => is_string($image) && $image !== '',
                                    'title' => __('admin.titles.hero_image'),
                                    'hint' => __('admin.descriptions.image_hint_wide'),
                                    'variant' => 'landscape',
                                ]);
                            })
                            ->columnSpanFull(),
                        FileUpload::make('hero_image_upload')
                            ->label(__('admin.fields.replace_image'))
                            ->helperText(__('admin.descriptions.upload_replace_wide'))
                            ->image()
                            ->disk('public')
                            ->directory('images/services')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                Section::make(__('admin.sections.capabilities_heading'))
                    ->description(__('admin.descriptions.services_heading'))
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', __('admin.fields.section_label'), required: true),
                            'title_line' => Bilingual::text('title_line', __('admin.fields.title_first_line'), required: true),
                            'title_em' => Bilingual::text('title_em', __('admin.fields.title_highlighted'), required: true),
                            'body' => Bilingual::textarea('body', __('admin.fields.body'), required: true),
                        ], 'head'),
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
                                ->label(__('admin.actions.save_services'))
                                ->submit('save'),
                        ])->key('form-actions'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $current = PageContent::for(ServicesContent::PAGE);

        $upload = $state['hero_image_upload'] ?? null;

        unset($state['hero_image_upload']);

        $currentImage = $current['hero']['image'];

        $state['hero']['image'] = is_string($upload) && $upload !== ''
            ? $upload
            : $currentImage;

        if (is_string($currentImage) && str_starts_with($currentImage, 'images/services/') && $currentImage !== $state['hero']['image']) {
            Storage::disk('public')->delete($currentImage);
        }

        PageContent::store(ServicesContent::PAGE, $state);

        $this->form->fill(PageContent::for(ServicesContent::PAGE));

        Notification::make()
            ->success()
            ->title(__('admin.notifications.services_saved'))
            ->send();
    }
}
