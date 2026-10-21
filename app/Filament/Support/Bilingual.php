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
     * @param  array<string, Closure(string): Component>  $fields
     */
    public static function tabs(array $fields): Tabs
    {
        return Tabs::make('translations')
            ->tabs([
                Tab::make('English')
                    ->schema(self::localize($fields, 'en')),
                Tab::make('العربية')
                    ->schema(self::localize($fields, 'ar')),
            ])
            ->columnSpanFull();
    }

    public static function text(string $name, string $label, bool $required = false, int $maxLength = 255): Closure
    {
        return function (string $locale) use ($name, $label, $required, $maxLength): TextInput {
            return TextInput::make("{$name}.{$locale}")
                ->label($label)
                ->required($required)
                ->maxLength($maxLength)
                ->extraAttributes(self::direction($locale));
        };
    }

    public static function textarea(string $name, string $label, bool $required = false, int $rows = 4): Closure
    {
        return function (string $locale) use ($name, $label, $required, $rows): Textarea {
            return Textarea::make("{$name}.{$locale}")
                ->label($label)
                ->required($required)
                ->rows($rows)
                ->extraAttributes(self::direction($locale));
        };
    }

    /**
     * @param  array<string, Closure(string): Component>  $fields
     * @return array<int, Component>
     */
    private static function localize(array $fields, string $locale): array
    {
        return array_values(array_map(
            fn (Closure $factory): Component => $factory($locale),
            $fields,
        ));
    }

    /**
     * @return array<string, string>
     */
    private static function direction(string $locale): array
    {
        return $locale === 'ar' ? ['dir' => 'rtl'] : [];
    }
}
