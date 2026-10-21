<?php

namespace Database\Factories;

use App\Models\PageContent;
use App\Support\HomeContent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PageContent>
 */
class PageContentFactory extends Factory
{
    protected $model = PageContent::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'page' => HomeContent::PAGE,
            'content' => HomeContent::defaults(),
        ];
    }
}
