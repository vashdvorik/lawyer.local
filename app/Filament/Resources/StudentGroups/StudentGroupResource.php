<?php

declare(strict_types=1);

namespace App\Filament\Resources\StudentGroups;

use App\Filament\Resources\StudentGroups\RelationManagers\UsersRelationManager;
use App\Models\StudentGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StudentGroupResource extends Resource
{
    protected static ?string $model = StudentGroup::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Группы';

    protected static string|\UnitEnum|null $navigationGroup = 'Обучение';

    protected static ?string $modelLabel = 'группа';

    protected static ?string $pluralModelLabel = 'группы';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Название')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('users_count')
                    ->label('Учеников')
                    ->counts('users')
                    ->sortable(),
                TextColumn::make('courses_count')
                    ->label('Курсов')
                    ->counts('courses')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Дата создания')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentGroups::route('/'),
            'create' => Pages\CreateStudentGroup::route('/create'),
            'edit' => Pages\EditStudentGroup::route('/{record}/edit'),
        ];
    }
}
