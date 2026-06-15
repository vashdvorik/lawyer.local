<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Course;
use App\Models\StudentGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseModuleAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_student_groups_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/admin/student-groups');

        $response->assertStatus(200);
    }

    public function test_student_cannot_access_student_groups_page(): void
    {
        $student = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($student)->get('/admin/student-groups');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_courses_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/admin/courses');

        $response->assertStatus(200);
    }

    public function test_student_cannot_access_courses_page(): void
    {
        $student = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($student)->get('/admin/courses');

        $response->assertStatus(403);
    }

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $response = $this->get('/admin/student-groups');

        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_access_create_group_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/admin/student-groups/create');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_edit_group_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $group = StudentGroup::factory()->create();

        $response = $this->actingAs($admin)->get("/admin/student-groups/{$group->id}/edit");

        $response->assertStatus(200);
    }

    public function test_admin_can_delete_group_via_model(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $group = StudentGroup::factory()->create();

        $this->actingAs($admin);
        $group->delete();

        $this->assertDatabaseMissing('student_groups', [
            'id' => $group->id,
        ]);
    }

    public function test_admin_can_create_course_via_model(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin);
        $course = Course::factory()->create(['title' => 'Новый курс']);

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'title' => 'Новый курс',
        ]);
    }

    public function test_admin_can_assign_groups_to_course_via_model(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $group = StudentGroup::factory()->create();
        $course = Course::factory()->create();

        $this->actingAs($admin);
        $course->studentGroups()->attach($group);

        $this->assertDatabaseHas('course_student_group', [
            'course_id' => $course->id,
            'student_group_id' => $group->id,
        ]);
    }

    public function test_detach_user_does_not_delete_user(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        $group = StudentGroup::factory()->create();

        $group->users()->attach($user);
        $this->assertDatabaseHas('student_group_user', [
            'student_group_id' => $group->id,
            'user_id' => $user->id,
        ]);

        $this->actingAs($admin);
        $group->users()->detach($user);

        $this->assertDatabaseMissing('student_group_user', [
            'student_group_id' => $group->id,
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }
}
