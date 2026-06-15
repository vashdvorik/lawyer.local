# Деплой — краткий порядок действий

## Шаг 1. Установить Node.js (только если не стоит)

На сервере выполнить:
```bash
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt install -y nodejs
```

## Шаг 2. Собрать фронтенд

На сервере, в папке проекта:
```bash
npm ci && npm run build
```

## Шаг 3. Настроить .env

```bash
cp .env.example .env
nano .env
```

Обязательно указать:
- `APP_KEY` — сгенерировать: `php artisan key:generate --force`
- `DB_PASSWORD` — пароль от БД
- `MAILGUN_SECRET` — ключ Mailgun

## Шаг 4. База данных

```bash
mysql -u root -p -e "CREATE DATABASE lawyer CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p -e "GRANT ALL ON lawyer.* TO 'lawyer'@'localhost' IDENTIFIED BY 'пароль'; FLUSH PRIVILEGES;"
```

## Шаг 5. Миграции

```bash
php artisan migrate --seed --force
```

## Шаг 6. Права и кэш

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```
