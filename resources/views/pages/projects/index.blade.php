@extends('layouts.app')

@section('content')
    <section class="page-hero compact-page-hero field-photo-banner">
        <img height="615" width="1600" src="{{ asset('assets/images/services-banner.jpeg') }}" alt="{{ __('projects.hero_alt') }}">
        <div class="container">
            <span class="section-label light">{{ __('projects.hero_label') }}</span>
            <h1>{{ __('projects.hero_title') }}</h1>
            <p>{{ __('projects.hero_body') }}</p>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <div class="row align-items-end gy-4 section-head">
                <div class="col-12">
                    <span class="section-label">{{ __('projects.head_label') }}</span>
                    <h2 class="display-title">{{ __('projects.head_title_line') }}<br><em>{{ __('projects.head_title_em') }}</em>
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
