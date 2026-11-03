<?php

namespace Tests\Feature;

use App\Mail\QuoteRequestSubmitted;
use App\Models\QuoteRequest;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuoteRequestMailTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_quote_request_email_renders_html_and_plain_text_versions(): void
    {
        $service = Service::factory()->create();

        $quote = QuoteRequest::factory()->create([
            'name' => 'Test Buyer',
            'company' => 'Test Company',
            'email' => 'buyer@example.com',
            'service_id' => $service->id,
            'message' => 'We need testing for a substation.',
        ]);

        $mailable = new QuoteRequestSubmitted($quote);

        $mailable->assertHasSubject('New quote request from Test Buyer — Energea');
        $mailable->assertSeeInHtml('<!DOCTYPE html>', false);
        $mailable->assertSeeInHtml('data:image/png;base64,', false);
        $mailable->assertSeeInHtml('Test Buyer');
        $mailable->assertSeeInHtml('Test Company');
        $mailable->assertSeeInHtml('buyer@example.com');
        $mailable->assertSeeInHtml('We need testing for a substation.');
        $mailable->assertSeeInText('Test Buyer');
        $mailable->assertSeeInText('Test Company');
        $mailable->assertSeeInText('buyer@example.com');
        $mailable->assertSeeInText('We need testing for a substation.');
    }

    #[Test]
    public function the_quote_request_email_replies_to_the_submitter(): void
    {
        $quote = QuoteRequest::factory()->create([
            'name' => 'Test Buyer',
            'email' => 'buyer@example.com',
        ]);

        (new QuoteRequestSubmitted($quote))
            ->assertHasReplyTo('buyer@example.com', 'Test Buyer');
    }

    #[Test]
    public function the_quote_request_email_skips_an_invalid_reply_to_address(): void
    {
        $quote = QuoteRequest::factory()->create([
            'email' => 'not-an-email',
        ]);

        $this->assertSame([], (new QuoteRequestSubmitted($quote))->envelope()->replyTo);
    }
}
