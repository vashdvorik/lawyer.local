# Настройка дизайна главной страницы

## ✅ Что было сделано:

### 1. Обновлена главная страница
- Файл: `resources/views/pages/index.blade.php`
- Использован дизайн из `index-three.html`
- Добавлены секции:
  - Hero баннер с анимированными формами
  - Форма поиска услуг
  - Категории услуг (9 блоков)

### 2. Обновлен Header
- Файл: `resources/views/layouts/partials/header.blade.php`
- Добавлен класс `back-header-three` для стиля
- Кнопки входа/регистрации вынесены в правую часть
- Удалена форма поиска из header

### 3. Обновлен Footer
- Файл: `resources/views/layouts/partials/footer.blade.php`
- Добавлены классы `back-footer-dark back-footer-dark2`
- Темный стиль футера
- Добавлена форма подписки на новости
- Контактная информация с иконками

### 4. Обновлен Layout
- Файл: `resources/views/layouts/app.blade.php`
- Добавлены библиотеки:
  - `isotope.pkgd.min.js` (для фильтрации)
  - `imagesloaded.pkgd.min.js` (для загрузки изображений)

---

## 🎨 Настройка дизайна

### Изменение цветов
Основные цвета определены в файле `public/style.css`:
- Основной цвет: `#f84e77` (розовый)
- Вторичный цвет: `#5f2dea` (фиолетовый)
- Темный: `#1e1e2d`

### Замена изображений

#### Логотип
Замените файлы:
- `public/assets/images/logo.png` - обычный логотип
- `public/assets/images/logo-light.png` - светлый логотип для темного футера

#### Баннер
Изображения баннера находятся в:
- `public/assets/images/banner2/normal-image/01.png`
- `public/assets/images/banner2/normal-image/02.png`
- `public/assets/images/banner2/shape/*.png` - анимированные формы

#### Иконки услуг
Иконки категорий:
- `public/assets/images/category3/icon/01.svg` до `09.svg`

---

## 📝 Кастомизация контента

### Изменение текста главной страницы
Отредактируйте файл `resources/views/pages/index.blade.php`:

```blade
{{-- Заголовок баннера --}}
<h1 class="hero3__title">Ваш текст</h1>

{{-- Описание --}}
<p class="hero3__paragraph">Ваше описание</p>
```

### Добавление новых категорий услуг
В файле `resources/views/pages/index.blade.php` найдите секцию `.category3__area` и добавьте блок:

```blade
<div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
    <div class="category3__wrapper mb-25">
        <div class="category3__wrapper-1">
            <img src="{{ asset('assets/images/category3/icon/XX.svg') }}" alt="Иконка">
        </div>
        <div class="category3__wrapper-2">
            <div class="category3__wrapper-2--one">
                <h4><a href="#">Название услуги</a></h4>
                <p>Описание</p>
            </div>
        </div>
    </div>
</div>
```

---

## 🔧 Проблемы и решения

### Стили не применяются
```bash
php artisan view:clear
php artisan cache:clear
```

### Изображения не отображаются
Проверьте права доступа:
```bash
chmod -R 755 public/assets/images
```

### JavaScript не работает
Очистите кэш браузера (Ctrl+Shift+Delete или Cmd+Shift+Delete)

---

## 📱 Адаптивность

Дизайн автоматически адаптируется для:
- Desktop (> 1200px)
- Tablet (768px - 1199px)
- Mobile (< 767px)

Стили адаптивности в файле: `public/assets/css/responsive.css`

---

## 🎯 Следующие шаги

1. **Замените изображения** на свои
2. **Отредактируйте тексты** в соответствии с вашей тематикой
3. **Настройте цвета** в `public/style.css`
4. **Добавьте реальные услуги** через админ-панель Filament
5. **Создайте страницы** "О нас", "Услуги", "Контакты"

---

## 📞 Контакты для обновления

Обновите контактную информацию в футере:
- Файл: `resources/views/layouts/partials/footer.blade.php`
- Строки 13-29 (адрес, телефон, email)

---

## ⚠️ Важно

- Не удаляйте файл `public/style.css` - он критически важен для дизайна
- Все пути к ассетам используют хелпер `asset()` для корректной работы
- После изменения Blade-шаблонов очищайте кэш: `php artisan view:clear`
