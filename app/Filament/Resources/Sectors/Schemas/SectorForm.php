<?php

namespace App\Filament\Resources\Sectors\Schemas;

use App\Filament\Support\Bilingual;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SectorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Sector')
                    ->schema([
                        TextInput::make('code')
                            ->required()
                            ->maxLength(3)
                            ->unique(ignoreRecord: true),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        Toggle::make('is_published')
                            ->default(true),
                    ])
                    ->columns(3),
                Bilingual::tabs([
                    'title' => Bilingual::text('title', 'Title', required: true),
                    'text' => Bilingual::textarea('text', 'Description', required: true, rows: 3),
                ]),
            ]);
    }
}
