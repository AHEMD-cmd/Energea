<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Resolve the active locale from the route parameter, remember the last
     * choice in the session (used by the root `/` redirect), and expose the
     * locale as a URL default so `route()` calls omit it.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locales = config('energea.locales');
        $locale = $request->route()?->parameter('locale');

        if (! is_string($locale) || ! in_array($locale, $locales, true)) {
            $locale = $request->session()->get('locale');
        }

        if (! is_string($locale) || ! in_array($locale, $locales, true)) {
            $locale = config('energea.default_locale');
        }

        app()->setLocale($locale);
        URL::defaults(['locale' => $locale]);

        $request->session()->put('locale', $locale);

        return $next($request);
    }
}
