<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\CourseMaterialObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([CourseMaterialObserver::class])]
class CourseMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'external_url',
        'file_path',
        'original_file_name',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    protected static function booted(): void
    {
        static::creating(function (CourseMaterial $material): void {
            if ($material->sort_order !== null) {
                return;
            }

            $material->sort_order = ((int) static::query()
                ->where('course_id', $material->course_id)
                ->max('sort_order')) + 1;
        });
    }
}
