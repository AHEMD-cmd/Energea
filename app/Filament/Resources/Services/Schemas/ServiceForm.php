<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Filament\Support\Bilingual;
use App\Models\Service;
use App\Support\Media;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.service'))
                    ->schema([
                        TextInput::make('slug')
                            ->label(__('admin.fields.slug'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('number')
                            ->label(__('admin.fields.number'))
                            ->required()
                            ->maxLength(10),
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
                            ->content(function (?Service $record) {
                                $image = $record?->image;

                                return view('filament.forms.image-preview', [
                                    'url' => filled($image) ? Media::url($image) : null,
                                    'isCustom' => filled($image),
                                    'title' => __('admin.titles.service_image'),
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
                            ->directory('services')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Bilingual::tabs([
                    'title' => Bilingual::text('title', __('admin.fields.title'), required: true),
                    'type' => Bilingual::text('type', __('admin.fields.type'), required: true),
                    'summary' => Bilingual::textarea('summary', __('admin.fields.summary'), required: true, rows: 3),
                    'overview' => Bilingual::textarea('overview', __('admin.fields.overview'), required: true, rows: 6),
                ]),
                Tabs::make('process')
                    ->tabs([
                        Tab::make(__('admin.sections.steps_facts_en'))
                            ->schema([
                                TagsInput::make('steps.en')
                                    ->label(__('admin.fields.steps'))
                                    ->helperText(__('admin.descriptions.steps_helper')),
                                TagsInput::make('facts.en')
                                    ->label(__('admin.fields.facts'))
                                    ->helperText(__('admin.descriptions.facts_helper')),
                            ]),
                        Tab::make(__('admin.sections.steps_facts_ar'))
                            ->schema([
                                TagsInput::make('steps.ar')
                                    ->label(__('admin.fields.steps'))
                                    ->extraAttributes(['dir' => 'rtl']),
                                TagsInput::make('facts.ar')
                                    ->label(__('admin.fields.facts'))
                                    ->extraAttributes(['dir' => 'rtl']),
                            ]),
                    ])
                    ->columnSpanFull(),
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
