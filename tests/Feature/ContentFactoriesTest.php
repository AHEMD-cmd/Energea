<?php

namespace Tests\Feature;

use App\Enums\PartyType;
use App\Enums\QuoteRequestStatus;
use App\Models\Certificate;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectParty;
use App\Models\QuoteRequest;
use App\Models\Sector;
use App\Models\Service;
use App\Models\ServiceGroup;
use App\Models\ServiceGroupItem;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ContentFactoriesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_published_content_through_factories(): void
    {
        $service = Service::factory()->withStepsAndFacts()->create();
        $group = ServiceGroup::factory()->for($service)->create();
        $item = ServiceGroupItem::factory()->for($group, 'group')->create();

        $sector = Sector::factory()->create();
        $project = Project::factory()->forSector($sector)->create();
        $party = ProjectParty::factory()->for($project)->type(PartyType::Consultant)->create();

        $client = Client::factory()->create();
        $certificate = Certificate::factory()->create();

        $this->assertTrue($service->is_published);
        $this->assertNotEmpty($service->steps['en']);
        $this->assertNotEmpty($service->facts['en']);
        $this->assertTrue($group->service->is($service));
        $this->assertTrue($item->group->is($group));
        $this->assertTrue($project->sector->is($sector));
        $this->assertTrue($party->project->is($project));
        $this->assertSame(PartyType::Consultant, $party->party_type);
        $this->assertNotNull($client->logo);
        $this->assertNotNull($certificate->image);
    }

    #[Test]
    public function it_creates_quote_requests_with_status_defaults(): void
    {
        $quote = QuoteRequest::factory()->create();
        $handled = QuoteRequest::factory()->handled()->create();

        $this->assertSame(QuoteRequestStatus::New, $quote->status);
        $this->assertSame('riyadh', $quote->location_key);
        $this->assertNull($quote->service_id);
        $this->assertSame(QuoteRequestStatus::Handled, $handled->status);
    }

    #[Test]
    public function it_creates_the_site_setting_singleton(): void
    {
        $settings = SiteSetting::factory()->create();

        $this->assertSame('Energea', $settings->company_name);
        $this->assertSame('hello@energea.me', $settings->general_email);
        $this->assertNotEmpty($settings->footer_text['ar']);
    }
}
