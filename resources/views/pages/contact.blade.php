@extends('layouts.app')

@section('content')
    <section class="page-hero compact-page-hero field-photo-banner">
        <img height="1024" width="1536" src="{{ asset('assets/images/field-electrical-testing.jpeg') }}" alt="{{ __('contact.hero_alt') }}">
        <div class="container">
            <span class="section-label light">{{ __('contact.hero_label') }}</span>
            <h1>{{ __('contact.hero_title') }}</h1>
            <p>{{ __('contact.hero_body') }}</p>
        </div>
    </section>

    <section class="content-section" id="quote">
        <div class="container">
            <div class="row g-4 quote-layout">
                <div class="col-lg-4">
                    <article class="contact-card reveal">
                        <span class="section-label light">{{ __('contact.direct_label') }}</span>
                        @foreach ($settings->departments ?? [] as $department)
                            <div class="direct-contact-entry">
                                <h3>{{ $department['name'][app()->getLocale()] ?? $department['name']['en'] ?? '' }}</h3>
                                @if (! empty($department['phone']))
                                    <a class="contact-line" href="tel:{{ preg_replace('/\s+/', '', $department['phone']) }}"><span class="direct-contact-icon" aria-hidden="true"><x-ui-icon name="phone" /></span><span dir="ltr">{{ $department['phone'] }}</span></a>
                                @endif
                                @if (! empty($department['email']))
                                    <a class="contact-line contact-email" href="mailto:{{ $department['email'] }}"><span class="direct-contact-icon" aria-hidden="true"><x-ui-icon name="mail" /></span><span dir="ltr">{{ $department['email'] }}</span></a>
                                @endif
                            </div>
                        @endforeach
                        @if ($settings->general_email)
                            <a class="contact-primary-email" href="mailto:{{ $settings->general_email }}"><span class="direct-contact-icon" aria-hidden="true"><x-ui-icon name="mail" /></span><span><small>{{ __('contact.general_email') }}</small><strong dir="ltr">{{ $settings->general_email }}</strong></span></a>
                        @endif
                        <h3 class="mt-4">{{ __('contact.offices') }}</h3>
                        <div class="office-locations">
                            @foreach ($settings->offices ?? [] as $office)
                                <a href="{{ $office['map_url'] ?? '#' }}" target="_blank" rel="noopener"><span>{{ $office['name'][app()->getLocale()] ?? $office['name']['en'] ?? '' }}</span><small>{{ __('common.open_location') }} ↗</small></a>
                            @endforeach
                        </div>
                    </article>
                </div>
                <div class="col-lg-8">
                    <form class="form-shell reveal quote-form" id="contactForm" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-heading">
                            <span class="section-label">{{ __('contact.form_label') }}</span>
                            <h2>{{ __('contact.form_title') }}</h2>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="quote-name">{{ __('contact.name') }}</label><input class="form-control" id="quote-name" name="name" autocomplete="name" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="quote-company">{{ __('contact.company') }}</label><input class="form-control" id="quote-company" name="company" autocomplete="organization" value="{{ old('company') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="quote-email">{{ __('contact.email') }}</label><input class="form-control" id="quote-email" name="email" type="email" dir="ltr" autocomplete="email" value="{{ old('email') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="quote-phone">{{ __('contact.phone') }}</label><input class="form-control" id="quote-phone" name="phone" type="tel" dir="ltr" autocomplete="tel" value="{{ old('phone') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="quote-service">{{ __('contact.service') }}</label><div class="select-wrap">
                                    <select class="form-select" id="quote-service" name="service" required>
                                        <option value="">{{ __('contact.select_service') }}</option>
                                        @foreach ($services as $service)
                                            <option value="{{ $service->slug }}" @selected(old('service') === $service->slug)>{{ $service->translate('title') }}</option>
                                        @endforeach
                                    </select><span class="select-chevron" aria-hidden="true"><x-ui-icon name="chevron" /></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="projectLocation">{{ __('contact.location') }}</label><div class="select-wrap">
                                    <select class="form-select" name="location" id="projectLocation" required>
                                        <option value="">{{ __('contact.select_city') }}</option>
                                        @foreach (config('quote.cities') as $city)
                                            <option value="{{ $city }}" @selected(old('location') === $city)>{{ __('contact.cities.'.$city) }}</option>
                                        @endforeach
                                    </select><span class="select-chevron" aria-hidden="true"><x-ui-icon name="chevron" /></span>
                                </div>
                            </div>
                            <div class="col-md-6" id="otherLocationGroup" hidden>
                                <label class="form-label" for="otherLocation">{{ __('contact.location_other') }}</label>
                                <input class="form-control" id="otherLocation" name="location_other" autocomplete="address-level2" value="{{ old('location_other') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="quote-message">{{ __('contact.message') }}</label><textarea class="form-control" id="quote-message" name="message" required>{{ old('message') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="quoteAttachment">{{ __('contact.attachment') }}</label>
                                <input class="visually-hidden file-control" id="quoteAttachment" name="attachment" type="file" accept="{{ collect(config('quote.attachment.extensions'))->map(fn ($extension) => '.'.$extension)->implode(',') }}">
                                <label class="upload-control" for="quoteAttachment"><span class="upload-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 16V4m0 0L7 9m5-5 5 5M4 16v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3"/></svg></span><span class="upload-copy"><strong>{{ __('contact.attachment_heading') }}</strong><span id="selectedFileName" aria-live="polite">{{ __('contact.no_file_selected') }}</span></span><span class="upload-action">{{ __('contact.choose_file') }}</span></label>
                                <small class="form-note">{{ __('contact.attachment_note') }}</small>
                            </div>
                            <div class="col-12">
                                <div class="visually-hidden" aria-hidden="true">
                                    <label for="website">{{ __('contact.website') }}</label>
                                    <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
                                </div>
                                <fieldset class="verification-panel">
                                    <legend class="visually-hidden">{{ __('contact.verification_legend') }}</legend>
                                    <div class="verification-head">
                                        <span class="verification-symbol" aria-hidden="true"><x-ui-icon name="shield" /></span>
                                        <div>
                                            <strong>{{ __('contact.verification_title') }}</strong><small>{{ __('contact.verification_subtitle') }}</small>
                                        </div>
                                        <span class="verification-step">{{ __('contact.verification_step') }}</span>
                                    </div>
                                    <div class="verification-body">
                                        <div class="verification-question"><span id="captchaQuestion" aria-live="polite"></span></div>
                                        <div class="verification-answer">
                                            <label class="visually-hidden" for="captchaAnswer">{{ __('contact.captcha_answer') }}</label><input class="form-control" id="captchaAnswer" name="captcha_answer" type="number" inputmode="numeric" min="0" autocomplete="off" placeholder="{{ __('contact.captcha_placeholder') }}" required>
                                        </div>
                                        <button type="button" class="verification-refresh" id="refreshCaptcha" aria-label="{{ __('contact.new_question') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20 11a8 8 0 1 0-1.5 5"></path><path d="M20 4v7h-7"></path></svg><span>{{ __('contact.new_question') }}</span></button>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-12">
                                <button class="btn-est orange" type="submit">
                                    <span>{{ __('contact.submit') }}</span><i>↗</i>
                                </button>
                                <div id="formMessage" class="alert d-none mt-3 form-status" role="status" aria-live="polite"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
