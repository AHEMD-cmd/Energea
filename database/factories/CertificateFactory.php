<?php

namespace Database\Factories;

use App\Models\Certificate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Certificate>
 */
class CertificateFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => ['en' => 'ISO '.fake()->numberBetween(9001, 45001)],
            'type' => ['en' => fake()->words(2, true), 'ar' => 'اعتماد'],
            'image' => 'certificates/demo.png',
            'is_published' => true,
            'sort_order' => 0,
        ];
    }
}
