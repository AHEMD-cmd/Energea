<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Support\Seo;
use Illuminate\Contracts\View\View;

class ProjectsController extends Controller
{
    public function index(): View
    {
        $projects = Project::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->paginate(config('quote.projects_per_page'))
            ->withQueryString();

        return view('pages.projects.index', [
            'projects' => $projects,
            'page' => 'projects',
            'seo' => Seo::make(__('projects.seo_title'), __('projects.seo_description')),
        ]);
    }

    public function show(string $locale, string $slug): View
    {
        $project = Project::query()
            ->with(['parties', 'sector'])
            ->where('is_published', true)
            ->where('slug', $slug)
            ->firstOrFail();

        return view('pages.projects.show', [
            'project' => $project,
            'page' => 'projects',
            'bodyClass' => 'project-detail-page',
            'seo' => Seo::make(
                $project->seo_title ?: $project->translate('title').' | Energea',
                $project->seo_description ?: $project->translate('location').' · '.$project->year.' · '.$project->translate('scope'),
                $project->image,
            ),
        ]);
    }
}
