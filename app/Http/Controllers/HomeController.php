<?php

namespace App\Http\Controllers;

use App\Models\PageContent;
use App\Models\Sector;
use App\Support\HomeContent;
use App\Support\Media;
use App\Support\Seo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class HomeController extends Controller
{
    public function root(): RedirectResponse
    {
        return redirect('/'.app()->getLocale(), 301);
    }

    public function index(): View
    {
        $sectors = Sector::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->get();

        $content = PageContent::localized(HomeContent::PAGE);

        return view('pages.home', [
            'sectors' => $sectors,
            'home' => $content,
            'page' => 'home',
            'bodyClass' => 'home-refined',
            'preloadImage' => Media::url($content['hero']['image'], 'assets/images/field-panel-desktop.jpeg'),
            'seo' => Seo::make($content['seo']['title'], $content['seo']['description']),
        ]);
    }
}
