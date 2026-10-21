<?php

namespace App\Support;

class ServicesContent
{
    public const PAGE = 'services';

    /**
     * Default bilingual content for the services page, taken from the lang files.
     *
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'seo' => [
                'title' => self::translated('services.seo_title'),
                'description' => self::translated('services.seo_description'),
            ],
            'hero' => [
                'image' => null,
                'label' => self::translated('services.hero_label'),
                'title' => self::translated('services.hero_title'),
                'body' => self::translated('services.hero_body'),
                'alt' => self::translated('services.hero_alt'),
            ],
            'head' => [
                'label' => self::translated('services.head_label'),
                'title_line' => self::translated('services.head_title_line'),
                'title_em' => self::translated('services.head_title_em'),
                'body' => self::translated('services.head_body'),
            ],
        ];
    }

    /**
     * @return array{en: string, ar: string}
     */
    private static function translated(string $key): array
    {
        return [
            'en' => (string) __($key, [], 'en'),
            'ar' => (string) __($key, [], 'ar'),
        ];
    }
}
