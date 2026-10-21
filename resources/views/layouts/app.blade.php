@php($isRtl = app()->getLocale() === 'ar')
<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
@include('partials.head', ['seo' => $seo, 'isRtl' => $isRtl, 'page' => $page ?? null, 'preloadImage' => $preloadImage ?? null])
</head>
<body data-page="{{ $page ?? '' }}" data-language="{{ app()->getLocale() }}"@if (! empty($bodyClass ?? null)) class="{{ $bodyClass }}"@endif>
<div id="siteHeader">@include('partials.header', ['isRtl' => $isRtl])</div>
<main>
@yield('content')
</main>
<div id="siteFooter">@include('partials.footer', ['isRtl' => $isRtl])</div>
<script src="{{ asset('assets/vendor/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>
