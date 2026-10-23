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

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('admin.navigation.groups.content');
    }

    public function getTitle(): string
    {
        return __('admin.pages.clients');
    }

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
        return __('admin.pages.clients');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.seo'))
                    ->description(__('admin.descriptions.clients_seo'))
                    ->schema([
                        Bilingual::tabs([
                            'title' => Bilingual::text('title', __('admin.fields.title'), required: true),
                            'description' => Bilingual::textarea('description', __('admin.fields.description'), required: true, rows: 3),
                        ], 'seo'),
                    ])
                    ->collapsible(),
                Section::make(__('admin.sections.hero'))
                    ->description(__('admin.descriptions.clients_hero'))
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
                                $image = PageContent::for(ClientsContent::PAGE)['hero']['image'];

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
                            ->directory('images/clients')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                Section::make(__('admin.sections.clients_heading'))
                    ->description(__('admin.descriptions.clients_intro'))
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', __('admin.fields.section_label'), required: true),
                            'title_line' => Bilingual::text('title_line', __('admin.fields.title_first_line'), required: true),
                            'title_em' => Bilingual::text('title_em', __('admin.fields.title_highlighted'), required: true),
                            'side_title' => Bilingual::text('side_title', __('admin.fields.side_card_title'), required: true),
                            'side_body' => Bilingual::textarea('side_body', __('admin.fields.side_card_body'), required: true, rows: 3),
                        ], 'head'),
                    ])
                    ->collapsible(),
                Section::make(__('admin.sections.final_cta'))
                    ->description(__('admin.descriptions.clients_cta'))
                    ->schema([
                        Bilingual::tabs([
                            'label' => Bilingual::text('label', __('admin.fields.section_label'), required: true),
                            'title_line' => Bilingual::text('title_line', __('admin.fields.title_first_line'), required: true),
                            'title_em' => Bilingual::text('title_em', __('admin.fields.title_highlighted'), required: true),
                            'cta' => Bilingual::text('cta', __('admin.fields.button_text'), required: true),
                            'alt' => Bilingual::text('alt', __('admin.fields.image_alt'), required: true),
                        ], 'final'),
                        Placeholder::make('current_final_image')
                            ->label(__('admin.fields.current_image'))
                            ->content(function () {
                                $image = PageContent::for(ClientsContent::PAGE)['final']['image'];

                                return view('filament.forms.image-preview', [
                                    'url' => Media::url($image, 'assets/images/projects/damac-data-center.webp'),
                                    'isCustom' => is_string($image) && $image !== '',
                                    'title' => __('admin.titles.call_to_action_image'),
                                    'hint' => __('admin.descriptions.image_hint_cta'),
                                    'variant' => 'landscape',
                                ]);
                            })
                            ->columnSpanFull(),
                        FileUpload::make('final_image_upload')
                            ->label(__('admin.fields.replace_image'))
                            ->helperText(__('admin.descriptions.upload_replace'))
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
                                ->label(__('admin.actions.save_clients'))
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
            ->title(__('admin.notifications.clients_saved'))
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
