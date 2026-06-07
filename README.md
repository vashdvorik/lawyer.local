# Юридический портал (Lawyer.Local)

Веб-платформа для юридических услуг с административной панелью на базе Laravel 12 + Filament 3.

## 📋 О проекте

Платформа включает:
- **Публичный сайт** — информационный портал для пользователей
- **Админ-панель** — управление контентом через Filament (/admin)
- **Аутентификация** — регистрация, вход, подтверждение email, восстановление пароля
- **Русская локализация** — все системные сообщения и ошибки валидации на русском языке

## 🚀 Технологический стек

- **PHP 8.4.1** с strict types
- **Laravel 12.60.2** — backend framework
- **Filament v4.0.0** — admin panel
- **MySQL** — база данных (lowyer @ 127.127.126.31:3306)
- **Bootstrap 5** — frontend
- **Livewire v3.8.0** — динамические компоненты

## 📁 Структура проекта

```
app/
├── Http/Controllers/
│   ├── PageController.php              # Публичные страницы (главная, вход, регистрация)
│   └── Auth/
│       ├── AuthController.php          # Аутентификация (вход, регистрация, выход)
│       ├── EmailVerificationController.php  # Подтверждение email
│       └── PasswordResetController.php      # Восстановление пароля
├── Models/
│   └── User.php                        # Модель пользователя (с MustVerifyEmail)
└── Providers/
    └── AppServiceProvider.php

resources/views/
├── layouts/
│   ├── app.blade.php                   # Основной layout
│   └── partials/
│       ├── header.blade.php            # Шапка сайта
│       └── footer.blade.php            # Подвал сайта
├── pages/
│   ├── index.blade.php                 # Главная страница
│   ├── login.blade.php                 # Страница входа
│   └── signup.blade.php                # Страница регистрации
└── auth/
    ├── verify-email.blade.php          # Уведомление о подтверждении email
    ├── forgot-password.blade.php       # Запрос сброса пароля
    └── reset-password.blade.php        # Установка нового пароля

routes/
└── web.php                             # Все маршруты приложения

database/
├── migrations/                         # Миграции БД
├── factories/                          # Фабрики для тестов
└── seeders/                            # Сидеры
```

## ⚙️ Установка и настройка

### 1. Клонирование и установка зависимостей

```bash
composer install
npm install
```

### 2. Настройка окружения

Скопируйте `.env.example` в `.env` и настройте:

```env
APP_NAME=Lawyer
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.127.126.31
DB_PORT=3306
DB_DATABASE=lowyer
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@example.com"
```

### 3. Генерация ключа

```bash
php artisan key:generate
```

### 4. Миграции

```bash
php artisan migrate
```

### 5. Публикация Filament assets

```bash
php artisan filament:assets
```

### 6. Создание администратора

```bash
php artisan make:filament-user
```

### 7. Запуск сервера

```bash
php artisan serve
```

Приложение будет доступно по адресу: http://127.0.0.1:8000

## 🔑 Основные маршруты

### Публичные страницы
- `/` — Главная страница
- `/login` — Вход
- `/signup` — Регистрация

### Админ-панель
- `/admin` — Административная панель Filament
- `/admin/login` — Вход в админ-панель

### Email Verification
- `/email/verify` — Уведомление о необходимости подтверждения
- `/email/verify/{id}/{hash}` — Подтверждение email
- `/email/verification-notification` — Повторная отправка письма

### Password Reset
- `/forgot-password` — Запрос сброса пароля
- `/reset-password/{token}` — Установка нового пароля

## 📧 Email Verification & Password Reset

### Быстрый старт

См. [QUICKSTART_EMAIL.md](QUICKSTART_EMAIL.md) для:
- Пошаговой инструкции по тестированию
- Настройки SMTP (Mailtrap, Gmail)
- Примеров команд для просмотра логов

### Полная документация

См. [EMAIL_PASSWORD_GUIDE.md](EMAIL_PASSWORD_GUIDE.md) для:
- Подробного описания процессов
- Кастомизации шаблонов
- Настроек безопасности
- FAQ и troubleshooting

## 🎨 Дизайн

Дизайн основан на шаблоне "Dlear Education" (адаптирован под юридическую тематику):
- Адаптивная вёрстка
- Bootstrap 5 компоненты
- Иконки и графика
- Формы поиска услуг

См. [DESIGN_GUIDE.md](DESIGN_GUIDE.md) для подробностей.

## 🏗️ Архитектура

Проект следует принципам чистого Laravel:

1. **Контроллеры** — только маршрутизация и ответы
2. **Модели** — Eloquent ORM с явными связями
3. **Views** — Blade шаблоны без бизнес-логики
4. **Form Requests** — валидация и авторизация запросов
5. **Middleware** — авторизация, CSRF, throttling

См. [STRUCTURE.md](STRUCTURE.md) для подробного описания.

## 🧪 Тестирование

```bash
# Запуск всех тестов
php artisan test

# Проверка стиля кода
vendor/bin/pint --test

# Автоисправление стиля
vendor/bin/pint
```

## 🛠️ Полезные команды

```bash
# Очистка кэша
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Просмотр маршрутов
php artisan route:list

# Просмотр логов
Get-Content storage/logs/laravel.log -Tail 50

# Информация о приложении
php artisan about
```

## 📖 Документация

- [STRUCTURE.md](STRUCTURE.md) — Архитектура приложения
- [DESIGN_GUIDE.md](DESIGN_GUIDE.md) — Руководство по дизайну
- [EMAIL_PASSWORD_GUIDE.md](EMAIL_PASSWORD_GUIDE.md) — Email и пароли (полная версия)
- [QUICKSTART_EMAIL.md](QUICKSTART_EMAIL.md) — Быстрый старт для email/пароля
- [LOCALIZATION_RU.md](LOCALIZATION_RU.md) — Русская локализация

## 🔒 Безопасность

- ✅ CSRF защита на всех формах
- ✅ Rate limiting (6 попыток/минуту)
- ✅ Подписанные URLs для верификации
- ✅ Bcrypt хеширование паролей
- ✅ Email verification для новых пользователей
- ✅ Защита от brute-force атак

## 📝 Стиль кода

Проект использует [Laravel Pint](https://laravel.com/docs/pint) для code style:
- PSR-12 стандарт
- Настройки в `pint.json`
- Автоматическое форматирование

1. Следуйте стилю кода (Laravel Pint)
2. Пишите тесты для новых функций
3. Обновляйте документацию
4. Создавайте Pull Request с описанием изменений

## 📄 Лицензия

Проект использует Laravel framework под лицензией [MIT](https://opensource.org/licenses/MIT).

## 📞 Контакты

При возникновении вопросов или проблем создайте Issue в репозитории.

---

**Версия проекта:** 1.0.0  
**Последнее обновление:** 2025-01-21  
**Laravel:** 12.60.2 | **PHP:** 8.4.1 | **Filament:** v4.0.0
