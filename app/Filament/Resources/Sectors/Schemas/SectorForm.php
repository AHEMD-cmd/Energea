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
                Section::make(__('admin.sections.sector'))
                    ->schema([
                        TextInput::make('code')
                            ->label(__('admin.fields.code'))
                            ->required()
                            ->maxLength(3)
                            ->unique(ignoreRecord: true),
                        TextInput::make('sort_order')
                            ->label(__('admin.fields.sort_order'))
                            ->numeric()
                            ->default(0)
                            ->required(),
                        Toggle::make('is_published')
                            ->label(__('admin.fields.is_published'))
                            ->default(true),
                    ])
                    ->columns(3),
                Bilingual::tabs([
                    'title' => Bilingual::text('title', __('admin.fields.title'), required: true),
                    'text' => Bilingual::textarea('text', __('admin.fields.text'), required: true, rows: 3),
                ]),
            ]);
    }
}
