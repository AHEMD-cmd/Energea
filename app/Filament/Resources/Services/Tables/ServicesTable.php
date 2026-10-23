<?php

namespace App\Filament\Resources\Services\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('image')
                    ->label(__('admin.fields.image'))
                    ->disk('public')
                    ->square(),
                TextColumn::make('number')
                    ->label(__('admin.fields.number'))
                    ->sortable(),
                TextColumn::make('title.en')
                    ->label(__('admin.fields.title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('groups_count')
                    ->counts('groups')
                    ->label(__('admin.fields.groups')),
                IconColumn::make('is_published')
                    ->label(__('admin.fields.is_published'))
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label(__('admin.fields.sort_order'))
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
