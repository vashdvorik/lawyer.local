<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_users_admin_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertStatus(200);
    }

    public function test_student_cannot_access_users_admin_page(): void
    {
        $student = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($student)->get('/admin/users');

        $response->assertStatus(403);
    }

    public function test_guest_is_redirected_to_admin_login_from_users_page(): void
    {
        $response = $this->get('/admin/users');

        $response->assertRedirect('/admin/login');
    }
}
