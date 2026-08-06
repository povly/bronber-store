[Back to README](../README.md) · [Архитектура →](architecture.md)

# Установка и настройка

Пошаговое руководство по разворачиванию проекта bronber-store для локальной разработки.

## Требования

| Компонент | Версия |
|-----------|--------|
| PHP | 8.5+ (расширения: `pdo-sqlite`, `mbstring`, `openssl`, `ctype`, `json`, `tokenizer`, `xml`) |
| Composer | 2.x |
| Node.js | 18+ (рекомендуется LTS) |
| npm / bun | npm 9+ или bun (есть `bun.lock`) |
| База данных | SQLite (по умолчанию) или MySQL 8+ |

> Проект использует SQLite как БД по умолчанию — отдельная установка БД не требуется для старта.

## Установка

### 1. Клонирование и зависимости

```bash
git clone <repo-url> bronber-store
cd bronber-store
composer install
npm install
```

> Можно использовать `bun install` вместо `npm install` — в репозитории есть `bun.lock`.

### 2. Конфигурация окружения

```bash
cp .env.example .env
php artisan key:generate
```

Проверьте ключевые переменные в `.env`:

| Переменная | Назначение | Значение по умолчанию |
|------------|------------|----------------------|
| `APP_NAME` | Название приложения | `Laravel` (рекомендуется `bronber-store`) |
| `APP_ENV` | Окружение | `local` (для разработки) |
| `APP_DEBUG` | Отладка | `true` (для разработки) |
| `APP_URL` | URL приложения | `http://localhost` |
| `APP_LOCALE` | Основная локаль | `ru` |
| `DB_CONNECTION` | Драйвер БД | `sqlite` |

### 3. База данных

Для SQLite файл создастся автоматически при первом `migrate`. Для MySQL — создайте пустую БД
и настройте `DB_*` переменные в `.env`.

```bash
php artisan migrate
```

> Сейчас в проекте только фреймворковые миграции (`users`, `password_reset_links`, `sessions` и т.д.).
> Доменные миграции (`products`, `categories`, `orders`) будут добавлены в следующих фазах разработки.

### 4. Сборка фронтенда

```bash
npm run build      # продакшн-сборка
# или
npm run dev        # Vite в режиме HMR (горячая перезагрузка)
```

### 5. Запуск

Один из вариантов:

```bash
php artisan serve                      # только PHP-сервер
```

Или полная dev-среда (сервер + очередь + логи + Vite в одном процессе):

```bash
composer run dev
```

После запуска откройте [http://localhost:8000](http://localhost:8000) (или URL из `APP_URL`).

## Создание администратора админки

MoonShine требует минимум одного администратора:

```bash
php artisan moonshine:user
```

Команда запросит email, имя и пароль. После этого админка доступна по адресу `/admin`.

## Проверка работоспособности

| Страница | URL | Что проверить |
|----------|-----|---------------|
| Главная | `/` | Отображается витрина (hero, категории, товары) |
| Каталог | `/catalog` | Сетка товаров, фильтры |
| Карточка товара | `/product` | Фото, характеристики, совместимость, отзывы |
| Корзина | `/cart` | Mock-товары, пересчёт суммы |
| Оформление | `/checkout` | Mock-итоги заказа |
| Админка | `/admin` | Экран входа MoonShine |

> Все страницы используют захардкоженные данные из `routes/web.php` — это нормальное состояние pre-MVP.

## Полная установка одной командой

В `composer.json` есть скрипт `setup`, автоматизирующий шаги установки:

```bash
composer run setup
```

Он выполняет: `composer install` → копирование `.env` → `key:generate` → `migrate` → `npm install` → `npm run build`.

## Решение проблем

### Vite manifest not found

Если видите ошибку `Unable to locate file in Vite manifest` — запустите сборку:

```bash
npm run build
# или
npm run dev
# или
composer run dev
```

### Очистка кеша

При странных ошибках после изменения конфигурации:

```bash
php artisan optimize:clear
```

В проекте также есть роут `/clear-cache` для быстрой очистки (только для разработки).

## Дальнейшие шаги

- [Архитектура](architecture.md) — изучить принципы и правила зависимостей
- [Разработка](development.md) — освоить процесс (форматирование, тесты, сборка)
- [Админ-панель](admin-panel.md) — настроить MoonShine-ресурсы
- [`.ai-factory/DESCRIPTION.md`](../.ai-factory/DESCRIPTION.md) — полная спецификация проекта

## See Also

- [Архитектура](architecture.md) — как устроен проект и почему именно так
- [Разработка](development.md) — повседневный рабочий процесс и инструменты качества
