<?php

namespace App\Filament\Resources\StudentGroups\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StudentGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
            ]);
    }
}
