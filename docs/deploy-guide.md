# Инструкция по деплою — lowyer.granatlab.com

Стек: **Laravel 12** + **PHP 8.2+** + **MySQL** + **Nginx** + **Supervisor**

---

## 📦 Подготовка локально (уже сделано)

Проект уже подготовлен к деплою на вашем локальном компьютере:
- ✅ `composer install --no-dev --optimize-autoloader` — только production-пакеты
- ✅ `.env.example` обновлён под продакшен

**Перед загрузкой на сервер** удалите папку `node_modules` — она не нужна, npm-зависимости установятся на сервере.

---

## 🚀 На сервере — по порядку

### 1. Системные пакеты

```bash
sudo apt update && sudo apt install -y \
    nginx mysql-server supervisor \
    php8.4-fpm php8.4-cli php8.4-mysql php8.4-xml php8.4-mbstring \
    php8.4-curl php8.4-zip php8.4-bcmath php8.4-gd php8.4-intl

# Composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"

# Node.js (для сборки Vite)
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt install -y nodejs
```

### 2. Загрузить файлы

Перенесите все файлы проекта через гид (панель хостинга) в `/var/www/lowyer.granatlab.com/`

### 3. База данных

```bash
mysql -u root -p -e "CREATE DATABASE lawyer CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p -e "CREATE USER 'lawyer'@'localhost' IDENTIFIED BY 'пароль';"
mysql -u root -p -e "GRANT ALL ON lawyer.* TO 'lawyer'@'localhost'; FLUSH PRIVILEGES;"
```

### 4. Настройка .env

```bash
cd /var/www/lowyer.granatlab.com
cp .env.example .env
nano .env
```

Обязательно поменять в `.env`:
- `APP_KEY` — оставить пустым, сгенерируется командой ниже
- `DB_PASSWORD` — пароль к БД
- `MAILGUN_SECRET` — ваш ключ

```bash
php artisan key:generate --force
```

### 5. Сборка фронтенда

```bash
npm ci && npm run build
```

### 6. Миграции

```bash
php artisan migrate --seed --force
```

### 7. Права и оптимизация

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 8. Nginx

Файл: `/etc/nginx/sites-available/lowyer.granatlab.com`

```nginx
server {
    listen 80;
    server_name lowyer.granatlab.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name lowyer.granatlab.com;

    root /var/www/lowyer.granatlab.com/public;
    index index.php;

    # SSL — сертификаты (или Let's Encrypt)
    ssl_certificate     /etc/ssl/certs/lowyer.granatlab.com.pem;
    ssl_certificate_key /etc/ssl/private/lowyer.granatlab.com.key;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/lowyer.granatlab.com /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx

# Let's Encrypt (если нужно):
# sudo apt install -y certbot python3-certbot-nginx
# sudo certbot --nginx -d lowyer.granatlab.com
```

### 9. Supervisor — очередь (обязательно для писем!)

Файл: `/etc/supervisor/conf.d/lawyer-worker.conf`

```ini
[program:lawyer-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/lowyer.granatlab.com/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/lawyer-worker.log
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all
sudo supervisorctl status
```

### 10. Проверка

```bash
# Открыть в браузере: https://lowyer.granatlab.com/admin/login
# Админ: granat.agcy@gmail.com / пароль из INITIAL_ADMIN_PASSWORD в .env

# Просмотр логов
tail -f storage/logs/laravel.log
tail -f /var/log/lawyer-worker.log
```

---

## ⚠️ Важно

- `QUEUE_CONNECTION=database` — без **Supervisor** письма не отправятся
- `APP_URL=https://lowyer.granatlab.com` — иначе ссылки в письмах битые
- После обновления кода: `php artisan optimize:clear` и перезапуск supervisor
- Для Mailgun в production — добавьте домен `lowyer.granatlab.com` в панели Mailgun (вместо sandbox)
