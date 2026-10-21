@extends('layouts.app')

@section('content')
    <section class="page-hero field-photo-banner">
        <img height="615" width="1600" src="{{ $heroImage }}" alt="{{ $clientsPage['hero']['alt'] }}" data-parallax>
        <div class="container">
            <span class="section-label light">{{ $clientsPage['hero']['label'] }}</span>
            <h1>{{ $clientsPage['hero']['title'] }}</h1>
            <p>{{ $clientsPage['hero']['body'] }}</p>
        </div>
    </section>

    <section class="clients section-space">
        <div class="container">
            <div class="section-head reveal client-directory-head">
                <div class="directory-title">
                    <span class="section-label">{{ $clientsPage['head']['label'] }}</span>
                    <h2 class="display-title">{{ $clientsPage['head']['title_line'] }}<br><em>{{ $clientsPage['head']['title_em'] }}</em>
                    </h2>
                </div>
                <div class="directory-side">
                    <span class="directory-side-icon" aria-hidden="true"><x-ui-icon name="partners" /></span>
                    <strong>{{ $clientsPage['head']['side_title'] }}</strong>
                    <p>{{ $clientsPage['head']['side_body'] }}</p>
                </div>
            </div>
            <div class="clients-grid">
                @foreach ($clients as $client)
                    @include('partials.client-card', ['client' => $client])
                @endforeach
            </div>
        </div>
    </section>

    <section class="final-cta">
        <img height="448" width="492" src="{{ $finalImage }}" alt="{{ $clientsPage['final']['alt'] }}">
        <div class="container reveal">
            <span class="section-label light">{{ $clientsPage['final']['label'] }}</span>
            <h2 class="display-title">{{ $clientsPage['final']['title_line'] }}<br><em>{{ $clientsPage['final']['title_em'] }}</em>
            </h2>
            <a href="{{ route('contact') }}" class="btn-est orange mt-4"><span>{{ $clientsPage['final']['cta'] }}</span><i>↗</i></a>
        </div>
    </section>
@endsection
