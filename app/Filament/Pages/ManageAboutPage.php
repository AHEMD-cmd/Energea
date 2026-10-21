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

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'About page';

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
        return 'About page';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('SEO')
                    ->description('Browser title and search description for the about page.')
                    ->schema([
                        Bilingual::tabs([
                            'title' => Bilingual::text('title', 'Title', required: true),
                            'description' => Bilingual::textarea('description', 'Description', required: true, rows: 3),
                        ], 'seo'),
                    ])
                    ->collapsible(),
                Section::make('Hero')
                    ->description('The banner at the top of the about page.')
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', 'Section label', required: true),
                            'title' => Bilingual::text('title', 'Title', required: true),
                            'body' => Bilingual::textarea('body', 'Body', required: true),
                            'alt' => Bilingual::text('alt', 'Image alt text', required: true),
                        ], 'hero'),
                        Placeholder::make('current_hero_image')
                            ->label('Current image')
                            ->content(function () {
                                $image = PageContent::for(AboutContent::PAGE)['hero']['image'];

                                return view('filament.forms.image-preview', [
                                    'url' => Media::url($image, 'assets/images/about-banner.jpeg'),
                                    'isCustom' => is_string($image) && $image !== '',
                                    'title' => 'Hero image',
                                    'hint' => 'Wide image, recommended 1600 × 615 px.',
                                    'variant' => 'landscape',
                                ]);
                            })
                            ->columnSpanFull(),
                        FileUpload::make('hero_image_upload')
                            ->label('Replace image')
                            ->helperText('Upload a new image to replace the current one. A wide image (recommended 1600×615) works best.')
                            ->image()
                            ->disk('public')
                            ->directory('images/about')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->columnSpanFull(),
                        Toggle::make('hero_image_reset')
                            ->label('Use the default hero image')
                            ->helperText('Turn on to remove the custom image when saving.')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                Section::make('Snapshot')
                    ->description('The introduction block with the testing image.')
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', 'Section label', required: true),
                            'title_line' => Bilingual::text('title_line', 'Title (first line)', required: true),
                            'title_em' => Bilingual::text('title_em', 'Title (highlighted)', required: true),
                            'body' => Bilingual::textarea('body', 'Body', required: true),
                            'cta' => Bilingual::text('cta', 'Link label', required: true),
                            'alt' => Bilingual::text('alt', 'Image alt text', required: true),
                        ], 'snapshot'),
                        Placeholder::make('current_snapshot_image')
                            ->label('Current image')
                            ->content(function () {
                                $image = PageContent::for(AboutContent::PAGE)['snapshot']['image'];

                                return view('filament.forms.image-preview', [
                                    'url' => Media::url($image, 'assets/images/field-electrical-testing.jpeg'),
                                    'isCustom' => is_string($image) && $image !== '',
                                    'title' => 'Snapshot image',
                                    'hint' => 'Landscape image, recommended 1536 × 1024 px.',
                                    'variant' => 'landscape',
                                ]);
                            })
                            ->columnSpanFull(),
                        FileUpload::make('snapshot_image_upload')
                            ->label('Replace image')
                            ->helperText('Upload a new image to replace the current one. A landscape image (recommended 1536×1024) works best.')
                            ->image()
                            ->disk('public')
                            ->directory('images/about')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->columnSpanFull(),
                        Toggle::make('snapshot_image_reset')
                            ->label('Use the default snapshot image')
                            ->helperText('Turn on to remove the custom image when saving.')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                Section::make('Purpose')
                    ->description('The heading above the mission, vision and values blocks.')
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', 'Section label', required: true),
                            'title' => Bilingual::text('title', 'Title', required: true),
                        ], 'purpose'),
                    ])
                    ->collapsible(),
                Section::make('Mission')
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', 'Section label', required: true),
                            'title' => Bilingual::text('title', 'Title', required: true),
                            'body' => Bilingual::textarea('body', 'Body', required: true),
                        ], 'mission'),
                    ])
                    ->collapsible(),
                Section::make('Vision')
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', 'Section label', required: true),
                            'title' => Bilingual::text('title', 'Title', required: true),
                            'body' => Bilingual::textarea('body', 'Body', required: true),
                        ], 'vision'),
                    ])
                    ->collapsible(),
                Section::make('Values')
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', 'Section label', required: true),
                            'title' => Bilingual::text('title', 'Title', required: true),
                            'body' => Bilingual::textarea('body', 'Body', required: true),
                            'aria' => Bilingual::text('aria', 'Accessible label', required: true),
                        ], 'values'),
                        Repeater::make('values.items')
                            ->label('Values')
                            ->schema([
                                TextInput::make('en')
                                    ->label('Value (English)')
                                    ->required(),
                                TextInput::make('ar')
                                    ->label('Value (Arabic)')
                                    ->required()
                                    ->extraAttributes(['dir' => 'rtl']),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->reorderable()
                            ->addActionLabel('Add value')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                Section::make('Metrics band')
                    ->description('The counters displayed below the purpose section.')
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
                                ->label('Save about page')
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
            ->title('About page saved')
            ->send();
    }
}
