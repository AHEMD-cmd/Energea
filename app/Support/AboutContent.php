<?php

namespace App\Support;

class AboutContent
{
    public const PAGE = 'about';

    public const METRIC_ICONS = ['building', 'shield', 'pin', 'gauge'];

    /**
     * Default bilingual content for the about page, taken from the lang files.
     *
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'seo' => [
                'title' => self::translated('about.seo_title'),
                'description' => self::translated('about.seo_description'),
            ],
            'hero' => [
                'image' => null,
                'label' => self::translated('about.hero_label'),
                'title' => self::translated('about.hero_title'),
                'body' => self::translated('about.hero_body'),
                'alt' => self::translated('about.hero_alt'),
            ],
            'snapshot' => [
                'image' => null,
                'label' => self::translated('about.snapshot_label'),
                'title_line' => self::translated('about.snapshot_title_line'),
                'title_em' => self::translated('about.snapshot_title_em'),
                'body' => self::translated('about.snapshot_body'),
                'cta' => self::translated('about.snapshot_cta'),
                'alt' => self::translated('about.snapshot_alt'),
            ],
            'purpose' => [
                'label' => self::translated('about.purpose_label'),
                'title' => self::translated('about.purpose_title'),
            ],
            'mission' => [
                'label' => self::translated('about.mission_label'),
                'title' => self::translated('about.mission_title'),
                'body' => self::translated('about.mission_body'),
            ],
            'vision' => [
                'label' => self::translated('about.vision_label'),
                'title' => self::translated('about.vision_title'),
                'body' => self::translated('about.vision_body'),
            ],
            'values' => [
                'label' => self::translated('about.values_label'),
                'title' => self::translated('about.values_title'),
                'body' => self::translated('about.values_body'),
                'aria' => self::translated('about.values_aria'),
                'items' => self::values(),
            ],
            'metrics' => self::metrics(),
        ];
    }

    /**
     * @return array<int, array{en: string, ar: string}>
     */
    private static function values(): array
    {
        $english = __('about.values', [], 'en');
        $arabic = __('about.values', [], 'ar');

        return collect($english)
            ->values()
            ->map(fn (string $value, int $index): array => [
                'en' => $value,
                'ar' => $arabic[$index] ?? $value,
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function metrics(): array
    {
        $english = __('about.metrics', [], 'en');
        $arabic = __('about.metrics', [], 'ar');

        return collect($english)
            ->values()
            ->map(fn (array $metric, int $index): array => [
                'label' => [
                    'en' => $metric['label'],
                    'ar' => $arabic[$index]['label'] ?? $metric['label'],
                ],
                'value' => $metric['value'],
                'suffix' => $metric['suffix'],
                'caption' => [
                    'en' => $metric['caption'],
                    'ar' => $arabic[$index]['caption'] ?? $metric['caption'],
                ],
            ])
            ->all();
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
