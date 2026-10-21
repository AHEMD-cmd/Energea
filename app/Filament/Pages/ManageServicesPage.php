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

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Services page';

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
        return 'Services page';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('SEO')
                    ->description('Browser title and search description for the services page.')
                    ->schema([
                        Bilingual::tabs([
                            'title' => Bilingual::text('title', 'Title', required: true),
                            'description' => Bilingual::textarea('description', 'Description', required: true, rows: 3),
                        ], 'seo'),
                    ])
                    ->collapsible(),
                Section::make('Hero')
                    ->description('The banner at the top of the services page.')
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
                                $image = PageContent::for(ServicesContent::PAGE)['hero']['image'];

                                return view('filament.forms.image-preview', [
                                    'url' => Media::url($image, 'assets/images/services-banner.jpeg'),
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
                            ->directory('images/services')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                Section::make('Capabilities heading')
                    ->description('The heading above the list of services.')
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', 'Section label', required: true),
                            'title_line' => Bilingual::text('title_line', 'Title (first line)', required: true),
                            'title_em' => Bilingual::text('title_em', 'Title (highlighted)', required: true),
                            'body' => Bilingual::textarea('body', 'Body', required: true),
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
                                ->label('Save services page')
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
            ->title('Services page saved')
            ->send();
    }
}
