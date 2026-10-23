<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetAdminLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locales = config('energea.locales');
        $locale = $request->session()->get('admin_locale');

        if (! is_string($locale) || ! in_array($locale, $locales, true)) {
            $user = $request->user();
            $locale = is_object($user) && is_string($user->locale) ? $user->locale : null;
        }

        if (! is_string($locale) || ! in_array($locale, $locales, true)) {
            $locale = config('energea.default_locale');
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
