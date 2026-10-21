<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function rootIndex(): RedirectResponse
    {
        return redirect('/'.config('energea.default_locale'), 301);
    }

    public function index(string $locale): RedirectResponse
    {
        return redirect()->route('home', ['locale' => $locale], 301);
    }

    public function page(string $locale, string $page): RedirectResponse
    {
        $routes = [
            'about' => 'about',
            'services' => 'services.index',
            'projects' => 'projects.index',
            'clients' => 'clients',
            'certificates' => 'certificates',
            'contact' => 'contact',
        ];

        abort_unless(isset($routes[$page]), 404);

        return redirect()->route($routes[$page], ['locale' => $locale], 301);
    }

    public function service(Request $request, string $locale): RedirectResponse
    {
        $slug = (string) $request->query('slug', '');

        $exists = $slug !== '' && Service::query()
            ->where('is_published', true)
            ->where('slug', $slug)
            ->exists();

        return $exists
            ? redirect()->route('services.show', ['locale' => $locale, 'slug' => $slug], 301)
            : redirect()->route('services.index', ['locale' => $locale], 301);
    }

    public function project(Request $request, string $locale): RedirectResponse
    {
        $slug = (string) $request->query('slug', '');

        $exists = $slug !== '' && Project::query()
            ->where('is_published', true)
            ->where('slug', $slug)
            ->exists();

        return $exists
            ? redirect()->route('projects.show', ['locale' => $locale, 'slug' => $slug], 301)
            : redirect()->route('projects.index', ['locale' => $locale], 301);
    }

    public function projects(Request $request, string $locale): RedirectResponse
    {
        $query = $request->only('page');

        return redirect()->route('projects.index', array_merge(['locale' => $locale], $query), 301);
    }
}
