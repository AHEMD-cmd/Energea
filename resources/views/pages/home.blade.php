@extends('layouts.app')

@section('content')
    <section class="hero overflow-hidden">
        <div class="container-fluid px-0 h-100"><div class="row g-0 h-100">
            <div class="col-xl-7 hero-copy-col"><div class="hero-copy">
                <div class="hero-kicker">
                    <b>ENERGEA / KSA</b><i></i><span>{{ __('home.hero_kicker') }}</span>
                </div>
                <h1>
                    <span>{{ __('home.hero_title_line') }}</span><strong>{{ __('home.hero_title_strong') }}</strong>
                </h1>
                <p>{{ __('home.hero_intro') }}</p>
                <div class="d-flex flex-wrap gap-3 hero-actions">
                    <a href="{{ route('contact') }}#quote" class="btn-est orange"><span>{{ __('common.request_quote') }}</span><i>↗</i></a>
                    <a href="{{ route('projects.index') }}" class="btn-est ghost"><span>{{ __('common.explore_projects') }}</span><i>↗</i></a>
                </div>
            </div></div>
            <div class="col-xl-5 hero-visual">
                <picture class="hero-photo">
                    <img src="{{ asset('assets/images/field-panel-desktop.jpeg') }}" alt="{{ __('home.hero_alt') }}" width="1201" height="1600" fetchpriority="high">
                </picture>
                <div class="visual-card">
                    <small>{{ __('home.hero_visual_label') }}</small><strong>{{ __('home.hero_visual_value') }}</strong><span>{{ __('home.hero_visual_caption') }}</span>
                </div>
                <div class="visual-caption">
                    <b>ENERGEA</b><span>{{ __('home.hero_cities') }}</span>
                </div>
            </div>
        </div></div>
    </section>

    <section class="home-positioning section-space">
        <div class="container"><div class="positioning-shell reveal">
            <div>
                <span class="section-label">{{ __('home.positioning_label') }}</span>
                <h2 class="display-title compact">{{ __('home.positioning_title_line') }}<br><em>{{ __('home.positioning_title_em') }}</em>
                </h2>
                <div class="positioning-points">
                    <span>{{ __('home.positioning_point_1') }}</span> <span>{{ __('home.positioning_point_2') }}</span>
                </div>
            </div>
            <div>
                <p class="body-copy">{{ __('home.positioning_body') }}</p>
                <a class="text-link" href="{{ route('about') }}"><span>{{ __('home.positioning_cta') }}</span><i>↗</i></a>
            </div>
        </div></div>
    </section>

    <section class="metric-band home-metrics"><div class="container"><div class="row g-0">
        @foreach (__('home.metrics') as $metric)
            <div><article><small>{{ $metric['label'] }}</small><strong dir="ltr"@if (is_numeric($metric['value'])) data-count="{{ $metric['value'] }}"@if ($metric['suffix'] !== '') data-suffix="{{ $metric['suffix'] }}"@endif @endif>{{ $metric['value'] }}{{ $metric['suffix'] }}</strong><span>{{ $metric['caption'] }}</span></article></div>
        @endforeach
    </div></div></section>

    <section class="dark-section section-space home-why"><div class="container">
        <div class="row align-items-end gy-3 section-head reveal">
            <div class="col-lg-8">
                <span class="section-label light">{{ __('home.why_label') }}</span>
                <h2 class="display-title">{{ __('home.why_title_line') }}<br><em>{{ __('home.why_title_em') }}</em>
                </h2>
            </div>
            <div class="col-lg-4"><p class="body-copy">{{ __('home.why_intro') }}</p></div>
        </div>
        <div class="why-grid compact-why">
            @php($whyIcons = ['shield', 'gauge', 'document', 'chart'])
            @foreach (__('home.why_cards') as $index => $card)
                <article class="why-card"><div class="why-card-top">
                    <span>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span><i class="why-card-icon" aria-hidden="true"><x-ui-icon :name="$whyIcons[$index]" /></i>
                </div>
                    <h3>{{ $card['title'] }}</h3>
                    <p>{{ $card['body'] }}</p></article>
            @endforeach
        </div>
    </div></section>

    <section class="section-space home-industries">
        <div class="container">
            <div class="row align-items-end gy-3 section-head reveal">
                <div class="col-lg-8">
                    <span class="section-label">{{ __('home.industries_label') }}</span>
                    <h2 class="display-title">{{ __('home.industries_title_line') }}<br><em>{{ __('home.industries_title_em') }}</em></h2>
                </div>
                <div class="col-lg-4"><p class="body-copy">{{ __('home.industries_intro') }}</p></div>
            </div>
            <div class="row g-3 home-industries-grid" id="homeIndustries">
                @foreach ($sectors as $index => $sector)
                    @include('partials.sector-card', ['sector' => $sector, 'index' => $index])
                @endforeach
            </div>
        </div>
    </section>

    <section class="final-cta quote-cta">
        <img src="{{ asset('assets/images/projects/makkah-haram.webp') }}" alt="{{ __('home.final_alt') }}" width="1600" height="900" loading="lazy">
        <div class="container reveal">
            <span class="section-label light">{{ __('home.final_label') }}</span><h2 class="display-title">{{ __('home.final_title_line') }}<br><em>{{ __('home.final_title_em') }}</em>
            </h2>
            <p>{{ __('home.final_body') }}</p>
            <a href="{{ route('contact') }}#quote" class="btn-est orange mt-4"><span>{{ __('common.request_quote') }}</span><i>↗</i></a>
        </div>
    </section>
@endsection
