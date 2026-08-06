# bronber-store

> Русскоязычный интернет-магазин автозапчастей: топливные насосы и компоненты для BMW / Audi / Volkswagen.

Проект на Laravel 13 + MoonShine 4. Сейчас находится на стадии **pre-MVP**: фронтенд-витрина реализована как
прототип на Blade + Alpine.js с захардкоженными данными, MoonShine-админка заскаффолжена. Доменные модели,
реальная корзина и оформление заказа — в планах развития.

## Быстрый старт

```bash
git clone <repo-url> bronber-store
cd bronber-store
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run build          # продакшн-сборка фронта (или npm run dev для HMR)
```

Для запуска полной dev-среды (сервер + очередь + логи + Vite параллельно):

```bash
composer run dev
```

> Подробно — в [Getting Started](docs/getting-started.md).

## Технологический стек

| Слой | Технологии |
|------|------------|
| Backend | PHP 8.5, Laravel 13.8, Eloquent ORM |
| Админ-панель | MoonShine 4.15 (+ image-editor, media-manager) |
| База данных | SQLite (default) / MySQL (сконфигурирован) |
| Frontend | Blade, Alpine.js 3.15, vanilla-lazyload |
| Сборка | Vite 8 + LightningCSS + Babel (IE11 / iOS 9 compat) |
| Стили | PostCSS (mixins, nested, simple-vars, кастомные функции) |
| Тестирование | Pest 4.7 |
| Качество кода | Laravel Pint 1.27, Rector Laravel 2.5 |
| Отладка | Laravel Debugbar, Laravel Pail, Laravel PAO |
| AI-инструменты | Laravel Boost 2.2 (MCP + гайдлайны + скиллы) |

## Ключевые команды

| Команда | Назначение |
|---------|------------|
| `composer run dev` | Dev-сервер + очередь + логи (Pail) + Vite HMR |
| `composer run test` | Очистка конфига + `php artisan test` |
| `npm run dev` | Vite в режиме HMR |
| `npm run build` | Продакшн-сборка фронтенда |
| `vendor/bin/pint --dirty --format agent` | Форматирование PHP (обязательно после изменений) |
| `vendor/bin/rector process` | Рефакторинг и апгрейд PHP-кода |
| `php artisan test --compact` | Запуск тестов (Pest) |
| `php artisan moonshine:user` | Создание администратора MoonShine |

## Структура проекта

- `routes/web.php` — все роуты (closure-based, i18n-дублирование по локали)
- `resources/views/blocks/{page}/` — Blade-паршлы постранично (block-based паттерн)
- `resources/{css,js}/blocks/{page}/` — стили и скрипты постранично
- `app/MoonShine/Resources/{Resource}/` — CRUD-ресурсы админки (каждый со своим `Pages/`)
- `lang/{ru,en}/` — переводы интерфейса
- `.ai-factory/` — спецификация, архитектура и правила проекта

## Локализация

Основная локаль — русская (`ru`), дополнительная — английская (`en`).
Валюта — российский рубль (₽). Роуты для `en` доступны с префиксом `/en/`.

---

## Документация

| Раздел | Описание |
|--------|----------|
| [Установка и настройка](docs/getting-started.md) | Окружение, зависимости, первый запуск |
| [Архитектура](docs/architecture.md) | Structured Modules, слои, правила зависимостей |
| [Разработка](docs/development.md) | Процесс: Pint, Pest, Rector, Vite, конвенции |
| [Админ-панель](docs/admin-panel.md) | Работа с MoonShine v4: ресурсы, поля, namespace split |
| [Фронтенд](docs/frontend.md) | Block-based структура, i18n, совместимость браузеров |
| [Деплой](docs/deployment.md) | Подготовка к продакшну (pre-MVP placeholder) |

Глубокие материалы: [`.ai-factory/DESCRIPTION.md`](.ai-factory/DESCRIPTION.md) (спецификация),
[`.ai-factory/ARCHITECTURE.md`](.ai-factory/ARCHITECTURE.md) (архитектурные принципы),
[`.ai-factory/rules/base.md`](.ai-factory/rules/base.md) (конвенции кода).

## Лицензия

MIT
