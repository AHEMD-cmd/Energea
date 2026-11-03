<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <title>New quote request from {{ $quote->name }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f1f0ea;">
    <div style="display:none;max-height:0;max-width:0;overflow:hidden;opacity:0;color:transparent;">
        New quote request from {{ $quote->name }}@if ($quote->company) ({{ $quote->company }})@endif
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f1f0ea;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:600px;background-color:#ffffff;border-radius:10px;overflow:hidden;font-family:Arial,Helvetica,sans-serif;color:#090c0d;">
                    <tr>
                        <td style="height:6px;background-color:#ff681d;font-size:0;line-height:0;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px 0;">
                            <img src="{{ $message->embed(public_path('assets/images/energea-logo.png')) }}" alt="Energea" width="150" style="display:block;width:150px;max-width:150px;height:auto;border:0;">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px 32px 0;">
                            <p style="margin:0 0 6px;font-size:12px;line-height:18px;letter-spacing:.08em;text-transform:uppercase;color:#ff681d;font-weight:700;">
                                New quote request
                            </p>
                            <h1 style="margin:0;font-size:22px;line-height:30px;color:#090c0d;">
                                {{ $quote->name }}@if ($quote->company) <span style="color:#68706e;font-weight:400;">&mdash; {{ $quote->company }}</span>@endif
                            </h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px 32px 0;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                                <tr>
                                    <td style="width:120px;padding:10px 0;border-top:1px solid #e3e2da;font-size:13px;line-height:20px;color:#68706e;vertical-align:top;">Name</td>
                                    <td style="padding:10px 0;border-top:1px solid #e3e2da;font-size:14px;line-height:20px;color:#090c0d;vertical-align:top;">{{ $quote->name }}</td>
                                </tr>
                                @if ($quote->company)
                                    <tr>
                                        <td style="width:120px;padding:10px 0;border-top:1px solid #e3e2da;font-size:13px;line-height:20px;color:#68706e;vertical-align:top;">Company</td>
                                        <td style="padding:10px 0;border-top:1px solid #e3e2da;font-size:14px;line-height:20px;color:#090c0d;vertical-align:top;">{{ $quote->company }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td style="width:120px;padding:10px 0;border-top:1px solid #e3e2da;font-size:13px;line-height:20px;color:#68706e;vertical-align:top;">Email</td>
                                    <td style="padding:10px 0;border-top:1px solid #e3e2da;font-size:14px;line-height:20px;vertical-align:top;">
                                        <a href="mailto:{{ $quote->email }}" style="color:#de4e0a;text-decoration:none;">{{ $quote->email }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:120px;padding:10px 0;border-top:1px solid #e3e2da;font-size:13px;line-height:20px;color:#68706e;vertical-align:top;">Phone</td>
                                    <td style="padding:10px 0;border-top:1px solid #e3e2da;font-size:14px;line-height:20px;vertical-align:top;">
                                        <a href="tel:{{ preg_replace('/\s+/', '', (string) $quote->phone) }}" style="color:#de4e0a;text-decoration:none;">{{ $quote->phone }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:120px;padding:10px 0;border-top:1px solid #e3e2da;font-size:13px;line-height:20px;color:#68706e;vertical-align:top;">Service</td>
                                    <td style="padding:10px 0;border-top:1px solid #e3e2da;font-size:14px;line-height:20px;color:#090c0d;vertical-align:top;">{{ $quote->service?->translate('title') ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td style="width:120px;padding:10px 0;border-top:1px solid #e3e2da;font-size:13px;line-height:20px;color:#68706e;vertical-align:top;">Location</td>
                                    <td style="padding:10px 0;border-top:1px solid #e3e2da;font-size:14px;line-height:20px;color:#090c0d;vertical-align:top;">
                                        {{ __('contact.cities.'.$quote->location_key) }}@if ($quote->location_key === 'other' && $quote->location_other) &mdash; {{ $quote->location_other }}@endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:120px;padding:10px 0;border-top:1px solid #e3e2da;font-size:13px;line-height:20px;color:#68706e;vertical-align:top;">Submitted</td>
                                    <td style="padding:10px 0;border-top:1px solid #e3e2da;font-size:14px;line-height:20px;color:#090c0d;vertical-align:top;">{{ $quote->created_at?->format('M j, Y H:i') }}</td>
                                </tr>
                                @if ($quote->attachment_original_name)
                                    <tr>
                                        <td style="width:120px;padding:10px 0;border-top:1px solid #e3e2da;font-size:13px;line-height:20px;color:#68706e;vertical-align:top;">Attachment</td>
                                        <td style="padding:10px 0;border-top:1px solid #e3e2da;font-size:14px;line-height:20px;color:#090c0d;vertical-align:top;">{{ $quote->attachment_original_name }}</td>
                                    </tr>
                                @endif
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px 32px 0;">
                            <p style="margin:0 0 8px;font-size:13px;line-height:20px;color:#68706e;font-weight:700;">Message</p>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td dir="auto" style="padding:16px;background-color:#f1f0ea;border-left:3px solid #ff681d;font-size:14px;line-height:22px;color:#202526;">
                                        {!! nl2br(e($quote->message)) !!}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px 32px;">
                            <p style="margin:0;font-size:12px;line-height:18px;color:#68706e;">
                                This notification was sent from the Energea website contact form.
                            </p>
                        </td>
                    </tr>
                </table>
                <p style="margin:16px 0 0;font-family:Arial,Helvetica,sans-serif;font-size:11px;line-height:16px;color:#68706e;">
                    Energea &mdash; Energy Solutions Technologies
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
