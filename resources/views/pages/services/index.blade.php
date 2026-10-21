@extends('layouts.app')

@section('content')
    <section class="page-hero">
        <img height="615" width="1600" src="{{ asset('assets/images/services-banner.jpeg') }}" alt="{{ __('services.hero_alt') }}">
        <div class="container">
            <span class="section-label light">{{ __('services.hero_label') }}</span>
            <h1>{{ __('services.hero_title') }}</h1>
            <p>{{ __('services.hero_body') }}</p>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <div class="row align-items-end gy-4 section-head">
                <div class="col-lg-8">
                    <span class="section-label">{{ __('services.head_label') }}</span>
                    <h2 class="display-title">{{ __('services.head_title_line') }}<br><em>{{ __('services.head_title_em') }}</em>
                    </h2>
                </div>
                <div class="col-lg-4">
                    <p class="body-copy">{{ __('services.head_body') }}</p>
                </div>
            </div>
            <div class="row g-3" id="serviceList">
                @foreach ($services as $index => $service)
                    @include('partials.service-card', [
                        'service' => $service,
                        'index' => $index,
                        'featured' => $index === $services->count() - 1 && $services->count() % 3 === 1,
                    ])
                @endforeach
            </div>
        </div>
    </section>
@endsection
