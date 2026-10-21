<?php

namespace App\Support;

class HomeContent
{
    public const PAGE = 'home';

    public const ICONS = ['shield', 'gauge', 'document', 'chart'];

    /**
     * Default bilingual content for the home page, taken from the lang files.
     *
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'seo' => [
                'title' => self::translated('home.seo_title'),
                'description' => self::translated('home.seo_description'),
            ],
            'hero' => [
                'image' => null,
                'kicker' => self::translated('home.hero_kicker'),
                'title_line' => self::translated('home.hero_title_line'),
                'title_strong' => self::translated('home.hero_title_strong'),
                'intro' => self::translated('home.hero_intro'),
                'alt' => self::translated('home.hero_alt'),
                'visual_label' => self::translated('home.hero_visual_label'),
                'visual_value' => self::translated('home.hero_visual_value'),
                'visual_caption' => self::translated('home.hero_visual_caption'),
                'cities' => self::translated('home.hero_cities'),
            ],
            'positioning' => [
                'label' => self::translated('home.positioning_label'),
                'title_line' => self::translated('home.positioning_title_line'),
                'title_em' => self::translated('home.positioning_title_em'),
                'point_1' => self::translated('home.positioning_point_1'),
                'point_2' => self::translated('home.positioning_point_2'),
                'body' => self::translated('home.positioning_body'),
                'cta' => self::translated('home.positioning_cta'),
            ],
            'metrics' => self::metrics(),
            'why' => [
                'label' => self::translated('home.why_label'),
                'title_line' => self::translated('home.why_title_line'),
                'title_em' => self::translated('home.why_title_em'),
                'intro' => self::translated('home.why_intro'),
                'cards' => self::whyCards(),
            ],
            'industries' => [
                'label' => self::translated('home.industries_label'),
                'title_line' => self::translated('home.industries_title_line'),
                'title_em' => self::translated('home.industries_title_em'),
                'intro' => self::translated('home.industries_intro'),
            ],
            'final' => [
                'label' => self::translated('home.final_label'),
                'title_line' => self::translated('home.final_title_line'),
                'title_em' => self::translated('home.final_title_em'),
                'body' => self::translated('home.final_body'),
                'alt' => self::translated('home.final_alt'),
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function metrics(): array
    {
        $english = __('home.metrics', [], 'en');
        $arabic = __('home.metrics', [], 'ar');

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
     * @return array<int, array<string, mixed>>
     */
    private static function whyCards(): array
    {
        $english = __('home.why_cards', [], 'en');
        $arabic = __('home.why_cards', [], 'ar');

        return collect($english)
            ->values()
            ->map(fn (array $card, int $index): array => [
                'icon' => self::ICONS[$index] ?? self::ICONS[0],
                'title' => [
                    'en' => $card['title'],
                    'ar' => $arabic[$index]['title'] ?? $card['title'],
                ],
                'body' => [
                    'en' => $card['body'],
                    'ar' => $arabic[$index]['body'] ?? $card['body'],
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
