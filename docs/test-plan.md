# План тестирования проекта lawyer.local

> Дата составления: 2026-06-14
> Версия: 1.0

---

## 1. Обзор проекта

### 1.1. Компоненты приложения

| Компонент | Файлы | Статус |
|---|---|---|
| **Модели** | `User` | ✅ Реализовано |
| | `StudentGroup`, `Course`, `CourseMaterial` | 🔲 Планируется (`instruction.md`) |
| **Контроллеры** | `AuthController`, `EmailVerificationController`, `PasswordResetController` | ✅ Реализовано |
| | `PageController`, `ProfileController` | ✅ Реализовано |
| **Filament** | `AdminPanelProvider`, `CourseResource`, `StudentGroupResource` | 🔲 Планируется |
| **Observers** | `CourseObserver`, `CourseMaterialObserver` | 🔲 Планируется |
| **Seeders** | `AdminUserSeeder`, `DatabaseSeeder` | ✅ Реализовано |
| **Factories** | `UserFactory` | ✅ Реализовано |
| | `StudentGroupFactory`, `CourseFactory`, `CourseMaterialFactory` | 🔲 Планируется |
| **Миграции** | `users`, `cache`, `jobs`, `is_admin`, `avatar` | ✅ Применены |
| | `create_course_module_tables` | 🔲 Планируется |
| **Blade-шаблоны** | 12 файлов (layouts, pages, auth, errors) | ✅ Реализовано |

### 1.2. Маршруты

| Группа | Маршруты | Middleware |
|---|---|---|
| Публичные страницы | `GET /`, `/login`, `/signup` | — |
| Аутентификация | `POST /login`, `/signup`, `/logout` | — |
| Верификация email | `GET/POST email/verify/*` | `auth` |
| Сброс пароля | `GET/POST forgot-password`, `reset-password/*` | `guest` |
| Профиль | `GET /profile`, `/profile/edit`, `PUT /profile`, `/profile/password` | `auth` |
| Filament | `/admin/*` | Filament middleware |

---

## 2. Существующие тесты — анализ

### 2.1. Структура

```
tests/
├── TestCase.php
├── Feature/
│   ├── AuthTest.php           (16 тестов)
│   ├── ProfileTest.php        (10 тестов)
│   ├── PageTest.php           (3 теста)
│   ├── AdminUserSeederTest.php(2 теста)
│   └── ExampleTest.php        (1 тест-заглушка)
└── Unit/
    └── ExampleTest.php        (1 тест-заглушка)
```

### 2.2. Детальный разбор покрытия

#### `AuthTest.php` — ✅ Хорошее покрытие

| # | Тест | Статус |
|---|---|---|
| 1 | `test_user_can_register_successfully` | ✅ |
| 2 | `test_registration_validation_errors` | ✅ |
| 3 | `test_user_can_login_successfully` | ✅ |
| 4 | `test_user_cannot_login_with_incorrect_credentials` | ✅ |
| 5 | `test_user_can_logout` | ✅ |
| 6 | `test_guest_cannot_access_verification_notice` | ✅ |
| 7 | `test_unverified_user_can_access_verification_notice` | ✅ |
| 8 | `test_user_can_verify_email` | ✅ |
| 9 | `test_user_can_resend_verification_notification` | ✅ |
| 10 | `test_forgot_password_page_is_accessible` | ✅ |
| 11 | `test_user_can_request_password_reset_link` | ✅ |
| 12 | `test_password_reset_form_is_accessible` | ✅ |
| 13 | `test_user_can_reset_password_with_token` | ✅ |
| 14 | `test_guest_is_redirected_to_admin_login` | ✅ |
| 15 | `test_non_admin_user_cannot_access_filament_panel` | ✅ |
| 16 | `test_admin_user_can_access_filament_panel` | ✅ |

#### `ProfileTest.php` — ✅ Хорошее покрытие

| # | Тест | Статус |
|---|---|---|
| 1 | `test_guest_cannot_access_profile` | ✅ |
| 2 | `test_authenticated_user_can_access_profile` | ✅ |
| 3 | `test_guest_cannot_access_profile_edit` | ✅ |
| 4 | `test_authenticated_user_can_access_profile_edit` | ✅ |
| 5 | `test_user_can_update_profile` | ✅ |
| 6 | `test_profile_update_validation` | ✅ |
| 7 | `test_user_can_update_password` | ✅ |
| 8 | `test_password_update_validation` | ✅ |
| 9 | `test_user_can_upload_avatar` | ✅ |
| 10 | `test_old_avatar_is_deleted_when_new_avatar_is_uploaded` | ✅ |

#### `PageTest.php` — ✅ Покрыто

| # | Тест | Статус |
|---|---|---|
| 1 | `test_home_page_is_accessible` | ✅ |
| 2 | `test_login_page_is_accessible` | ✅ |
| 3 | `test_signup_page_is_accessible` | ✅ |

#### `AdminUserSeederTest.php` — ✅ Покрыто

| # | Тест | Статус |
|---|---|---|
| 1 | `test_it_creates_an_initial_filament_administrator_once` | ✅ |
| 2 | `test_it_does_not_reset_an_existing_users_password` | ✅ |

#### `ExampleTest.php` (Feature) — 🟡 Дублирует PageTest

Проверяет `GET / → 200`. Уже покрыто `PageTest::test_home_page_is_accessible`.

#### `ExampleTest.php` (Unit) — 🔴 Заглушка

Содержит `assertTrue(true)` — не тестирует реальный код.

---

## 3. Пробелы в текущем покрытии

### 3.1. Unit-тесты (КРИТИЧЕСКИ)

**Не написано ни одного реального Unit-теста.** Требуется:

#### Модель `User`

| Тест | Описание |
|---|---|
| `test_can_access_panel_returns_true_for_admin` | `is_admin = true` → `canAccessPanel()` = `true` |
| `test_can_access_panel_returns_false_for_non_admin` | `is_admin = false` → `canAccessPanel()` = `false` |
| `test_is_admin_is_cast_to_boolean` | Проверка каста `is_admin` (0/1 → bool) |
| `test_password_is_hashed` | Проверка каста `password` → `hashed` |
| `test_email_verified_at_is_cast_to_datetime` | Проверка каста `email_verified_at` |
| `test_fillable_contains_expected_fields` | Проверка `$fillable` |
| `test_hidden_contains_password_and_remember_token` | Проверка `$hidden` |

#### `AdminUserSeeder`

| Тест | Описание |
|---|---|
| `test_throws_exception_when_email_not_configured` | `config('initial_admin.email')` = `null` → `RuntimeException` |
| `test_throws_exception_when_email_is_empty_string` | `config('initial_admin.email')` = `''` → `RuntimeException` |
| `test_throws_exception_when_password_not_configured` | `config('initial_admin.password')` = `null` → `RuntimeException` |
| `test_uses_default_name_when_name_is_empty` | `config('initial_admin.name')` = `''` → имя = `'Administrator'` |

#### Фабрики

| Тест | Описание |
|---|---|
| `test_user_factory_creates_valid_model` | `UserFactory` создаёт модель с заполненными полями |
| `test_user_factory_unverified_state` | `unverified()` → `email_verified_at = null` |

### 3.2. Feature-тесты — краевые случаи

#### Сброс пароля

| Тест | Описание |
|---|---|
| `test_password_reset_with_invalid_token_fails` | Невалидный токен → ошибка |
| `test_password_reset_with_expired_token_fails` | Просроченный токен → ошибка |
| `test_password_reset_validation_errors` | Пустые поля, несовпадающие пароли |
| `test_authenticated_user_cannot_access_forgot_password` | Залогиненный → редирект с `password.request` |

#### Верификация email

| Тест | Описание |
|---|---|
| `test_verified_user_is_redirected_from_verification_notice` | Подтверждённый email → редирект на `profile.show` |
| `test_verified_user_cannot_resend_verification` | Повторная отправка → редирект на `home` |
| `test_verification_with_invalid_hash_fails` | Подпись с неверным hash → 403 |

#### Профиль / Аватар

| Тест | Описание |
|---|---|
| `test_avatar_validation_rejects_non_image` | PDF/текстовый файл → ошибка валидации |
| `test_avatar_validation_rejects_file_larger_than_20mb` | Файл > 20480 KB → ошибка валидации |
| `test_resize_and_compress_handles_missing_gd_extension` | GD не загружен → аватар сохраняется без сжатия |

#### Аутентификация

| Тест | Описание |
|---|---|
| `test_login_with_remember_me` | `remember` = true → проверка наличия remember_token |
| `test_registration_with_remember_me` | remember_token в сессии |
| `test_authenticated_user_cannot_access_login_page` | Залогиненный → редирект с `/login` |
| `test_authenticated_user_cannot_access_signup_page` | Залогиненный → редирект с `/signup` |

### 3.3. Providers / Конфигурация

| Тест | Описание |
|---|---|
| `test_admin_panel_provider_configures_panel_correctly` | Проверка `id('admin')`, `path('admin')`, `login()` |
| `test_admin_panel_provider_registers_middleware_stack` | Проверка `middleware()` и `authMiddleware()` |
| `test_app_service_provider_registers_and_boots` | `AppServiceProvider` не падает |

### 3.4. Ошибки и исключения

| Тест | Описание |
|---|---|
| `test_404_page_is_rendered` | Несуществующий маршрут → `resources/views/errors/404.blade.php` |
| `test_csrf_protection_on_post_routes` | POST без CSRF → 419 |

---

## 4. Планируемые тесты для модуля курсов

На основе `docs/instruction.md` (Этап 8):

### 4.1. `tests/Feature/CourseAccessTest.php`

| # | Тест | Описание |
|---|---|---|
| 1 | `test_user_sees_course_of_their_group` | Пользователь в группе → курс доступен через `availableTo()` |
| 2 | `test_user_does_not_see_course_of_other_group` | Пользователь не в группе → курс недоступен |
| 3 | `test_course_through_two_groups_returned_once` | Курс в двух группах → `availableTo()` возвращает 1 запись |
| 4 | `test_access_disappears_after_detach_from_only_group` | Detach из единственной группы → доступ исчезает |
| 5 | `test_access_persists_after_detach_from_one_of_multiple_groups` | Detach из одной группы → доступ через вторую сохраняется |
| 6 | `test_unassigned_course_not_available_to_anyone` | Курс без групп → никому не доступен |
| 7 | `test_materials_loaded_by_sort_order` | Материалы сортируются по `sort_order`, затем `id` |

### 4.2. `tests/Feature/ProfileCoursesTest.php`

| # | Тест | Описание |
|---|---|---|
| 1 | `test_guest_redirected_to_login` | `GET /profile` → редирект на `login` |
| 2 | `test_authenticated_user_sees_courses_section` | Пользователь с курсами → видит `<h2>Мои курсы</h2>` |
| 3 | `test_authenticated_user_sees_course_title_and_materials` | Название курса и материалы в HTML |
| 4 | `test_unavailable_course_not_in_html` | Чужой курс отсутствует в ответе |
| 5 | `test_no_courses_section_when_collection_empty` | Нет курсов → `<h2>Мои курсы</h2>` отсутствует |
| 6 | `test_external_url_shows_open_material_link` | `external_url` → кнопка «Открыть материал» |
| 7 | `test_file_shows_download_document_link` | `file_path` → кнопка «Скачать документ» |
| 8 | `test_both_buttons_shown_when_both_values_present` | Есть и ссылка, и файл → обе кнопки |
| 9 | `test_material_dates_not_rendered` | `created_at`, `updated_at` не выводятся |

### 4.3. `tests/Feature/CourseMaterialFileTest.php`

| # | Тест | Описание |
|---|---|---|
| 1 | `test_file_exists_after_save` | Файл существует в `Storage::fake('public')` после сохранения |
| 2 | `test_file_deleted_when_material_deleted` | `$material->delete()` → файл удалён |
| 3 | `test_old_file_deleted_after_replacement` | Замена `file_path` → старый файл удалён |
| 4 | `test_all_material_files_deleted_when_course_deleted` | `$course->delete()` → все файлы материалов удалены |

### 4.4. `tests/Feature/CourseModuleAdminTest.php`

| # | Тест | Описание |
|---|---|---|
| 1 | `test_guest_redirected_to_admin_login` | `GET /admin` → `/admin/login` |
| 2 | `test_non_admin_receives_403` | `is_admin = false` → 403 |
| 3 | `test_admin_opens_student_group_list` | `is_admin = true` → страница списка групп |
| 4 | `test_admin_opens_course_list` | Страница списка курсов |
| 5 | `test_admin_can_create_edit_delete_group` | CRUD группы через Filament |
| 6 | `test_admin_can_create_course_and_assign_groups` | Создание курса с назначением групп |
| 7 | `test_detach_from_relation_manager_does_not_delete_user` | Отсоединение → user существует в БД |

### 4.5. `tests/Unit/Models/` — Unit-тесты новых моделей

#### `StudentGroupTest.php`

| Тест | Описание |
|---|---|
| `test_fillable_contains_name` | Проверка `$fillable` |
| `test_has_many_users` | Связь `users()` → `BelongsToMany` |
| `test_has_many_courses` | Связь `courses()` → `BelongsToMany` |
| `test_factory_creates_valid_model` | `StudentGroupFactory` → валидная модель |

#### `CourseTest.php`

| Тест | Описание |
|---|---|
| `test_fillable_contains_title` | Проверка `$fillable` |
| `test_has_many_materials_ordered` | Связь `materials()` → отсортирована |
| `test_belongs_to_many_student_groups` | Связь `studentGroups()` |
| `test_available_to_scope_returns_only_user_courses` | `availableTo($user)` → только курсы групп пользователя |
| `test_available_to_scope_returns_course_once_for_multiple_groups` | Дубликаты исключаются |
| `test_factory_creates_valid_model` | `CourseFactory` → валидная модель |

#### `CourseMaterialTest.php`

| Тест | Описание |
|---|---|
| `test_fillable_contains_expected_fields` | Все поля в `$fillable` |
| `test_belongs_to_course` | Связь `course()` |
| `test_sort_order_is_cast_to_integer` | Каст `sort_order` |
| `test_auto_assigns_sort_order_on_create` | `creating` → `sort_order = max + 1` |
| `test_respects_explicit_sort_order` | Явный `sort_order` не перезаписывается |
| `test_factory_creates_valid_model` | `CourseMaterialFactory` |

### 4.6. `tests/Unit/Observers/` — Unit-тесты Observer'ов

#### `CourseMaterialObserverTest.php`

| Тест | Описание |
|---|---|
| `test_deletes_file_when_material_deleted` | `deleted()` → файл удалён с диска |
| `test_deletes_old_file_on_file_path_update` | `updated()` с новым `file_path` → старый файл удалён |
| `test_does_nothing_when_file_path_is_null` | `file_path = null` → нет ошибок |
| `test_does_nothing_when_file_does_not_exist` | Файл уже отсутствует → нет ошибок |

#### `CourseObserverTest.php`

| Тест | Описание |
|---|---|
| `test_deletes_all_materials_when_course_deleted` | `deleting()` → все `CourseMaterial` удалены |
| `test_deletes_all_material_files_when_course_deleted` | Файлы всех материалов удалены |

---

## 5. Сводная таблица

### 5.1. Статистика (обновлено 2026-06-14)

| Категория | Было | Стало |
|---|---|---|
| **Feature-тесты (текущий проект)** | 31 | **49** ✅ |
| **Unit-тесты (текущий проект)** | 0 | **18** ✅ |
| **Feature-тесты (модуль курсов)** | 0 | ~23 (будущее) |
| **Unit-тесты (модуль курсов + Observers)** | 0 | ~22 (будущее) |
| **Всего сейчас** | **31** | **67** ✅ |

### 5.2. Приоритеты

| Приоритет | Группа тестов | Статус |
|---|---|---|
| 🔴 P0 | Unit-тесты модели `User` | ✅ Сделано |
| 🔴 P0 | Краевые случаи сброса пароля | ✅ Сделано |
| 🔴 P0 | Валидация аватара | ✅ Сделано |
| 🟡 P1 | Краевые случаи верификации email | ✅ Сделано |
| 🟡 P1 | Unit-тесты `AdminUserSeeder` | ✅ Сделано |
| 🟡 P1 | Тесты Providers | ✅ Сделано |
| 🟢 P2 | 404 | ✅ Сделано |
| 🔵 Будущее | Все тесты модуля курсов | По `instruction.md` этап 8 |

---

## 6. Стратегия запуска

### 6.1. Локальная разработка

```bash
# Все тесты
php artisan test

# Параллельно (если доступно)
php artisan test --parallel

# Конкретный файл
php artisan test --filter=AuthTest

# Unit-тесты
php artisan test --testsuite=Unit

# Feature-тесты
php artisan test --testsuite=Feature
```

### 6.2. CI/CD (рекомендация)

```yaml
# .github/workflows/tests.yml
steps:
  - name: Code style
    run: vendor/bin/pint --test
  - name: Unit tests
    run: php artisan test --testsuite=Unit
  - name: Feature tests
    run: php artisan test --testsuite=Feature
```

### 6.3. Порядок при реализации модуля курсов

Согласно `instruction.md` (этап 8), тесты запускаются в порядке:

```bash
php artisan test --filter=CourseAccessTest
php artisan test --filter=ProfileCoursesTest
php artisan test --filter=CourseMaterialFileTest
php artisan test --filter=CourseModuleAdminTest
php artisan test                   # полный прогон
vendor/bin/pint                    # автофикс стиля
vendor/bin/pint --test             # проверка стиля
```

---

## 7. Чек-лист готовности

- [x] Unit-тесты: модель `User` (9 тестов) → `tests/Unit/UserTest.php`
- [x] Unit-тесты: `AdminUserSeeder` (5 тестов) → `tests/Unit/AdminUserSeederUnitTest.php`
- [x] Feature-тесты: краевые случаи сброса пароля (5 тестов) → `tests/Feature/PasswordResetEdgeCasesTest.php`
- [x] Feature-тесты: краевые случаи верификации email (4 теста) → `tests/Feature/EmailVerificationEdgeCasesTest.php`
- [x] Feature-тесты: валидация аватара (4 теста) → `tests/Feature/AvatarValidationTest.php`
- [x] Feature-тесты: remember-me, редиректы аутентифицированных (3 теста) → `tests/Feature/AuthEdgeCasesTest.php`
- [x] Feature-тесты: 404 (2 теста) → `tests/Feature/HttpErrorPagesTest.php`
- [x] Unit-тесты: Providers (4 теста) → `tests/Unit/ProvidersTest.php`
- [x] Удалён `tests/Feature/ExampleTest.php` (дублирует PageTest)
- [x] Удалён `tests/Unit/ExampleTest.php` (заглушка)
- [x] Все тесты проходят: `php artisan test` → **67 passed, 172 assertions**
- [ ] Модуль курсов: модели, фабрики, миграции (этапы 1-2)
- [ ] Модуль курсов: Unit-тесты моделей `StudentGroup`, `Course`, `CourseMaterial` (~12 тестов)
- [ ] Модуль курсов: Observer'ы + Unit-тесты (этап 3, ~6 тестов)
- [ ] Модуль курсов: Feature-тесты `CourseAccessTest` (7 тестов)
- [ ] Модуль курсов: Feature-тесты `ProfileCoursesTest` (9 тестов)
- [ ] Модуль курсов: Feature-тесты `CourseMaterialFileTest` (4 теста)
- [ ] Модуль курсов: Feature-тесты `CourseModuleAdminTest` (7 тестов)
- [ ] Удалён `tests/Feature/ExampleTest.php` (дублирует PageTest)
- [ ] `tests/Unit/ExampleTest.php` заменён на реальные тесты или удалён
- [ ] Все тесты проходят: `php artisan test`
- [ ] Стиль кода: `vendor/bin/pint --test`
