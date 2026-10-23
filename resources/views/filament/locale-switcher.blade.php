<x-filament::dropdown placement="bottom-end" teleport>
    <x-slot name="trigger">
        <x-filament::icon-button
            icon="heroicon-o-language"
            :label="__('admin.locale.switch')"
            color="gray"
        />
    </x-slot>

    <x-filament::dropdown.list>
        @php
            $currentLocale = app()->getLocale();
            $locales = config('energea.locales', ['en', 'ar']);
        @endphp

        @foreach ($locales as $locale)
            <x-filament::dropdown.list.item
                :tag="'form'"
                :action="route('filament.admin.locale', ['locale' => $locale])"
                method="post"
                :color="$currentLocale === $locale ? 'primary' : 'gray'"
                :icon="$currentLocale === $locale ? 'heroicon-m-check' : null"
            >
                @csrf
                {{ $locale === 'ar' ? __('admin.locale.arabic') : __('admin.locale.english') }}
            </x-filament::dropdown.list.item>
        @endforeach
    </x-filament::dropdown.list>
</x-filament::dropdown>
