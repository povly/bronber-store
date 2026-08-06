# bronber-store

## Обзор

Русскоязычный интернет-магазин автозапчастей (топливные насосы, компоненты для BMW/Audi/VW). Проект находится на стадии **pre-MVP**: фронтенд реализован как статический прототип на Blade + Alpine.js с захардкоженными данными в `routes/web.php`, MoonShine-админка заскаффолжена, но доменных моделей и ресурсов пока нет.

Главная локаль — русская (`ru`), дополнительная — английская (`en`). Валюта — российский рубль (₽).

## Текущие возможности (frontend-прототип)

- Витрина: главная, каталог, карточка товара, отзывы
- Корзина и оформление заказа (mock-данные)
- Контентные страницы: блог, статьи, FAQ, контакты, о компании
- i18n через кастомный `SetLocale` middleware + дублирование роутов по локали
- Ленивая загрузка изображений (vanilla-lazyload)

## Запланированные возможности

- Доменные модели: Product, Category, Brand, Order, Review
- Перенос mock-данных в БД (SQLite/MySQL)
- CRUD-ресурсы MoonShine для управления каталогом
- Реальная логика корзины и оформления заказа
- Аутентификация клиентов и история заказов

## Технический стек

- **Язык:** PHP 8.5
- **Фреймворк:** Laravel 13.8
- **Админ-панель:** MoonShine 4.15 (+ `povly/moonshine-image-editor`, `yurizoom/moonshine-media-manager`)
- **БД:** SQLite (default) / MySQL (сконфигурирован)
- **Frontend JS:** Alpine.js 3.15 + vanilla-lazyload
- **CSS:** PostCSS (mixins, nested, simple-vars, кастомные функции `fluidType`, `pxToVw`)
- **Сборка:** Vite 8 + LightningCSS + Babel (`@babel/preset-env` для совместимости с IE11/iOS9)
- **Тестирование:** Pest 4.7 + `pestphp/pest-plugin-laravel` 4.1
- **Качество кода:** Laravel Pint 1.27, Rector Laravel 2.5 (`driftingly/rector-laravel`)
- **Отладка:** Laravel Debugbar 4.3, Laravel Pail 1.2, Laravel PAO 1.0
- **AI-интеграция:** Laravel Boost 2.2 (MCP + гайдлайны + скиллы)
- **Тинкер:** Laravel Tinker 3.0

## Архитектурные заметки

- **Frontend-first MVC**: переход от статического прототипа к полному MVC. Следующая фаза — доменные модели и миграции.
- **Block-based фронтенд**: CSS, JS и Blade-шаблоны организованы постранично в каталогах `resources/{css,js,views}/blocks/{page}/`. Это кастомный component-подобный паттерн, не Livewire/Inertia.
- **Closure-based роуты**: вся логика страниц — в `routes/web.php` через замыкания (без классов-контроллеров). При миграции на MVC заменить на ресурс-контроллеры.
- **i18n-паттерн**: роуты регистрируются один раз в `$register`-замыкании, затем дублируются для каждой локали через префикс `/{locale}` (кроме дефолтной `ru`).
- **MoonShine v4 namespace split**: ресурсы используют `MoonShine\Laravel\Resources\ModelResource`, поля — `MoonShine\UI\Fields\*`. Не путать с v3-неймспейсами.
- **Совместимость браузеров**: консервативные таргеты (IE11, iOS 9+, Android 4.4+) через Babel + LightningCSS. Избегать современного JS-API без полифиллов.

## Нефункциональные требования

- **Локализация**: двуязычность ru/en через `lang/{locale}/` + middleware; тексты в шаблонах через `__()`
- **Совместимость с браузерами**: IE11+, iOS 9+, Android 4.4+ — критичное требование для целевой аудитории
- **Валюта**: рубль (₽), цены в целых числах, форматирование с пробелом как разделителем тысяч
- **Безопасность**: следовать глобальным security-правилам (whitelist полей в API, no mass-assignment, no secrets в коде, rate limiting на публичных эндпоинтах)
- **Качество кода**: `vendor/bin/pint --dirty --format agent` после каждого изменения PHP; `declare(strict_types=1)` во всех новых классах
- **Логирование**: логи не должны содержать токены, пароли, PII; использовать структурированные error responses без stack traces в production

## Архитектура

Подробные архитектурные принципы и правила зависимостей — в `.ai-factory/ARCHITECTURE.md`.
**Паттерн:** Structured Modules (Technical Layer) — модули по фичам (Catalog, Orders, Reviews, Content) с техническими слоями внутри (Controllers → Services → Models), rich domain models и облегчённой dependency inversion.
