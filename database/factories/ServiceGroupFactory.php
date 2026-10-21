<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceGroup>
 */
class ServiceGroupFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'title' => ['en' => fake()->words(3, true), 'ar' => 'مجموعة'],
            'sort_order' => 0,
        ];
    }
}
