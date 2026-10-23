<?php

namespace App\Filament\Pages;

use App\Filament\Support\Bilingual;
use App\Models\PageContent;
use App\Support\HomeContent;
use App\Support\Media;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
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

class ManageHomePage extends Page
{
    protected string $view = 'filament.pages.manage-home-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('admin.navigation.groups.content');
    }

    protected static ?int $navigationSort = 0;

    public function getTitle(): string
    {
        return __('admin.pages.home');
    }

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(PageContent::for(HomeContent::PAGE));
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.pages.home');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.seo'))
                    ->description(__('admin.descriptions.home_seo'))
                    ->schema([
                        Bilingual::tabs([
                            'title' => Bilingual::text('title', __('admin.fields.title'), required: true),
                            'description' => Bilingual::textarea('description', __('admin.fields.description'), required: true, rows: 3),
                        ], 'seo'),
                    ])
                    ->collapsible(),
                Section::make(__('admin.sections.hero'))
                    ->description(__('admin.descriptions.home_hero'))
                    ->schema([
                        Bilingual::tabs([
                            'kicker' => Bilingual::text('kicker', __('admin.fields.kicker'), required: true),
                            'title_line' => Bilingual::text('title_line', __('admin.fields.title_first_line'), required: true),
                            'title_strong' => Bilingual::text('title_strong', __('admin.fields.title_highlighted'), required: true),
                            'intro' => Bilingual::textarea('intro', __('admin.fields.introduction'), required: true),
                            'alt' => Bilingual::text('alt', __('admin.fields.image_alt'), required: true),
                            'visual_label' => Bilingual::text('visual_label', __('admin.fields.visual_card_label'), required: true),
                            'visual_value' => Bilingual::text('visual_value', __('admin.fields.visual_card_value'), required: true),
                            'visual_caption' => Bilingual::text('visual_caption', __('admin.fields.visual_card_caption'), required: true),
                            'cities' => Bilingual::text('cities', __('admin.fields.cities_line'), required: true),
                        ], 'hero'),
                        Placeholder::make('current_hero_image')
                            ->label(__('admin.fields.current_image'))
                            ->content(function () {
                                $image = PageContent::for(HomeContent::PAGE)['hero']['image'];

                                return view('filament.forms.image-preview', [
                                    'url' => Media::url($image, 'assets/images/field-panel-desktop.jpeg'),
                                    'isCustom' => is_string($image) && $image !== '',
                                    'title' => __('admin.sections.hero'),
                                    'hint' => __('admin.descriptions.image_hint_portrait'),
                                ]);
                            })
                            ->columnSpanFull(),
                        FileUpload::make('hero_image_upload')
                            ->label(__('admin.fields.replace_image'))
                            ->helperText(__('admin.descriptions.image_replace'))
                            ->image()
                            ->disk('public')
                            ->directory('images/home')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->columnSpanFull(),
                        Toggle::make('hero_image_reset')
                            ->label(__('admin.fields.use_default_hero_image'))
                            ->helperText(__('admin.descriptions.remove_custom_image'))
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make(__('admin.sections.positioning'))
                    ->description(__('admin.descriptions.home_positioning'))
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', __('admin.fields.section_label'), required: true),
                            'title_line' => Bilingual::text('title_line', __('admin.fields.title_first_line'), required: true),
                            'title_em' => Bilingual::text('title_em', __('admin.fields.title_highlighted'), required: true),
                            'point_1' => Bilingual::text('point_1', __('admin.fields.point_1'), required: true),
                            'point_2' => Bilingual::text('point_2', __('admin.fields.point_2'), required: true),
                            'body' => Bilingual::textarea('body', __('admin.fields.body'), required: true),
                            'cta' => Bilingual::text('cta', __('admin.fields.link_label'), required: true),
                        ], 'positioning'),
                    ])
                    ->collapsible(),
                Section::make(__('admin.sections.metrics_band'))
                    ->description(__('admin.descriptions.home_metrics'))
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
                Section::make(__('admin.sections.why_energea'))
                    ->description(__('admin.descriptions.home_why'))
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', __('admin.fields.section_label'), required: true),
                            'title_line' => Bilingual::text('title_line', __('admin.fields.title_first_line'), required: true),
                            'title_em' => Bilingual::text('title_em', __('admin.fields.title_highlighted'), required: true),
                            'intro' => Bilingual::textarea('intro', __('admin.fields.introduction'), required: true, rows: 3),
                        ], 'why'),
                        Repeater::make('why.cards')
                            ->label(__('admin.sections.cards'))
                            ->schema([
                                Select::make('icon')
                                    ->label(__('admin.fields.icon'))
                                    ->options(collect(HomeContent::ICONS)->mapWithKeys(
                                        fn (string $icon): array => [$icon => ucfirst($icon)],
                                    )->all())
                                    ->required(),
                                TextInput::make('title.en')
                                    ->label(__('admin.fields.title_en'))
                                    ->required(),
                                TextInput::make('title.ar')
                                    ->label(__('admin.fields.title_ar'))
                                    ->required()
                                    ->extraAttributes(['dir' => 'rtl']),
                                TextInput::make('body.en')
                                    ->label(__('admin.fields.body_en'))
                                    ->required(),
                                TextInput::make('body.ar')
                                    ->label(__('admin.fields.body_ar'))
                                    ->required()
                                    ->extraAttributes(['dir' => 'rtl']),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->reorderable()
                            ->addActionLabel(__('admin.actions.add_card'))
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                Section::make(__('admin.sections.industries'))
                    ->description(__('admin.descriptions.home_sectors'))
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', __('admin.fields.section_label'), required: true),
                            'title_line' => Bilingual::text('title_line', __('admin.fields.title_first_line'), required: true),
                            'title_em' => Bilingual::text('title_em', __('admin.fields.title_highlighted'), required: true),
                            'intro' => Bilingual::textarea('intro', __('admin.fields.introduction'), required: true, rows: 3),
                        ], 'industries'),
                    ])
                    ->collapsible(),
                Section::make(__('admin.sections.final_cta'))
                    ->description(__('admin.descriptions.home_cta'))
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', __('admin.fields.section_label'), required: true),
                            'title_line' => Bilingual::text('title_line', __('admin.fields.title_first_line'), required: true),
                            'title_em' => Bilingual::text('title_em', __('admin.fields.title_highlighted'), required: true),
                            'body' => Bilingual::textarea('body', __('admin.fields.body'), required: true, rows: 3),
                            'alt' => Bilingual::text('alt', __('admin.fields.image_alt'), required: true),
                        ], 'final'),
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
                                ->label(__('admin.actions.save_home'))
                                ->submit('save'),
                        ])->key('form-actions'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $upload = $state['hero_image_upload'] ?? null;
        $resetImage = (bool) ($state['hero_image_reset'] ?? false);

        unset($state['hero_image_upload'], $state['hero_image_reset']);

        $current = PageContent::for(HomeContent::PAGE);
        $currentImage = $current['hero']['image'];

        $state['hero']['image'] = match (true) {
            $resetImage => null,
            is_string($upload) && $upload !== '' => $upload,
            default => $currentImage,
        };

        PageContent::store(HomeContent::PAGE, $state);

        if (is_string($currentImage) && str_starts_with($currentImage, 'images/home/') && $currentImage !== $state['hero']['image']) {
            Storage::disk('public')->delete($currentImage);
        }

        $this->form->fill(PageContent::for(HomeContent::PAGE));

        Notification::make()
            ->success()
            ->title(__('admin.notifications.home_saved'))
            ->send();
    }
}
