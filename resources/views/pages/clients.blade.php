@extends('layouts.app')

@section('content')
    <section class="page-hero field-photo-banner">
        <img height="615" width="1600" src="{{ asset('assets/images/about-banner.jpeg') }}" alt="{{ __('clients.hero_alt') }}" data-parallax>
        <div class="container">
            <span class="section-label light">{{ __('clients.hero_label') }}</span>
            <h1>{{ __('clients.hero_title') }}</h1>
            <p>{{ __('clients.hero_body') }}</p>
        </div>
    </section>

    <section class="clients section-space">
        <div class="container">
            <div class="section-head reveal client-directory-head">
                <div class="directory-title">
                    <span class="section-label">{{ __('clients.head_label') }}</span>
                    <h2 class="display-title">{{ __('clients.head_title_line') }}<br><em>{{ __('clients.head_title_em') }}</em>
                    </h2>
                </div>
                <div class="directory-side">
                    <span class="directory-side-icon" aria-hidden="true"><x-ui-icon name="partners" /></span>
                    <strong>{{ __('clients.side_title') }}</strong>
                    <p>{{ __('clients.side_body') }}</p>
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
        <img height="448" width="492" src="{{ asset('assets/images/projects/damac-data-center.webp') }}" alt="{{ __('clients.final_alt') }}">
        <div class="container reveal">
            <span class="section-label light">{{ __('clients.final_label') }}</span>
            <h2 class="display-title">{{ __('clients.final_title_line') }}<br><em>{{ __('clients.final_title_em') }}</em>
            </h2>
            <a href="{{ route('contact') }}" class="btn-est orange mt-4"><span>{{ __('clients.final_cta') }}</span><i>↗</i></a>
        </div>
    </section>
@endsection
