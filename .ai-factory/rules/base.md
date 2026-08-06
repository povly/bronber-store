# Базовые правила bronber-store

> Автоопределённые конвенции из анализа кодовой базы. Отредактируйте при необходимости.

## Соглашения об именовании

- **Модели Eloquent:** SingularPascalCase (`Product`, `Category`, `Order`)
- **Контроллеры:** PascalCase, ресурсные — во множественном числе (`ProductsController`) при миграции с closures
- **MoonShine-ресурсы:** PascalCase с суффиксом `Resource` (`ProductResource`). Неймспейс по имени ресурса: `App\MoonShine\Resources\Product\` с подкаталогом `Pages/` (`IndexPage`, `FormPage`)
- **Blade-шаблоны и компоненты:** kebab-case (`product-card.blade.php`, `product-reviews.blade.php`)
- **CSS/JS блоки:** kebab-case, каталоги соответствуют именам views (`blocks/product/`, `css/blocks/product/`, `js/blocks/product/`)
- **Middleware:** PascalCase (`SetLocale`)
- **Миграции:** snake_case, `create_{table}_table.php`
- **Локали:** lowercase (`ru`, `en`)
- **БД-таблицы (планируемые):** snake_case во множественном числе (`products`, `categories`, `order_items`)

## Структура модулей

- `app/Models/` — Eloquent-модели (доменные модели будут добавлены)
- `app/Http/Controllers/` — контроллеры (пока только базовый; миграция с closures ожидается)
- `app/Http/Middleware/` — middleware (`SetLocale` для i18n)
- `app/MoonShine/Resources/{Resource}/` — каждый ресурс в своём подкаталоге с `Pages/`
- `app/MoonShine/Layouts/`, `app/MoonShine/Pages/` — layout и standalone-страницы админки
- `app/Providers/` — сервис-провайдеры (`MoonShineServiceProvider` регистрирует ресурсы)
- `resources/views/blocks/{page}/` — Blade-паршлы постранично
- `resources/css/blocks/{page}/` + `resources/js/blocks/{page}/` — стили и скрипты постранично
- `routes/web.php` — все роуты (closure-based; `routes/api.php` отсутствует)
- `lang/{locale}/` — переводы

## PHP-конвенции

- **PHP 8 attributes для моделей:** использовать `#[Fillable]`, `#[Hidden]` вместо свойств `$fillable`/`$hidden` (как в `app/Models/User.php`)
- **`declare(strict_types=1)`** во всех новых PHP-классах (уже соблюдается в MoonShine/Provider)
- **Constructor property promotion:** `public function __construct(public GitHub $github) {}`
- **Явные type-hints** для параметров и return-типов всех методов: `function isAccessible(User $user, ?string $path = null): bool`
- **Фигурные скобки** для всех control structures, даже однострочных
- **TitleCase для Enum-ключей** (`FavoritePerson`, `Monthly`)
- **PHPDoc** предпочтительнее inline-комментариев; array-shape type definitions в PHPDoc (`@return array<string, string>`)
- **Pint:** всегда `vendor/bin/pint --dirty --format agent` после изменения PHP-файлов

## Control Flow

- Предпочитать плоский, читаемый flow глубоко вложенным условным конструкциям. Использовать guard clauses, ранние `return`/`continue`, небольшие именованные helper-методы. Обрабатывать edge cases и нерелевантные ветки рано, чтобы основной путь оставался видимым.

## Обработка ошибок

- Структурированные error responses без stack traces в production (см. глобальные security-правила)
- Whitelist полей в API-ответах — никогда не возвращать ORM-модель целиком
- Не логировать токены, пароли, PII (см. `security-rules.md`)

## i18n-паттерн

- Роуты регистрируются один раз в `$register`-замыкании, затем дублируются для каждой локали
- Дефолтная локаль (`ru`) — без префикса; остальные — с `/{locale}` префиксом
- `SetLocale` middleware вызывает `app()->setLocale()` по параметру роута
- Тексты в шаблонах — через `__('key')` с ключами в `lang/{locale}/`

## MoonShine v4

- Базовый класс ресурсов: `MoonShine\Laravel\Resources\ModelResource` (НЕ `MoonShine\UI\Resources\CrudResource` для Eloquent)
- Поля импортировать из `MoonShine\UI\Fields\*` (`Text`, `Image`, `Number`, `BelongsTo`, и т.д.)
- Регистрация ресурсов — в `MoonShineServiceProvider::boot()` через `$core->resources([...])`
- Создание через Artisan: `php artisan moonshine:resource {Model}` → генерирует в `app/MoonShine/Resources/`
- Тестирование: флаг `--pest` при генерации

## Frontend-конвенции

- **Alpine.js** — единственный JS-фреймворк; директивы `x-data`, `x-show`, `x-on:` в Blade
- **Совместимость:** IE11+, iOS 9+, Android 4.4+ — избегать современного JS-API без полифиллов; Babel + LightningCSS обрабатывают транпспиляцию
- **Изображения:** vanilla-lazyload (`resources/js/lazyload.js`) — использовать `data-src` вместо `src` для lazy-элементов
- **CSS:** PostCSS с `postcss-mixins`, `postcss-nested`, `postcss-simple-vars`; кастомные функции в `postcss/js/functions/` (`fluidType`, `pxToVw`)
- **Сборка:** `npm run dev` (Vite HMR) / `npm run build` (продакшн); `composer run dev` запускает server+queue+logs+vite параллельно

## Тестирование (Pest 4)

- Синтаксис: `test()`, `it()`, `expect()`
- Создание: `php artisan make:test --pest {Name}` (БЕЗ префикса директории в аргументе)
- Запуск: `php artisan test --compact` или фильтр `--filter=testName`
- Модели для тестов — через factories, не вручную
- Faker: `$this->faker->word()` или `fake()->randomDigit()` (следовать существующим конвенциям)
- Удалять тесты без одобрения запрещено

## Логирование

- PSR-3 / JSON-логи через `laravel/pail` (real-time) + стандартные Laravel-логи
- Не логировать: `Authorization` header, токены, пароли, PII, тела запросов/ответов целиком, SQL с интерполированными параметрами
- Скраббинг через маскер перед логированием (см. `security-rules.md` паттерн)
- Frontend-ошибки — через `browser-logs` (Laravel Boost MCP)
