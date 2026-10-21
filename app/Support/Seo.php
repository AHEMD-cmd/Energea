<?php

namespace App\Support;

use App\Models\SiteSetting;

class Seo
{
    /**
     * @return array{title: string, description: string, image_url: string}
     */
    public static function make(string $title, string $description, ?string $image = null): array
    {
        $settings = SiteSetting::current();

        return [
            'title' => $title,
            'description' => $description,
            'image_url' => Media::url($image ?? $settings->og_image, 'assets/images/social-icon-preview.jpg'),
        ];
    }
}
