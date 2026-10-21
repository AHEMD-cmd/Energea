<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // The locale-grouped routes need an explicit locale for route() calls
        // outside a request that has already set URL defaults (e.g. 404 pages).
        URL::defaults(['locale' => config('energea.default_locale')]);
        if (! app()->isProduction()) {
            Model::preventLazyLoading();
        }
    }
}
