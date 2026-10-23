<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminLocalizationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_login_page_is_english_and_ltr_by_default(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('dir="ltr"', false)
            ->assertSee(__('admin.locale.switch', [], 'en'));
    }

    #[Test]
    public function guests_can_switch_the_panel_to_arabic(): void
    {
        $this->from('/admin/login')
            ->post('/admin/locale/ar')
            ->assertRedirect('/admin/login');

        $this->assertSame('ar', session('admin_locale'));

        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('dir="rtl"', false)
            ->assertSee(__('admin.locale.switch', [], 'ar'));
    }

    #[Test]
    public function users_with_an_arabic_locale_see_an_rtl_panel(): void
    {
        $admin = User::factory()->admin()->create(['locale' => 'ar']);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('dir="rtl"', false)
            ->assertSee(__('admin.navigation.groups.content', [], 'ar'));
    }

    #[Test]
    public function switching_locale_persists_to_the_user(): void
    {
        $admin = User::factory()->admin()->create(['locale' => 'en']);

        $this->actingAs($admin)
            ->from('/admin')
            ->post('/admin/locale/ar')
            ->assertRedirect('/admin');

        $this->assertSame('ar', $admin->refresh()->locale);
        $this->assertSame('ar', session('admin_locale'));
    }

    #[Test]
    public function unsupported_locales_are_rejected(): void
    {
        $this->post('/admin/locale/zz')->assertNotFound();

        $this->assertNull(session('admin_locale'));
    }
}
