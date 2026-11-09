@extends('layouts.app')

@section('content')
<section class="page-hero compact-page-hero field-photo-banner">
    <img height="615" width="1600" src="{{ $heroImage }}" alt="{{ $certificatesPage['hero']['alt'] }}">
    <div class="container">
        <span class="section-label light">{{ $certificatesPage['hero']['label'] }}</span>
        <h1>{{ $certificatesPage['hero']['title'] }}</h1>
        <p>{{ $certificatesPage['hero']['body'] }}</p>
    </div>
</section>

<section class="section-space credentials-section credentials-page">
    <div class="container">
        <div class="section-head reveal credentials-intro">
            <div class="credentials-intro-copy">
                <span class="section-label">{{ $certificatesPage['head']['label'] }}</span>
                <h2 class="display-title">
                    {{ $certificatesPage['head']['title_line'] }}<br><em>{{ $certificatesPage['head']['title_em'] }}</em>
                </h2>
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
