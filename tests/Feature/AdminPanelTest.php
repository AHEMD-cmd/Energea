<?php

namespace Tests\Feature;

use App\Enums\PartyType;
use App\Models\Certificate;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectParty;
use App\Models\QuoteRequest;
use App\Models\Sector;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guests_are_redirected_to_the_login_page(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    #[Test]
    public function non_admin_users_cannot_access_the_panel(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    #[Test]
    public function admins_can_access_every_panel_page(): void
    {
        $this->seedContent();

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        $this->get('/admin')->assertOk();
        $this->get('/admin/services')->assertOk();
        $this->get('/admin/projects')->assertOk();
        $this->get('/admin/clients')->assertOk();
        $this->get('/admin/certificates')->assertOk();
        $this->get('/admin/sectors')->assertOk();
        $this->get('/admin/quote-requests')->assertOk();
        $this->get('/admin/manage-site-settings')->assertOk();

        $service = Service::query()->firstOrFail();
        $this->get("/admin/services/{$service->id}/edit")->assertOk();

        $project = Project::query()->firstOrFail();
        $this->get("/admin/projects/{$project->id}/edit")->assertOk();

        $quote = QuoteRequest::query()->firstOrFail();
        $this->get("/admin/quote-requests/{$quote->id}")->assertOk();
    }

    #[Test]
    public function the_make_admin_command_promotes_and_creates_users(): void
    {
        $existing = User::factory()->create(['email' => 'owner@example.com']);

        $this->artisan('energea:make-admin', ['email' => 'owner@example.com'])
            ->assertSuccessful();

        $this->assertTrue($existing->refresh()->is_admin);

        $this->artisan('energea:make-admin', [
            'email' => 'new-admin@example.com',
            '--name' => 'New Admin',
            '--password' => 'secret-password',
        ])->assertSuccessful();

        $created = User::query()->where('email', 'new-admin@example.com')->firstOrFail();

        $this->assertTrue($created->is_admin);
        $this->assertSame('New Admin', $created->name);
    }

    private function seedContent(): void
    {
        $sector = Sector::factory()->create();
        $service = Service::factory()->create();
        $project = Project::factory()->forSector($sector)->create();

        ProjectParty::factory()->for($project)->type(PartyType::Client)->create();
        Client::factory()->create();
        Certificate::factory()->create();
        QuoteRequest::factory()->create(['service_id' => $service->id]);
        SiteSetting::factory()->create();
    }
}
