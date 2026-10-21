<p><strong>Name:</strong> {{ $quote->name }}</p>
<p><strong>Company:</strong> {{ $quote->company }}</p>
<p><strong>Email:</strong> {{ $quote->email }}</p>
<p><strong>Phone:</strong> {{ $quote->phone }}</p>
<p><strong>Locale:</strong> {{ $quote->locale }}</p>
<p><strong>Service:</strong> {{ $quote->service?->translate('title') ?? '-' }}</p>
<p><strong>Location:</strong> {{ __('contact.cities.'.$quote->location_key) }} @if ($quote->location_key === 'other' && $quote->location_other) — {{ $quote->location_other }} @endif</p>
<p><strong>Message:</strong></p>
<p>{!! nl2br(e($quote->message)) !!}</p>
@if ($quote->attachment_original_name)
    <p><strong>Attachment:</strong> {{ $quote->attachment_original_name }}</p>
@endif
<p><strong>Submitted at:</strong> {{ $quote->created_at }}</p>
