<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Filament\Support\Bilingual;
use Filament\Forms\Components\FileUpload;
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
                Section::make('Service')
                    ->schema([
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('number')
                            ->required()
                            ->maxLength(10),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        Toggle::make('is_published')
                            ->default(true),
                        FileUpload::make('image')
                            ->image()
                            ->disk('public')
                            ->directory('services')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->columnSpanFull()
                            ->required(),
                    ])
                    ->columns(2),
                Bilingual::tabs([
                    'title' => Bilingual::text('title', 'Title', required: true),
                    'type' => Bilingual::text('type', 'Type', required: true),
                    'summary' => Bilingual::textarea('summary', 'Summary', required: true, rows: 3),
                    'overview' => Bilingual::textarea('overview', 'Overview', required: true, rows: 6),
                ]),
                Tabs::make('process')
                    ->tabs([
                        Tab::make('Steps & facts (English)')
                            ->schema([
                                TagsInput::make('steps.en')
                                    ->label('Steps')
                                    ->helperText('Leave empty to use the default process steps on the website.'),
                                TagsInput::make('facts.en')
                                    ->label('Facts')
                                    ->helperText('Leave empty to use the default facts on the website.'),
                            ]),
                        Tab::make('Steps & facts (العربية)')
                            ->schema([
                                TagsInput::make('steps.ar')
                                    ->label('Steps')
                                    ->extraAttributes(['dir' => 'rtl']),
                                TagsInput::make('facts.ar')
                                    ->label('Facts')
                                    ->extraAttributes(['dir' => 'rtl']),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('SEO')
                    ->schema([
                        TextInput::make('seo_title')
                            ->maxLength(255),
                        Textarea::make('seo_description')
                            ->rows(3)
                            ->maxLength(500),
                    ])
                    ->collapsed()
                    ->columnSpanFull(),
            ]);
    }
}
