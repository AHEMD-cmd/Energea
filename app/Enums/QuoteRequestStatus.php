<?php

namespace App\Enums;

enum QuoteRequestStatus: string
{
    case New = 'new';
    case Read = 'read';
    case Handled = 'handled';

    public function label(): string
    {
        return match ($this) {
            self::New => 'New',
            self::Read => 'Read',
            self::Handled => 'Handled',
        };
    }
}
