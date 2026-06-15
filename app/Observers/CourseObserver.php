<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Course;

class CourseObserver
{
    public function deleting(Course $course): void
    {
        $course->materials()->get()->each->delete();
    }
}
