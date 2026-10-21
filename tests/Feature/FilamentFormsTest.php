<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageSiteSettings;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\RelationManagers\PartiesRelationManager;
use App\Filament\Resources\Services\Pages\EditService;
use App\Filament\Resources\Services\RelationManagers\GroupsRelationManager;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\User;
use Filament\Actions\CreateAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FilamentFormsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create());
    }

    #[Test]
    public function the_service_edit_form_saves_bilingual_fields(): void
    {
        $service = Service::factory()->create();

        Livewire::test(EditService::class, ['record' => $service->getRouteKey()])
            ->fillForm([
                'image' => [$service->image],
                'title' => ['en' => 'Updated title', 'ar' => 'عنوان محدث'],
                'summary' => ['en' => 'Updated summary', 'ar' => 'ملخص محدث'],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $service->refresh();

        $this->assertSame('Updated title', $service->title['en']);
        $this->assertSame('عنوان محدث', $service->title['ar']);
    }

    #[Test]
    public function service_groups_relation_manager_renders(): void
    {
        $service = Service::factory()->create();

        Livewire::test(GroupsRelationManager::class, [
            'ownerRecord' => $service,
            'pageClass' => EditService::class,
        ])->assertSuccessful();
    }

    #[Test]
    public function a_service_group_can_be_created_with_items(): void
    {
        $service = Service::factory()->create();

        Livewire::test(GroupsRelationManager::class, [
            'ownerRecord' => $service,
            'pageClass' => EditService::class,
        ])
            ->callTableAction(CreateAction::class, data: [
                'title' => ['en' => 'Fire safety', 'ar' => 'السلامة من الحرائق'],
                'items' => [
                    ['text' => ['en' => 'Item A', 'ar' => 'عنصر أ']],
                    ['text' => ['en' => 'Item B', 'ar' => 'عنصر ب']],
                ],
            ])
            ->assertHasNoActionErrors();

        $group = $service->groups()->where('title->en', 'Fire safety')->firstOrFail();

        $this->assertSame('السلامة من الحرائق', $group->title['ar']);
        $this->assertCount(2, $group->items);
        $this->assertSame('عنصر ب', $group->items->get(1)->text['ar']);
    }

    #[Test]
    public function the_project_image_can_be_replaced(): void
    {
        Storage::fake('public');

        $project = Project::factory()->create(['image' => 'projects/old.jpg']);

        Livewire::test(EditProject::class, ['record' => $project->getRouteKey()])
            ->set('data.image_upload', UploadedFile::fake()->image('new.jpg'))
            ->call('save')
            ->assertHasNoFormErrors();

        $project->refresh();

        $this->assertNotSame('projects/old.jpg', $project->image);
        Storage::disk('public')->assertExists($project->image);
        Storage::disk('public')->assertMissing('projects/old.jpg');
    }

    #[Test]
    public function saving_a_project_without_an_upload_keeps_its_image(): void
    {
        Storage::fake('public');

        $project = Project::factory()->create(['image' => 'projects/keep.jpg']);

        Livewire::test(EditProject::class, ['record' => $project->getRouteKey()])
            ->set('data.slug', 'updated-slug')
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('projects/keep.jpg', $project->refresh()->image);
    }

    #[Test]
    public function a_project_can_be_created_with_an_image(): void
    {
        Storage::fake('public');

        Livewire::test(CreateProject::class)
            ->fillForm([
                'slug' => 'new-project',
                'year' => '2026',
                'title' => ['en' => 'New project', 'ar' => 'مشروع جديد'],
                'location' => ['en' => 'Riyadh', 'ar' => 'الرياض'],
                'scope' => ['en' => 'Testing', 'ar' => 'اختبار'],
            ])
            ->set('data.image_upload', UploadedFile::fake()->image('cover.jpg'))
            ->call('create')
            ->assertHasNoFormErrors();

        $project = Project::query()->where('slug', 'new-project')->firstOrFail();

        $this->assertIsString($project->image);
        Storage::disk('public')->assertExists($project->image);
    }

    #[Test]
    public function project_parties_relation_manager_renders(): void
    {
        $project = Project::factory()->create();

        Livewire::test(PartiesRelationManager::class, [
            'ownerRecord' => $project,
            'pageClass' => EditProject::class,
        ])->assertSuccessful();
    }

    #[Test]
    public function the_site_settings_page_saves(): void
    {
        $settings = SiteSetting::factory()->create(['company_name' => 'Old name']);

        Livewire::test(ManageSiteSettings::class)
            ->assertSuccessful()
            ->fillForm([
                'company_name' => 'New name',
                'footer_text' => ['en' => 'New footer', 'ar' => 'تذييل جديد'],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('New name', $settings->refresh()->company_name);
        $this->assertSame('تذييل جديد', $settings->footer_text['ar']);
    }
}
