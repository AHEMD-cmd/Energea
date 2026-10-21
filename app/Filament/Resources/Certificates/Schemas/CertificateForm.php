<?php

namespace App\Filament\Resources\Certificates\Schemas;

use App\Filament\Support\Bilingual;
use App\Models\Certificate;
use App\Support\Media;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
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
                        Placeholder::make('current_image')
                            ->label('Current image')
                            ->content(function (?Certificate $record) {
                                $image = $record?->image;

                                return view('filament.forms.image-preview', [
                                    'url' => filled($image) ? Media::url($image) : null,
                                    'isCustom' => filled($image),
                                    'title' => 'Certificate image',
                                    'hint' => 'Clear scan or photo of the certificate recommended.',
                                    'variant' => 'landscape',
                                ]);
                            })
                            ->columnSpanFull(),
                        FileUpload::make('image_upload')
                            ->label('Upload image')
                            ->helperText('Upload a new image to replace the current one.')
                            ->image()
                            ->disk('public')
                            ->directory('certificates')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Bilingual::tabs([
                    'name' => Bilingual::text('name', 'Name'),
                    'type' => Bilingual::text('type', 'Type'),
                ]),
            ]);
    }
}
