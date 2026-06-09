# Поэтапная разработка модуля курсов

Документ составлен по ТЗ и текущему состоянию проекта `lawyer.local`.

## 1. Основа проекта

Модуль разрабатывается внутри существующего приложения:

- PHP 8.4.1;
- Laravel 12.60.2;
- Filament 4.0.0;
- MySQL в локальном окружении;
- SQLite `:memory:` в тестах;
- существующая модель пользователя `App\Models\User`;
- существующий профиль `ProfileController::show()` и `resources/views/pages/profile.blade.php`;
- существующая админ-панель `/admin`;
- существующий публичный диск `public` и активная ссылка `public/storage`;
- существующий флаг администратора `users.is_admin`.

Проверка доступа в Filament уже реализована в `User::canAccessPanel()`:

```php
public function canAccessPanel(Panel $panel): bool
{
    return (bool) $this->is_admin;
}
```

Отдельный столбец `role`, enum ролей или пакет управления ролями добавлять не нужно. В рамках этого проекта пользователь с `is_admin = true` является администратором, остальные пользователи являются учениками.

## 2. Зафиксированные архитектурные решения

### Сущности

Использовать три модели:

- `StudentGroup` — группа учеников;
- `Course` — курс;
- `CourseMaterial` — материал курса.

Название `StudentGroup` выбрано вместо общего `Group`, чтобы код и таблицы однозначно относились к учебным группам.

### Таблицы

| Таблица | Назначение |
|---|---|
| `student_groups` | Группы учеников |
| `student_group_user` | Связь many-to-many между группами и пользователями |
| `courses` | Курсы |
| `course_student_group` | Связь many-to-many между курсами и группами |
| `course_materials` | Материалы курсов |

Pivot-таблицам не нужны отдельные `id` и timestamps: в проекте не требуется хранить свойства назначения или дату добавления в группу. От повторных связей защищают составные уникальные индексы.

### Доступ ученика

Курс доступен пользователю, если существует хотя бы одна его группа, связанная с курсом. Запрос должен строиться от `Course` через `whereHas()`. Такой запрос не создаёт дубликаты, даже если курс доступен через несколько групп.

### Файлы

Материалы хранить на диске `public` в каталоге `course-materials`. Дополнительный контроллер скачивания и защита ссылок по ТЗ не требуются.

Нужно хранить:

- `file_path` — внутренний путь в storage;
- `original_file_name` — исходное имя для атрибута `download`.

`original_file_name` является техническим полем и не расширяет пользовательский функционал.

### Удаление

- удаление группы удаляет только назначения пользователей и курсов;
- удаление группы не удаляет пользователей и курсы;
- удаление курса удаляет его материалы;
- удаление или замена материала удаляет старый физический файл;
- удаление пользователя удаляет его связи с группами;
- удаление ученика из одной группы не закрывает курс, если тот же курс доступен через другую группу.

## 3. Итоговая структура файлов

После реализации ожидается следующая структура:

```text
app/
├── Filament/
│   └── Resources/
│       ├── Courses/
│       │   ├── CourseResource.php
│       │   ├── Pages/
│       │   ├── RelationManagers/
│       │   │   └── MaterialsRelationManager.php
│       │   ├── Schemas/
│       │   │   └── CourseForm.php
│       │   └── Tables/
│       │       └── CoursesTable.php
│       └── StudentGroups/
│           ├── StudentGroupResource.php
│           ├── Pages/
│           ├── RelationManagers/
│           │   └── UsersRelationManager.php
│           ├── Schemas/
│           │   └── StudentGroupForm.php
│           └── Tables/
│               └── StudentGroupsTable.php
├── Models/
│   ├── Course.php
│   ├── CourseMaterial.php
│   ├── StudentGroup.php
│   └── User.php
└── Observers/
    ├── CourseMaterialObserver.php
    └── CourseObserver.php

database/
├── factories/
│   ├── CourseFactory.php
│   ├── CourseMaterialFactory.php
│   └── StudentGroupFactory.php
└── migrations/
    └── *_create_course_module_tables.php

tests/Feature/
├── CourseAccessTest.php
├── CourseMaterialFileTest.php
├── CourseModuleAdminTest.php
└── ProfileCoursesTest.php
```

Filament 4 по умолчанию создаёт ресурсы внутри каталогов `Courses` и `StudentGroups`, а формы и таблицы выносит в отдельные классы.

## Этап 0. Подготовка

Перед изменениями проверить текущее состояние:

```bash
php artisan about
php artisan test
vendor/bin/pint --test
php artisan storage:link
```

`storage:link` можно выполнять повторно: если ссылка уже существует, Laravel сообщит об этом.

Не изменять существующие применённые миграции `users`, `is_admin` и `avatar`. Для модуля создать новую миграцию.

## Этап 1. Модели, фабрики и миграция

Создать модели с фабриками:

```bash
php artisan make:model StudentGroup -f
php artisan make:model Course -f
php artisan make:model CourseMaterial -f
php artisan make:migration create_course_module_tables
```

### Таблица `student_groups`

```php
Schema::create('student_groups', function (Blueprint $table): void {
    $table->id();
    $table->string('name');
    $table->timestamps();
});
```

Название не обязательно делать уникальным: ТЗ не запрещает группы с одинаковым названием. Если бизнес-правило изменится, уникальность добавляется отдельной миграцией.

### Таблица `student_group_user`

```php
Schema::create('student_group_user', function (Blueprint $table): void {
    $table->foreignId('student_group_id')
        ->constrained()
        ->cascadeOnDelete();
    $table->foreignId('user_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->unique(['student_group_id', 'user_id']);
});
```

### Таблица `courses`

```php
Schema::create('courses', function (Blueprint $table): void {
    $table->id();
    $table->string('title');
    $table->timestamps();
});
```

Описание курса не добавлять.

### Таблица `course_student_group`

```php
Schema::create('course_student_group', function (Blueprint $table): void {
    $table->foreignId('course_id')
        ->constrained()
        ->cascadeOnDelete();
    $table->foreignId('student_group_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->unique(['course_id', 'student_group_id']);
});
```

### Таблица `course_materials`

```php
Schema::create('course_materials', function (Blueprint $table): void {
    $table->id();
    $table->foreignId('course_id')
        ->constrained()
        ->cascadeOnDelete();
    $table->string('title');
    $table->text('description');
    $table->text('external_url')->nullable();
    $table->string('file_path')->nullable();
    $table->string('original_file_name')->nullable();
    $table->unsignedInteger('sort_order')->nullable();
    $table->timestamps();

    $table->index(['course_id', 'sort_order']);
});
```

`external_url` хранить как `text`, чтобы не ограничивать рабочие ссылки длиной 255 символов.

В `down()` удалить таблицы в обратном порядке:

```php
Schema::dropIfExists('course_materials');
Schema::dropIfExists('course_student_group');
Schema::dropIfExists('courses');
Schema::dropIfExists('student_group_user');
Schema::dropIfExists('student_groups');
```

Применить миграцию:

```bash
php artisan migrate
```

### Контроль этапа

Проверить:

- все пять таблиц созданы;
- внешние ключи работают и поддерживаются SQLite-тестами;
- повторно добавить одного пользователя в ту же группу нельзя;
- повторно назначить курс той же группе нельзя.

## Этап 2. Eloquent-модели и связи

Все новые PHP-файлы должны содержать `declare(strict_types=1);`.

### `StudentGroup`

В `app/Models/StudentGroup.php`:

- подключить `HasFactory`;
- разрешить mass assignment поля `name`;
- определить `users(): BelongsToMany`;
- определить `courses(): BelongsToMany`.

```php
public function users(): BelongsToMany
{
    return $this->belongsToMany(User::class);
}

public function courses(): BelongsToMany
{
    return $this->belongsToMany(Course::class);
}
```

Laravel автоматически использует таблицы `student_group_user` и `course_student_group`.

### `User`

В существующую модель `app/Models/User.php` добавить только связь:

```php
public function studentGroups(): BelongsToMany
{
    return $this->belongsToMany(StudentGroup::class);
}
```

Не изменять существующие `fillable`, casts, email verification и `canAccessPanel()`.

### `Course`

В `app/Models/Course.php`:

- `fillable`: `title`;
- `studentGroups(): BelongsToMany`;
- `materials(): HasMany`;
- scope для доступных ученику курсов.

```php
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
```

`whereHas()` формирует условие существования связи и не дублирует курс при доступе через несколько групп.

### `CourseMaterial`

В `app/Models/CourseMaterial.php`:

- `fillable`: `course_id`, `title`, `description`, `external_url`, `file_path`, `original_file_name`, `sort_order`;
- `course(): BelongsTo`;
- cast `sort_order` в integer.

При создании материала автоматически назначать следующий порядок внутри курса:

```php
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
```

Это помещает новый материал в конец списка. После ручной сортировки Filament обновит `sort_order`.

### Фабрики

Настроить фабрики:

- `StudentGroupFactory` создаёт `name`;
- `CourseFactory` создаёт `title`;
- `CourseMaterialFactory` создаёт `course_id`, `title`, `description`, nullable `external_url`, `file_path`, `original_file_name` и последовательный `sort_order`.

Фабрики нужны для изолированных Feature-тестов. Изменять production-сидеры для выполнения ТЗ не требуется.

### Контроль этапа

В Tinker проверить связи:

```bash
php artisan tinker
```

Проверить сценарий:

1. Создать пользователя, две группы и один курс.
2. Добавить пользователя в обе группы.
3. Назначить курс обеим группам.
4. Выполнить `Course::query()->availableTo($user)->get()`.
5. Убедиться, что коллекция содержит курс один раз.

## Этап 3. Очистка файлов

Создать observers:

```bash
php artisan make:observer CourseMaterialObserver --model=CourseMaterial
php artisan make:observer CourseObserver --model=Course
```

Подключить их через атрибут `ObservedBy` на соответствующих моделях.

### `CourseMaterialObserver`

Observer должен:

- после успешной замены `file_path` удалить предыдущий файл с диска `public`;
- при удалении материала удалить его текущий файл;
- ничего не делать для `null` или отсутствующего файла.

Использовать:

```php
Storage::disk('public')->delete($path);
```

### `CourseObserver`

При удалении курса удалять его материалы через Eloquent по одному:

```php
public function deleting(Course $course): void
{
    $course->materials()->get()->each->delete();
}
```

Это необходимо, потому что SQL `cascadeOnDelete()` удаляет строки материалов напрямую и не вызывает model events для каждого материала. Без observer физические файлы останутся в storage.

### Контроль этапа

Автоматическим тестом проверить:

- файл удаляется при удалении материала;
- старый файл удаляется после замены;
- все файлы материалов удаляются при удалении курса.

Для тестов использовать `Storage::fake('public')`.

## Этап 4. Ресурс групп в Filament

После применения миграций создать ресурс:

```bash
php artisan make:filament-resource StudentGroup --generate --panel=admin --record-title-attribute=name
php artisan make:filament-relation-manager StudentGroupResource users name --attach --generate --panel=admin
```

### `StudentGroupResource`

Настроить русские подписи:

- model label: `группа`;
- plural model label: `группы`;
- navigation label: `Группы`;
- navigation group: `Обучение`.

Форма группы содержит только:

```php
TextInput::make('name')
    ->label('Название')
    ->required()
    ->maxLength(255);
```

Таблица групп содержит:

- название;
- количество учеников через `users_count`;
- количество курсов через `courses_count`;
- дату создания только для администратора;
- действия редактирования и удаления.

Для счётчиков использовать `counts('users')` и `counts('courses')`, а не загружать все связанные записи.

### `UsersRelationManager`

Relation manager должен показывать внутри группы:

- имя пользователя;
- email;
- статус администратора при необходимости;
- поиск по имени и email.

Разрешённые действия:

- `AttachAction` для добавления уже зарегистрированных пользователей;
- `DetachAction` для удаления пользователя из группы;
- `DetachBulkAction` для массового удаления из группы.

Настроить `AttachAction`:

```php
AttachAction::make()
    ->label('Добавить учеников')
    ->multiple()
    ->preloadRecordSelect()
    ->recordSelectSearchColumns(['name', 'email']);
```

Из сгенерированного relation manager обязательно удалить:

- `CreateAction`;
- `EditAction`;
- `DeleteAction`;
- `DeleteBulkAction`.

Эти действия изменяют или удаляют самих пользователей, тогда как ТЗ разрешает здесь только добавление в группу и удаление из группы.

### Контроль этапа

В `/admin` проверить:

1. Администратор создаёт группу.
2. В группу можно добавить нескольких существующих пользователей.
3. Пользователь не повторяется в одной группе.
4. Пользователя можно отсоединить без удаления его аккаунта.
5. Один пользователь добавляется в несколько групп.
6. Обычный пользователь не получает доступ к `/admin`.

## Этап 5. Ресурс курсов в Filament

Создать ресурс и relation manager:

```bash
php artisan make:filament-resource Course --generate --panel=admin --record-title-attribute=title
php artisan make:filament-relation-manager CourseResource materials title --associate --generate --panel=admin
```

Флаг `--associate` сообщает генератору, что `materials` является `HasMany`. После генерации удалить действия associate/dissociate: материалы должны создаваться и удаляться только внутри текущего курса.

### `CourseResource`

Настроить подписи:

- model label: `курс`;
- plural model label: `курсы`;
- navigation label: `Курсы`;
- navigation group: `Обучение`.

Форма курса:

```php
TextInput::make('title')
    ->label('Название')
    ->required()
    ->maxLength(255);

Select::make('studentGroups')
    ->label('Группы')
    ->relationship('studentGroups', 'name')
    ->multiple()
    ->searchable()
    ->preload();
```

Выбор групп можно оставить необязательным. Неназначенный курс существует в админ-панели, но не отображается ни одному ученику.

Таблица курсов содержит:

- название;
- назначенные группы;
- количество материалов;
- действия редактирования и удаления.

Не создавать публичные маршруты и страницы курса.

### `MaterialsRelationManager`

Форма материала содержит:

```php
TextInput::make('title')
    ->label('Название')
    ->required()
    ->maxLength(255);

Textarea::make('description')
    ->label('Описание')
    ->required()
    ->rows(5)
    ->columnSpanFull();

TextInput::make('external_url')
    ->label('Внешняя ссылка')
    ->url()
    ->rules(['starts_with:http://,https://'])
    ->maxLength(2048);
```

Загрузка файла:

```php
FileUpload::make('file_path')
    ->label('Файл')
    ->disk('public')
    ->directory('course-materials')
    ->visibility('public')
    ->storeFileNamesIn('original_file_name')
    ->rules([
        'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,rtf,jpg,jpeg,png,webp,zip,rar,7z',
    ])
    ->maxSize(20480)
    ->downloadable();
```

Лимит 20 МБ выбран в соответствии с уже используемым в проекте лимитом аватара. Если учебные архивы должны быть больше, изменить лимит отдельно после согласования и одновременно проверить `upload_max_filesize` и `post_max_size` PHP.

Whitelist разрешает только форматы из ТЗ. Не использовать общий список вроде `application/*`, поскольку он может пропустить исполняемые или скриптовые файлы.

Для клиентского фильтра дополнительно настроить `acceptedFileTypes()` с MIME-типами PDF, Office, RTF, изображений и архивов. Архивы RAR/7Z и старые Office-файлы нужно отдельно проверить в браузере под OSPanel: их MIME-типы могут отличаться. Серверное правило `mimes` должно оставаться обязательным.

Таблица материалов:

- скрытый или компактный `sort_order`;
- название;
- наличие внешней ссылки;
- имя загруженного файла;
- действия редактирования и удаления;
- `defaultSort('sort_order')`;
- `reorderable('sort_order')`.

Оставить действия:

- `CreateAction`;
- `EditAction`;
- `DeleteAction`;
- `DeleteBulkAction`.

Удалить действия:

- `AssociateAction`;
- `DissociateAction`;
- `DissociateBulkAction`.

Материал принадлежит одному курсу и не должен существовать отдельно от него.

### Контроль этапа

Проверить:

1. Курс создаётся только с названием.
2. Курс назначается одной или нескольким группам.
3. Материал создаётся только внутри курса.
4. Ссылка и файл независимо являются необязательными.
5. При наличии ссылки и файла сохраняются оба значения.
6. Запрещённые расширения не загружаются.
7. Материалы меняют порядок перетаскиванием.
8. После обновления страницы порядок сохраняется.
9. Удаление материала удаляет его файл.
10. Удаление курса удаляет материалы и их файлы.

## Этап 6. Получение курсов в профиле

Изменить только метод `show()` в существующем `ProfileController`. Редактирование профиля, аватара и пароля не затрагивать.

Рекомендуемая реализация:

```php
public function show(Request $request): View
{
    $user = $request->user();

    $courses = Course::query()
        ->availableTo($user)
        ->with('materials')
        ->orderBy('title')
        ->get();

    return view('pages.profile', [
        'user' => $user,
        'courses' => $courses,
    ]);
}
```

Необходимые импорты:

```php
use App\Models\Course;
use Illuminate\View\View;
```

Почему этот запрос подходит:

- `availableTo()` ограничивает курсы группами текущего пользователя;
- `whereHas()` исключает дубликаты курса;
- `with('materials')` предотвращает N+1;
- отношение `materials()` уже сортирует записи по `sort_order`, затем по `id`;
- выполняется только для авторизованного маршрута `/profile`.

Не добавлять новый маршрут курса и не передавать в запрос идентификатор пользователя из URL.

## Этап 7. Отображение в Blade

Изменить `resources/views/pages/profile.blade.php`.

Блок курсов расположить после существующего блока с фото и личной информацией. Существующий профиль не переделывать.

Главное условие:

```blade
@if ($courses->isNotEmpty())
    <section class="student-courses">
        <h2>Мои курсы</h2>

        {{-- Курсы и материалы --}}
    </section>
@endif
```

При пустой коллекции не должно выводиться:

- заголовка;
- пустого контейнера;
- сообщения об отсутствии курсов.

Для каждого курса вывести только название и список материалов:

```blade
@foreach ($courses as $course)
    <article class="student-course">
        <h3>{{ $course->title }}</h3>

        @foreach ($course->materials as $material)
            <div class="course-material">
                <h4>{{ $material->title }}</h4>
                <p class="course-material__description">{{ $material->description }}</p>

                @if ($material->external_url)
                    <a
                        href="{{ $material->external_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        Открыть материал
                    </a>
                @endif

                @if ($material->file_path)
                    <a
                        href="{{ asset('storage/' . $material->file_path) }}"
                        download="{{ $material->original_file_name }}"
                    >
                        Скачать документ
                    </a>
                @endif
            </div>
        @endforeach
    </article>
@endforeach
```

Требования к шаблону:

- пользовательские значения выводить через `{{ }}`;
- не использовать `{!! !!}` для описания;
- для переносов строк применить CSS `white-space: pre-line`;
- внешние ссылки открывать с `rel="noopener noreferrer"`;
- кнопки показывать независимо друг от друга;
- дату создания и загрузки материала не показывать;
- не добавлять счётчики просмотров или скачиваний;
- стили оформить в существующей Bootstrap/template-стилистике страницы профиля.

## Этап 8. Автоматические тесты

Создать focused Feature-тесты. Все тесты проекта уже используют SQLite `:memory:`, поэтому новые миграции должны оставаться совместимыми с MySQL и SQLite.

### `CourseAccessTest`

Проверить:

1. Пользователь видит курс своей группы.
2. Пользователь не видит курс чужой группы.
3. Курс через две группы возвращается один раз.
4. После detach из единственной группы доступ исчезает.
5. После detach из одной группы доступ сохраняется через вторую.
6. Неназначенный курс никому не доступен.
7. Материалы загружаются по `sort_order`.

### `ProfileCoursesTest`

Проверить HTTP-ответ `/profile`:

1. Гость по-прежнему перенаправляется на login.
2. Авторизованный пользователь видит заголовок `Мои курсы`, доступный курс и его материалы.
3. Недоступный курс отсутствует в HTML.
4. При отсутствии курсов текст `Мои курсы` отсутствует.
5. Для внешней ссылки присутствует `Открыть материал`.
6. Для файла присутствует `Скачать документ`.
7. При наличии обоих значений присутствуют обе кнопки.
8. Даты материала не выводятся.

### `CourseMaterialFileTest`

С `Storage::fake('public')` проверить:

1. Файл материала существует после сохранения.
2. Удаление материала удаляет файл.
3. Замена файла удаляет старый.
4. Удаление курса удаляет все файлы материалов.

### `CourseModuleAdminTest`

Дополнить существующие проверки Filament:

1. Гость перенаправляется на `/admin/login`.
2. Пользователь с `is_admin = false` получает 403.
3. Пользователь с `is_admin = true` открывает списки групп и курсов.
4. Администратор может создать, изменить и удалить группу.
5. Администратор может создать курс и назначить группы.
6. Detach пользователя из relation manager не удаляет запись `users`.

Для проверки точных URL использовать маршруты ресурсов Filament, а не хардкодить адреса там, где доступен `Resource::getUrl()`.

### Запуск

Сначала запускать узкие тесты:

```bash
php artisan test --filter=CourseAccessTest
php artisan test --filter=ProfileCoursesTest
php artisan test --filter=CourseMaterialFileTest
php artisan test --filter=CourseModuleAdminTest
```

Затем полный набор:

```bash
php artisan test
vendor/bin/pint
vendor/bin/pint --test
```

## Этап 9. Ручная приёмка

Создать тестовые данные:

- администратор;
- ученик A;
- ученик B;
- группы `Группа 1` и `Группа 2`;
- курс A, назначенный обеим группам;
- курс B, назначенный только `Группе 2`;
- материалы со ссылкой, файлом и обоими вариантами.

Проверить сценарии:

1. Добавить ученика A в обе группы.
2. Добавить ученика B только в первую группу.
3. Убедиться, что ученик A видит курсы A и B.
4. Убедиться, что курс A показан ученику A один раз.
5. Убедиться, что ученик B видит только курс A.
6. Удалить ученика A из второй группы.
7. Убедиться, что курс B исчез, а курс A остался.
8. Удалить ученика A из первой группы.
9. Убедиться, что блок `Мои курсы` полностью исчез.
10. Изменить порядок материалов в Filament и проверить профиль.
11. Открыть внешнюю ссылку.
12. Скачать каждый разрешённый тип тестового файла.
13. Попытаться загрузить `.exe`, `.php`, `.js` и убедиться в отказе.
14. Удалить материал и проверить отсутствие файла в `storage/app/public/course-materials`.
15. Войти обычным пользователем в `/admin` и убедиться в запрете.

## Этап 10. Финальная проверка и деплой

Перед завершением:

```bash
php artisan test
vendor/bin/pint --test
php artisan route:list
php artisan storage:link
npm run build
```

Проверить, что `route:list` не содержит публичных маршрутов курсов или материалов.

При production-деплое:

```bash
php artisan migrate --force
php artisan storage:link
php artisan optimize
```

Убедиться, что web-сервер имеет права на запись в:

```text
storage/app/public/course-materials
```

## Критерии готовности

- [ ] Используется существующий `users.is_admin`, новая система ролей не добавлена.
- [ ] Только администраторы входят в Filament.
- [ ] Группы создаются, редактируются и удаляются в Filament.
- [ ] В группы добавляются только существующие пользователи.
- [ ] Один пользователь состоит в нескольких группах.
- [ ] Отсоединение от группы не удаляет пользователя.
- [ ] Курсы создаются, редактируются и удаляются в Filament.
- [ ] У курса есть только название и связи с группами.
- [ ] Материалы управляются внутри курса.
- [ ] Материалы сортируются drag-and-drop через `sort_order`.
- [ ] Разрешены только форматы файлов из ТЗ.
- [ ] Старые и удалённые файлы очищаются из storage.
- [ ] Ученик видит только курсы своих групп.
- [ ] Один курс не дублируется при доступе через несколько групп.
- [ ] Материалы выводятся в заданном порядке.
- [ ] Кнопки ссылки и файла выводятся независимо.
- [ ] При отсутствии курсов блок полностью отсутствует.
- [ ] Отдельных страниц и маршрутов курсов нет.
- [ ] Нет видео-загрузки, уведомлений, статистики и дат материалов.
- [ ] Новые и существующие тесты проходят.

## Что не входит в реализацию

Не добавлять без отдельного ТЗ:

- отдельную страницу курса;
- публичный каталог курсов;
- просмотр отдельного материала;
- загрузку видео;
- приватные или подписанные ссылки на файлы;
- уведомления об открытии или закрытии доступа;
- статистику просмотров и скачиваний;
- прогресс прохождения;
- домашние задания;
- оплату;
- роли преподавателей или кураторов;
- soft deletes;
- управление администраторами через новый Filament-ресурс пользователей.
