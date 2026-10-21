@php($settings = \App\Models\SiteSetting::current())
@php($departments = collect($settings->departments ?? [])->take(2))
@php($locale = app()->getLocale())
<footer class="site-footer">
    <div class="container">
        <div class="row gy-5 footer-main">
            <div class="col-lg-4 footer-brand">
                <a class="brand" href="{{ route('home', ['locale' => $locale]) }}">
                    <img class="brand-logo footer-logo" src="{{ asset('assets/images/energea-logo.png') }}" alt="Energea" width="770" height="224" loading="lazy" decoding="async">
                </a>
                <p>{{ $settings->translate('footer_text') }}</p>
            </div>
            <div class="col-6 col-lg-2 footer-links">
                <h3>{{ __('footer.company') }}</h3>
                <a href="{{ route('about', ['locale' => $locale]) }}">{{ __('footer.about') }}</a>
                <a href="{{ route('clients', ['locale' => $locale]) }}">{{ __('footer.clients') }}</a>
                <a href="{{ route('certificates', ['locale' => $locale]) }}">{{ __('footer.certificates') }}</a>
            </div>
            <div class="col-6 col-lg-2 footer-links">
                <h3>{{ __('footer.explore') }}</h3>
                <a href="{{ route('services.index', ['locale' => $locale]) }}">{{ __('footer.services') }}</a>
                <a href="{{ route('projects.index', ['locale' => $locale]) }}">{{ __('footer.projects') }}</a>
                <a href="{{ route('contact', ['locale' => $locale]) }}">{{ __('footer.contact') }}</a>
            </div>
            <div class="col-lg-2 footer-links">
                <h3>{{ __('footer.offices') }}</h3>
                <p><x-ui-icon name="pin" />{{ __('footer.saudi_arabia') }}</p>
                <p><x-ui-icon name="building" />{{ __('footer.cities') }}</p>
            </div>
            <div class="col-lg-2 footer-links">
                <h3>{{ __('footer.direct_lines') }}</h3>
                @foreach ($departments as $department)
                    @if (! empty($department['phone']))
                        <a href="tel:{{ preg_replace('/\s+/', '', $department['phone']) }}"><x-ui-icon name="phone" /><span dir="ltr">{{ $department['phone'] }}</span></a>
                    @endif
                @endforeach
                @if ($settings->general_email)
                    <a href="mailto:{{ $settings->general_email }}"><x-ui-icon name="mail" /><span dir="ltr">{{ $settings->general_email }}</span></a>
                @endif
            </div>
        </div>
        <div class="footer-bottom">
            <span>{{ __('footer.copyright', ['year' => date('Y')]) }}</span>
            <span>{{ __('footer.bottom_location') }}</span>
        </div>
    </div>
</footer>
<a class="floating-contact" href="{{ route('contact', ['locale' => $locale]) }}#quote" aria-label="{{ __('common.talk_to_engineer') }}">
    <span>{{ __('common.talk_to_engineer') }}</span><x-ui-icon name="contact" />
</a>
