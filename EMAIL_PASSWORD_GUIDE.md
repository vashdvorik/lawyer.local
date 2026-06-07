# Подтверждение Email и Восстановление Пароля

## ✅ Реализованный функционал

### 1. Подтверждение Email (Email Verification)
После регистрации пользователь получает письмо со ссылкой для подтверждения email.

### 2. Восстановление Пароля (Password Reset)
Пользователь может запросить ссылку для сброса пароля, если забыл его.

---

## 📁 Структура файлов

### Контроллеры
```
app/Http/Controllers/Auth/
├── AuthController.php                    # Вход, регистрация, выход
├── EmailVerificationController.php       # Подтверждение email
└── PasswordResetController.php           # Сброс пароля
```

### Views
```
resources/views/auth/
├── verify-email.blade.php               # Уведомление о необходимости подтверждения
├── forgot-password.blade.php            # Форма запроса сброса пароля
└── reset-password.blade.php             # Форма установки нового пароля
```

### Маршруты
Все маршруты в `routes/web.php`:
- `/email/verify` - страница уведомления о верификации
- `/email/verify/{id}/{hash}` - ссылка подтверждения email
- `/email/verification-notification` - повторная отправка письма
- `/forgot-password` - форма запроса сброса пароля
- `/reset-password/{token}` - форма установки нового пароля

---

## 🔄 Процесс подтверждения Email

### 1. Регистрация
Пользователь заполняет форму регистрации на `/signup`:
- Имя
- Email
- Пароль
- Подтверждение пароля

### 2. Отправка письма
После регистрации:
- Создаётся аккаунт пользователя
- Автоматически выполняется вход
- Отправляется письмо с ссылкой подтверждения
- Пользователь перенаправляется на `/email/verify`

### 3. Подтверждение
Пользователь:
- Получает письмо на указанный email
- Переходит по ссылке в письме
- Email подтверждается автоматически
- Перенаправление на главную страницу

### 4. Повторная отправка
Если письмо не пришло:
- На странице `/email/verify` есть кнопка "Отправить письмо повторно"
- Ограничение: не более 6 попыток в минуту

---

## 🔑 Процесс восстановления пароля

### 1. Запрос сброса
На странице входа `/login`:
- Нажать "Забыли пароль?"
- Ввести email
- Нажать "Отправить ссылку для сброса пароля"

### 2. Получение письма
Пользователь получает письмо с:
- Ссылкой для сброса пароля
- Токеном (действителен 60 минут)

### 3. Установка нового пароля
Переход по ссылке:
- Открывается форма `/reset-password/{token}`
- Пользователь вводит:
  - Email
  - Новый пароль
  - Подтверждение пароля
- Нажимает "Сбросить пароль"

### 4. Завершение
После успешного сброса:
- Пароль изменён
- Перенаправление на страницу входа
- Уведомление об успехе

---

## ⚙️ Настройка отправки Email

### Текущие настройки (development)
В `.env` настроен `MAIL_MAILER=log`:
```env
MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

Все письма сохраняются в файл:
```
storage/logs/laravel.log
```

### Для production (реальная отправка)

#### Вариант 1: SMTP (например, Gmail)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

#### Вариант 2: Mailgun
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.mailgun.org
MAILGUN_SECRET=your-mailgun-api-key
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

#### Вариант 3: SendGrid, AWS SES, Postmark
Laravel поддерживает множество драйверов. См. документацию:
https://laravel.com/docs/mail

---

## 🔒 Защита маршрутов

### Для требования подтверждённого email
Добавьте middleware `verified` к маршрутам:

```php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
```

### Пример в контроллере
```php
public function __construct()
{
    $this->middleware(['auth', 'verified']);
}
```

---

## 🎨 Кастомизация писем

### Изменение шаблонов писем

#### 1. Публикация шаблонов
```bash
php artisan vendor:publish --tag=laravel-mail
```

#### 2. Редактирование
Шаблоны появятся в:
```
resources/views/vendor/mail/
```

### Кастомные уведомления

Создайте свой класс уведомления:
```bash
php artisan make:notification CustomVerifyEmail
```

В модели `User` переопределите метод:
```php
public function sendEmailVerificationNotification()
{
    $this->notify(new \App\Notifications\CustomVerifyEmail);
}
```

---

## 🧪 Тестирование

### Проверка в логах
Когда `MAIL_MAILER=log`, смотрите письма в:
```
storage/logs/laravel.log
```

Найдите строки:
```
local.INFO: Mail sent to user@example.com
```

### Просмотр писем
Для удобного просмотра писем в разработке установите:
```bash
composer require laravel/telescope --dev
```

Или используйте Mailtrap.io:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
```

---

## 🛠️ Команды Artisan

### Очистка кэша
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Просмотр маршрутов
```bash
php artisan route:list --name=password
php artisan route:list --name=verification
```

### Статус миграций
```bash
php artisan migrate:status
```

---

## 📝 Требования к паролю

По умолчанию используется `Password::defaults()`:
- Минимум 8 символов
- Можно изменить в `AppServiceProvider`

Пример кастомизации:
```php
use Illuminate\Validation\Rules\Password;

Password::defaults(function () {
    return Password::min(8)
        ->letters()
        ->mixedCase()
        ->numbers()
        ->symbols()
        ->uncompromised();
});
```

---

## ❓ FAQ

### Письма не приходят?
1. Проверьте настройки SMTP в `.env`
2. Проверьте `storage/logs/laravel.log`
3. Убедитесь, что очищен кэш: `php artisan config:clear`

### Ссылка подтверждения не работает?
1. Проверьте `APP_URL` в `.env`
2. Убедитесь, что ссылка содержит правильный домен
3. Проверьте срок действия ссылки (по умолчанию 60 минут)

### Как отключить верификацию email?
Удалите `implements MustVerifyEmail` из модели `User`:
```php
class User extends Authenticatable  // без MustVerifyEmail
```

### Как изменить время жизни токена сброса?
В `config/auth.php`:
```php
'passwords' => [
    'users' => [
        'expire' => 60, // минуты
    ],
],
```

---

## 🔐 Безопасность

1. **Rate Limiting**: Ограничение попыток (6 в минуту)
2. **Signed URLs**: Подписанные ссылки для верификации
3. **Token Expiration**: Токены истекают через 60 минут
4. **CSRF Protection**: Включена для всех POST-запросов
5. **Password Hashing**: Bcrypt с настраиваемыми раундами

---

## 📚 Дополнительные ресурсы

- [Laravel Email Verification](https://laravel.com/docs/verification)
- [Laravel Password Reset](https://laravel.com/docs/passwords)
- [Laravel Mail](https://laravel.com/docs/mail)
- [Laravel Notifications](https://laravel.com/docs/notifications)
