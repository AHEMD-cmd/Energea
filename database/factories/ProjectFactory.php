<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Sector;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'slug' => fake()->unique()->slug(3),
            'sector_id' => null,
            'image' => 'projects/demo.webp',
            'year' => (string) fake()->numberBetween(2018, 2026),
            'title' => ['en' => $title, 'ar' => $title],
            'location' => ['en' => 'Riyadh, KSA', 'ar' => 'الرياض، السعودية'],
            'scope' => ['en' => fake()->sentence(), 'ar' => fake()->sentence()],
            'systems' => ['en' => fake()->sentence(), 'ar' => fake()->sentence()],
            'seo_title' => null,
            'seo_description' => null,
            'is_published' => true,
            'sort_order' => 0,
        ];
    }

    public function forSector(?Sector $sector = null): static
    {
        return $this->state(fn (): array => [
            'sector_id' => $sector?->id ?? Sector::factory(),
        ]);
    }
}
