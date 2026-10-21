<?php

namespace App\Filament\Support;

use Closure;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

class Bilingual
{
    /**
     * @param  array<string, Closure(string, string): Component>  $fields
     */
    public static function tabs(array $fields, string $prefix = ''): Tabs
    {
        return Tabs::make($prefix !== '' ? "{$prefix}-translations" : 'translations')
            ->tabs([
                Tab::make('English')
                    ->schema(self::localize($fields, 'en', $prefix)),
                Tab::make('العربية')
                    ->schema(self::localize($fields, 'ar', $prefix)),
            ])
            ->columnSpanFull();
    }

    public static function text(string $name, string $label, bool $required = false, int $maxLength = 255): Closure
    {
        return function (string $locale, string $prefix = '') use ($name, $label, $required, $maxLength): TextInput {
            return TextInput::make(self::name($name, $locale, $prefix))
                ->label($label)
                ->required($required)
                ->maxLength($maxLength)
                ->extraAttributes(self::direction($locale));
        };
    }

    public static function textarea(string $name, string $label, bool $required = false, int $rows = 4): Closure
    {
        return function (string $locale, string $prefix = '') use ($name, $label, $required, $rows): Textarea {
            return Textarea::make(self::name($name, $locale, $prefix))
                ->label($label)
                ->required($required)
                ->rows($rows)
                ->extraAttributes(self::direction($locale));
        };
    }

    /**
     * @param  array<string, Closure(string, string): Component>  $fields
     * @return array<int, Component>
     */
    private static function localize(array $fields, string $locale, string $prefix): array
    {
        return array_values(array_map(
            fn (Closure $factory): Component => $factory($locale, $prefix),
            $fields,
        ));
    }

    private static function name(string $name, string $locale, string $prefix): string
    {
        $path = $prefix !== '' ? "{$prefix}.{$name}" : $name;

        return "{$path}.{$locale}";
    }

    /**
     * @return array<string, string>
     */
    private static function direction(string $locale): array
    {
        return $locale === 'ar' ? ['dir' => 'rtl'] : [];
    }
}
