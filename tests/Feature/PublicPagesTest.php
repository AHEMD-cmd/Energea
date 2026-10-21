<?php

namespace Tests\Feature;

use App\Enums\PartyType;
use App\Models\Certificate;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectParty;
use App\Models\Sector;
use App\Models\Service;
use App\Models\ServiceGroup;
use App\Models\ServiceGroupItem;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_root_path_redirects_to_the_default_locale(): void
    {
        $this->get('/')->assertRedirect('/en');
        $this->get('/index.html')->assertRedirect('/en');
    }

    #[Test]
    public function every_public_page_renders_in_both_locales(): void
    {
        $this->seedContent();

        $paths = [
            '',
            '/about',
            '/services',
            '/services/electrical-testing',
            '/projects',
            '/projects/makkah-haram',
            '/clients',
            '/certificates',
            '/contact',
        ];

        foreach (['en', 'ar'] as $locale) {
            foreach ($paths as $path) {
                $this->get("/{$locale}{$path}")->assertOk();
            }
        }
    }

    #[Test]
    public function pages_render_localized_content_and_direction(): void
    {
        $this->seedContent();

        $this->get('/en/about')
            ->assertOk()
            ->assertSee('dir="ltr"', false)
            ->assertSee(__('about.hero_title', [], 'en'));

        $this->get('/ar/about')
            ->assertOk()
            ->assertSee('dir="rtl"', false)
            ->assertSee(__('about.hero_title', [], 'ar'));
    }

    #[Test]
    public function the_projects_listing_is_paginated_five_per_page(): void
    {
        $this->seedContent();

        for ($index = 0; $index < 6; $index++) {
            Project::factory()->create(['sort_order' => $index]);
        }

        $first = $this->get('/en/projects')->assertOk();

        $this->assertSame(5, substr_count($first->getContent(), 'project-stack-item'));

        $first->assertSee('?page=2', false);

        $second = $this->get('/en/projects?page=2')->assertOk();
        $this->assertSame(2, substr_count($second->getContent(), 'project-stack-item'));
    }

    #[Test]
    public function unpublished_records_hidden_from_the_public_lists(): void
    {
        $this->seedContent();

        Service::factory()->unpublished()->create(['slug' => 'hidden-service']);

        $this->get('/en/services')->assertOk()->assertDontSee('hidden-service');
        $this->get('/en/services/hidden-service')->assertNotFound();
    }

    #[Test]
    public function legacy_preview_urls_redirect_to_the_new_routes(): void
    {
        $this->seedContent();

        $this->get('/en/index.html')->assertRedirect('/en');
        $this->get('/en/about.html')->assertRedirect('/en/about');
        $this->get('/en/projects.html?page=2')->assertRedirect('/en/projects?page=2');
        $this->get('/en/service.html?slug=electrical-testing')
            ->assertRedirect('/en/services/electrical-testing');
        $this->get('/en/project.html?slug=makkah-haram')
            ->assertRedirect('/en/projects/makkah-haram');
    }

    #[Test]
    public function unknown_pages_return_the_localized_not_found_view(): void
    {
        $this->get('/en/does-not-exist')
            ->assertNotFound()
            ->assertSee(__('common.not_found_title', [], 'en'));
    }

    private function seedContent(): void
    {
        $settings = SiteSetting::factory()->create();

        $sector = Sector::factory()->create(['sort_order' => 0]);

        $service = Service::factory()->create([
            'slug' => 'electrical-testing',
            'sort_order' => 0,
            'steps' => null,
            'facts' => null,
        ]);

        $group = ServiceGroup::factory()->create(['service_id' => $service->id]);
        ServiceGroupItem::factory()->create(['service_group_id' => $group->id]);

        $project = Project::factory()->create([
            'slug' => 'makkah-haram',
            'sector_id' => $sector->id,
            'sort_order' => 0,
        ]);

        ProjectParty::factory()->for($project)->type(PartyType::Client)->create();

        Client::factory()->create(['sort_order' => 0]);
        Certificate::factory()->create(['sort_order' => 0]);

        $settings->update(['og_image' => null]);
    }
}
