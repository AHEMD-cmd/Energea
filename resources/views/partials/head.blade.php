    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Energea">
    <meta property="og:locale" content="{{ $isRtl ? 'ar_SA' : 'en_US' }}">
    <meta property="og:title" content="{{ $seo['title'] }}">
    <meta property="og:description" content="{{ $seo['description'] }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $seo['image_url'] }}">
    <meta property="og:image:secure_url" content="{{ $seo['image_url'] }}">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Energea — Energy Solutions Technologies">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seo['title'] }}">
    <meta name="twitter:description" content="{{ $seo['description'] }}">
    <meta name="twitter:image" content="{{ $seo['image_url'] }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}" sizes="180x180">
    <meta name="description" content="{{ $seo['description'] }}">
    <title>{{ $seo['title'] }}</title>
    @if (($page ?? null) === 'home')
        <link rel="preload" href="{{ asset('assets/images/field-panel-desktop.jpeg') }}" as="image" fetchpriority="high">
    @elseif ($isRtl)
        <link rel="preload" href="{{ asset('assets/fonts/tajawal-400.woff2') }}" as="font" type="font/woff2" crossorigin>
    @else
        <link rel="preload" href="{{ asset('assets/fonts/inter-latin-400-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
    @endif
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
