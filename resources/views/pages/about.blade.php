@extends('layouts.app')

@section('content')
    <section class="page-hero">
        <img height="615" width="1600" src="{{ $heroImage }}" alt="{{ $about['hero']['alt'] }}" data-parallax>
        <div class="container">
            <span class="section-label light">{{ $about['hero']['label'] }}</span>
            <h1>{{ $about['hero']['title'] }}</h1>
            <p>{{ $about['hero']['body'] }}</p>
        </div>
    </section>

    <section class="split-showcase snapshot-section">
        <div class="media reveal">
            <img src="{{ $snapshotImage }}" alt="{{ $about['snapshot']['alt'] }}" width="1536" height="1024" loading="lazy" decoding="async">
        </div>
        <div class="copy-panel reveal">
            <span class="section-label">{{ $about['snapshot']['label'] }}</span>
            <h2 class="display-title compact">{{ $about['snapshot']['title_line'] }}<br><em>{{ $about['snapshot']['title_em'] }}</em>
            </h2>
            <p class="body-copy mt-4">{{ $about['snapshot']['body'] }}</p>
            <a href="{{ route('services.index') }}" class="btn-est dark mt-3"><span>{{ $about['snapshot']['cta'] }}</span><i>↗</i></a>
        </div>
    </section>

    <section class="section-space about-purpose" aria-labelledby="purposeTitle">
        <div class="container">
            <div class="purpose-manifesto">
                <header class="purpose-manifesto-head reveal">
                    <span class="section-label">{{ $about['purpose']['label'] }}</span>
                    <h2 class="display-title" id="purposeTitle">{{ $about['purpose']['title'] }}</h2>
                </header>
                <div class="purpose-manifesto-lines">
                    <article class="purpose-line purpose-line-mission reveal">
                        <span class="purpose-line-index">01</span>
                        <div class="purpose-line-copy">
                            <small>{{ $about['mission']['label'] }}</small>
                            <h3>{{ $about['mission']['title'] }}</h3>
                            <p>{{ $about['mission']['body'] }}</p>
                        </div>
                        <span class="purpose-line-icon" aria-hidden="true"><x-ui-icon name="shield" /></span>
                    </article>
                    <article class="purpose-line purpose-line-vision reveal">
                        <span class="purpose-line-index">02</span>
                        <div class="purpose-line-copy">
                            <small>{{ $about['vision']['label'] }}</small>
                            <h3>{{ $about['vision']['title'] }}</h3>
                            <p>{{ $about['vision']['body'] }}</p>
                        </div>
                        <span class="purpose-line-icon" aria-hidden="true"><x-ui-icon name="eye" /></span>
                    </article>
                    <article class="purpose-line purpose-line-values reveal">
                        <span class="purpose-line-index">03</span>
                        <div class="purpose-line-copy">
                            <small>{{ $about['values']['label'] }}</small>
                            <h3>{{ $about['values']['title'] }}</h3>
                            <p>{{ $about['values']['body'] }}</p>
                        </div>
                        <div class="purpose-line-values-list" aria-label="{{ $about['values']['aria'] }}">
                            @foreach ($about['values']['items'] as $value)
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
                @foreach ($about['metrics'] as $index => $metric)
                    <div class="col-6 col-lg-3">
                        <article>
                            <span class="metric-icon" aria-hidden="true"><x-ui-icon :name="\App\Support\AboutContent::METRIC_ICONS[$index] ?? 'gauge'" /></span><small>{{ $metric['label'] }}</small><strong @if (is_numeric($metric['value'])) data-count="{{ $metric['value'] }}" @if ($metric['suffix'] !== '') data-suffix="{{ $metric['suffix'] }}" @endif @endif>{{ $metric['value'] }}{{ $metric['suffix'] }}</strong><span>{{ $metric['caption'] }}</span>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
