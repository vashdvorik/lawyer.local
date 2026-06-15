<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\StudentGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentGroupFactory extends Factory
{
    protected $model = StudentGroup::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
        ];
    }
}
