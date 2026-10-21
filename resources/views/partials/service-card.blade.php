@php($icons = ['bolt', 'gauge', 'building', 'chart', 'shield', 'network', 'projects'])
<div class="col-md-6 {{ $featured ? 'col-xl-12 service-listing-featured' : 'col-xl-4' }}">
    <a class="listing-card reveal" href="{{ route('services.show', ['slug' => $service->slug]) }}">
        <div class="image-wrap">
            <img src="{{ \App\Support\Media::url($service->image) }}" alt="{{ $service->translate('title') }}" width="900" height="650" loading="lazy" decoding="async">
            <span>{{ $service->number }}</span>
        </div>
        <div class="copy">
            <span class="card-icon"><x-ui-icon :name="$icons[$index % count($icons)]" /></span>
            <small>{{ $service->translate('type') }}</small>
            <h2>{{ $service->translate('title') }}</h2>
            <p>{{ $service->translate('summary') }}</p>
            <b><span>{{ __('common.explore_service') }}</span><x-ui-icon name="arrow" /></b>
        </div>
    </a>
</div>
