# Оптимизация проекта `lawyer.local`

Документ составлен по фактическому состоянию проекта на 9 июня 2026 года.

## Текущее состояние

| Компонент | Состояние проекта |
|---|---|
| PHP | 8.4.1 |
| Laravel | 12.60.2 |
| Filament | 4.0.0 |
| База данных | MySQL |
| Cache | `database` |
| Session | `database` |
| Queue | `database` |
| Mail | SMTP |
| Сборка фронтенда | Vite 7 настроен, но основной Blade-шаблон его не использует |

Основное приложение пока небольшое: публичные страницы статичны, а запросы к БД в основном связаны с пользователем, сессией, кешем и очередью. Поэтому добавлять сложное кеширование запросов сейчас не требуется.

## Приоритеты

1. Сократить CSS, JavaScript и изображения, загружаемые общим Blade-шаблоном.
2. Убрать отправку email из HTTP-запросов в очередь.
3. Настроить корректный production-деплой и постоянный queue worker.
4. Добавлять кеш, Redis и новые индексы только после измерения нагрузки.

## 1. Production-деплой

В production должны быть заданы как минимум:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://example.com
LOG_LEVEL=warning
```

Рекомендуемая последовательность деплоя:

```bash
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
npm ci
npm run build
php artisan migrate --force
php artisan optimize
php artisan queue:restart
```

`php artisan optimize` в установленной версии Laravel кеширует конфигурацию, события, маршруты и Blade-представления. Перед диагностикой проблем кеши можно очистить:

```bash
php artisan optimize:clear
```

В production следует использовать `composer install` и зафиксированный `composer.lock`. Команда `composer update` предназначена для контролируемого обновления зависимостей в разработке с последующим запуском тестов.

После `config:cache` нельзя обращаться к `env()` вне файлов `config/*.php`: прикладной код должен читать значения через `config()`.

## 2. Фронтенд

### Текущая проблема

`resources/views/layouts/app.blade.php` подключает на каждой странице 10 CSS-файлов и 13 JavaScript-файлов из `public/assets`. Их суммарный размер на диске составляет около 1,1 МБ без учёта изображений и шрифтов.

При этом `vite.config.js` собирает `resources/css/app.css` и `resources/js/app.js`, но в Blade нет директивы `@vite`. Поэтому сам по себе `npm run build` сейчас не минифицирует и не объединяет файлы основного шаблона.

### Что сделать

1. Проверить, какие плагины действительно используются: Owl Carousel, Magnific Popup, CounterUp, Waypoints, WOW, Isotope, ImagesLoaded и Modernizr не должны загружаться на всех страницах без необходимости.
2. Перенести используемые CSS/JS в `resources/`, импортировать их из точек входа Vite и подключить:

   ```blade
   @vite(['resources/css/app.css', 'resources/js/app.js'])
   ```

3. Разделить общие и страничные скрипты. Код главной страницы не должен загружаться на страницах входа, регистрации и профиля.
4. После перехода на Vite настроить длительное HTTP-кеширование файлов из `public/build`, потому что их имена содержат content hash.
5. Включить Brotli или gzip на Apache/Nginx.
6. Удалять неиспользуемые файлы из `public/assets` только после проверки ссылок в Blade, CSS и JavaScript. Сейчас каталог содержит 352 файла общим размером около 10 МБ, значительная часть похожа на демонстрационные материалы шаблона.

### Изображения

В первую очередь следует оптимизировать реально используемые крупные изображения:

- `public/assets/images/banner2/normal-image/01.png` — около 247 КБ;
- `public/assets/images/profile/1.png` — около 210 КБ;
- фоновые изображения страниц входа, если они видимы пользователю.

Рекомендации:

- использовать WebP или AVIF с разумным fallback;
- отдавать изображение в размере, близком к фактическому размеру отображения;
- задавать `width` и `height`, чтобы уменьшить layout shift;
- использовать `loading="lazy"` только для изображений ниже первого экрана;
- не применять lazy loading к логотипу, главному hero-изображению и другим кандидатам на LCP;
- проверить необходимость визуального preloader: он может задерживать показ уже загруженной страницы.

Загружаемые аватары уже обрабатываются через GD в `ProfileController`. Установка `spatie/laravel-image-optimizer` только ради аватаров не обязательна. Сначала нужно измерить качество и время существующей обработки.

## 3. База данных и кеш

### Что уже настроено

Текущие миграции уже создают необходимые индексы:

- `users.email` — уникальный индекс;
- `sessions.user_id` и `sessions.last_activity`;
- `jobs.queue`;
- `failed_jobs.uuid`;
- ключи и сроки действия database-cache.

Не следует повторно добавлять индекс для `users.email`. Индексы на новые поля нужно создавать под реальные `WHERE`, `JOIN`, `ORDER BY` и проверять через `EXPLAIN`.

### Что применять при росте проекта

Для будущих списков выбирайте только нужные поля и используйте пагинацию:

```php
$users = User::query()
    ->select(['id', 'name', 'email'])
    ->paginate(25);
```

При появлении отношений используйте eager loading только для реально выводимых связей:

```php
$users = User::query()
    ->with('roles:id,name')
    ->paginate(25);
```

Для пакетной обработки записей безопаснее `chunkById()`:

```php
User::query()->chunkById(500, function ($users): void {
    // Обработка пакета.
});
```

В текущем проекте нет моделей `Post`, `Role` и `StatisticsService`, поэтому примеры с ними не являются готовым кодом проекта.

### Когда нужен прикладной кеш

Публичная главная страница сейчас содержит статические данные в Blade и не выполняет тяжёлых запросов. `Cache::remember()` не даст ей заметного выигрыша.

Кешировать следует только подтверждённо дорогие и часто повторяемые операции. Для изменяемых данных используйте конечный TTL и продуманную инвалидацию:

```php
$services = Cache::remember(
    'services:published',
    now()->addMinutes(10),
    fn () => Service::query()->published()->get(),
);
```

Этот пример станет актуален только после появления модели `Service`.

Database-драйверы кеша и сессий подходят для текущего объёма. Redis имеет смысл подключать, когда нагрузка на MySQL от кеша, сессий или очереди становится измеримой, либо требуется несколько серверов приложения. Redis не нужно добавлять как обязательную зависимость заранее.

На production-сервере должен быть включён PHP OPcache.

## 4. Очереди и email

Проект уже использует `QUEUE_CONNECTION=database`, а таблицы `jobs`, `job_batches` и `failed_jobs` уже созданы миграцией. Команда `queue:failed-table` не нужна.

В `AuthController` подтверждение email отправляется внутри запроса через `sendEmailVerificationNotification()`. При SMTP это увеличивает время регистрации. Аналогично следует проверить отправку ссылки сброса пароля.

Для этих уведомлений нужно создать queued notification, реализующую `ShouldQueue`, и отправлять её через существующую database-очередь.

В development проект уже запускает `queue:listen` через:

```bash
composer run dev
```

В production используйте `queue:work` под Supervisor, systemd или другим process manager:

```bash
php artisan queue:work database --sleep=3 --tries=3 --timeout=60 --max-time=3600
```

Опция `--daemon` у `queue:work` в Laravel 12 помечена как устаревшая и не нужна. После деплоя выполняйте `php artisan queue:restart`, чтобы воркеры загрузили новый код.

Обработка аватара выполняется синхронно, а допустимый размер загрузки составляет 20 МБ. Если измерения покажут задержку, следует уменьшить лимит и ограничения размеров либо сохранять оригинал и обрабатывать его отдельной задачей.

## 5. Безопасность, влияющая на стабильность

Маршруты подтверждения email уже ограничены middleware `throttle:6,1`. Аналогичное ограничение нужно добавить как минимум для:

- `POST /login`;
- `POST /signup`;
- `POST /forgot-password`;
- `POST /reset-password`.

Для простого ограничения можно использовать `throttle:5,1`. Если нужны разные ключи для email и IP, следует зарегистрировать именованные limiter-ы через `RateLimiter::for()` в `AppServiceProvider`.

`URL::forceScheme('https')` не выполняет полноценный HTTP-редирект. HTTPS нужно принудительно включать на уровне Apache/Nginx или reverse proxy. Если приложение находится за proxy, дополнительно нужно корректно настроить trusted proxies, иначе Laravel может неверно определять схему запроса.

Также необходимо:

- держать `APP_DEBUG=false` в production;
- не отключать CSRF middleware для web-маршрутов;
- продолжать выводить пользовательские данные через `{{ }}`, как это сделано в текущих Blade-файлах;
- запускать `composer audit` и `npm audit`;
- не устанавливать Debugbar в production.

## 6. Измерение производительности

Оптимизацию нужно начинать с базовых метрик:

- TTFB и время полного ответа;
- LCP, CLS и INP;
- количество и размер CSS/JS/изображений;
- количество SQL-запросов и наличие медленных запросов;
- время SMTP и обработки загружаемого изображения;
- глубина очереди и количество failed jobs.

Для текущего проекта достаточно браузерных DevTools, Lighthouse, slow query log MySQL и логов Laravel. `laravel/pail` уже установлен для удобного просмотра логов в development.

Telescope, Debugbar и внешние профилировщики следует подключать только под конкретную задачу и отключать после диагностики. Они не являются обязательным пунктом production-релиза.

## 7. Тесты и CI

В репозитории пока нет workflow GitHub Actions. Минимальный pipeline должен выполнять:

```bash
composer validate --strict
composer install --prefer-dist --no-interaction
cp .env.example .env
php artisan key:generate
php artisan test
vendor/bin/pint --test
npm ci
npm run build
composer audit
npm audit
```

`php artisan test --coverage` работает только при наличии Xdebug или PCOV. Не следует делать coverage обязательным, пока соответствующий драйвер не настроен в CI.

Статический анализ сейчас не установлен. Если он потребуется, для Laravel следует добавить Larastan, настроить `phpstan.neon` и запускать:

```bash
vendor/bin/phpstan analyse
```

Одной установки `phpstan/extension-installer` недостаточно.

## Чек-лист релиза

- [ ] `APP_ENV=production`, `APP_DEBUG=false`, правильный `APP_URL`.
- [ ] Зависимости установлены через `composer install`, а не `composer update`.
- [ ] Тесты и Pint прошли до деплоя.
- [ ] Выполнены `npm ci` и `npm run build`.
- [ ] Выполнены `php artisan migrate --force` и `php artisan optimize`.
- [ ] Queue worker запущен под process manager.
- [ ] После деплоя выполнен `php artisan queue:restart`.
- [ ] На сервере включены HTTPS, OPcache и HTTP-сжатие.
- [ ] Для статических файлов настроены корректные cache headers.
- [ ] Debugbar и другие dev-инструменты отсутствуют в production.
- [ ] После релиза проверены главная, авторизация, профиль, email и Filament `/admin`.
