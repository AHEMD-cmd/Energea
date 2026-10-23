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
            self::New => __('admin.enums.quote_status.new'),
            self::Read => __('admin.enums.quote_status.read'),
            self::Handled => __('admin.enums.quote_status.handled'),
        };
    }
}
