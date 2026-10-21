<?php

namespace App\Models;

use App\Support\AboutContent;
use App\Support\HomeContent;
use App\Support\ServicesContent;
use Database\Factories\PageContentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['page', 'content'])]
class PageContent extends Model
{
    /** @use HasFactory<PageContentFactory> */
    use HasFactory;

    /**
     * Content for the page, with any saved edits merged over the defaults.
     *
     * @return array<string, mixed>
     */
    public static function for(string $page): array
    {
        $defaults = match ($page) {
            HomeContent::PAGE => HomeContent::defaults(),
            AboutContent::PAGE => AboutContent::defaults(),
            ServicesContent::PAGE => ServicesContent::defaults(),
            default => [],
        };
        $stored = static::query()->where('page', $page)->value('content');

        return static::merge($defaults, is_array($stored) ? $stored : []);
    }

    /**
     * Content resolved to a single locale, ready to be rendered.
     *
     * @return array<string, mixed>
     */
    public static function localized(string $page, ?string $locale = null): array
    {
        return static::localize(static::for($page), $locale ?? app()->getLocale());
    }

    /**
     * @param  array<string, mixed>  $content
     */
    public static function store(string $page, array $content): self
    {
        return static::query()->updateOrCreate(['page' => $page], ['content' => $content]);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'content' => 'array',
        ];
    }

    /**
     * Stored values win, associative arrays are merged so newly added fields
     * keep their defaults, and lists (repeaters) are replaced wholesale.
     *
     * @param  array<string, mixed>  $defaults
     * @param  array<string, mixed>  $stored
     * @return array<string, mixed>
     */
    private static function merge(array $defaults, array $stored): array
    {
        foreach ($stored as $key => $value) {
            $current = $defaults[$key] ?? null;

            $defaults[$key] = is_array($value)
                && is_array($current)
                && ! array_is_list($value)
                && ! array_is_list($current)
                    ? static::merge($current, $value)
                    : $value;
        }

        return $defaults;
    }

    private static function localize(mixed $value, string $locale): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        if (static::isTranslation($value)) {
            return $value[$locale] ?? $value[config('energea.default_locale')] ?? reset($value);
        }

        return array_map(fn (mixed $item): mixed => static::localize($item, $locale), $value);
    }

    /**
     * @param  array<mixed>  $value
     */
    private static function isTranslation(array $value): bool
    {
        $keys = array_keys($value);
        sort($keys);

        return $keys === ['ar', 'en']
            && ! is_array($value['en'])
            && ! is_array($value['ar']);
    }
}
