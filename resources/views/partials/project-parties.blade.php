@php
    $parties = $project->parties->filter(fn ($party) => filled($party->logo) && filled($party->translate('name')));
    $groups = [
        ['label' => __('project.client'), 'items' => $parties->filter(fn ($party) => $party->party_type === \App\Enums\PartyType::Client)],
        ['label' => __('project.contractor'), 'items' => $parties->filter(fn ($party) => $party->party_type === \App\Enums\PartyType::Contractor)],
        ['label' => __('project.consultant'), 'items' => $parties->filter(fn ($party) => $party->party_type === \App\Enums\PartyType::Consultant)],
    ];
    $hasParties = collect($groups)->contains(fn (array $group) => $group['items']->isNotEmpty());
@endphp
@if ($hasParties)
    <section class="project-parties-inline" aria-label="{{ __('project.parties_aria') }}">
        <span class="project-parties-inline-title">{{ __('project.parties_label') }}</span>
        <div class="project-parties-grid">
            @foreach ($groups as $group)
                @foreach ($group['items'] as $party)
                    <article class="project-party-card">
                        <span class="project-party-label">{{ $group['label'] }}</span>
                        <span class="project-party-item">
                            <span class="project-party-logo"><img src="{{ \App\Support\Media::url($party->logo) }}" alt="{{ $party->translate('name') }}" loading="lazy" decoding="async"></span>
                            <strong class="project-party-name">{{ $party->translate('name') }}</strong>
                        </span>
                    </article>
                @endforeach
            @endforeach
        </div>
    </section>
@endif
