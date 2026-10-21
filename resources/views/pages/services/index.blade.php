@extends('layouts.app')

@section('content')
    <section class="page-hero">
        <img height="615" width="1600" src="{{ $heroImage }}" alt="{{ $servicesPage['hero']['alt'] }}">
        <div class="container">
            <span class="section-label light">{{ $servicesPage['hero']['label'] }}</span>
            <h1>{{ $servicesPage['hero']['title'] }}</h1>
            <p>{{ $servicesPage['hero']['body'] }}</p>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <div class="row align-items-end gy-4 section-head">
                <div class="col-lg-8">
                    <span class="section-label">{{ $servicesPage['head']['label'] }}</span>
                    <h2 class="display-title">{{ $servicesPage['head']['title_line'] }}<br><em>{{ $servicesPage['head']['title_em'] }}</em>
                    </h2>
                </div>
                <div class="col-lg-4">
                    <p class="body-copy">{{ $servicesPage['head']['body'] }}</p>
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
