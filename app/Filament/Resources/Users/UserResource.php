<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Пользователи';

    protected static string|\UnitEnum|null $navigationGroup = 'Администрирование';

    protected static ?string $modelLabel = 'пользователь';

    protected static ?string $pluralModelLabel = 'пользователи';

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('avatar')
                    ->label('Фото')
                    ->state(fn (User $record): ?string => $record->avatar ? route('profile.avatar', $record) : null)
                    ->defaultImageUrl(asset('assets/images/profile/1.png'))
                    ->circular()
                    ->imageSize(44)
                    ->checkFileExistence(false),

                TextColumn::make('name')
                    ->label('Имя')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('is_admin')
                    ->label('Роль')
                    ->state(fn (User $record): string => $record->is_admin ? 'Администратор' : 'Пользователь')
                    ->badge()
                    ->color(fn (User $record): string => $record->is_admin ? 'warning' : 'gray'),

                TextColumn::make('blocked_at')
                    ->label('Статус')
                    ->state(fn (User $record): string => $record->blocked_at ? 'Заблокирован' : 'Активен')
                    ->badge()
                    ->color(fn (User $record): string => $record->blocked_at ? 'danger' : 'success'),

                TextColumn::make('email_verified_at')
                    ->label('Подтверждение email')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('Не подтверждён')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('blocked_at')
                    ->label('Блокировка')
                    ->nullable()
                    ->trueLabel('Заблокированные')
                    ->falseLabel('Активные'),
            ])
            ->actions([
                Action::make('toggleBlock')
                    ->label(fn (User $record): string => $record->blocked_at ? 'Разблокировать' : 'Заблокировать')
                    ->icon(fn (User $record): string => $record->blocked_at ? 'heroicon-o-lock-open' : 'heroicon-o-lock-closed')
                    ->color(fn (User $record): string => $record->blocked_at ? 'success' : 'warning')
                    ->requiresConfirmation()
                    ->modalHeading(fn (User $record): string => $record->blocked_at ? 'Разблокировать пользователя' : 'Заблокировать пользователя')
                    ->modalDescription(fn (User $record): string => $record->blocked_at ? 'Пользователь снова сможет входить в систему.' : 'Пользователь потеряет доступ к системе.')
                    ->hidden(fn (User $record): bool => auth()->id() === $record->id)
                    ->action(function (User $record): void {
                        if ($record->blocked_at) {
                            $record->unblock();

                            return;
                        }

                        $record->block();
                    }),
                DeleteAction::make()
                    ->hidden(fn (User $record): bool => auth()->id() === $record->id),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
        ];
    }
}
