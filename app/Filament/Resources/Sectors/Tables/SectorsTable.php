<?php

namespace App\Filament\Resources\Sectors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SectorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('code')
                    ->label(__('admin.fields.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title.en')
                    ->label(__('admin.fields.title'))
                    ->searchable(),
                TextColumn::make('projects_count')
                    ->counts('projects')
                    ->label(__('admin.fields.projects_count')),
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
