<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Enums\PartyType;
use App\Filament\Support\Bilingual;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PartiesRelationManager extends RelationManager
{
    protected static string $relationship = 'parties';

    protected static ?string $title = 'Project parties';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Party')
                    ->schema([
                        Select::make('party_type')
                            ->options(fn (): array => collect(PartyType::cases())
                                ->mapWithKeys(fn (PartyType $type): array => [$type->value => $type->label()])
                                ->all())
                            ->required(),
                        FileUpload::make('logo')
                            ->image()
                            ->disk('public')
                            ->directory('projects/parties')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->required(),
                        Bilingual::tabs([
                            'name' => Bilingual::text('name', 'Name', required: true),
                        ]),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('logo')
                    ->disk('public')
                    ->square(),
                TextColumn::make('name.en')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('party_type')
                    ->badge()
                    ->formatStateUsing(fn (PartyType $state): string => $state->label()),
                TextColumn::make('sort_order')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
