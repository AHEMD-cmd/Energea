<?php

namespace App\Http\Controllers;

use App\Models\PageContent;
use App\Support\AboutContent;
use App\Support\Media;
use App\Support\Seo;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        $content = PageContent::localized(AboutContent::PAGE);

        return view('pages.about', [
            'about' => $content,
            'page' => 'about',
            'heroImage' => Media::url($content['hero']['image'], 'assets/images/about-banner.jpeg'),
            'snapshotImage' => Media::url($content['snapshot']['image'], 'assets/images/field-electrical-testing.jpeg'),
            'seo' => Seo::make($content['seo']['title'], $content['seo']['description']),
        ]);
    }
}
