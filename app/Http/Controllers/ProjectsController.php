<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Support\Seo;
use App\Models\PageContent;
use App\Support\ProjectsContent;
use Illuminate\Contracts\View\View;
use App\Support\Media;

class ProjectsController extends Controller
{
    public function index(): View
    {
        $projects = Project::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->paginate(config('quote.projects_per_page'))
            ->withQueryString();

        $content = PageContent::localized(ProjectsContent::PAGE);


        return view('pages.projects.index', [
            'projects' => $projects,
            'projectsPage' => $content,
            'heroImage' => Media::url($content['hero']['image'], 'assets/images/services-banner.jpeg'),
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
