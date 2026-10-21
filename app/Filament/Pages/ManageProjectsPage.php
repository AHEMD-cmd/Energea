<?php

namespace App\Filament\Pages;

use App\Filament\Support\Bilingual;
use App\Models\PageContent;
use App\Support\Media;
use App\Support\ProjectsContent;
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

class ManageProjectsPage extends Page
{
    protected string $view = 'filament.pages.manage-projects-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Projects page';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(PageContent::for(ProjectsContent::PAGE));
    }

    public static function getNavigationLabel(): string
    {
        return 'Projects page';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('SEO')
                    ->description('Browser title and search description for the projects page.')
                    ->schema([
                        Bilingual::tabs([
                            'title' => Bilingual::text('title', 'Title', required: true),
                            'description' => Bilingual::textarea('description', 'Description', required: true, rows: 3),
                        ], 'seo'),
                    ])
                    ->collapsible(),
                Section::make('Hero')
                    ->description('The banner at the top of the projects page.')
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
                                $image = PageContent::for(ProjectsContent::PAGE)['hero']['image'];

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
                            ->directory('images/projects')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                Section::make('Projects heading')
                    ->description('The heading above the list of projects.')
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', 'Section label', required: true),
                            'title_line' => Bilingual::text('title_line', 'Title (first line)', required: true),
                            'title_em' => Bilingual::text('title_em', 'Title (highlighted)', required: true),
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
                                ->label('Save projects page')
                                ->submit('save'),
                        ])->key('form-actions'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $current = PageContent::for(ProjectsContent::PAGE);

        $upload = $state['hero_image_upload'] ?? null;

        unset($state['hero_image_upload']);

        $currentImage = $current['hero']['image'];

        $state['hero']['image'] = is_string($upload) && $upload !== ''
            ? $upload
            : $currentImage;

        if (is_string($currentImage) && str_starts_with($currentImage, 'images/projects/') && $currentImage !== $state['hero']['image']) {
            Storage::disk('public')->delete($currentImage);
        }

        PageContent::store(ProjectsContent::PAGE, $state);

        $this->form->fill(PageContent::for(ProjectsContent::PAGE));

        Notification::make()
            ->success()
            ->title('Projects page saved')
            ->send();
    }
}
