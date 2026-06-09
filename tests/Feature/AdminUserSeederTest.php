<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_initial_filament_administrator_once(): void
    {
        config()->set('initial_admin', [
            'name' => 'Initial Administrator',
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $this->seed(AdminUserSeeder::class);
        $this->seed(AdminUserSeeder::class);

        $admin = User::query()
            ->where('email', 'admin@example.com')
            ->firstOrFail();

        $this->assertSame(1, User::query()->where('email', 'admin@example.com')->count());
        $this->assertSame('Initial Administrator', $admin->name);
        $this->assertTrue($admin->is_admin);
        $this->assertNotNull($admin->email_verified_at);
        $this->assertTrue(Hash::check('password123', $admin->password));
        $this->assertTrue($admin->canAccessPanel(filament()->getPanel('admin')));
    }

    public function test_it_does_not_reset_an_existing_users_password(): void
    {
        config()->set('initial_admin', [
            'name' => 'Initial Administrator',
            'email' => 'admin@example.com',
            'password' => 'seed-password',
        ]);

        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'existing-password',
            'is_admin' => false,
        ]);

        $this->seed(AdminUserSeeder::class);

        $user->refresh();

        $this->assertTrue($user->is_admin);
        $this->assertTrue(Hash::check('existing-password', $user->password));
        $this->assertFalse(Hash::check('seed-password', $user->password));
    }
}
