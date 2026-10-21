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
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'company_name' => 'Energea',
            'footer_text' => [
                'en' => 'Energy Solutions Technologies. Hands-on electrical testing and commissioning, alongside comprehensive O&M services for all building systems.',
                'ar' => 'تقنيات حلول الطاقة. اختبارات كهربائية ميدانية وتشغيل تجريبي، إلى جانب خدمات تشغيل وصيانة شاملة لجميع أنظمة المباني.',
            ],
            'general_email' => 'hello@energea.me',
            'quote_recipients' => ['hello@energea.me'],
            'departments' => [
                [
                    'name' => ['en' => 'Electrical Testing', 'ar' => 'الاختبارات الكهربائية'],
                    'phone' => '+966 54 452 0490',
                    'email' => 'Nader@energea.me',
                ],
                [
                    'name' => ['en' => 'Operations & Maintenance', 'ar' => 'التشغيل والصيانة'],
                    'phone' => '+966 55 629 4274',
                    'email' => 'mubarak.rashid@energea.me',
                ],
            ],
            'offices' => [
                [
                    'name' => ['en' => 'Riyadh office', 'ar' => 'مكتب الرياض'],
                    'map_url' => 'https://www.google.com/maps/search/?api=1&query=Riyadh%2C+Saudi+Arabia',
                ],
                [
                    'name' => ['en' => 'Jeddah office', 'ar' => 'مكتب جدة'],
                    'map_url' => 'https://www.google.com/maps/search/?api=1&query=Jeddah%2C+Saudi+Arabia',
                ],
                [
                    'name' => ['en' => 'Al Khobar office', 'ar' => 'مكتب الخبر'],
                    'map_url' => 'https://www.google.com/maps/search/?api=1&query=Al+Khobar%2C+Saudi+Arabia',
                ],
            ],
            'social_links' => [],
            'og_image' => 'images/social-icon-preview.jpg',
        ];
    }

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
