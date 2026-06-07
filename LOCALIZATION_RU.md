# Русская локализация Laravel

## ✅ Установлено

Приложение настроено на русский язык для всех системных сообщений.

## 📁 Языковые файлы

Созданы файлы перевода в `lang/ru/`:

### validation.php
Содержит переводы всех правил валидации Laravel:
- `unique` → "Такое значение поля :attribute уже существует."
- `required` → "Поле :attribute обязательно для заполнения."
- `email` → "Значение поля :attribute должно быть действительным электронным адресом."
- `confirmed` → "Значение поля :attribute не совпадает с подтверждением."
- `min`, `max`, `between` и другие правила
- Названия атрибутов (`email`, `password`, `name` и т.д.)

### auth.php
Переводы сообщений аутентификации:
- `failed` → "Неверный email или пароль."
- `throttle` → "Слишком много попыток входа. Пожалуйста, попробуйте снова через :seconds секунд."

### passwords.php
Переводы для восстановления пароля:
- `sent` → "Ссылка для сброса пароля отправлена на ваш email."
- `reset` → "Ваш пароль был сброшен."
- `token` → "Недействительный токен сброса пароля."
- `user` → "Не удалось найти пользователя с указанным email."

### pagination.php
Переводы пагинации:
- `previous` → "Назад"
- `next` → "Вперёд"

## ⚙️ Конфигурация

### config/app.php
```php
'locale' => env('APP_LOCALE', 'ru'),
'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
'faker_locale' => env('APP_FAKER_LOCALE', 'ru_RU'),
```

### .env
```env
APP_LOCALE=ru
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=ru_RU
```

## 🧪 Примеры использования

### Валидация в Form Request

```php
// app/Http/Requests/SignupRequest.php
public function rules(): array
{
    return [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ];
}
```

**Результат (если email уже существует):**
```
"Такое значение поля email уже существует."
```

### Ручная валидация в контроллере

```php
$validated = $request->validate([
    'email' => 'required|email|unique:users',
    'password' => 'required|min:8|confirmed',
]);
```

**Результат (если пароль короче 8 символов):**
```
"Количество символов в поле пароль должно быть не меньше 8."
```

### Кастомные сообщения

Если нужно переопределить стандартное сообщение в Form Request:

```php
public function messages(): array
{
    return [
        'email.unique' => 'Этот email уже зарегистрирован в системе.',
        'password.min' => 'Пароль должен содержать минимум :min символов.',
    ];
}
```

### Изменение названий атрибутов

В `lang/ru/validation.php` в разделе `attributes`:

```php
'attributes' => [
    'email' => 'email',
    'password' => 'пароль',
    'password_confirmation' => 'подтверждение пароля',
    'name' => 'имя',
    // Добавьте свои
    'phone' => 'телефон',
    'company' => 'компания',
],
```

## 🌐 Поддержка нескольких языков

### Переключение языка в runtime

```php
// В контроллере или middleware
app()->setLocale('ru'); // или 'en'
```

### Middleware для выбора языка

```php
// app/Http/Middleware/SetLocale.php
public function handle($request, Closure $next)
{
    $locale = $request->user()->locale ?? 'ru';
    app()->setLocale($locale);
    
    return $next($request);
}
```

### По выбору пользователя

```php
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ru'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
});

// В AppServiceProvider или Middleware
app()->setLocale(session('locale', 'ru'));
```

## 📝 Добавление новых переводов

### Для кастомных сообщений

Создайте файл `lang/ru/messages.php`:

```php
<?php

return [
    'welcome' => 'Добро пожаловать!',
    'goodbye' => 'До свидания!',
    'user_created' => 'Пользователь успешно создан.',
];
```

Использование:
```php
return redirect()->back()->with('success', __('messages.user_created'));
```

### В Blade шаблонах

```blade
<h1>{{ __('messages.welcome') }}</h1>
<p>@lang('messages.goodbye')</p>
```

## 🔧 Команды Artisan

```bash
# Очистка кэша после изменения языковых файлов
php artisan config:clear
php artisan cache:clear

# Проверка текущей локали
php artisan tinker --execute="echo app()->getLocale();"

# Проверка перевода
php artisan tinker --execute="echo __('validation.required', ['attribute' => 'email']);"
```

## 🎨 Filament Admin Panel

Filament также поддерживает русский язык. Для его активации можно установить пакет:

```bash
composer require filament/spatie-laravel-translatable-plugin
```

Или создать свои переводы в `lang/ru/filament.php`

## 📚 Дополнительные ресурсы

- [Laravel Localization Docs](https://laravel.com/docs/localization)
- [Laravel Validation Docs](https://laravel.com/docs/validation)
- [Исходники переводов Laravel](https://github.com/Laravel-Lang/lang)

## ✨ Примеры ошибок

### До локализации (английский)
```
The email has already been taken.
The password confirmation does not match.
The name field is required.
```

### После локализации (русский)
```
Такое значение поля email уже существует.
Значение поля пароль не совпадает с подтверждением.
Поле имя обязательно для заполнения.
```

---

**Статус:** ✅ Полностью настроено  
**Версия:** 1.0.0  
**Дата:** 2026-05-22
