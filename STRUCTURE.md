# Структура проекта - Публичная часть и Админ-панель

## Обзор

Проект состоит из двух частей:

### 1. Публичная часть (для обычных пользователей)
- **URL**: `http://127.0.0.1:8000/`
- **Шаблоны**: `resources/views/pages/`, `resources/views/layouts/`
- **Контроллеры**: `app/Http/Controllers/PageController.php`

### 2. Админ-панель Filament (для администраторов)
- **URL**: `http://127.0.0.1:8000/admin/login`
- **Управление**: через Filament Resources
- **Конфигурация**: `app/Providers/Filament/AdminPanelProvider.php`

---

## Публичная часть - Структура файлов

### Layouts (Шаблоны)
```
resources/views/layouts/
├── app.blade.php              # Базовый layout для всех страниц
└── partials/
    ├── header.blade.php       # Шапка сайта с меню
    └── footer.blade.php       # Подвал сайта
```

### Pages (Страницы)
```
resources/views/pages/
├── index.blade.php            # Главная страница
├── login.blade.php            # Страница входа пользователей
└── signup.blade.php           # Страница регистрации
```

### Errors (Ошибки)
```
resources/views/errors/
└── 404.blade.php              # Страница 404
```

---

## Маршруты (routes/web.php)

```php
// Публичные страницы
Route::get('/', [PageController::class, 'index'])->name('home');
Route::get('/login', [PageController::class, 'login'])->name('login');
Route::get('/signup', [PageController::class, 'signup'])->name('signup');

// Аутентификация
Route::post('/login', [AuthController::class, 'loginPost']);
Route::post('/signup', [AuthController::class, 'signupPost']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
```

---

## Контроллеры

### PageController
**Файл**: `app/Http/Controllers/PageController.php`

Методы:
- `index()` - Главная страница
- `login()` - Страница входа
- `signup()` - Страница регистрации

### AuthController
**Файл**: `app/Http/Controllers/Auth/AuthController.php`

Методы:
- `loginPost()` - Обработка входа
- `signupPost()` - Обработка регистрации
- `logout()` - Выход из системы

---

## Работа с Filament

### Публикация ассетов Filament
Если стили админ-панели не работают:
```bash
php artisan filament:assets
php artisan optimize:clear
```

### Создание пользователя-администратора
```bash
php artisan make:filament-user
```

### Создание ресурсов Filament
```bash
php artisan make:filament-resource ModelName
```

---

## Ассеты (CSS/JS)

Все статичные файлы находятся в `public/assets/`:
```
public/assets/
├── css/                # Стили
├── js/                 # JavaScript
├── images/             # Изображения
└── fonts/              # Шрифты
```

В Blade используется хелпер `asset()`:
```blade
<link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
<img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
```

---

## Разработка

### Добавление новых страниц

1. Создать метод в `PageController`:
```php
public function about()
{
    return view('pages.about');
}
```

2. Добавить маршрут в `routes/web.php`:
```php
Route::get('/about', [PageController::class, 'about'])->name('about');
```

3. Создать Blade-шаблон `resources/views/pages/about.blade.php`:
```blade
@extends('layouts.app')

@section('title', 'О нас')

@section('content')
    <div class="container">
        <h1>О нас</h1>
    </div>
@endsection
```

### Обновление меню

Редактировать файл: `resources/views/layouts/partials/header.blade.php`

---

## Разделение доступа

### Для обычных пользователей
- Доступ ко всем публичным страницам (`/`, `/login`, `/signup`)
- После входа - доступ к защищённым маршрутам

### Для администраторов
- Доступ к админ-панели Filament: `/admin/login`
- Управление контентом через Filament Resources
- Все CRUD операции через Filament

---

## Проверка кода

```bash
# Запуск тестов
php artisan test

# Проверка стиля кода
vendor/bin/pint --test

# Автоисправление стиля
vendor/bin/pint
```

---

## Полезные команды

```bash
# Очистка кэша
php artisan optimize:clear

# Публикация ассетов Filament
php artisan filament:assets

# Создание контроллера
php artisan make:controller ControllerName

# Создание модели с миграцией
php artisan make:model ModelName -m

# Запуск миграций
php artisan migrate
```
