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
use Illuminate\Database\Eloquent\Model;

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
            ->components($this->getMaterialFormSchema());
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
                CreateAction::make()
                    ->modalWidth('5xl')
                    ->form($this->getMaterialFormSchema(multipleFiles: true))
                    ->using(function (array $data): Model {
                        $filePaths = array_values(array_filter((array) ($data['file_path'] ?? [])));
                        $fileNames = (array) ($data['original_file_name'] ?? []);

                        unset($data['file_path'], $data['original_file_name']);

                        if ($filePaths === []) {
                            return $this->getOwnerRecord()
                                ->materials()
                                ->create([
                                    ...$data,
                                    'file_path' => null,
                                    'original_file_name' => null,
                                ]);
                        }

                        $firstRecord = null;

                        foreach ($filePaths as $filePath) {
                            $record = $this->getOwnerRecord()
                                ->materials()
                                ->create([
                                    ...$data,
                                    'file_path' => $filePath,
                                    'original_file_name' => $fileNames[$filePath] ?? basename($filePath),
                                ]);

                            $firstRecord ??= $record;
                        }

                        return $firstRecord;
                    }),
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

    private function getMaterialFormSchema(bool $multipleFiles = false): array
    {
        $fileUpload = FileUpload::make('file_path')
            ->label($multipleFiles ? 'Файлы' : 'Файл')
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
            ->downloadable()
            ->columnSpanFull();

        if ($multipleFiles) {
            $fileUpload
                ->multiple()
                ->appendFiles()
                ->maxFiles(50)
                ->maxParallelUploads(5)
                ->panelLayout('grid')
                ->imagePreviewHeight('160px')
                ->helperText('Можно перетащить сразу несколько файлов. Для каждого файла будет создан отдельный материал с этим названием и описанием.');
        }

        return [
            TextInput::make('title')
                ->label('Название')
                ->required()
                ->maxLength(255),

            Textarea::make('description')
                ->label('Описание')
                ->nullable()
                ->required(false)
                ->markAsRequired(false)
                ->rows(5)
                ->columnSpanFull(),

            TextInput::make('external_url')
                ->label('Внешняя ссылка')
                ->url()
                ->rules(['starts_with:http://,https://'])
                ->maxLength(2048),

            $fileUpload,
        ];
    }
}
