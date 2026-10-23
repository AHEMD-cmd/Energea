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
use Illuminate\Database\Eloquent\Model;

class GroupsRelationManager extends RelationManager
{
    protected static string $relationship = 'groups';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('admin.titles.specification_groups');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.group'))
                    ->schema([
                        Bilingual::tabs([
                            'title' => Bilingual::text('title', __('admin.fields.title'), required: true),
                        ]),
                        Repeater::make('items')
                            ->relationship('items')
                            ->orderColumn('sort_order')
                            ->label(__('admin.fields.items'))
                            ->schema([
                                TextInput::make('text.en')
                                    ->label(__('admin.fields.title_en'))
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('text.ar')
                                    ->label(__('admin.fields.title_ar'))
                                    ->required()
                                    ->maxLength(255)
                                    ->extraAttributes(['dir' => 'rtl']),
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->addActionLabel(__('admin.actions.add_item'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title.en')
                    ->label(__('admin.fields.title'))
                    ->searchable(),
                TextColumn::make('items_count')
                    ->counts('items')
                    ->label(__('admin.fields.items')),
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
