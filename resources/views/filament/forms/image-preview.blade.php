@props([
    'url',
    'isCustom' => false,
    'title' => 'Image',
    'hint' => null,
    'variant' => 'portrait',
])

<div @class(['energea-image-preview', "is-{$variant}"])>
    @if (filled($url))
        <img src="{{ $url }}" alt="" class="energea-image-preview__image">
    @else
        <div class="energea-image-preview__image energea-image-preview__image--empty">{{ __('admin.image.empty') }}</div>
    @endif

    <div class="energea-image-preview__body">
        <div class="energea-image-preview__heading">
            <span class="energea-image-preview__title">{{ $title }}</span>
            <span @class(['energea-image-preview__badge', 'is-custom' => $isCustom])>
                {{ $isCustom ? __('admin.image.custom') : __('admin.image.default') }}
            </span>
        </div>

        @if ($hint)
            <p class="energea-image-preview__hint">{{ $hint }}</p>
        @endif
    </div>
</div>

<style>
    .energea-image-preview {
        display: inline-flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem;
        border: 1px solid rgb(229 231 235);
        border-radius: 0.75rem;
        background: #fff;
    }

    .energea-image-preview__image {
        display: block;
        flex: none;
        width: 120px;
        height: 160px;
        object-fit: cover;
        border-radius: 0.5rem;
        box-shadow: 0 1px 2px rgb(0 0 0 / 0.08);
    }

    .energea-image-preview.is-landscape .energea-image-preview__image {
        width: 160px;
        height: 100px;
    }

    .energea-image-preview__image--empty {
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px dashed rgb(209 213 219);
        background: rgb(249 250 251);
        color: rgb(107 114 128);
        font-size: 0.75rem;
        box-shadow: none;
    }

    .energea-image-preview__body {
        min-width: 220px;
    }

    .energea-image-preview__heading {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .energea-image-preview__title {
        font-size: 0.875rem;
        font-weight: 600;
        color: rgb(17 24 39);
    }

    .energea-image-preview__badge {
        padding: 0.125rem 0.5rem;
        border-radius: 9999px;
        background: rgb(243 244 246);
        color: rgb(75 85 99);
        font-size: 0.75rem;
        font-weight: 500;
        line-height: 1.25rem;
    }

    .energea-image-preview__badge.is-custom {
        background: rgb(255 247 237);
        color: rgb(194 65 12);
    }

    .energea-image-preview__hint {
        margin-top: 0.25rem;
        font-size: 0.8125rem;
        color: rgb(107 114 128);
    }

    .dark .energea-image-preview {
        border-color: rgb(255 255 255 / 0.1);
        background: rgb(17 24 39);
    }

    .dark .energea-image-preview__title {
        color: rgb(255 255 255);
    }

    .dark .energea-image-preview__badge {
        background: rgb(255 255 255 / 0.1);
        color: rgb(209 213 219);
    }

    .dark .energea-image-preview__badge.is-custom {
        background: rgb(251 146 60 / 0.1);
        color: rgb(251 146 60);
    }

    .dark .energea-image-preview__hint {
        color: rgb(156 163 175);
    }

    .dark .energea-image-preview__image--empty {
        border-color: rgb(255 255 255 / 0.2);
        background: rgb(255 255 255 / 0.05);
        color: rgb(156 163 175);
    }
</style>
