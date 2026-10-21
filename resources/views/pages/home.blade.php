@extends('layouts.app')

@section('content')
    <section class="hero overflow-hidden">
        <div class="container-fluid px-0 h-100"><div class="row g-0 h-100">
            <div class="col-xl-7 hero-copy-col"><div class="hero-copy">
                <div class="hero-kicker">
                    <b>ENERGEA / KSA</b><i></i><span>{{ $home['hero']['kicker'] }}</span>
                </div>
                <h1>
                    <span>{{ $home['hero']['title_line'] }}</span><strong>{{ $home['hero']['title_strong'] }}</strong>
                </h1>
                <p>{{ $home['hero']['intro'] }}</p>
                <div class="d-flex flex-wrap gap-3 hero-actions">
                    <a href="{{ route('contact') }}#quote" class="btn-est orange"><span>{{ __('common.request_quote') }}</span><i>↗</i></a>
                    <a href="{{ route('projects.index') }}" class="btn-est ghost"><span>{{ __('common.explore_projects') }}</span><i>↗</i></a>
                </div>
            </div></div>
            <div class="col-xl-5 hero-visual">
                <picture class="hero-photo">
                    <img src="{{ \App\Support\Media::url($home['hero']['image'], 'assets/images/field-panel-desktop.jpeg') }}" alt="{{ $home['hero']['alt'] }}" width="1201" height="1600" fetchpriority="high">
                </picture>
                <div class="visual-card">
                    <small>{{ $home['hero']['visual_label'] }}</small><strong>{{ $home['hero']['visual_value'] }}</strong><span>{{ $home['hero']['visual_caption'] }}</span>
                </div>
                <div class="visual-caption">
                    <b>ENERGEA</b><span>{{ $home['hero']['cities'] }}</span>
                </div>
            </div>
        </div></div>
    </section>

    <section class="home-positioning section-space">
        <div class="container"><div class="positioning-shell reveal">
            <div>
                <span class="section-label">{{ $home['positioning']['label'] }}</span>
                <h2 class="display-title compact">{{ $home['positioning']['title_line'] }}<br><em>{{ $home['positioning']['title_em'] }}</em>
                </h2>
                <div class="positioning-points">
                    <span>{{ $home['positioning']['point_1'] }}</span> <span>{{ $home['positioning']['point_2'] }}</span>
                </div>
            </div>
            <div>
                <p class="body-copy">{{ $home['positioning']['body'] }}</p>
                <a class="text-link" href="{{ route('about') }}"><span>{{ $home['positioning']['cta'] }}</span><i>↗</i></a>
            </div>
        </div></div>
    </section>

    <section class="metric-band home-metrics"><div class="container"><div class="row g-0">
        @foreach ($home['metrics'] as $metric)
            <div><article><small>{{ $metric['label'] }}</small><strong dir="ltr"@if (is_numeric($metric['value'])) data-count="{{ $metric['value'] }}"@if ($metric['suffix'] !== '') data-suffix="{{ $metric['suffix'] }}"@endif @endif>{{ $metric['value'] }}{{ $metric['suffix'] }}</strong><span>{{ $metric['caption'] }}</span></article></div>
        @endforeach
    </div></div></section>

    <section class="dark-section section-space home-why"><div class="container">
        <div class="row align-items-end gy-3 section-head reveal">
            <div class="col-lg-8">
                <span class="section-label light">{{ $home['why']['label'] }}</span>
                <h2 class="display-title">{{ $home['why']['title_line'] }}<br><em>{{ $home['why']['title_em'] }}</em>
                </h2>
            </div>
            <div class="col-lg-4"><p class="body-copy">{{ $home['why']['intro'] }}</p></div>
        </div>
        <div class="why-grid compact-why">
            @foreach ($home['why']['cards'] as $index => $card)
                <article class="why-card"><div class="why-card-top">
                    <span>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span><i class="why-card-icon" aria-hidden="true"><x-ui-icon :name="$card['icon'] ?? 'shield'" /></i>
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
                    <span class="section-label">{{ $home['industries']['label'] }}</span>
                    <h2 class="display-title">{{ $home['industries']['title_line'] }}<br><em>{{ $home['industries']['title_em'] }}</em></h2>
                </div>
                <div class="col-lg-4"><p class="body-copy">{{ $home['industries']['intro'] }}</p></div>
            </div>
            <div class="row g-3 home-industries-grid" id="homeIndustries">
                @foreach ($sectors as $index => $sector)
                    @include('partials.sector-card', ['sector' => $sector, 'index' => $index])
                @endforeach
            </div>
        </div>
    </section>

    <section class="final-cta quote-cta">
        <img src="{{ asset('assets/images/projects/makkah-haram.webp') }}" alt="{{ $home['final']['alt'] }}" width="1600" height="900" loading="lazy">
        <div class="container reveal">
            <span class="section-label light">{{ $home['final']['label'] }}</span><h2 class="display-title">{{ $home['final']['title_line'] }}<br><em>{{ $home['final']['title_em'] }}</em>
            </h2>
            <p>{{ $home['final']['body'] }}</p>
            <a href="{{ route('contact') }}#quote" class="btn-est orange mt-4"><span>{{ __('common.request_quote') }}</span><i>↗</i></a>
        </div>
    </section>
@endsection
