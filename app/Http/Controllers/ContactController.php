<?php

namespace App\Http\Controllers;

use App\Enums\QuoteRequestStatus;
use App\Http\Requests\QuoteRequestStoreRequest;
use App\Mail\QuoteRequestSubmitted;
use App\Models\PageContent;
use App\Models\QuoteRequest;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Services\ClamavScanner;
use App\Support\ContactContent;
use App\Support\Media;
use App\Support\Seo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(): View
    {
        $services = Service::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->get();

        $content = PageContent::localized(ContactContent::PAGE);

        $formTs = now()->timestamp;
        $issuedTimestamps = $this->issuedFormTimestamps();
        $issuedTimestamps[] = $formTs;

        session(['quote_form_ts' => array_slice($issuedTimestamps, -5)]);

        return view('pages.contact', [
            'services' => $services,
            'settings' => SiteSetting::current(),
            'contactPage' => $content,
            'formTs' => $formTs,
            'heroImage' => Media::url($content['hero']['image'], 'assets/images/field-electrical-testing.jpeg'),
            'page' => 'contact',
            'seo' => Seo::make(__('contact.seo_title'), __('contact.seo_description')),
        ]);
    }

    public function store(QuoteRequestStoreRequest $request, ClamavScanner $scanner): RedirectResponse|JsonResponse
    {
        $ip = $request->ip();
        $keyMinute = 'quote:minute:'.$ip;
        $keyHour = 'quote:hour:'.$ip;

        if (RateLimiter::tooManyAttempts($keyMinute, config('quote.throttle.per_minute')) || RateLimiter::tooManyAttempts($keyHour, config('quote.throttle.per_hour'))) {
            return $this->errorResponse($request, 'rate_limited');
        }

        RateLimiter::hit($keyMinute, 60);
        RateLimiter::hit($keyHour, 3600);

        if (! is_numeric($request->input('form_ts'))) {
            return $this->errorResponse($request, 'too_fast');
        }

        $elapsed = now()->timestamp - (int) $request->input('form_ts');
        if ($elapsed < (int) config('quote.min_fill_seconds')) {
            return $this->errorResponse($request, 'too_fast');
        }

        $issuedTimestamps = $this->issuedFormTimestamps();
        if ($issuedTimestamps !== [] && ! in_array((int) $request->input('form_ts'), $issuedTimestamps, true)) {
            return $this->errorResponse($request, 'too_fast');
        }

        $expected = (int) session('quote_captcha_total');
        $answer = (int) $request->input('captcha_answer');
        if ($expected === 0 || $answer !== $expected) {
            session()->forget('quote_captcha_total');

            return $this->errorResponse($request, 'invalid_captcha');
        }
        session()->forget('quote_captcha_total');

        if ($request->filled('website')) {
            return $this->errorResponse($request, 'honeypot');
        }

        $service = Service::query()->where('slug', $request->input('service'))->firstOrFail();

        $attachmentPath = null;
        $attachmentName = null;
        $file = $request->file('attachment');

        if ($file && $file->isValid()) {
            if (! $scanner->scan($file)) {
                return $this->errorResponse($request, 'invalid_file');
            }

            $attachmentName = basename($file->getClientOriginalName());
            $extension = strtolower($file->getClientOriginalExtension());

            if (! in_array($extension, config('quote.attachment.extensions'), true)) {
                $extension = $file->extension() ?: 'bin';
            }

            $storedName = Str::uuid().'.'.$extension;
            $attachmentPath = $file->storeAs('attachments', $storedName, 'local');
        }

        $quote = QuoteRequest::query()->create([
            'locale' => app()->getLocale(),
            'name' => $request->input('name'),
            'company' => $request->input('company'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'service_id' => $service->id,
            'location_key' => $request->input('location'),
            'location_other' => $request->input('location_other'),
            'message' => $request->input('message'),
            'attachment_path' => $attachmentPath,
            'attachment_original_name' => $attachmentName,
            'status' => QuoteRequestStatus::New,
        ]);

        $recipients = collect(data_get(SiteSetting::current(), 'quote_recipients', ['hello@energea.me']))
            ->filter(fn ($email) => is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL))
            ->all();

        if (empty($recipients)) {
            $recipients = ['hello@energea.me'];
        }

        try {
            Mail::to($recipients)->send(new QuoteRequestSubmitted($quote));
            $quote->update(['mail_sent_at' => now(), 'mail_error' => null]);
        } catch (\Throwable $exception) {
            Log::error('Quote request email failed', ['quote_id' => $quote->id, 'error' => $exception->getMessage()]);
            $quote->update(['mail_error' => Str::limit($exception->getMessage(), 1000)]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'ok',
                'message' => __('contact.messages.success'),
            ]);
        }

        return redirect()->route('contact', ['locale' => app()->getLocale()])
            ->with('quote_status', 'success')
            ->with('quote_message', __('contact.messages.success'));
    }

    public function captcha(): JsonResponse
    {
        $first = random_int(2, 8);
        $second = random_int(1, 7);
        $total = $first + $second;

        session(['quote_captcha_total' => $total]);

        return response()->json([
            'question' => __('contact.captcha_question', ['first' => $first, 'second' => $second]),
        ]);
    }

    /**
     * @return array<int, int>
     */
    private function issuedFormTimestamps(): array
    {
        $timestamps = session('quote_form_ts', []);

        if (! is_array($timestamps)) {
            $timestamps = is_numeric($timestamps) ? [$timestamps] : [];
        }

        return array_values(array_map('intval', $timestamps));
    }

    private function errorResponse(QuoteRequestStoreRequest $request, string $key): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'key' => $key,
                'message' => __('contact.messages.'.$key),
            ], 422);
        }

        return redirect()->route('contact', ['locale' => app()->getLocale()])
            ->withInput()
            ->with('quote_status', 'error')
            ->with('quote_message', __('contact.messages.'.$key));
    }
}
