<?php

namespace App\Mail;

use App\Models\QuoteRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class QuoteRequestSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public QuoteRequest $quoteRequest) {}

    public function envelope(): Envelope
    {
        $replyTo = filter_var($this->quoteRequest->email, FILTER_VALIDATE_EMAIL)
            ? [new Address($this->quoteRequest->email, $this->quoteRequest->name)]
            : [];

        return new Envelope(
            subject: 'New quote request from '.$this->quoteRequest->name.' — Energea',
            replyTo: $replyTo,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.quote-request',
            text: 'emails.quote-request-text',
            with: ['quote' => $this->quoteRequest],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        if (! $this->quoteRequest->attachment_path) {
            return [];
        }

        $disk = Storage::disk('local');

        if (! $disk->exists($this->quoteRequest->attachment_path)) {
            return [];
        }

        $attachment = Attachment::fromStorageDisk('local', $this->quoteRequest->attachment_path);

        if ($this->quoteRequest->attachment_original_name) {
            $attachment = $attachment->as($this->quoteRequest->attachment_original_name);
        }

        return [$attachment];
    }
}
