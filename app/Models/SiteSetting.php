<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Database\Factories\SiteSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'company_name',
    'footer_text',
    'general_email',
    'quote_recipients',
    'departments',
    'offices',
    'social_links',
    'og_image',
    'ga4_measurement_id',
    'search_console_token',
])]
class SiteSetting extends Model
{
    /** @use HasFactory<SiteSettingFactory> */
    use HasFactory, HasTranslations;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'footer_text' => 'array',
            'quote_recipients' => 'array',
            'departments' => 'array',
            'offices' => 'array',
            'social_links' => 'array',
        ];
    }
}
