<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\CourseObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([CourseObserver::class])]
class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
    ];

    public function studentGroups(): BelongsToMany
    {
        return $this->belongsToMany(StudentGroup::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(CourseMaterial::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function scopeAvailableTo(Builder $query, User $user): Builder
    {
        return $query->whereHas(
            'studentGroups.users',
            fn (Builder $users): Builder => $users->whereKey($user->getKey()),
        );
    }
}
