<?php

namespace Database\Factories;

use App\Models\Sector;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sector>
 */
class SectorFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('?#'),
            'title' => ['en' => fake()->words(2, true), 'ar' => 'قطاع'],
            'text' => ['en' => fake()->sentence(), 'ar' => fake()->sentence()],
            'is_published' => true,
            'sort_order' => 0,
        ];
    }
}
