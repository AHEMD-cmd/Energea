@extends('layouts.app')

@section('content')
    <section class="page-hero compact-page-hero field-photo-banner">
        <img height="615" width="1600" src="{{ asset('assets/images/services-banner.jpeg') }}" alt="{{ __('certificates.hero_alt') }}">
        <div class="container">
            <span class="section-label light">{{ __('certificates.hero_label') }}</span>
            <h1>{{ __('certificates.hero_title') }}</h1>
            <p>{{ __('certificates.hero_body') }}</p>
        </div>
    </section>

    <section class="section-space credentials-section credentials-page">
        <div class="container">
            <div class="section-head reveal credentials-intro">
                <div class="credentials-intro-copy">
                    <span class="section-label">{{ __('certificates.head_label') }}</span>
                    <h2 class="display-title">{{ __('certificates.head_title_line') }}<br><em>{{ __('certificates.head_title_em') }}</em></h2>
                </div>
            </div>
            <div class="row g-4 compact-certificates" id="certList">
                @foreach ($certificates as $certificate)
                    @include('partials.certificate-card', ['certificate' => $certificate])
                @endforeach
            </div>
        </div>
    </section>
@endsection
