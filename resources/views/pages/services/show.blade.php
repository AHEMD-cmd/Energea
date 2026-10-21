@extends('layouts.app')

@php($title = $service->translate('title'))

@section('content')
    <section class="page-hero">
        <img src="{{ \App\Support\Media::url($service->image) }}" alt="{{ $title }}" width="1600" height="900" fetchpriority="high">
        <div class="container">
            <span class="section-label light">{{ $service->number }} / {{ $service->translate('type') }}</span>
            <h1>{{ $title }}</h1>
            <p>{{ $service->translate('summary') }}</p>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <div class="row align-items-center gy-5">
                <div class="col-lg-6">
                    <div class="detail-image">
                        <img src="{{ \App\Support\Media::url($service->image) }}" alt="{{ $title }}" width="1100" height="900" loading="lazy" decoding="async">
                        <span>{{ __('service.image_caption', ['number' => $service->number]) }}</span>
                    </div>
                </div>
                <div class="col-lg-6 ps-lg-5">
                    <span class="section-label">{{ __('service.overview_label') }}</span>
                    <h2 class="display-title compact">{{ $title }}</h2>
                    <p class="body-copy mt-4">{{ $service->translate('overview') }}</p>
                    <div class="rule-list neutral">
                        @foreach ($steps as $index => $step)
                            <div><span>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span><strong>{{ $step }}</strong><i><x-ui-icon name="check" /></i></div>
                        @endforeach
                    </div>
                    <a href="{{ route('contact') }}#quote" class="btn-est orange mt-4"><span>{{ __('service.request_quote') }}</span><x-ui-icon name="arrow" /></a>
                </div>
            </div>
            <div class="service-deliverables">
                @php($factMeta = [['search', 'scope'], ['network', 'systems'], ['gauge', 'works'], ['document', 'outputs']])
                @foreach ($factMeta as $index => [$iconName, $labelKey])
                    <article><x-ui-icon :name="$iconName" /><strong>{{ __('service.'.$labelKey) }}</strong><span>{{ $facts[$index] }}</span></article>
                @endforeach
            </div>
            <div class="scope-grid mt-4">
                @foreach ($service->groups as $groupIndex => $group)
                    <article class="scope-card reveal">
                        <span class="card-icon"><x-ui-icon :name="$groupIndex ? 'gauge' : 'list'" /></span>
                        <small>{{ str_pad($groupIndex + 1, 2, '0', STR_PAD_LEFT) }}</small>
                        <h3>{{ $group->translate('title') }}</h3>
                        <ul>
                            @foreach ($group->items as $item)
                                <li><x-ui-icon name="check" /><span>{{ $item->translate('text') }}</span></li>
                            @endforeach
                        </ul>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endsection
