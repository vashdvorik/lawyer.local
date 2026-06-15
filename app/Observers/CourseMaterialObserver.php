<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\CourseMaterial;
use Illuminate\Support\Facades\Storage;

class CourseMaterialObserver
{
    public function updated(CourseMaterial $material): void
    {
        if ($material->wasChanged('file_path')) {
            $oldPath = $material->getOriginal('file_path');

            if ($oldPath && $oldPath !== $material->file_path) {
                Storage::disk('public')->delete($oldPath);
            }
        }
    }

    public function deleted(CourseMaterial $material): void
    {
        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }
    }
}
