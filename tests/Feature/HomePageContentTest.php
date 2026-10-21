<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageHomePage;
use App\Models\PageContent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HomePageContentTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_home_page_falls_back_to_the_language_defaults(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertSee(__('home.hero_intro', [], 'en'))
            ->assertSee(asset('assets/images/field-panel-desktop.jpeg'));

        $this->get('/ar')
            ->assertOk()
            ->assertSee(__('home.hero_intro', [], 'ar'));
    }

    #[Test]
    public function the_hero_image_can_be_replaced_more_than_once(): void
    {
        Storage::fake('public');

        $this->actingAs(User::factory()->admin()->create());

        $component = Livewire::test(ManageHomePage::class)
            ->set('data.hero_image_upload', UploadedFile::fake()->image('first.jpg'))
            ->call('save')
            ->assertHasNoFormErrors();

        $first = PageContent::for('home')['hero']['image'];

        $component
            ->set('data.hero_image_upload', UploadedFile::fake()->image('second.jpg'))
            ->call('save')
            ->assertHasNoFormErrors();

        $second = PageContent::for('home')['hero']['image'];

        $this->assertIsString($first);
        $this->assertIsString($second);
        $this->assertNotSame($first, $second);
        Storage::disk('public')->assertExists($second);
        Storage::disk('public')->assertMissing($first);

        Livewire::test(ManageHomePage::class)
            ->set('data.hero_image_upload', UploadedFile::fake()->image('third.jpg'))
            ->call('save')
            ->assertHasNoFormErrors();

        $third = PageContent::for('home')['hero']['image'];

        $this->assertIsString($third);
        $this->assertNotSame($second, $third);
        Storage::disk('public')->assertExists($third);
        Storage::disk('public')->assertMissing($second);
    }

    #[Test]
    public function the_hero_image_can_be_replaced(): void
    {
        $content = PageContent::for('home');
        $content['hero']['image'] = 'images/home/hero.jpg';

        PageContent::store('home', $content);

        $url = Storage::disk('public')->url('images/home/hero.jpg');

        $this->get('/en')
            ->assertOk()
            ->assertSee('src="'.$url.'"', false)
            ->assertSee('href="'.$url.'"', false);
    }

    #[Test]
    public function admins_can_edit_and_save_every_home_section(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $content = PageContent::for('home');

        $content['seo']['title'] = ['en' => 'Custom EN title', 'ar' => 'عنوان مخصص'];
        $content['hero']['intro'] = ['en' => 'Updated English intro', 'ar' => 'مقدمة عربية محدثة'];
        $content['hero']['title_line'] = ['en' => 'Custom hero line', 'ar' => 'سطر رئيسي مخصص'];
        $content['positioning']['title_line'] = ['en' => 'Custom positioning line', 'ar' => 'سطر تموضع مخصص'];
        $content['metrics'][0]['value'] = '99';
        $content['metrics'][0]['caption'] = ['en' => 'Updated metric', 'ar' => 'مؤشر محدث'];
        $content['why']['title_line'] = ['en' => 'Custom why line', 'ar' => 'سطر مخصص'];
        $content['why']['cards'][0]['title'] = ['en' => 'Updated card', 'ar' => 'بطاقة محدثة'];
        $content['why']['cards'][0]['icon'] = 'chart';
        $content['industries']['title_line'] = ['en' => 'Custom industries line', 'ar' => 'سطر قطاعات مخصص'];
        $content['final']['body'] = ['en' => 'Updated final body', 'ar' => 'نص ختامي محدث'];

        Livewire::test(ManageHomePage::class)
            ->assertSuccessful()
            ->fillForm($content)
            ->call('save')
            ->assertHasNoFormErrors();

        $stored = PageContent::query()->where('page', 'home')->firstOrFail()->content;

        $this->assertSame('Updated English intro', $stored['hero']['intro']['en']);
        $this->assertSame('99', $stored['metrics'][0]['value']);
        $this->assertSame('chart', $stored['why']['cards'][0]['icon']);
        $this->assertSame('مقدمة عربية محدثة', $stored['hero']['intro']['ar']);

        $this->get('/en')
            ->assertOk()
            ->assertSee('Updated English intro')
            ->assertSee('Custom hero line')
            ->assertSee('Custom positioning line')
            ->assertSee('Updated metric')
            ->assertSee('Updated card')
            ->assertSee('Custom industries line')
            ->assertSee('Updated final body')
            ->assertSee('<title>Custom EN title</title>', false);

        $this->get('/ar')
            ->assertOk()
            ->assertSee('مقدمة عربية محدثة')
            ->assertSee('عنوان مخصص');
    }
}
