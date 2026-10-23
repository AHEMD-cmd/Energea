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
            self::Client => __('admin.enums.party.client'),
            self::Contractor => __('admin.enums.party.contractor'),
            self::Consultant => __('admin.enums.party.consultant'),
        };
    }
}
