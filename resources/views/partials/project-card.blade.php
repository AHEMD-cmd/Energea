@php($systems = $project->translate('systems'))
<article class="project-stack-item reveal{{ $index % 2 ? ' is-reverse' : '' }}">
    <a href="{{ route('projects.show', ['slug' => $project->slug]) }}">
        <div class="project-stack-image">
            <img src="{{ \App\Support\Media::url($project->image) }}" alt="{{ $project->translate('title') }}" width="1200" height="800" loading="lazy" decoding="async">
            <span>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div class="project-stack-copy">
            <small>{{ $project->translate('location') }} / {{ $project->year }}</small>
            <h2>{{ $project->translate('title') }}</h2>
            <dl class="project-summary">
                <div><dt>{{ __('projects.scope') }}</dt><dd>{{ $project->translate('scope') }}</dd></div>
                @if ($systems)
                    <div><dt>{{ __('projects.systems') }}</dt><dd>{{ $systems }}</dd></div>
                @endif
            </dl>
            <span class="project-card-action"><span>{{ __('common.view_project_details') }}</span><x-ui-icon name="arrow" /></span>
        </div>
    </a>
</article>
