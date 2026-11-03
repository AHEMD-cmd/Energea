<?php

namespace Tests\Feature;

use App\Mail\QuoteRequestSubmitted;
use App\Models\QuoteRequest;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuoteRequestTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_captcha_endpoint_returns_only_the_question_and_keeps_the_answer_in_the_session(): void
    {
        $response = $this->getJson('/en/contact/captcha')->assertOk();

        $response->assertJsonStructure(['question'])->assertJsonMissingPath('total');

        $this->assertIsInt(session('quote_captcha_total'));
    }

    #[Test]
    public function a_wrong_captcha_answer_is_rejected_without_storing_the_request(): void
    {
        $this->seedDependencies();

        $timestamp = now()->subSeconds(10)->timestamp;

        $this->withSession([
            'quote_form_ts' => $timestamp,
            'quote_captcha_total' => 7,
        ]);

        $this->postJson('/en/contact', $this->payload($timestamp, 5))
            ->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'key' => 'invalid_captcha',
                'message' => __('contact.messages.invalid_captcha'),
            ]);

        $this->assertDatabaseCount(QuoteRequest::class, 0);
    }

    #[Test]
    public function a_valid_quote_request_is_stored_and_answered_with_json(): void
    {
        Mail::fake();

        $this->seedDependencies();

        $timestamp = now()->subSeconds(10)->timestamp;

        $this->withSession([
            'quote_form_ts' => $timestamp,
            'quote_captcha_total' => 7,
        ]);

        $this->postJson('/en/contact', $this->payload($timestamp, 7))
            ->assertOk()
            ->assertJson([
                'status' => 'ok',
                'message' => __('contact.messages.success'),
            ]);

        $this->assertDatabaseCount(QuoteRequest::class, 1);
        $this->assertDatabaseHas('quote_requests', [
            'email' => 'buyer@example.com',
            'location_key' => 'riyadh',
            'status' => 'new',
        ]);

        Mail::assertSent(QuoteRequestSubmitted::class);
    }

    #[Test]
    public function a_submission_without_the_json_header_redirects_back_to_the_form(): void
    {
        Mail::fake();

        $this->seedDependencies();

        $timestamp = now()->subSeconds(10)->timestamp;

        $this->withSession([
            'quote_form_ts' => $timestamp,
            'quote_captcha_total' => 7,
        ]);

        $this->post('/en/contact', $this->payload($timestamp, 7))
            ->assertRedirect('/en/contact')
            ->assertSessionHas('quote_status', 'success');

        $this->assertDatabaseCount(QuoteRequest::class, 1);
    }

    #[Test]
    public function a_submission_filled_too_quickly_is_rejected(): void
    {
        $this->seedDependencies();

        $timestamp = now()->timestamp;

        $this->withSession([
            'quote_form_ts' => $timestamp,
            'quote_captcha_total' => 7,
        ]);

        $this->postJson('/en/contact', $this->payload($timestamp, 7))
            ->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'key' => 'too_fast',
            ]);

        $this->assertDatabaseCount(QuoteRequest::class, 0);
    }

    #[Test]
    public function a_form_from_an_earlier_page_load_is_still_accepted(): void
    {
        Mail::fake();

        $this->seedDependencies();

        $oldTimestamp = now()->subSeconds(10)->timestamp;
        $newTimestamp = now()->timestamp;

        $this->withSession([
            'quote_form_ts' => [$oldTimestamp, $newTimestamp],
            'quote_captcha_total' => 7,
        ]);

        $this->postJson('/en/contact', $this->payload($oldTimestamp, 7))
            ->assertOk()
            ->assertJson(['status' => 'ok']);

        $this->assertDatabaseCount(QuoteRequest::class, 1);
    }

    #[Test]
    public function a_form_timestamp_that_was_not_issued_by_the_server_is_rejected(): void
    {
        $this->seedDependencies();

        $issuedTimestamp = now()->subSeconds(10)->timestamp;

        $this->withSession([
            'quote_form_ts' => [$issuedTimestamp + 1],
            'quote_captcha_total' => 7,
        ]);

        $this->postJson('/en/contact', $this->payload($issuedTimestamp, 7))
            ->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'key' => 'too_fast',
            ]);

        $this->assertDatabaseCount(QuoteRequest::class, 0);
    }

    #[Test]
    public function an_attachment_is_stored_on_the_private_disk_and_never_on_the_public_one(): void
    {
        Mail::fake();
        Storage::fake('local');
        Storage::fake('public');

        $this->seedDependencies();

        $timestamp = now()->subSeconds(10)->timestamp;

        $this->withSession([
            'quote_form_ts' => $timestamp,
            'quote_captcha_total' => 7,
        ]);

        $this->postJson('/en/contact', [
            ...$this->payload($timestamp, 7),
            'attachment' => UploadedFile::fake()->create('specification.pdf', 10, 'application/pdf'),
        ])->assertOk();

        $quote = QuoteRequest::query()->firstOrFail();

        $this->assertNotNull($quote->attachment_path);
        $this->assertStringStartsWith('attachments/', $quote->attachment_path);

        Storage::disk('local')->assertExists($quote->attachment_path);
        Storage::disk('public')->assertMissing($quote->attachment_path);
    }

    #[Test]
    public function guests_are_redirected_and_non_admins_are_forbidden_from_downloading_attachments(): void
    {
        $quote = $this->quoteWithAttachment();

        $this->get(route('admin.quote-requests.attachment', $quote))
            ->assertRedirect('/admin/login');

        $this->actingAs(User::factory()->create())
            ->get(route('admin.quote-requests.attachment', $quote))
            ->assertForbidden();
    }

    #[Test]
    public function admins_can_download_the_attachment_with_its_original_name(): void
    {
        $quote = $this->quoteWithAttachment();

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.quote-requests.attachment', $quote))
            ->assertOk()
            ->assertDownload('specification.pdf');
    }

    private function quoteWithAttachment(): QuoteRequest
    {
        Storage::fake('local');

        $quote = QuoteRequest::factory()->create([
            'attachment_path' => 'attachments/specification.pdf',
            'attachment_original_name' => 'specification.pdf',
        ]);

        Storage::disk('local')->put('attachments/specification.pdf', 'file contents');

        return $quote;
    }

    private function seedDependencies(): void
    {
        SiteSetting::factory()->create();
        Service::factory()->create(['slug' => 'electrical-testing']);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(int $timestamp, int $captchaAnswer): array
    {
        return [
            'name' => 'Test Buyer',
            'company' => 'Test Company',
            'email' => 'buyer@example.com',
            'phone' => '+966500000000',
            'service' => 'electrical-testing',
            'location' => 'riyadh',
            'message' => 'We need testing for a substation.',
            'website' => '',
            'captcha_answer' => $captchaAnswer,
            'form_ts' => $timestamp,
        ];
    }
}
