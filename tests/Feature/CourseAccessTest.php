<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\StudentGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_sees_course_of_their_group(): void
    {
        $user = User::factory()->create();
        $group = StudentGroup::factory()->create();
        $course = Course::factory()->create();

        $group->users()->attach($user);
        $course->studentGroups()->attach($group);

        $availableCourses = Course::availableTo($user)->get();

        $this->assertCount(1, $availableCourses);
        $this->assertEquals($course->id, $availableCourses->first()->id);
    }

    public function test_user_does_not_see_course_of_other_group(): void
    {
        $user = User::factory()->create();
        $otherGroup = StudentGroup::factory()->create();
        $course = Course::factory()->create();

        $course->studentGroups()->attach($otherGroup);

        $availableCourses = Course::availableTo($user)->get();

        $this->assertCount(0, $availableCourses);
    }

    public function test_course_via_two_groups_returns_once(): void
    {
        $user = User::factory()->create();
        $group1 = StudentGroup::factory()->create();
        $group2 = StudentGroup::factory()->create();
        $course = Course::factory()->create();

        $group1->users()->attach($user);
        $group2->users()->attach($user);

        $course->studentGroups()->attach($group1);
        $course->studentGroups()->attach($group2);

        $availableCourses = Course::availableTo($user)->get();

        $this->assertCount(1, $availableCourses);
    }

    public function test_access_lost_after_detach_from_only_group(): void
    {
        $user = User::factory()->create();
        $group = StudentGroup::factory()->create();
        $course = Course::factory()->create();

        $group->users()->attach($user);
        $course->studentGroups()->attach($group);

        $group->users()->detach($user);

        $availableCourses = Course::availableTo($user)->get();

        $this->assertCount(0, $availableCourses);
    }

    public function test_access_kept_after_detach_from_one_group_if_another_remains(): void
    {
        $user = User::factory()->create();
        $group1 = StudentGroup::factory()->create();
        $group2 = StudentGroup::factory()->create();
        $course = Course::factory()->create();

        $group1->users()->attach($user);
        $group2->users()->attach($user);

        $course->studentGroups()->attach($group1);
        $course->studentGroups()->attach($group2);

        $group1->users()->detach($user);

        $availableCourses = Course::availableTo($user)->get();

        $this->assertCount(1, $availableCourses);
    }

    public function test_unassigned_course_is_not_available_to_anyone(): void
    {
        $user = User::factory()->create();
        Course::factory()->create();

        $availableCourses = Course::availableTo($user)->get();

        $this->assertCount(0, $availableCourses);
    }

    public function test_materials_loaded_by_sort_order(): void
    {
        $course = Course::factory()->create();

        $material2 = CourseMaterial::factory()->create([
            'course_id' => $course->id,
            'title' => 'Second',
            'sort_order' => 2,
        ]);

        $material1 = CourseMaterial::factory()->create([
            'course_id' => $course->id,
            'title' => 'First',
            'sort_order' => 1,
        ]);

        $loadedCourse = Course::with('materials')->find($course->id);

        $this->assertEquals($material1->id, $loadedCourse->materials[0]->id);
        $this->assertEquals($material2->id, $loadedCourse->materials[1]->id);
    }
}
