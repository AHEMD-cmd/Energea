@php($name = $client->translate('name'))
<article class="client-logo client-logo-3d logo-card-{{ $client->logo_code }}" title="{{ $name }}">
    <span class="client-logo-media">
        <img src="{{ \App\Support\Media::url($client->logo) }}" alt="{{ $name }}" loading="lazy" decoding="async" width="220" height="100">
    </span>
    <span class="client-logo-name">{{ $name }}</span>
</article>
