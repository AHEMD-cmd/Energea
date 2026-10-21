<?php

namespace App\Models\Concerns;

trait HasTranslations
{
    /**
     * Resolve a translated JSON attribute for the given (or current) locale,
     * falling back to the application fallback locale and then to the first
     * available translation.
     */
    public function translate(string $attribute, ?string $locale = null): ?string
    {
        $translations = $this->getAttribute($attribute);

        if (! is_array($translations) || $translations === []) {
            return null;
        }

        $locale ??= app()->getLocale();

        foreach ([$locale, config('app.fallback_locale')] as $candidate) {
            $value = $translations[$candidate] ?? null;

            if (is_string($value) && trim($value) !== '') {
                return $value;
            }
        }

        $first = reset($translations);

        return is_string($first) && trim($first) !== '' ? $first : null;
    }

    /**
     * @return array<string, string>
     */
    public function translations(string $attribute): array
    {
        $translations = $this->getAttribute($attribute);

        return is_array($translations) ? $translations : [];
    }
}
