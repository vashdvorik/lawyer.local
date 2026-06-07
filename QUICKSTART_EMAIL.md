# Быстрый старт: Email Verification & Password Reset

## ✅ Что реализовано:

1. ✅ Подтверждение email после регистрации
2. ✅ Восстановление забытого пароля
3. ✅ Повторная отправка письма подтверждения
4. ✅ Защита от спама (rate limiting)
5. ✅ Безопасные подписанные ссылки

---

## 🚀 Как проверить:

### 1. Регистрация с подтверждением email

```bash
# Откройте в браузере
http://127.0.0.1:8000/signup
```

1. Заполните форму регистрации
2. После регистрации вы будете перенаправлены на `/email/verify`
3. Письмо будет записано в `storage/logs/laravel.log`

**Посмотреть письмо в логе:**
```bash
# Windows PowerShell
Get-Content storage/logs/laravel.log -Tail 50 | Select-String "verify"
```

### 2. Восстановление пароля

```bash
# Откройте в браузере
http://127.0.0.1:8000/login
```

1. Нажмите "Забыли пароль?"
2. Введите email
3. Письмо будет записано в `storage/logs/laravel.log`

**Посмотреть письмо в логе:**
```bash
# Windows PowerShell
Get-Content storage/logs/laravel.log -Tail 50 | Select-String "reset"
```

---

## 📧 Настройка реальной отправки email

### Для тестирования (Mailtrap)

1. Зарегистрируйтесь на https://mailtrap.io (бесплатно)
2. Получите SMTP credentials
3. Обновите `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=ваш-username
MAIL_PASSWORD=ваш-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

4. Очистите кэш:
```bash
php artisan config:clear
```

### Для production (Gmail)

**⚠️ Внимание**: Для Gmail нужен App Password!

1. Включите 2FA в вашем Google аккаунте
2. Создайте App Password: https://myaccount.google.com/apppasswords
3. Обновите `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-digit-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-email@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 🔒 Защита маршрутов (требовать подтверждённый email)

### Метод 1: Middleware в маршрутах

```php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
});
```

### Метод 2: Middleware в контроллере

```php
public function __construct()
{
    $this->middleware(['auth', 'verified']);
}
```

---

## 📋 Полезные команды

```bash
# Просмотр логов (последние 50 строк)
Get-Content storage/logs/laravel.log -Tail 50

# Просмотр только email-логов
Get-Content storage/logs/laravel.log | Select-String "mail"

# Очистка логов
Remove-Item storage/logs/laravel.log

# Очистка кэша
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Проверка маршрутов
php artisan route:list --name=password
php artisan route:list --name=verification
```

---

## 🎨 Кастомизация

### Изменение текстов

#### Страница подтверждения email:
`resources/views/auth/verify-email.blade.php`

#### Страница запроса сброса:
`resources/views/auth/forgot-password.blade.php`

#### Страница нового пароля:
`resources/views/auth/reset-password.blade.php`

### Изменение шаблонов писем

```bash
php artisan vendor:publish --tag=laravel-mail
```

Шаблоны появятся в:
```
resources/views/vendor/mail/html/
```

---

## 🐛 Troubleshooting

### Письма не отправляются?

1. **Проверьте настройки:**
```bash
php artisan config:show mail
```

2. **Очистите кэш:**
```bash
php artisan config:clear
```

3. **Проверьте логи:**
```bash
Get-Content storage/logs/laravel.log -Tail 100
```

### Ссылка подтверждения не работает?

1. Проверьте `APP_URL` в `.env`:
```env
APP_URL=http://127.0.0.1:8000
```

2. Очистите кэш:
```bash
php artisan config:clear
```

### Ошибка "Signature invalid"?

Это происходит, если:
- Изменился `APP_KEY` в `.env`
- URL в ссылке не совпадает с `APP_URL`

**Решение:**
```bash
php artisan config:clear
```

---

## 📖 Подробная документация

См. файл [EMAIL_PASSWORD_GUIDE.md](EMAIL_PASSWORD_GUIDE.md) для полной документации.

---

## ✅ Чек-лист

- [x] Модель User implements MustVerifyEmail
- [x] Контроллеры для верификации и сброса
- [x] Маршруты настроены
- [x] Views созданы
- [x] Миграция password_reset_tokens
- [x] Mail настроен (log для development)
- [x] Ссылка "Забыли пароль?" на странице входа
- [x] Автоматическая отправка письма после регистрации

**Всё готово к использованию!** 🎉
