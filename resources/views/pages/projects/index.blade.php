@extends('layouts.app')

@section('content')
    <section class="page-hero compact-page-hero field-photo-banner">
        <img height="615" width="1600" src="{{ $heroImage }}" alt="{{ $projectsPage['hero']['alt'] }}">
        <div class="container">
            <span class="section-label light">{{ $projectsPage['hero']['label'] }}</span>
            <h1>{{ $projectsPage['hero']['title'] }}</h1>
            <p>{{ $projectsPage['hero']['body'] }}</p>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <div class="row align-items-end gy-4 section-head">
                <div class="col-12">
                    <span class="section-label">{{ $projectsPage['head']['label'] }}</span>
                    <h2 class="display-title">{{ $projectsPage['head']['title_line'] }}<br><em>{{ $projectsPage['head']['title_em'] }}</em>
                    </h2>
                </div>
            </div>
            <div class="project-stack" id="projectList">
                @php($offset = ($projects->currentPage() - 1) * $projects->perPage())
                @foreach ($projects as $project)
                    @include('partials.project-card', ['project' => $project, 'index' => $offset + $loop->index])
                @endforeach
            </div>
            {{ $projects->links('partials.pagination') }}
            @include('partials.quote-banner')
        </div>
    </section>
@endsection
