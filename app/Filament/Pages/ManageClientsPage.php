<?php

namespace App\Filament\Pages;

use App\Filament\Support\Bilingual;
use App\Models\PageContent;
use App\Support\ClientsContent;
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

class ManageClientsPage extends Page
{
    protected string $view = 'filament.pages.manage-clients-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 5;

    protected static ?string $title = 'Clients page';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(PageContent::for(ClientsContent::PAGE));
    }

    public static function getNavigationLabel(): string
    {
        return 'Clients page';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('SEO')
                    ->description('Browser title and search description for the clients page.')
                    ->schema([
                        Bilingual::tabs([
                            'title' => Bilingual::text('title', 'Title', required: true),
                            'description' => Bilingual::textarea('description', 'Description', required: true, rows: 3),
                        ], 'seo'),
                    ])
                    ->collapsible(),
                Section::make('Hero')
                    ->description('The banner at the top of the clients page.')
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
                                $image = PageContent::for(ClientsContent::PAGE)['hero']['image'];

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
                            ->directory('images/clients')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                Section::make('Clients heading')
                    ->description('The heading and side card above the list of clients.')
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', 'Section label', required: true),
                            'title_line' => Bilingual::text('title_line', 'Title (first line)', required: true),
                            'title_em' => Bilingual::text('title_em', 'Title (highlighted)', required: true),
                            'side_title' => Bilingual::text('side_title', 'Side card title', required: true),
                            'side_body' => Bilingual::textarea('side_body', 'Side card body', required: true, rows: 3),
                        ], 'head'),
                    ])
                    ->collapsible(),
                Section::make('Final call to action')
                    ->description('The banner at the bottom of the clients page.')
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', 'Section label', required: true),
                            'title_line' => Bilingual::text('title_line', 'Title (first line)', required: true),
                            'title_em' => Bilingual::text('title_em', 'Title (highlighted)', required: true),
                            'cta' => Bilingual::text('cta', 'Button text', required: true),
                            'alt' => Bilingual::text('alt', 'Image alt text', required: true),
                        ], 'final'),
                        Placeholder::make('current_final_image')
                            ->label('Current image')
                            ->content(function () {
                                $image = PageContent::for(ClientsContent::PAGE)['final']['image'];

                                return view('filament.forms.image-preview', [
                                    'url' => Media::url($image, 'assets/images/projects/damac-data-center.webp'),
                                    'isCustom' => is_string($image) && $image !== '',
                                    'title' => 'Call to action image',
                                    'hint' => 'Recommended around 492 × 448 px.',
                                    'variant' => 'landscape',
                                ]);
                            })
                            ->columnSpanFull(),
                        FileUpload::make('final_image_upload')
                            ->label('Replace image')
                            ->helperText('Upload a new image to replace the current one.')
                            ->image()
                            ->disk('public')
                            ->directory('images/clients')
                            ->visibility('public')
                            ->maxSize(5120)
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
                                ->label('Save clients page')
                                ->submit('save'),
                        ])->key('form-actions'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $current = PageContent::for(ClientsContent::PAGE);

        $heroUpload = $state['hero_image_upload'] ?? null;
        $finalUpload = $state['final_image_upload'] ?? null;

        unset($state['hero_image_upload'], $state['final_image_upload']);

        $state['hero']['image'] = $this->resolveImage($heroUpload, $current['hero']['image']);
        $state['final']['image'] = $this->resolveImage($finalUpload, $current['final']['image']);

        PageContent::store(ClientsContent::PAGE, $state);

        $this->form->fill(PageContent::for(ClientsContent::PAGE));

        Notification::make()
            ->success()
            ->title('Clients page saved')
            ->send();
    }

    /**
     * Use the new upload if there is one, and delete the replaced file
     * when it was one of this page's own uploads.
     */
    private function resolveImage(mixed $upload, mixed $currentImage): mixed
    {
        $image = is_string($upload) && $upload !== '' ? $upload : $currentImage;

        if (is_string($currentImage)
            && str_starts_with($currentImage, 'images/clients/')
            && $currentImage !== $image) {
            Storage::disk('public')->delete($currentImage);
        }

        return $image;
    }
}
