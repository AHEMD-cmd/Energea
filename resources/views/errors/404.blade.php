@php
    $locale = request()->route()?->parameter('locale') ?? session('locale') ?? config('energea.default_locale');
    $seo = \App\Support\Seo::make(__('common.not_found_title').' | Energea', __('common.not_found_body'));
@endphp

@extends('layouts.app')

@section('content')
    <section class="content-section error-page">
        <div class="container">
            <div class="error-panel reveal">
                <span class="section-label">{{ __('common.not_found_label') }}</span>
                <h1 class="display-title">{{ __('common.not_found_title') }}</h1>
                <p class="body-copy">{{ __('common.not_found_body') }}</p>
                <div class="d-flex flex-wrap gap-3 mt-4">
                    <a href="{{ route('home', ['locale' => $locale]) }}" class="btn-est orange"><span>{{ __('common.not_found_cta') }}</span><x-ui-icon name="arrow" /></a>
                    <a href="{{ route('services.index', ['locale' => $locale]) }}" class="btn-est dark"><span>{{ __('nav.services') }}</span><x-ui-icon name="arrow" /></a>
                </div>
            </div>
        </div>
    </section>
@endsection
