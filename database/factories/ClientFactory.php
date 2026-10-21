<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => ['en' => fake()->unique()->company()],
            'logo' => 'clients/logo-demo.png',
            'logo_code' => str_pad((string) fake()->unique()->numberBetween(0, 999), 3, '0', STR_PAD_LEFT),
            'is_published' => true,
            'sort_order' => 0,
        ];
    }
}
