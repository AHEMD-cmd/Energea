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

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 0;

    protected static ?string $title = 'Home page';

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
        return 'Home page';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('SEO')
                    ->description('Browser title and search description for the home page.')
                    ->schema([
                        Bilingual::tabs([
                            'title' => Bilingual::text('title', 'Title', required: true),
                            'description' => Bilingual::textarea('description', 'Description', required: true, rows: 3),
                        ], 'seo'),
                    ])
                    ->collapsible(),
                Section::make('Hero')
                    ->description('The first screen: headline, introduction and visual caption.')
                    ->schema([
                        Bilingual::tabs([
                            'kicker' => Bilingual::text('kicker', 'Kicker', required: true),
                            'title_line' => Bilingual::text('title_line', 'Title (first line)', required: true),
                            'title_strong' => Bilingual::text('title_strong', 'Title (highlighted)', required: true),
                            'intro' => Bilingual::textarea('intro', 'Introduction', required: true),
                            'alt' => Bilingual::text('alt', 'Image alt text', required: true),
                            'visual_label' => Bilingual::text('visual_label', 'Visual card label', required: true),
                            'visual_value' => Bilingual::text('visual_value', 'Visual card value', required: true),
                            'visual_caption' => Bilingual::text('visual_caption', 'Visual card caption', required: true),
                            'cities' => Bilingual::text('cities', 'Cities line', required: true),
                        ], 'hero'),
                        Placeholder::make('current_hero_image')
                            ->label('Current image')
                            ->content(function () {
                                $image = PageContent::for(HomeContent::PAGE)['hero']['image'];

                                return view('filament.forms.image-preview', [
                                    'url' => Media::url($image, 'assets/images/field-panel-desktop.jpeg'),
                                    'isCustom' => is_string($image) && $image !== '',
                                    'title' => 'Hero image',
                                    'hint' => 'Portrait image, recommended 1201 × 1600 px.',
                                ]);
                            })
                            ->columnSpanFull(),
                        FileUpload::make('hero_image_upload')
                            ->label('Replace image')
                            ->helperText('Upload a new image to replace the current one. A portrait image (recommended 1201×1600) works best.')
                            ->image()
                            ->disk('public')
                            ->directory('images/home')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->columnSpanFull(),
                        Toggle::make('hero_image_reset')
                            ->label('Use the default hero image')
                            ->helperText('Turn on to remove the custom image when saving.')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                Section::make('Positioning')
                    ->description('The introduction block that follows the hero.')
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', 'Section label', required: true),
                            'title_line' => Bilingual::text('title_line', 'Title (first line)', required: true),
                            'title_em' => Bilingual::text('title_em', 'Title (highlighted)', required: true),
                            'point_1' => Bilingual::text('point_1', 'Point 1', required: true),
                            'point_2' => Bilingual::text('point_2', 'Point 2', required: true),
                            'body' => Bilingual::textarea('body', 'Body', required: true),
                            'cta' => Bilingual::text('cta', 'Link label', required: true),
                        ], 'positioning'),
                    ])
                    ->collapsible(),
                Section::make('Metrics band')
                    ->description('The counters displayed under the positioning block.')
                    ->schema([
                        Repeater::make('metrics')
                            ->hiddenLabel()
                            ->schema([
                                TextInput::make('label.en')
                                    ->label('Label (English)')
                                    ->required(),
                                TextInput::make('label.ar')
                                    ->label('Label (Arabic)')
                                    ->required()
                                    ->extraAttributes(['dir' => 'rtl']),
                                TextInput::make('value')
                                    ->required()
                                    ->maxLength(20),
                                TextInput::make('suffix')
                                    ->maxLength(20),
                                TextInput::make('caption.en')
                                    ->label('Caption (English)')
                                    ->required(),
                                TextInput::make('caption.ar')
                                    ->label('Caption (Arabic)')
                                    ->required()
                                    ->extraAttributes(['dir' => 'rtl']),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->reorderable()
                            ->addActionLabel('Add metric')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                Section::make('Why Energea')
                    ->description('The dark section with the four reason cards.')
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', 'Section label', required: true),
                            'title_line' => Bilingual::text('title_line', 'Title (first line)', required: true),
                            'title_em' => Bilingual::text('title_em', 'Title (highlighted)', required: true),
                            'intro' => Bilingual::textarea('intro', 'Introduction', required: true, rows: 3),
                        ], 'why'),
                        Repeater::make('why.cards')
                            ->label('Cards')
                            ->schema([
                                Select::make('icon')
                                    ->options(collect(HomeContent::ICONS)->mapWithKeys(
                                        fn (string $icon): array => [$icon => ucfirst($icon)],
                                    )->all())
                                    ->required(),
                                TextInput::make('title.en')
                                    ->label('Title (English)')
                                    ->required(),
                                TextInput::make('title.ar')
                                    ->label('Title (Arabic)')
                                    ->required()
                                    ->extraAttributes(['dir' => 'rtl']),
                                TextInput::make('body.en')
                                    ->label('Body (English)')
                                    ->required(),
                                TextInput::make('body.ar')
                                    ->label('Body (Arabic)')
                                    ->required()
                                    ->extraAttributes(['dir' => 'rtl']),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->reorderable()
                            ->addActionLabel('Add card')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                Section::make('Industries')
                    ->description('The heading above the sector cards. Sector cards themselves are edited under Sectors.')
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', 'Section label', required: true),
                            'title_line' => Bilingual::text('title_line', 'Title (first line)', required: true),
                            'title_em' => Bilingual::text('title_em', 'Title (highlighted)', required: true),
                            'intro' => Bilingual::textarea('intro', 'Introduction', required: true, rows: 3),
                        ], 'industries'),
                    ])
                    ->collapsible(),
                Section::make('Final call to action')
                    ->description('The closing banner at the bottom of the home page.')
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', 'Section label', required: true),
                            'title_line' => Bilingual::text('title_line', 'Title (first line)', required: true),
                            'title_em' => Bilingual::text('title_em', 'Title (highlighted)', required: true),
                            'body' => Bilingual::textarea('body', 'Body', required: true, rows: 3),
                            'alt' => Bilingual::text('alt', 'Image alt text', required: true),
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
                                ->label('Save home page')
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
            ->title('Home page saved')
            ->send();
    }
}
