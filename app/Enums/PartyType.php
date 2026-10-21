<?php

namespace App\Enums;

enum PartyType: string
{
    case Client = 'client';
    case Contractor = 'contractor';
    case Consultant = 'consultant';

    public function label(): string
    {
        return match ($this) {
            self::Client => 'Client',
            self::Contractor => 'Contractor',
            self::Consultant => 'Consultant',
        };
    }
}
