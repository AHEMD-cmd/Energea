<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Enums\PartyType;
use App\Filament\Support\Bilingual;
use App\Filament\Support\ImageUpload;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PartiesRelationManager extends RelationManager
{
    protected static string $relationship = 'parties';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('admin.titles.project_parties');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.party'))
                    ->schema([
                        Select::make('party_type')
                            ->label(__('admin.fields.party_type'))
                            ->options(fn (): array => collect(PartyType::cases())
                                ->mapWithKeys(fn (PartyType $type): array => [$type->value => $type->label()])
                                ->all())
                            ->required(),
                        ImageUpload::make('logo', 'projects/parties', label: __('admin.fields.logo')),
                        Bilingual::tabs([
                            'name' => Bilingual::text('name', __('admin.fields.name'), required: true),
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
                    ->label(__('admin.fields.logo'))
                    ->disk('public')
                    ->square(),
                TextColumn::make('name.en')
                    ->label(__('admin.fields.name'))
                    ->searchable(),
                TextColumn::make('party_type')
                    ->label(__('admin.fields.party_type'))
                    ->badge()
                    ->formatStateUsing(fn (PartyType $state): string => $state->label()),
                TextColumn::make('sort_order')
                    ->label(__('admin.fields.sort_order'))
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
