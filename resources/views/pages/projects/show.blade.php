@extends('layouts.app')

@php($title = $project->translate('title'))
@php($systems = $project->translate('systems'))

@section('content')
    <section class="page-hero">
        <img src="{{ \App\Support\Media::url($project->image) }}" alt="{{ $title }}" width="1600" height="900" fetchpriority="high">
        <div class="container">
            <span class="section-label light">{{ $project->translate('location') }} / {{ $project->year }}</span>
            <h1>{{ $title }}</h1>
            <p>{{ $project->translate('scope') }}</p>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <div class="project-record">
                <div class="project-record-visual reveal">
                    <div class="detail-image">
                        <img src="{{ \App\Support\Media::url($project->image) }}" alt="{{ $title }}" width="1200" height="900" loading="lazy" decoding="async">
                        <span>{{ __('project.image_caption', ['year' => $project->year]) }}</span>
                    </div>
                </div>
                <article class="project-record-copy reveal">
                    <span class="section-label">{{ __('project.record_label') }}</span>
                    <h2 class="display-title compact">{{ $title }}</h2>
                    <div class="project-meta-row">
                        <div><small>{{ __('project.location') }}</small><strong>{{ $project->translate('location') }}</strong></div>
                        <div><small>{{ __('project.year') }}</small><strong>{{ $project->year }}</strong></div>
                    </div>
                    @include('partials.project-parties')
                    <div class="project-scope-facts">
                        <div><small>{{ __('project.our_scope') }}</small><strong>{{ $project->translate('scope') }}</strong></div>
                        <div><small>{{ __('project.systems_services') }}</small><strong>{{ $systems ?: $project->translate('scope') }}</strong></div>
                    </div>
                    <a href="{{ route('contact') }}#quote" class="btn-est dark project-record-cta"><span>{{ __('project.request_similar') }}</span><x-ui-icon name="arrow" /></a>
                </article>
            </div>
        </div>
    </section>
@endsection
