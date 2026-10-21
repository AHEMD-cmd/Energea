<?php

namespace Database\Factories;

use App\Models\ServiceGroup;
use App\Models\ServiceGroupItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceGroupItem>
 */
class ServiceGroupItemFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_group_id' => ServiceGroup::factory(),
            'text' => ['en' => fake()->sentence(), 'ar' => 'عنصر'],
            'sort_order' => 0,
        ];
    }
}
