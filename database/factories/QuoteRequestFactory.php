<?php

namespace Database\Factories;

use App\Enums\QuoteRequestStatus;
use App\Models\QuoteRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuoteRequest>
 */
class QuoteRequestFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'locale' => 'en',
            'name' => fake()->name(),
            'company' => fake()->company(),
            'email' => fake()->safeEmail(),
            'phone' => '+966 5'.fake()->numerify('########'),
            'service_id' => null,
            'location_key' => 'riyadh',
            'location_other' => null,
            'message' => fake()->paragraph(),
            'attachment_path' => null,
            'attachment_original_name' => null,
            'status' => QuoteRequestStatus::New,
            'mail_sent_at' => null,
            'mail_error' => null,
        ];
    }

    public function handled(): static
    {
        return $this->state(fn (): array => ['status' => QuoteRequestStatus::Handled]);
    }
}
