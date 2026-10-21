<?php

namespace App\Filament\Resources\Certificates\Schemas;

use App\Filament\Support\Bilingual;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CertificateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Certificate')
                    ->schema([
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        Toggle::make('is_published')
                            ->default(true),
                        FileUpload::make('image')
                            ->image()
                            ->disk('public')
                            ->directory('certificates')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->columnSpanFull()
                            ->required(),
                    ])
                    ->columns(3),
                Bilingual::tabs([
                    'name' => Bilingual::text('name', 'Name'),
                    'type' => Bilingual::text('type', 'Type'),
                ]),
            ]);
    }
}
