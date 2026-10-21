@extends('layouts.app')

@section('content')
    <section class="page-hero">
        <img height="615" width="1600" src="{{ asset('assets/images/about-banner.jpeg') }}" alt="{{ __('about.hero_alt') }}" data-parallax>
        <div class="container">
            <span class="section-label light">{{ __('about.hero_label') }}</span>
            <h1>{{ __('about.hero_title') }}</h1>
            <p>{{ __('about.hero_body') }}</p>
        </div>
    </section>

    <section class="split-showcase snapshot-section">
        <div class="media reveal">
            <img src="{{ asset('assets/images/field-electrical-testing.jpeg') }}" alt="{{ __('about.snapshot_alt') }}" width="1536" height="1024" loading="lazy" decoding="async">
        </div>
        <div class="copy-panel reveal">
            <span class="section-label">{{ __('about.snapshot_label') }}</span>
            <h2 class="display-title compact">{{ __('about.snapshot_title_line') }}<br><em>{{ __('about.snapshot_title_em') }}</em>
            </h2>
            <p class="body-copy mt-4">{{ __('about.snapshot_body') }}</p>
            <a href="{{ route('services.index') }}" class="btn-est dark mt-3"><span>{{ __('about.snapshot_cta') }}</span><i>↗</i></a>
        </div>
    </section>

    <section class="section-space about-purpose" aria-labelledby="purposeTitle">
        <div class="container">
            <div class="purpose-manifesto">
                <header class="purpose-manifesto-head reveal">
                    <span class="section-label">{{ __('about.purpose_label') }}</span>
                    <h2 class="display-title" id="purposeTitle">{{ __('about.purpose_title') }}</h2>
                </header>
                <div class="purpose-manifesto-lines">
                    <article class="purpose-line purpose-line-mission reveal">
                        <span class="purpose-line-index">01</span>
                        <div class="purpose-line-copy">
                            <small>{{ __('about.mission_label') }}</small>
                            <h3>{{ __('about.mission_title') }}</h3>
                            <p>{{ __('about.mission_body') }}</p>
                        </div>
                        <span class="purpose-line-icon" aria-hidden="true"><x-ui-icon name="shield" /></span>
                    </article>
                    <article class="purpose-line purpose-line-vision reveal">
                        <span class="purpose-line-index">02</span>
                        <div class="purpose-line-copy">
                            <small>{{ __('about.vision_label') }}</small>
                            <h3>{{ __('about.vision_title') }}</h3>
                            <p>{{ __('about.vision_body') }}</p>
                        </div>
                        <span class="purpose-line-icon" aria-hidden="true"><x-ui-icon name="eye" /></span>
                    </article>
                    <article class="purpose-line purpose-line-values reveal">
                        <span class="purpose-line-index">03</span>
                        <div class="purpose-line-copy">
                            <small>{{ __('about.values_label') }}</small>
                            <h3>{{ __('about.values_title') }}</h3>
                            <p>{{ __('about.values_body') }}</p>
                        </div>
                        <div class="purpose-line-values-list" aria-label="{{ __('about.values_aria') }}">
                            @foreach (__('about.values') as $value)
                                <span>{{ $value }}</span>
                            @endforeach
                        </div>
                        <span class="purpose-line-icon" aria-hidden="true"><x-ui-icon name="chart" /></span>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section class="metric-band about-metrics">
        <div class="container">
            <div class="row g-0">
                @php($metricIcons = ['building', 'shield', 'pin', 'gauge'])
                @foreach (__('about.metrics') as $index => $metric)
                    <div class="col-6 col-lg-3">
                        <article>
                            <span class="metric-icon" aria-hidden="true"><x-ui-icon :name="$metricIcons[$index]" /></span><small>{{ $metric['label'] }}</small><strong @if (is_numeric($metric['value'])) data-count="{{ $metric['value'] }}" @if ($metric['suffix'] !== '') data-suffix="{{ $metric['suffix'] }}" @endif @endif>{{ $metric['value'] }}{{ $metric['suffix'] }}</strong><span>{{ $metric['caption'] }}</span>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
