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

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 6;

    protected static ?string $title = 'Contact page';

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
        return 'Contact page';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('SEO')
                    ->description('Browser title and search description for the contact page.')
                    ->schema([
                        Bilingual::tabs([
                            'title' => Bilingual::text('title', 'Title', required: true),
                            'description' => Bilingual::textarea('description', 'Description', required: true, rows: 3),
                        ], 'seo'),
                    ])
                    ->collapsible(),
                Section::make('Hero')
                    ->description('The banner at the top of the contact page.')
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
                                $image = PageContent::for(ContactContent::PAGE)['hero']['image'];

                                return view('filament.forms.image-preview', [
                                    'url' => Media::url($image, 'assets/images/field-electrical-testing.jpeg'),
                                    'isCustom' => is_string($image) && $image !== '',
                                    'title' => 'Hero image',
                                    'hint' => 'Wide image, recommended 1536 × 1024 px.',
                                    'variant' => 'landscape',
                                ]);
                            })
                            ->columnSpanFull(),
                        FileUpload::make('hero_image_upload')
                            ->label('Replace image')
                            ->helperText('Upload a new image to replace the current one.')
                            ->image()
                            ->disk('public')
                            ->directory('images/contact')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                Section::make('Contact card')
                    ->description('Labels on the card beside the form. Departments, offices, and the general email are managed in Settings.')
                    ->schema([
                        Bilingual::tabs([
                            'direct_label' => Bilingual::text('direct_label', 'Direct contact label', required: true),
                            'general_email' => Bilingual::text('general_email', 'General email label', required: true),
                            'offices' => Bilingual::text('offices', 'Offices heading', required: true),
                        ], 'card'),
                    ])
                    ->collapsible(),
                Section::make('Form heading')
                    ->description('The heading above the quote request form.')
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', 'Section label', required: true),
                            'title' => Bilingual::text('title', 'Title', required: true),
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
                                ->label('Save contact page')
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
            ->title('Contact page saved')
            ->send();
    }
}
