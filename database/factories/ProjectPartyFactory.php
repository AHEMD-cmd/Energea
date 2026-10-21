<?php

namespace Database\Factories;

use App\Enums\PartyType;
use App\Models\Project;
use App\Models\ProjectParty;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectParty>
 */
class ProjectPartyFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'party_type' => PartyType::Client,
            'name' => ['en' => fake()->company(), 'ar' => 'شركة'],
            'logo' => 'projects/parties/demo.png',
            'sort_order' => 0,
        ];
    }

    public function type(PartyType $type): static
    {
        return $this->state(fn (): array => ['party_type' => $type]);
    }
}
