<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Filament\Support\Bilingual;
use App\Models\Client;
use App\Support\Media;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
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
                        Placeholder::make('current_logo')
                            ->label('Current logo')
                            ->content(function (?Client $record) {
                                $logo = $record?->logo;

                                return view('filament.forms.image-preview', [
                                    'url' => filled($logo) ? Media::url($logo) : null,
                                    'isCustom' => filled($logo),
                                    'title' => 'Client logo',
                                    'hint' => 'Logo on a transparent or plain background recommended.',
                                    'variant' => 'landscape',
                                ]);
                            })
                            ->columnSpanFull(),
                        FileUpload::make('logo_upload')
                            ->label('Upload logo')
                            ->helperText('Upload a new logo to replace the current one.')
                            ->image()
                            ->disk('public')
                            ->directory('clients')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Bilingual::tabs([
                    'name' => Bilingual::text('name', 'Name'),
                ]),
            ]);
    }
}
