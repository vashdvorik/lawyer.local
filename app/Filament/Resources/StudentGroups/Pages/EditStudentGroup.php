<?php

namespace App\Filament\Resources\StudentGroups\Pages;

use App\Filament\Resources\StudentGroups\StudentGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStudentGroup extends EditRecord
{
    protected static string $resource = StudentGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
