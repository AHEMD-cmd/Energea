<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    |
    | Public routes are constrained to these locale prefixes. The default
    | locale is used for pages without a locale segment (sitemap, robots,
    | the root redirect fallback) and as the fallback for missing keys.
    |
    */

    'locales' => ['en', 'ar'],

    'default_locale' => env('APP_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Path
    |--------------------------------------------------------------------------
    */

    'admin_path' => env('ADMIN_PATH', 'admin'),

    /*
    |--------------------------------------------------------------------------
    | Frontend Preview Path
    |--------------------------------------------------------------------------
    |
    | Location of the static preview repository. Only the importer command
    | (energea:import-preview) reads from here; it is never served publicly.
    |
    */

    'frontend_path' => env('ENERGEA_FRONTEND_PATH'),

];
