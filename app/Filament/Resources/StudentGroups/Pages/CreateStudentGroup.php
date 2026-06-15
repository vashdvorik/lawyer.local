<?php

namespace App\Filament\Resources\StudentGroups\Pages;

use App\Filament\Resources\StudentGroups\StudentGroupResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStudentGroup extends CreateRecord
{
    protected static string $resource = StudentGroupResource::class;
}
