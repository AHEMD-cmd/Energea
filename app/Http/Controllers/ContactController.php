<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\SiteSetting;
use App\Support\Seo;
use Illuminate\Contracts\View\View;

class ContactController extends Controller
{
    public function show(): View
    {
        $services = Service::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->get();

        return view('pages.contact', [
            'services' => $services,
            'settings' => SiteSetting::current(),
            'page' => 'contact',
            'seo' => Seo::make(__('contact.seo_title'), __('contact.seo_description')),
        ]);
    }
}
