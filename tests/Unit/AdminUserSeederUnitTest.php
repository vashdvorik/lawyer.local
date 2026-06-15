<?php

declare(strict_types=1);

namespace Tests\Unit;

use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class AdminUserSeederUnitTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that exception is thrown when email is null.
     */
    public function test_throws_exception_when_email_not_configured(): void
    {
        config()->set('initial_admin.email', null);
        config()->set('initial_admin.password', 'secret');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('INITIAL_ADMIN_EMAIL must be configured before seeding.');

        $this->seed(AdminUserSeeder::class);
    }

    /**
     * Test that exception is thrown when email is empty string.
     */
    public function test_throws_exception_when_email_is_empty_string(): void
    {
        config()->set('initial_admin.email', '');
        config()->set('initial_admin.password', 'secret');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('INITIAL_ADMIN_EMAIL must be configured before seeding.');

        $this->seed(AdminUserSeeder::class);
    }

    /**
     * Test that exception is thrown when password is null.
     */
    public function test_throws_exception_when_password_not_configured(): void
    {
        config()->set('initial_admin.email', 'admin@example.com');
        config()->set('initial_admin.password', null);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('INITIAL_ADMIN_PASSWORD must be configured before seeding.');

        $this->seed(AdminUserSeeder::class);
    }

    /**
     * Test that default name is used when name is empty.
     */
    public function test_uses_default_name_when_name_is_empty(): void
    {
        config()->set('initial_admin', [
            'name' => '',
            'email' => 'admin@example.com',
            'password' => 'secret',
        ]);

        $this->seed(AdminUserSeeder::class);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
            'name' => 'Administrator',
            'is_admin' => true,
        ]);
    }

    /**
     * Test that exception is thrown when password is empty string.
     */
    public function test_throws_exception_when_password_is_empty_string(): void
    {
        config()->set('initial_admin.email', 'admin@example.com');
        config()->set('initial_admin.password', '');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('INITIAL_ADMIN_PASSWORD must be configured before seeding.');

        $this->seed(AdminUserSeeder::class);
    }
}
