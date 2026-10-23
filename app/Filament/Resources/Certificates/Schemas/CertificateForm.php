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
                Section::make(__('admin.sections.certificate'))
                    ->schema([
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
                            ->content(function (?Certificate $record) {
                                $image = $record?->image;

                                return view('filament.forms.image-preview', [
                                    'url' => filled($image) ? Media::url($image) : null,
                                    'isCustom' => filled($image),
                                    'title' => __('admin.titles.certificate_image'),
                                    'hint' => __('admin.descriptions.image_hint_certificate'),
                                    'variant' => 'landscape',
                                ]);
                            })
                            ->columnSpanFull(),
                        FileUpload::make('image_upload')
                            ->label(__('admin.fields.upload_image'))
                            ->helperText(__('admin.descriptions.upload_replace'))
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
                    'name' => Bilingual::text('name', __('admin.fields.name')),
                    'type' => Bilingual::text('type', __('admin.fields.type')),
                ]),
            ]);
    }
}
