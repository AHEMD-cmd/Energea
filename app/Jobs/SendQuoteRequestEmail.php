<?php

namespace App\Jobs;

use App\Mail\QuoteRequestSubmitted;
use App\Models\QuoteRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class SendQuoteRequestEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [60, 300];

    public function __construct(public int $quoteId, public array $recipients) {}

    public function handle(): void
    {
        $quote = QuoteRequest::find($this->quoteId);

        if (! $quote) {
            return;
        }

        Mail::to($this->recipients)->send(new QuoteRequestSubmitted($quote));

        $quote->update(['mail_sent_at' => now(), 'mail_error' => null]);
    }

    public function failed(Throwable $exception): void
    {
        QuoteRequest::whereKey($this->quoteId)->update([
            'mail_error' => Str::limit($exception->getMessage(), 1000),
        ]);
    }
}
