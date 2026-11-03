<?php

namespace App\Filament\Resources\QuoteRequests\Tables;

use App\Enums\QuoteRequestStatus;
use App\Models\Service;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuoteRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label(__('admin.fields.received')),
                TextColumn::make('name')
                    ->label(__('admin.fields.name'))
                    ->searchable(),
                TextColumn::make('company')
                    ->label(__('admin.fields.company'))
                    ->searchable()
                    ->placeholder(__('admin.placeholders.none')),
                TextColumn::make('email')
                    ->label(__('admin.fields.email'))
                    ->searchable()
                    ->copyable(),
                TextColumn::make('service.title.en')
                    ->label(__('admin.fields.service'))
                    ->placeholder(__('admin.placeholders.none')),
                TextColumn::make('location_key')
                    ->label(__('admin.fields.location'))
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                SelectColumn::make('status')
                    ->label(__('admin.fields.status'))
                    ->options(fn (): array => collect(QuoteRequestStatus::cases())
                        ->mapWithKeys(fn (QuoteRequestStatus $status): array => [$status->value => $status->label()])
                        ->all()),
                IconColumn::make('mail_sent_at')
                    ->label(__('admin.fields.emailed'))
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('admin.fields.status'))
                    ->options(fn (): array => collect(QuoteRequestStatus::cases())
                        ->mapWithKeys(fn (QuoteRequestStatus $status): array => [$status->value => $status->label()])
                        ->all()),
                SelectFilter::make('service_id')
                    ->label(__('admin.fields.service'))
                    ->options(fn (): array => Service::query()
                        ->orderBy('sort_order')
                        ->get()
                        ->mapWithKeys(fn (Service $service): array => [$service->id => $service->translate('title')])
                        ->all())
                    ->searchable(),
            ])
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
