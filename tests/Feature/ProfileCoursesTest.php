<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\StudentGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileCoursesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/profile');
        $response->assertRedirect('/login');
    }

    public function test_user_sees_my_courses_and_available_course(): void
    {
        $user = User::factory()->create();
        $group = StudentGroup::factory()->create();
        $course = Course::factory()->create(['title' => 'Test Course ABC']);

        $material = CourseMaterial::factory()->create([
            'course_id' => $course->id,
            'title' => 'Test Material XYZ',
        ]);

        $group->users()->attach($user);
        $course->studentGroups()->attach($group);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertSee('Мои курсы');
        $response->assertSee('Test Course ABC');
        $response->assertSee('Test Material XYZ');
    }

    public function test_unavailable_course_is_not_in_html(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create(['title' => 'Secret Course']);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertDontSee('Secret Course');
    }

    public function test_my_courses_title_is_hidden_if_no_courses(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertDontSee('Мои курсы');
    }

    public function test_external_url_button_is_visible(): void
    {
        $user = User::factory()->create();
        $group = StudentGroup::factory()->create();
        $course = Course::factory()->create();

        CourseMaterial::factory()->create([
            'course_id' => $course->id,
            'external_url' => 'https://example.com',
            'file_path' => null,
        ]);

        $group->users()->attach($user);
        $course->studentGroups()->attach($group);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertSee('Открыть материал');
        $response->assertDontSee('Скачать документ');
    }

    public function test_file_download_button_is_visible(): void
    {
        $user = User::factory()->create();
        $group = StudentGroup::factory()->create();
        $course = Course::factory()->create();

        CourseMaterial::factory()->create([
            'course_id' => $course->id,
            'external_url' => null,
            'file_path' => 'course-materials/test.pdf',
        ]);

        $group->users()->attach($user);
        $course->studentGroups()->attach($group);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertDontSee('Открыть материал');
        $response->assertSee('Скачать документ');
    }

    public function test_both_buttons_are_visible_if_both_values_exist(): void
    {
        $user = User::factory()->create();
        $group = StudentGroup::factory()->create();
        $course = Course::factory()->create();

        CourseMaterial::factory()->create([
            'course_id' => $course->id,
            'external_url' => 'https://example.com',
            'file_path' => 'course-materials/test.pdf',
        ]);

        $group->users()->attach($user);
        $course->studentGroups()->attach($group);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertSee('Открыть материал');
        $response->assertSee('Скачать документ');
    }
}
