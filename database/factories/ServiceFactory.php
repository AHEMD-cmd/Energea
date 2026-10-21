<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'slug' => fake()->unique()->slug(3),
            'number' => str_pad((string) fake()->numberBetween(1, 99), 2, '0', STR_PAD_LEFT),
            'image' => 'services/demo.webp',
            'title' => ['en' => $title, 'ar' => $title],
            'type' => ['en' => 'Core Engineering', 'ar' => 'هندسة أساسية'],
            'summary' => ['en' => fake()->sentence(), 'ar' => fake()->sentence()],
            'overview' => ['en' => fake()->paragraph(), 'ar' => fake()->paragraph()],
            'steps' => null,
            'facts' => null,
            'seo_title' => null,
            'seo_description' => null,
            'is_published' => true,
            'sort_order' => 0,
        ];
    }

    public function unpublished(): static
    {
        return $this->state(fn (): array => ['is_published' => false]);
    }

    public function withStepsAndFacts(): static
    {
        return $this->state(fn (): array => [
            'steps' => ['en' => ['Step one', 'Step two', 'Step three'], 'ar' => ['خطوة', 'خطوة', 'خطوة']],
            'facts' => ['en' => ['Fact one', 'Fact two'], 'ar' => ['حقيقة', 'حقيقة']],
        ]);
    }
}
