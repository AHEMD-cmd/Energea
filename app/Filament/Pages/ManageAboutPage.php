<?php

namespace App\Filament\Pages;

use App\Filament\Support\Bilingual;
use App\Models\PageContent;
use App\Support\AboutContent;
use App\Support\Media;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;

class ManageAboutPage extends Page
{
    protected string $view = 'filament.pages.manage-about-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInformationCircle;

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('admin.navigation.groups.content');
    }

    protected static ?int $navigationSort = 1;

    public function getTitle(): string
    {
        return __('admin.pages.about');
    }

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(PageContent::for(AboutContent::PAGE));
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.pages.about');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.seo'))
                    ->description(__('admin.descriptions.about_seo'))
                    ->schema([
                        Bilingual::tabs([
                            'title' => Bilingual::text('title', __('admin.fields.title'), required: true),
                            'description' => Bilingual::textarea('description', __('admin.fields.description'), required: true, rows: 3),
                        ], 'seo'),
                    ])
                    ->collapsible(),
                Section::make(__('admin.sections.hero'))
                    ->description(__('admin.descriptions.about_hero'))
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
                                $image = PageContent::for(AboutContent::PAGE)['hero']['image'];

                                return view('filament.forms.image-preview', [
                                    'url' => Media::url($image, 'assets/images/about-banner.jpeg'),
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
                            ->directory('images/about')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->columnSpanFull(),
                        Toggle::make('hero_image_reset')
                            ->label(__('admin.fields.use_default_hero_image'))
                            ->helperText(__('admin.descriptions.remove_custom_image'))
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                Section::make(__('admin.sections.snapshot'))
                    ->description(__('admin.descriptions.about_intro'))
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', __('admin.fields.section_label'), required: true),
                            'title_line' => Bilingual::text('title_line', __('admin.fields.title_first_line'), required: true),
                            'title_em' => Bilingual::text('title_em', __('admin.fields.title_highlighted'), required: true),
                            'body' => Bilingual::textarea('body', __('admin.fields.body'), required: true),
                            'cta' => Bilingual::text('cta', __('admin.fields.link_label'), required: true),
                            'alt' => Bilingual::text('alt', __('admin.fields.image_alt'), required: true),
                        ], 'snapshot'),
                        Placeholder::make('current_snapshot_image')
                            ->label(__('admin.fields.current_image'))
                            ->content(function () {
                                $image = PageContent::for(AboutContent::PAGE)['snapshot']['image'];

                                return view('filament.forms.image-preview', [
                                    'url' => Media::url($image, 'assets/images/field-electrical-testing.jpeg'),
                                    'isCustom' => is_string($image) && $image !== '',
                                    'title' => __('admin.titles.snapshot_image'),
                                    'hint' => __('admin.descriptions.image_hint_about_snapshot'),
                                    'variant' => 'landscape',
                                ]);
                            })
                            ->columnSpanFull(),
                        FileUpload::make('snapshot_image_upload')
                            ->label(__('admin.fields.replace_image'))
                            ->helperText(__('admin.descriptions.upload_replace_landscape'))
                            ->image()
                            ->disk('public')
                            ->directory('images/about')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->columnSpanFull(),
                        Toggle::make('snapshot_image_reset')
                            ->label(__('admin.fields.use_default_snapshot_image'))
                            ->helperText(__('admin.descriptions.remove_custom_image'))
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                Section::make(__('admin.sections.purpose'))
                    ->description(__('admin.descriptions.about_heading'))
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', __('admin.fields.section_label'), required: true),
                            'title' => Bilingual::text('title', __('admin.fields.title'), required: true),
                        ], 'purpose'),
                    ])
                    ->collapsible(),
                Section::make(__('admin.sections.mission'))
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', __('admin.fields.section_label'), required: true),
                            'title' => Bilingual::text('title', __('admin.fields.title'), required: true),
                            'body' => Bilingual::textarea('body', __('admin.fields.body'), required: true),
                        ], 'mission'),
                    ])
                    ->collapsible(),
                Section::make(__('admin.sections.vision'))
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', __('admin.fields.section_label'), required: true),
                            'title' => Bilingual::text('title', __('admin.fields.title'), required: true),
                            'body' => Bilingual::textarea('body', __('admin.fields.body'), required: true),
                        ], 'vision'),
                    ])
                    ->collapsible(),
                Section::make(__('admin.sections.values'))
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', __('admin.fields.section_label'), required: true),
                            'title' => Bilingual::text('title', __('admin.fields.title'), required: true),
                            'body' => Bilingual::textarea('body', __('admin.fields.body'), required: true),
                            'aria' => Bilingual::text('aria', __('admin.fields.accessible_label'), required: true),
                        ], 'values'),
                        Repeater::make('values.items')
                            ->label(__('admin.sections.values'))
                            ->schema([
                                TextInput::make('en')
                                    ->label(__('admin.fields.value_en'))
                                    ->required(),
                                TextInput::make('ar')
                                    ->label(__('admin.fields.value_ar'))
                                    ->required()
                                    ->extraAttributes(['dir' => 'rtl']),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->reorderable()
                            ->addActionLabel(__('admin.actions.add_value'))
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                Section::make(__('admin.sections.metrics_band'))
                    ->description(__('admin.descriptions.about_metrics'))
                    ->schema([
                        Repeater::make('metrics')
                            ->hiddenLabel()
                            ->schema([
                                TextInput::make('label.en')
                                    ->label(__('admin.fields.label_en'))
                                    ->required(),
                                TextInput::make('label.ar')
                                    ->label(__('admin.fields.label_ar'))
                                    ->required()
                                    ->extraAttributes(['dir' => 'rtl']),
                                TextInput::make('value')
                                    ->label(__('admin.fields.value'))
                                    ->required()
                                    ->maxLength(20),
                                TextInput::make('suffix')
                                    ->label(__('admin.fields.suffix'))
                                    ->maxLength(20),
                                TextInput::make('caption.en')
                                    ->label(__('admin.fields.caption_en'))
                                    ->required(),
                                TextInput::make('caption.ar')
                                    ->label(__('admin.fields.caption_ar'))
                                    ->required()
                                    ->extraAttributes(['dir' => 'rtl']),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->reorderable()
                            ->addActionLabel(__('admin.actions.add_metric'))
                            ->columnSpanFull(),
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
                                ->label(__('admin.actions.save_about'))
                                ->submit('save'),
                        ])->key('form-actions'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $current = PageContent::for(AboutContent::PAGE);

        $images = [
            'hero' => ['upload' => 'hero_image_upload', 'reset' => 'hero_image_reset'],
            'snapshot' => ['upload' => 'snapshot_image_upload', 'reset' => 'snapshot_image_reset'],
        ];

        foreach ($images as $key => $fields) {
            $upload = $state[$fields['upload']] ?? null;
            $reset = (bool) ($state[$fields['reset']] ?? false);

            unset($state[$fields['upload']], $state[$fields['reset']]);

            $currentImage = $current[$key]['image'];

            $state[$key]['image'] = match (true) {
                $reset => null,
                is_string($upload) && $upload !== '' => $upload,
                default => $currentImage,
            };

            if (is_string($currentImage) && str_starts_with($currentImage, 'images/about/') && $currentImage !== $state[$key]['image']) {
                Storage::disk('public')->delete($currentImage);
            }
        }

        PageContent::store(AboutContent::PAGE, $state);

        $this->form->fill(PageContent::for(AboutContent::PAGE));

        Notification::make()
            ->success()
            ->title(__('admin.notifications.about_saved'))
            ->send();
    }
}
