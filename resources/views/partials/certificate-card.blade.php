@php($image = \App\Support\Media::url($certificate->image))
<div class="col-md-6 col-lg-4">
    <a class="document-card reveal" href="{{ $image }}" target="_blank" rel="noopener">
        <div class="document-preview">
            <img src="{{ $image }}" alt="{{ $certificate->translate('name') }}" loading="lazy" decoding="async">
        </div>
        <div class="document-copy">
            <small>{{ $certificate->translate('type') }}</small>
            <strong>{{ $certificate->translate('name') }}</strong>
            <i><x-ui-icon name="arrow" /></i>
        </div>
    </a>
</div>
