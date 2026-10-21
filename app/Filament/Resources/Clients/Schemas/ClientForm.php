<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Filament\Support\Bilingual;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Client')
                    ->schema([
                        TextInput::make('logo_code')
                            ->required()
                            ->maxLength(3)
                            ->unique(ignoreRecord: true),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        Toggle::make('is_published')
                            ->default(true),
                        FileUpload::make('logo')
                            ->image()
                            ->disk('public')
                            ->directory('clients')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->columnSpanFull()
                            ->required(),
                    ])
                    ->columns(3),
                Bilingual::tabs([
                    'name' => Bilingual::text('name', 'Name'),
                ]),
            ]);
    }
}
