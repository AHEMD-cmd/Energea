<?php

namespace App\Support;

class ContactContent
{
    public const PAGE = 'contact';

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'hero' => [
                'image' => null,
                'alt' => self::t('contact.hero_alt'),
                'label' => self::t('contact.hero_label'),
                'title' => self::t('contact.hero_title'),
                'body' => self::t('contact.hero_body'),
            ],
            'card' => [
                'direct_label' => self::t('contact.direct_label'),
                'general_email' => self::t('contact.general_email'),
                'offices' => self::t('contact.offices'),
            ],
            'form' => [
                'label' => self::t('contact.form_label'),
                'title' => self::t('contact.form_title'),
            ],
            'seo' => [
                'title' => self::t('contact.seo_title'),
                'description' => self::t('contact.seo_description'),
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
