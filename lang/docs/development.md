[← Архитектура](architecture.md) · [Back to README](../README.md) · [Админ-панель →](admin-panel.md)

# Разработка

Повседневный рабочий процесс: инструменты качества, тестирование, конвенции кода.

## Dev-окружение

Полная dev-среда одной командой (сервер + очередь + логи Pail + Vite HMR параллельно):

```bash
composer run dev
```

Процессы запускаются через `concurrently` с цветовой меткой: `server`, `queue`, `logs`, `vite`.

Для тестов:

```bash
composer run test        # config:clear + php artisan test
# или
php artisan test --compact
php artisan test --compact --filter=testName   # фильтр по имени
```

## Инструменты качества кода

### Laravel Pint (форматирование)

**Обязательно** после любых изменений PHP-файлов:

```bash
vendor/bin/pint --dirty --format agent
```

- `--dirty` — только изменённые файлы (по git status)
- `--format agent` — формат вывода без лишнего шума
- Не запускайте `--test`, только `--format agent` (автоисправление)

### Rector (рефакторинг / апгрейд)

Конфигурация в `rector.php`. Применяет PHP 8.5 sets + Laravel 13 sets + набор CODE_QUALITY / CODING_STYLE / DEAD_CODE / TYPE_DECLARATION и др.

```bash
vendor/bin/rector process          # применить
vendor/bin/rector process --dry-run   # только показать изменения
```

Пути сканирования: `app/`, `database/`, `tests/`. В миграциях пропущен `RenameParamToMatchTypeRector`.

### Pest (тестирование)

Проект использует Pest 4.7 (не PHPUnit). Создание тестов:

```bash
php artisan make:test --pest SomeFeatureTest      # feature-тест
php artisan make:test --pest SomeUnitTest --unit  # unit-тест
```

**Важно:** аргумент `{name}` не должен включать путь к директории тестов — только имя файла.

Конвенции Pest:
- Синтаксис: `test()`, `it()`, `expect()`
- Модели для тестов — через factories, не вручную
- Faker: `$this->faker->word()` или `fake()->randomDigit()`
- Удалять тесты без одобрения запрещено

> Подробнее — в скилле `pest-testing` (проектный skill) и [`.ai-factory/rules/base.md`](../.ai-factory/rules/base.md).

## PHP-конвенции

| Правило | Пример |
|---------|--------|
| `declare(strict_types=1)` | Во всех новых PHP-классах |
| PHP 8 attributes для моделей | `#[Fillable]`, `#[Hidden]` вместо свойств (как в `app/Models/User.php`) |
| Constructor property promotion | `public function __construct(public GitHub $github) {}` |
| Явные type-hints | `function isAccessible(User $user, ?string $path = null): bool` |
| Фигурные скобки всегда | Даже для однострочных тел |
| TitleCase для Enum-ключей | `FavoritePerson`, `Monthly` |
| PHPDoc > inline-комментарии | Array-shape: `@return array<string, string>` |
| Guard clauses / ранний return | Предпочитать плоский, читаемый flow |

> Полные конвенции — в [`.ai-factory/rules/base.md`](../.ai-factory/rules/base.md).
> Laravel-специфичные гайдлайны — в скилле `laravel-best-practices` (проектный skill).

## Соглашения об именовании

| Объект | Конвенция | Пример |
|--------|-----------|--------|
| Eloquent-модели | SingularPascalCase | `Product`, `Category`, `Order` |
| Контроллеры | PascalCase | `ProductController` |
| MoonShine-ресурсы | PascalCase + `Resource` | `ProductResource` |
| Blade-шаблоны | kebab-case | `product-card.blade.php` |
| CSS/JS блоки | kebab-case (каталог = имя view) | `blocks/product/`, `css/blocks/product/` |
| Middleware | PascalCase | `SetLocale` |
| Миграции | snake_case | `create_products_table.php` |
| БД-таблицы | snake_case, множественное число | `products`, `order_items` |
| Локали | lowercase | `ru`, `en` |

## Работа с Artisan

```bash
php artisan list                    # список доступных команд
php artisan [command] --help        # параметры команды
php artisan make:model Product      # создание модели (--all для всего набора)
php artisan route:list              # список роутов (--path=, --name=, --method=)
php artisan config:show app.name    # значения конфига
```

Всегда передавайте `--no-interaction` для неинтерактивного режима и корректные `--options`.

## Создание моделей

При создании доменных моделей используйте:

```bash
php artisan make:model Product --all
```

Создаёт модель + миграцию + factory + seeder + controller. Проверьте параметры через `php artisan make:model --help`.

> Создавайте factory и seeder вместе с моделью — они нужны для тестов (Pest).

## Tinker

```bash
php artisan tinker --execute 'User::count();'
```

- Одинарные кавычки для shell, двойные для PHP-строк внутри
- Не создавать модели без одобрения — использовать factories в тестах

## Laravel Boost (AI-инструменты)

Проект интегрирован с Laravel Boost 2.2 (MCP-сервер). Доступные инструменты:

| Инструмент | Назначение |
|------------|------------|
| `search-docs` | Поиск version-specific документации (Laravel 13) — **использовать перед изменениями кода** |
| `database-schema` | Инспекция структуры БД перед миграциями/моделями |
| `database-query` | Read-only SQL-запросы вместо tinker |
| `browser-logs` | Чтение логов браузера для дебага фронтенда |
| `get-absolute-url` | Корректный URL для分享 |
| `record-rule` | Запись durable-правил в `.ai/rules/` |

> Гайдлайны Boost встроены в `AGENTS.md` в секции `<laravel-boost-guidelines>` (не редактировать вручную).

## Логирование

- PSR-3 / JSON-логи через `laravel/pail` (real-time) + стандартные Laravel-логи
- **Запрещено логировать:** токены, пароли, PII, `Authorization` header, тела запросов/ответов целиком, SQL с интерполированными параметрами
- Структурированные error responses без stack traces в production
- Frontend-ошибки — через `browser-logs` (Boost MCP)

## See Also

- [Архитектура](architecture.md) — куда класть новый код (Controllers, Services, Models)
- [Фронтенд](frontend.md) — Vite, PostCSS, Blade block-based организация
- [`.ai-factory/rules/base.md`](../.ai-factory/rules/base.md) — полные конвенции кода
