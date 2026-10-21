<?php

namespace App\Http\Controllers;

use App\Models\Sector;
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

        return view('pages.home', [
            'sectors' => $sectors,
            'page' => 'home',
            'bodyClass' => 'home-refined',
            'seo' => Seo::make(__('home.seo_title'), __('home.seo_description')),
        ]);
    }
}
