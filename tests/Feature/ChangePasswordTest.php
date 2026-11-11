<?php

namespace Tests\Feature;

use App\Filament\Pages\ChangePassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admins_can_access_the_change_password_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin/change-password')
            ->assertOk();
    }

    #[Test]
    public function admins_can_change_their_password(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        Livewire::test(ChangePassword::class)
            ->fillForm([
                'current_password' => 'password',
                'password' => 'new-secret-password',
                'password_confirmation' => 'new-secret-password',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertTrue(Hash::check('new-secret-password', $admin->refresh()->password));
    }

    #[Test]
    public function the_current_password_must_be_correct(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        Livewire::test(ChangePassword::class)
            ->fillForm([
                'current_password' => 'wrong-password',
                'password' => 'new-secret-password',
                'password_confirmation' => 'new-secret-password',
            ])
            ->call('save')
            ->assertHasFormErrors(['current_password']);

        $this->assertTrue(Hash::check('password', $admin->refresh()->password));
    }

    #[Test]
    public function the_new_password_must_be_confirmed(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        Livewire::test(ChangePassword::class)
            ->fillForm([
                'current_password' => 'password',
                'password' => 'new-secret-password',
                'password_confirmation' => 'different-password',
            ])
            ->call('save')
            ->assertHasFormErrors(['password']);

        $this->assertTrue(Hash::check('password', $admin->refresh()->password));
    }
}
