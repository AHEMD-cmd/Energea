<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class Media
{
    public static function url(?string $path, ?string $fallback = null): string
    {
        if (! is_string($path) || trim($path) === '') {
            return $fallback ? asset($fallback) : '';
        }

        if (preg_match('#^(https?:)?//#', $path) === 1 || str_starts_with($path, '/')) {
            return $path;
        }

        if (str_starts_with($path, 'assets/')) {
            return asset($path);
        }

        return Storage::disk('public')->url($path);
    }
}
