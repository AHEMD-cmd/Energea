<?php

namespace App\Support;

class ClientsContent
{
    public const PAGE = 'clients';

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'hero' => [
                'image' => null,
                'alt' => self::t('clients.hero_alt'),
                'label' => self::t('clients.hero_label'),
                'title' => self::t('clients.hero_title'),
                'body' => self::t('clients.hero_body'),
            ],
            'head' => [
                'label' => self::t('clients.head_label'),
                'title_line' => self::t('clients.head_title_line'),
                'title_em' => self::t('clients.head_title_em'),
                'side_title' => self::t('clients.side_title'),
                'side_body' => self::t('clients.side_body'),
            ],
            'final' => [
                'image' => null,
                'alt' => self::t('clients.final_alt'),
                'label' => self::t('clients.final_label'),
                'title_line' => self::t('clients.final_title_line'),
                'title_em' => self::t('clients.final_title_em'),
                'cta' => self::t('clients.final_cta'),
            ],
            'seo' => [
                'title' => self::t('clients.seo_title'),
                'description' => self::t('clients.seo_description'),
            ],
        ];
    }

    /**
     * @return array{en: string, ar: string}
     */
    private static function t(string $key): array
    {
        return [
            'en' => __($key, [], 'en'),
            'ar' => __($key, [], 'ar'),
        ];
    }
}
