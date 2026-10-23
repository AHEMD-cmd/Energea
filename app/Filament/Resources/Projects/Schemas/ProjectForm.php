<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Filament\Support\Bilingual;
use App\Models\Project;
use App\Models\Sector;
use App\Support\Media;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.project'))
                    ->schema([
                        TextInput::make('slug')
                            ->label(__('admin.fields.slug'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('year')
                            ->label(__('admin.fields.year'))
                            ->required()
                            ->maxLength(10),
                        Select::make('sector_id')
                            ->relationship('sector', 'id')
                            ->getOptionLabelFromRecordUsing(fn (Sector $record): string => $record->translate('title'))
                            ->searchable()
                            ->preload()
                            ->label(__('admin.fields.sector')),
                        TextInput::make('sort_order')
                            ->label(__('admin.fields.sort_order'))
                            ->numeric()
                            ->default(0)
                            ->required(),
                        Toggle::make('is_published')
                            ->label(__('admin.fields.is_published'))
                            ->default(true),
                        Placeholder::make('current_image')
                            ->label(__('admin.fields.current_image'))
                            ->content(function (?Project $record) {
                                $image = $record?->image;

                                return view('filament.forms.image-preview', [
                                    'url' => filled($image) ? Media::url($image) : null,
                                    'isCustom' => filled($image),
                                    'title' => __('admin.titles.project_image'),
                                    'hint' => __('admin.descriptions.image_hint_landscape'),
                                    'variant' => 'landscape',
                                ]);
                            })
                            ->columnSpanFull(),
                        FileUpload::make('image_upload')
                            ->label(__('admin.fields.upload_image'))
                            ->helperText(__('admin.descriptions.upload_replace'))
                            ->image()
                            ->disk('public')
                            ->directory('projects')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Bilingual::tabs([
                    'title' => Bilingual::text('title', __('admin.fields.title'), required: true),
                    'location' => Bilingual::text('location', __('admin.fields.location'), required: true),
                    'scope' => Bilingual::textarea('scope', __('admin.fields.scope'), required: true, rows: 3),
                    'systems' => Bilingual::textarea('systems', __('admin.fields.systems'), rows: 3),
                ]),
                Section::make(__('admin.sections.seo'))
                    ->schema([
                        TextInput::make('seo_title')
                            ->label(__('admin.fields.seo_title'))
                            ->maxLength(255),
                        Textarea::make('seo_description')
                            ->label(__('admin.fields.seo_description'))
                            ->rows(3)
                            ->maxLength(500),
                    ])
                    ->collapsed()
                    ->columnSpanFull(),
            ]);
    }
}
