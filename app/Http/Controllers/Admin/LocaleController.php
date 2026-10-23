<?php

namespace App\Http\Controllers\Admin;

use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController
{
    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        $locales = config('energea.locales');

        abort_unless(is_array($locales) && in_array($locale, $locales, true), 404);

        $request->session()->put('admin_locale', $locale);

        $user = $request->user();
        if (is_object($user)) {
            $user->forceFill(['locale' => $locale])->save();
        }

        $fallback = Filament::getCurrentPanel()?->getUrl() ?? url('/');

        return redirect()->back(fallback: $fallback);
    }
}
