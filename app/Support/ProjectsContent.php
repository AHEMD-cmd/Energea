<?php

namespace App\Support;

class ProjectsContent
{
    public const PAGE = 'projects';

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'hero' => [
                'image' => null,
                'alt' => self::t('projects.hero_alt'),
                'label' => self::t('projects.hero_label'),
                'title' => self::t('projects.hero_title'),
                'body' => self::t('projects.hero_body'),
            ],
            'head' => [
                'label' => self::t('projects.head_label'),
                'title_line' => self::t('projects.head_title_line'),
                'title_em' => self::t('projects.head_title_em'),
            ],
            'seo' => [
                'title' => self::t('projects.seo_title'),
                'description' => self::t('projects.seo_description'),
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
