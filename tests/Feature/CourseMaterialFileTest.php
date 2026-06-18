<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseMaterial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CourseMaterialFileTest extends TestCase
{
    use RefreshDatabase;

    public function test_file_is_deleted_when_material_is_deleted(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('test.pdf', 100);
        $path = $file->store('course-materials', 'public');

        $course = Course::factory()->create();
        $material = CourseMaterial::factory()->create([
            'course_id' => $course->id,
            'file_path' => $path,
        ]);

        $this->assertTrue(Storage::disk('public')->exists($path));

        $material->delete();

        $this->assertFalse(Storage::disk('public')->exists($path));
    }

    public function test_old_file_is_deleted_when_replaced(): void
    {
        Storage::fake('public');

        $oldFile = UploadedFile::fake()->create('old.pdf', 100);
        $oldPath = $oldFile->store('course-materials', 'public');

        $course = Course::factory()->create();
        $material = CourseMaterial::factory()->create([
            'course_id' => $course->id,
            'file_path' => $oldPath,
        ]);

        $this->assertTrue(Storage::disk('public')->exists($oldPath));

        $newFile = UploadedFile::fake()->create('new.pdf', 100);
        $newPath = $newFile->store('course-materials', 'public');

        $material->update([
            'file_path' => $newPath,
        ]);

        $this->assertFalse(Storage::disk('public')->exists($oldPath));
        $this->assertTrue(Storage::disk('public')->exists($newPath));
    }

    public function test_all_material_files_are_deleted_when_course_is_deleted(): void
    {
        Storage::fake('public');

        $file1 = UploadedFile::fake()->create('1.pdf', 100);
        $path1 = $file1->store('course-materials', 'public');

        $file2 = UploadedFile::fake()->create('2.pdf', 100);
        $path2 = $file2->store('course-materials', 'public');

        $course = Course::factory()->create();

        CourseMaterial::factory()->create([
            'course_id' => $course->id,
            'file_path' => $path1,
        ]);

        CourseMaterial::factory()->create([
            'course_id' => $course->id,
            'file_path' => $path2,
        ]);

        $this->assertTrue(Storage::disk('public')->exists($path1));
        $this->assertTrue(Storage::disk('public')->exists($path2));

        $course->delete();

        $this->assertFalse(Storage::disk('public')->exists($path1));
        $this->assertFalse(Storage::disk('public')->exists($path2));
    }

    public function test_material_can_be_created_without_description(): void
    {
        $course = Course::factory()->create();

        $material = CourseMaterial::factory()->create([
            'course_id' => $course->id,
            'description' => null,
        ]);

        $this->assertNull($material->description);
        $this->assertDatabaseHas('course_materials', [
            'id' => $material->id,
            'description' => null,
        ]);
    }
}
