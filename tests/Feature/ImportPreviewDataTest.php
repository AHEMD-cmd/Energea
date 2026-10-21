<?php

namespace Tests\Feature;

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
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ImportPreviewDataTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_imports_the_preview_catalog_idempotently(): void
    {
        $path = config('energea.frontend_path');

        if (! is_string($path) || ! is_dir($path)) {
            $this->markTestSkipped('Frontend preview directory is not available.');
        }

        Storage::fake('public');

        $this->artisan('energea:import-preview', ['--path' => $path])->assertSuccessful();
        $this->artisan('energea:import-preview', ['--path' => $path])->assertSuccessful();

        $this->assertSame(9, Service::count());
        $this->assertSame(14, ServiceGroup::count());
        $this->assertSame(60, ServiceGroupItem::count());
        $this->assertSame(8, Sector::count());
        $this->assertSame(10, Project::count());
        $this->assertSame(14, ProjectParty::count());
        $this->assertSame(38, Client::count());
        $this->assertSame(18, Certificate::count());
        $this->assertSame(1, SiteSetting::count());

        $this->assertSame(0, Project::whereNotNull('sector_id')->count());

        $service = Service::where('slug', 'air-duct-cleaning')->firstOrFail();
        $this->assertNotNull($service->steps);
        $this->assertNotNull($service->facts);
        $this->assertGreaterThan(0, $service->groups()->count());

        $other = Service::where('slug', 'electrical-testing')->firstOrFail();
        $this->assertNull($other->steps);
        $this->assertNull($other->facts);

        foreach (Client::all() as $client) {
            Storage::disk('public')->assertExists($client->logo);
        }

        foreach (Certificate::all() as $certificate) {
            Storage::disk('public')->assertExists($certificate->image);
        }

        $project = Project::where('slug', 'sheybarah')->firstOrFail();
        $this->assertNull($project->systems);
        $this->assertSame('2024', $project->year);

        $settings = SiteSetting::firstOrFail();
        $this->assertSame('Energea', $settings->company_name);
        $this->assertSame(['hello@energea.me'], $settings->quote_recipients);
        $this->assertCount(2, $settings->departments);
        $this->assertCount(3, $settings->offices);
    }

    #[Test]
    public function it_fails_when_the_preview_directory_is_missing(): void
    {
        $this->artisan('energea:import-preview', ['--path' => 'C:/missing-energea-preview'])
            ->assertFailed();
    }

    #[Test]
    public function it_fails_loudly_on_unexpected_catalog_counts(): void
    {
        $directory = sys_get_temp_dir().'/energea-import-fixture-'.uniqid();
        mkdir($directory.'/assets/js', 0777, true);
        file_put_contents($directory.'/assets/js/data.js', '/* test */ window.ENERGEA_DATA = {"services":[],"serviceDetails":{},"projects":[],"sectors":[],"clients":[],"certifications":[]};');

        try {
            $this->artisan('energea:import-preview', ['--path' => $directory])
                ->expectsOutputToContain('Expected 9 entries in services, found 0.')
                ->assertFailed();
        } finally {
            unlink($directory.'/assets/js/data.js');
            rmdir($directory.'/assets/js');
            rmdir($directory.'/assets');
            rmdir($directory);
        }
    }
}
