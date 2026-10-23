<?php

namespace App\Filament\Resources\QuoteRequests\Schemas;

use App\Enums\QuoteRequestStatus;
use App\Models\QuoteRequest;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuoteRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.contact'))
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('admin.fields.name')),
                        TextEntry::make('company')
                            ->label(__('admin.fields.company'))
                            ->placeholder(__('admin.placeholders.none')),
                        TextEntry::make('email')
                            ->label(__('admin.fields.email'))
                            ->copyable(),
                        TextEntry::make('phone')
                            ->label(__('admin.fields.phone'))
                            ->copyable(),
                        TextEntry::make('locale')
                            ->label(__('admin.fields.locale'))
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => $state === 'ar'
                                ? __('admin.locale.arabic')
                                : __('admin.locale.english')),
                        TextEntry::make('location')
                            ->label(__('admin.fields.location'))
                            ->state(fn (QuoteRequest $record): string => $record->location_key === 'other'
                                ? (string) $record->location_other
                                : ucfirst((string) $record->location_key)),
                    ])
                    ->columns(3),
                Section::make(__('admin.sections.request'))
                    ->schema([
                        TextEntry::make('service.title.en')
                            ->label(__('admin.fields.service'))
                            ->placeholder(__('admin.placeholders.none')),
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->label(__('admin.fields.received')),
                        TextEntry::make('message')
                            ->label(__('admin.fields.message'))
                            ->columnSpanFull(),
                        TextEntry::make('attachment_path')
                            ->label(__('admin.fields.attachment'))
                            ->placeholder(__('admin.placeholders.none'))
                            ->formatStateUsing(fn (?string $state, QuoteRequest $record): string => $record->attachment_original_name ?: ($state ? basename($state) : __('admin.placeholders.none')))
                            ->url(fn (QuoteRequest $record): ?string => $record->attachment_path
                                ? route('admin.quote-requests.attachment', $record)
                                : null)
                            ->openUrlInNewTab(),
                    ])
                    ->columns(2),
                Section::make(__('admin.sections.delivery'))
                    ->schema([
                        TextEntry::make('status')
                            ->label(__('admin.fields.status'))
                            ->badge()
                            ->formatStateUsing(fn (QuoteRequestStatus $state): string => $state->label()),
                        TextEntry::make('mail_sent_at')
                            ->label(__('admin.fields.emailed'))
                            ->dateTime()
                            ->placeholder(__('admin.placeholders.not_sent')),
                        TextEntry::make('mail_error')
                            ->label(__('admin.fields.mail_error'))
                            ->placeholder(__('admin.placeholders.none'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
