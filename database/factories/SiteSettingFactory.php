<?php

namespace Database\Factories;

use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SiteSetting>
 */
class SiteSettingFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_name' => 'Energea',
            'footer_text' => [
                'en' => 'Energy Solutions Technologies.',
                'ar' => 'تقنيات حلول الطاقة.',
            ],
            'general_email' => 'hello@energea.me',
            'quote_recipients' => ['hello@energea.me'],
            'departments' => [],
            'offices' => [],
            'social_links' => [],
            'og_image' => 'images/social-icon-preview.jpg',
            'ga4_measurement_id' => null,
            'search_console_token' => null,
        ];
    }
}
