<?php

namespace App\Http\Controllers;

use App\Support\Seo;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return view('pages.about', [
            'page' => 'about',
            'seo' => Seo::make(__('about.seo_title'), __('about.seo_description')),
        ]);
    }
}
