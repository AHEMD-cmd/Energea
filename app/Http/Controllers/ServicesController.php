<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Support\Seo;
use Illuminate\Contracts\View\View;

class ServicesController extends Controller
{
    public function index(): View
    {
        $services = Service::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->get();

        return view('pages.services.index', [
            'services' => $services,
            'page' => 'services',
            'seo' => Seo::make(__('services.seo_title'), __('services.seo_description')),
        ]);
    }

    public function show(string $locale, string $slug): View
    {
        $service = Service::query()
            ->with(['groups.items'])
            ->where('is_published', true)
            ->where('slug', $slug)
            ->firstOrFail();

        $locale = app()->getLocale();
        $steps = $service->steps[$locale] ?? $service->steps['en'] ?? null;
        $facts = $service->facts[$locale] ?? $service->facts['en'] ?? null;

        if (! is_array($steps) || count($steps) < 3) {
            $steps = [
                __('service.default_steps.0'),
                __('service.default_steps.1'),
                __('service.default_steps.2'),
            ];
        }

        if (! is_array($facts) || count($facts) < 4) {
            $facts = [
                __('service.default_facts.0'),
                __('service.default_facts.1'),
                __('service.default_facts.2'),
                __('service.default_facts.3'),
            ];
        }

        return view('pages.services.show', [
            'service' => $service,
            'steps' => $steps,
            'facts' => $facts,
            'page' => 'services',
            'seo' => Seo::make(
                $service->seo_title ?: $service->translate('title').' | Energea',
                $service->seo_description ?: $service->translate('summary'),
                $service->image,
            ),
        ]);
    }
}
