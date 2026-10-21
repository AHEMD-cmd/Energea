<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageAboutPage;
use App\Models\PageContent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AboutPageContentTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_about_page_falls_back_to_the_language_defaults(): void
    {
        $this->get('/en/about')
            ->assertOk()
            ->assertSee(__('about.hero_body', [], 'en'))
            ->assertSee(asset('assets/images/about-banner.jpeg'));

        $this->get('/ar/about')
            ->assertOk()
            ->assertSee(__('about.hero_body', [], 'ar'));
    }

    #[Test]
    public function the_hero_image_can_be_replaced_more_than_once(): void
    {
        Storage::fake('public');

        $this->actingAs(User::factory()->admin()->create());

        $component = Livewire::test(ManageAboutPage::class)
            ->set('data.hero_image_upload', UploadedFile::fake()->image('first.jpg'))
            ->call('save')
            ->assertHasNoFormErrors();

        $first = PageContent::for('about')['hero']['image'];

        $component
            ->set('data.hero_image_upload', UploadedFile::fake()->image('second.jpg'))
            ->call('save')
            ->assertHasNoFormErrors();

        $second = PageContent::for('about')['hero']['image'];

        $this->assertIsString($first);
        $this->assertIsString($second);
        $this->assertNotSame($first, $second);
        Storage::disk('public')->assertExists($second);
        Storage::disk('public')->assertMissing($first);

        Livewire::test(ManageAboutPage::class)
            ->set('data.hero_image_upload', UploadedFile::fake()->image('third.jpg'))
            ->call('save')
            ->assertHasNoFormErrors();

        $third = PageContent::for('about')['hero']['image'];

        $this->assertIsString($third);
        $this->assertNotSame($second, $third);
        Storage::disk('public')->assertExists($third);
        Storage::disk('public')->assertMissing($second);
    }

    #[Test]
    public function the_snapshot_image_can_be_replaced(): void
    {
        Storage::fake('public');

        $this->actingAs(User::factory()->admin()->create());

        Livewire::test(ManageAboutPage::class)
            ->set('data.snapshot_image_upload', UploadedFile::fake()->image('snapshot.jpg'))
            ->call('save')
            ->assertHasNoFormErrors();

        $image = PageContent::for('about')['snapshot']['image'];

        $this->assertIsString($image);
        Storage::disk('public')->assertExists($image);

        $url = Storage::disk('public')->url($image);

        $this->get('/en/about')
            ->assertOk()
            ->assertSee('src="'.$url.'"', false);
    }

    #[Test]
    public function admins_can_edit_and_save_every_about_section(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $content = PageContent::for('about');

        $content['seo']['title'] = ['en' => 'Custom EN about title', 'ar' => 'عنوان صفحة عن مخصص'];
        $content['hero']['title'] = ['en' => 'Custom hero title', 'ar' => 'عنوان رئيسي مخصص'];
        $content['snapshot']['body'] = ['en' => 'Updated snapshot body', 'ar' => 'نص اللمحة المحدث'];
        $content['purpose']['title'] = ['en' => 'Custom purpose title', 'ar' => 'عنوان رسالة مخصص'];
        $content['mission']['title'] = ['en' => 'Custom mission', 'ar' => 'رسالة مخصصة'];
        $content['vision']['title'] = ['en' => 'Custom vision', 'ar' => 'رؤية مخصصة'];
        $content['values']['title'] = ['en' => 'Custom values', 'ar' => 'قيم مخصصة'];
        $content['values']['items'][0] = ['en' => 'Reliability', 'ar' => 'الموثوقية'];
        $content['metrics'][0]['value'] = '99';
        $content['metrics'][0]['caption'] = ['en' => 'Updated about metric', 'ar' => 'مؤشر محدث'];

        Livewire::test(ManageAboutPage::class)
            ->assertSuccessful()
            ->fillForm($content)
            ->call('save')
            ->assertHasNoFormErrors();

        $stored = PageContent::query()->where('page', 'about')->firstOrFail()->content;

        $this->assertSame('Updated snapshot body', $stored['snapshot']['body']['en']);
        $this->assertSame('99', $stored['metrics'][0]['value']);
        $this->assertSame('Reliability', $stored['values']['items'][0]['en']);
        $this->assertSame('نص اللمحة المحدث', $stored['snapshot']['body']['ar']);

        $this->get('/en/about')
            ->assertOk()
            ->assertSee('Custom hero title')
            ->assertSee('Updated snapshot body')
            ->assertSee('Custom purpose title')
            ->assertSee('Custom mission')
            ->assertSee('Custom vision')
            ->assertSee('Custom values')
            ->assertSee('Reliability')
            ->assertSee('Updated about metric')
            ->assertSee('<title>Custom EN about title</title>', false);

        $this->get('/ar/about')
            ->assertOk()
            ->assertSee('نص اللمحة المحدث')
            ->assertSee('عنوان صفحة عن مخصص');
    }
}
