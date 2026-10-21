@php
    $currentRoute = request()->route()?->getName();
    $active = static fn (array $routes): string => in_array($currentRoute, $routes, true) ? ' active' : '';
    $targetLocale = $isRtl ? 'en' : 'ar';
    $parameters = array_merge(request()->route()?->parameters() ?? [], ['locale' => $targetLocale]);
    $switchUrl = route($currentRoute ?? 'home', $parameters);
    if (request()->getQueryString()) {
        $switchUrl .= '?'.request()->getQueryString();
    }
@endphp
<div class="scroll-progress" id="scrollProgress"></div>
<header class="site-nav">
    <nav class="navbar navbar-expand-xl" aria-label="{{ __('nav.main_navigation') }}">
        <div class="container-fluid nav-shell">
            <a class="navbar-brand brand" href="{{ route('home', ['locale' => app()->getLocale()]) }}" aria-label="{{ __('nav.home') }}">
                <img class="brand-logo" src="{{ asset('assets/images/energea-logo.png') }}" alt="Energea" width="770" height="224" decoding="async">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="{{ __('nav.open_menu') }}">
                <span></span><span></span><span></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link{{ $active(['about']) }}" href="{{ route('about', ['locale' => app()->getLocale()]) }}">{{ __('nav.about') }}</a></li>
                    <li class="nav-item"><a class="nav-link{{ $active(['services.index', 'services.show']) }}" href="{{ route('services.index', ['locale' => app()->getLocale()]) }}">{{ __('nav.services') }}</a></li>
                    <li class="nav-item"><a class="nav-link{{ $active(['projects.index', 'projects.show']) }}" href="{{ route('projects.index', ['locale' => app()->getLocale()]) }}">{{ __('nav.projects') }}</a></li>
                    <li class="nav-item"><a class="nav-link{{ $active(['clients']) }}" href="{{ route('clients', ['locale' => app()->getLocale()]) }}">{{ __('nav.clients') }}</a></li>
                    <li class="nav-item"><a class="nav-link{{ $active(['certificates']) }}" href="{{ route('certificates', ['locale' => app()->getLocale()]) }}">{{ __('nav.certificates') }}</a></li>
                    <li class="nav-item"><a class="nav-link{{ $active(['contact']) }}" href="{{ route('contact', ['locale' => app()->getLocale()]) }}">{{ __('nav.contact') }}</a></li>
                </ul>
                <div class="nav-actions">
                    <a class="lang-btn" id="langSwitch" href="{{ $switchUrl }}" aria-label="{{ $isRtl ? __('nav.switch_to_english') : __('nav.switch_to_arabic') }}">{{ $isRtl ? 'EN' : 'AR' }}</a>
                    <a class="nav-cta" href="{{ route('contact', ['locale' => app()->getLocale()]) }}#quote"><span>{{ __('common.request_quote') }}</span><x-ui-icon name="arrow" /></a>
                </div>
            </div>
        </div>
    </nav>
</header>
