<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageServicesPage;
use App\Models\PageContent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ServicesPageContentTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_services_page_falls_back_to_the_language_defaults(): void
    {
        $this->get('/en/services')
            ->assertOk()
            ->assertSee(__('services.hero_body', [], 'en'))
            ->assertSee(asset('assets/images/services-banner.jpeg'));

        $this->get('/ar/services')
            ->assertOk()
            ->assertSee(__('services.hero_body', [], 'ar'));
    }

    #[Test]
    public function the_hero_image_can_be_replaced_more_than_once(): void
    {
        Storage::fake('public');

        $this->actingAs(User::factory()->admin()->create());

        Livewire::test(ManageServicesPage::class)
            ->set('data.hero_image_upload', UploadedFile::fake()->image('first.jpg'))
            ->call('save')
            ->assertHasNoFormErrors();

        $first = PageContent::for('services')['hero']['image'];

        Livewire::test(ManageServicesPage::class)
            ->set('data.hero_image_upload', UploadedFile::fake()->image('second.jpg'))
            ->call('save')
            ->assertHasNoFormErrors();

        $second = PageContent::for('services')['hero']['image'];

        $this->assertIsString($first);
        $this->assertIsString($second);
        $this->assertNotSame($first, $second);
        Storage::disk('public')->assertExists($second);
        Storage::disk('public')->assertMissing($first);
    }

    #[Test]
    public function admins_can_edit_and_save_every_services_section(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $content = PageContent::for('services');

        $content['seo']['title'] = ['en' => 'Custom EN services title', 'ar' => 'عنوان صفحة خدمات مخصص'];
        $content['hero']['title'] = ['en' => 'Custom services hero', 'ar' => 'عنوان رئيسي مخصص للخدمات'];
        $content['hero']['body'] = ['en' => 'Updated services hero body', 'ar' => 'نص رئيسي محدث للخدمات'];
        $content['head']['title_line'] = ['en' => 'Custom capabilities line', 'ar' => 'سطر قدرات مخصص'];
        $content['head']['title_em'] = ['en' => 'custom lifecycle', 'ar' => 'دورة حياة مخصصة'];
        $content['head']['body'] = ['en' => 'Updated capabilities body', 'ar' => 'نص قدرات محدث'];

        Livewire::test(ManageServicesPage::class)
            ->assertSuccessful()
            ->fillForm($content)
            ->call('save')
            ->assertHasNoFormErrors();

        $stored = PageContent::query()->where('page', 'services')->firstOrFail()->content;

        $this->assertSame('Updated services hero body', $stored['hero']['body']['en']);
        $this->assertSame('Custom capabilities line', $stored['head']['title_line']['en']);
        $this->assertSame('نص قدرات محدث', $stored['head']['body']['ar']);

        $this->get('/en/services')
            ->assertOk()
            ->assertSee('Custom services hero')
            ->assertSee('Updated services hero body')
            ->assertSee('Custom capabilities line')
            ->assertSee('custom lifecycle')
            ->assertSee('Updated capabilities body')
            ->assertSee('<title>Custom EN services title</title>', false);

        $this->get('/ar/services')
            ->assertOk()
            ->assertSee('نص رئيسي محدث للخدمات')
            ->assertSee('عنوان صفحة خدمات مخصص');
    }
}
