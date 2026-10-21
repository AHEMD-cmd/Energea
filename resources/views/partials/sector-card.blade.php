@php($icons = ['building', 'network', 'shield', 'gauge', 'projects', 'bolt', 'portfolio', 'check'])
<div class="col-12 col-md-6 col-lg-3">
    <article class="sector-card home-sector-card reveal">
        <span>{{ $sector->code }} / {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
        <i class="sector-icon" aria-hidden="true"><x-ui-icon :name="$icons[$index % count($icons)]" /></i>
        <h3>{{ $sector->translate('title') }}</h3>
        <p>{{ $sector->translate('text') }}</p>
    </article>
</div>
