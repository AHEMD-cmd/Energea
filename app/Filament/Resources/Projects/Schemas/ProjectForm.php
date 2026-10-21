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
                Section::make('Project')
                    ->schema([
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('year')
                            ->required()
                            ->maxLength(10),
                        Select::make('sector_id')
                            ->relationship('sector', 'id')
                            ->getOptionLabelFromRecordUsing(fn (Sector $record): string => $record->translate('title'))
                            ->searchable()
                            ->preload()
                            ->label('Sector'),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        Toggle::make('is_published')
                            ->default(true),
                        Placeholder::make('current_image')
                            ->label('Current image')
                            ->content(function (?Project $record) {
                                $image = $record?->image;

                                return view('filament.forms.image-preview', [
                                    'url' => filled($image) ? Media::url($image) : null,
                                    'isCustom' => filled($image),
                                    'title' => 'Project image',
                                    'hint' => 'Landscape image recommended.',
                                    'variant' => 'landscape',
                                ]);
                            })
                            ->columnSpanFull(),
                        FileUpload::make('image_upload')
                            ->label('Upload image')
                            ->helperText('Upload a new image to replace the current one.')
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
                    'title' => Bilingual::text('title', 'Title', required: true),
                    'location' => Bilingual::text('location', 'Location', required: true),
                    'scope' => Bilingual::textarea('scope', 'Scope', required: true, rows: 3),
                    'systems' => Bilingual::textarea('systems', 'Systems', rows: 3),
                ]),
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
