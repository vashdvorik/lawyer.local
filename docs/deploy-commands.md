# Команды для деплоя (копировать и выполнять по порядку)

## 1. Системные пакеты

```bash
sudo apt update && sudo apt install -y nginx mysql-server supervisor php8.4-fpm php8.4-cli php8.4-mysql php8.4-xml php8.4-mbstring php8.4-curl php8.4-zip php8.4-bcmath php8.4-gd php8.4-intl

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"

curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt install -y nodejs
```

## 2. База данных

```bash
mysql -u root -p -e "CREATE DATABASE lawyer CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p -e "CREATE USER 'lawyer'@'localhost' IDENTIFIED BY 'пароль';"
mysql -u root -p -e "GRANT ALL ON lawyer.* TO 'lawyer'@'localhost'; FLUSH PRIVILEGES;"
```

## 3. Настройка .env

```bash
cd /var/www/lowyer.granatlab.com
cp .env.example .env
nano .env   # <- отредактировать: APP_URL, DB_*, MAIL_*

php artisan key:generate --force
```

## 4. Миграции

```bash
php artisan migrate --seed --force
```

## 5. Зависимости

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
php artisan storage:link
```

## 6. Production-кэш

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

## 7. Nginx (создать конфиг, затем выполнить)

```bash
sudo ln -s /etc/nginx/sites-available/lowyer.granatlab.com /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx

# Let's Encrypt (если нужно):
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d lowyer.granatlab.com
```

## 8. Supervisor — очередь

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all
# Проверка:
sudo supervisorctl status
```

## 9. Полезное

```bash
# Логи
tail -f storage/logs/laravel.log

# Остановка очереди перед обновлением
sudo supervisorctl stop all

# Сброс кэша
php artisan optimize:clear

# Перезапуск после обновления
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo supervisorctl start all
```
