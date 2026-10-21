<?php

namespace App\Filament\Resources\Services\RelationManagers;

use App\Filament\Support\Bilingual;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GroupsRelationManager extends RelationManager
{
    protected static string $relationship = 'groups';

    protected static ?string $title = 'Specification groups';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Group')
                    ->schema([
                        Bilingual::tabs([
                            'title' => Bilingual::text('title', 'Group title', required: true),
                        ]),
                        Repeater::make('items')
                            ->relationship('items')
                            ->orderColumn('sort_order')
                            ->label('Items')
                            ->schema([
                                TextInput::make('text.en')
                                    ->label('English')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('text.ar')
                                    ->label('Arabic')
                                    ->required()
                                    ->maxLength(255)
                                    ->extraAttributes(['dir' => 'rtl']),
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->addActionLabel('Add item')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title.en')
                    ->label('Title')
                    ->searchable(),
                TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Items'),
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
