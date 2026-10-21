@props(['name' => 'arrow'])

@php
    $paths = [
        'home' => '<path d="M3 11.5 12 4l9 7.5"/><path d="M5.5 10.5V20h13v-9.5"/><path d="M9.5 20v-6h5v6"/>',
        'company' => '<circle cx="12" cy="7" r="3"/><path d="M5.5 20c.5-4 2.7-6 6.5-6s6 2 6.5 6"/>',
        'network' => '<rect x="9" y="3" width="6" height="5"/><rect x="3" y="16" width="6" height="5"/><rect x="15" y="16" width="6" height="5"/><path d="M12 8v4M6 16v-4h12v4"/>',
        'document' => '<path d="M6 3h8l4 4v14H6z"/><path d="M14 3v5h5M9 12h6M9 16h6"/>',
        'bolt' => '<path d="m13 2-8 12h7l-1 8 8-12h-7z"/>',
        'gauge' => '<path d="M4 18a8 8 0 1 1 16 0"/><path d="m12 14 4-4M7 18h10"/>',
        'building' => '<path d="M4 21V7l8-4v18M12 9h8v12M7 9h2M7 13h2M7 17h2M15 13h2M15 17h2"/>',
        'portfolio' => '<rect x="3" y="5" width="18" height="15" rx="1"/><path d="M8 5V3h8v2M3 11h18M10 11v2h4v-2"/>',
        'projects' => '<path d="M3 6h7l2 2h9v11H3z"/><path d="M3 6V4h7l2 2"/>',
        'list' => '<path d="M9 6h12M9 12h12M9 18h12"/><circle cx="4" cy="6" r="1"/><circle cx="4" cy="12" r="1"/><circle cx="4" cy="18" r="1"/>',
        'clients' => '<path d="M8 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM16 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/><path d="M2.5 20c.3-4 2.1-6 5.5-6 1.7 0 3 .5 4 1.5 1-1 2.3-1.5 4-1.5 3.4 0 5.2 2 5.5 6"/>',
        'partners' => '<circle cx="8" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M2 20c0-4 2-6 6-6s6 2 6 6M15 15c3.5 0 6 2 6 5"/>',
        'shield' => '<path d="M12 3 4.5 6v5c0 4.8 2.7 8 7.5 10 4.8-2 7.5-5.2 7.5-10V6z"/><path d="m8.5 12 2.2 2.2 4.8-5"/>',
        'contact' => '<path d="M4 4h16v12H8l-4 4z"/><path d="M8 9h8M8 12h5"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="1"/><path d="m4 7 8 6 8-6"/>',
        'phone' => '<path d="M7 3 4 5c-.5 8 7 15.5 15 15l2-3-4-3-2 2c-3.5-1.5-5.5-3.5-7-7l2-2z"/>',
        'pin' => '<path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/>',
        'chevron' => '<path d="m8 10 4 4 4-4"/>',
        'chevronLeft' => '<path d="m14.5 6-6 6 6 6"/>',
        'chevronRight' => '<path d="m9.5 6 6 6-6 6"/>',
        'arrow' => '<path d="M5 19 19 5M9 5h10v10"/>',
        'check' => '<path d="m5 12 4 4L19 6"/>',
        'eye' => '<path d="M3 12s3.5-6 9-6 9 6 9 6-3.5 6-9 6-9-6-9-6Z"/><circle cx="12" cy="12" r="2.5"/>',
        'search' => '<circle cx="11" cy="11" r="6"/><path d="m16 16 5 5"/>',
        'chart' => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
    ];
@endphp

<svg {{ $attributes->merge([
    'class' => 'ui-icon',
    'viewBox' => '0 0 24 24',
    'aria-hidden' => 'true',
    'fill' => 'none',
    'stroke' => 'currentColor',
    'stroke-width' => '1.8',
    'stroke-linecap' => 'round',
    'stroke-linejoin' => 'round',
]) }}>{!! $paths[$name] ?? $paths['arrow'] !!}</svg>
