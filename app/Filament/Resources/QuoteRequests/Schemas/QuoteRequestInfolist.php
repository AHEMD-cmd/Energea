<?php

namespace App\Filament\Resources\QuoteRequests\Schemas;

use App\Enums\QuoteRequestStatus;
use App\Models\QuoteRequest;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class QuoteRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contact')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('company')
                            ->placeholder('—'),
                        TextEntry::make('email')
                            ->copyable(),
                        TextEntry::make('phone')
                            ->copyable(),
                        TextEntry::make('locale')
                            ->badge(),
                        TextEntry::make('location')
                            ->state(fn (QuoteRequest $record): string => $record->location_key === 'other'
                                ? (string) $record->location_other
                                : ucfirst((string) $record->location_key)),
                    ])
                    ->columns(3),
                Section::make('Request')
                    ->schema([
                        TextEntry::make('service.title.en')
                            ->label('Service')
                            ->placeholder('—'),
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->label('Received'),
                        TextEntry::make('message')
                            ->columnSpanFull(),
                        TextEntry::make('attachment_path')
                            ->label('Attachment')
                            ->placeholder('—')
                            ->formatStateUsing(fn (?string $state): string => $state ? basename($state) : '—')
                            ->url(fn (QuoteRequest $record): ?string => $record->attachment_path
                                ? Storage::disk('public')->url($record->attachment_path)
                                : null)
                            ->openUrlInNewTab(),
                    ])
                    ->columns(2),
                Section::make('Delivery')
                    ->schema([
                        TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn (QuoteRequestStatus $state): string => $state->label()),
                        TextEntry::make('mail_sent_at')
                            ->dateTime()
                            ->placeholder('Not sent'),
                        TextEntry::make('mail_error')
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
