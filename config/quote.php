<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Projects Listing
    |--------------------------------------------------------------------------
    */

    'projects_per_page' => 5,

    /*
    |--------------------------------------------------------------------------
    | Quote Form
    |--------------------------------------------------------------------------
    |
    | Cities are stored by key; labels live in lang files (contact.php).
    | min_fill_seconds rejects instant bot submissions (first render stamps
    | a timestamp, the POST must arrive at least this many seconds later).
    |
    */

    'cities' => ['riyadh', 'jeddah', 'khobar', 'makkah', 'madinah', 'dammam', 'other'],

    'min_fill_seconds' => 3,

    'throttle' => [
        'per_minute' => 5,
        'per_hour' => 20,
    ],

    'attachment' => [
        'max_kb' => (int) env('QUOTE_ATTACHMENT_MAX_KB', 10240),
        'extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'dwg', 'dxf', 'jpg', 'jpeg', 'png', 'zip'],
        'mimes' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'dwg', 'dxf', 'jpg', 'jpeg', 'png', 'zip'],
    ],

    'clamav' => [
        'enabled' => (bool) env('QUOTE_CLAMAV_ENABLED', false),
        'path' => env('QUOTE_CLAMAV_PATH', 'clamscan'),
    ],

];
