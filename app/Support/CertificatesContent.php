<?php

namespace App\Support;

class CertificatesContent
{
    public const PAGE = 'certificates';

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'hero' => [
                'image' => null,
                'alt' => self::t('certificates.hero_alt'),
                'label' => self::t('certificates.hero_label'),
                'title' => self::t('certificates.hero_title'),
                'body' => self::t('certificates.hero_body'),
            ],
            'head' => [
                'label' => self::t('certificates.head_label'),
                'title_line' => self::t('certificates.head_title_line'),
                'title_em' => self::t('certificates.head_title_em'),
            ],
            'seo' => [
                'title' => self::t('certificates.seo_title'),
                'description' => self::t('certificates.seo_description'),
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
