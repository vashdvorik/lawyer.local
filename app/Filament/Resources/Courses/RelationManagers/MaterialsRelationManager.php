<?php

declare(strict_types=1);

namespace App\Filament\Resources\Courses\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MaterialsRelationManager extends RelationManager
{
    protected static string $relationship = 'materials';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $modelLabel = 'материал';

    protected static ?string $pluralModelLabel = 'материалы';

    protected static ?string $title = 'Материалы курса';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Название')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->label('Описание')
                    ->nullable()
                    ->rows(5)
                    ->columnSpanFull(),

                TextInput::make('external_url')
                    ->label('Внешняя ссылка')
                    ->url()
                    ->rules(['starts_with:http://,https://'])
                    ->maxLength(2048),

                FileUpload::make('file_path')
                    ->label('Файл')
                    ->disk('public')
                    ->directory('course-materials')
                    ->visibility('public')
                    ->storeFileNamesIn('original_file_name')
                    ->rules([
                        'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,rtf,jpg,jpeg,png,webp,zip,rar,7z',
                    ])
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-powerpoint',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                        'text/plain',
                        'application/rtf',
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                        'application/zip',
                        'application/x-rar-compressed',
                        'application/x-7z-compressed',
                    ])
                    ->maxSize(20480)
                    ->downloadable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('external_url')
                    ->label('Ссылка')
                    ->boolean()
                    ->getStateUsing(fn ($record) => filled($record->external_url)),

                TextColumn::make('original_file_name')
                    ->label('Файл')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
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
}
