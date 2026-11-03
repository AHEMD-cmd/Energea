New quote request from {{ $quote->name }}

Name: {{ $quote->name }}
@if ($quote->company)
Company: {{ $quote->company }}
@endif
Email: {{ $quote->email }}
Phone: {{ $quote->phone }}
Service: {{ $quote->service?->translate('title') ?? '—' }}
Location: {{ __('contact.cities.'.$quote->location_key) }}@if ($quote->location_key === 'other' && $quote->location_other) — {{ $quote->location_other }}@endif
Submitted: {{ $quote->created_at?->format('M j, Y H:i') }}
@if ($quote->attachment_original_name)
Attachment: {{ $quote->attachment_original_name }}
@endif

Message:
{!! $quote->message !!}

--
Energea — Energy Solutions Technologies
