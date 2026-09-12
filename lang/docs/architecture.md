[← Установка](getting-started.md) · [Back to README](../README.md) · [Разработка →](development.md)

# Архитектура

Этот документ — краткое изложение архитектурных принципов bronber-store.
**Полная спецификация архитектуры:** [`.ai-factory/ARCHITECTURE.md`](../.ai-factory/ARCHITECTURE.md).

## Паттерн: Structured Modules (Technical Layer)

Архитектура — **Structured Modules**: облегчённая, доменно-ориентированная модульность.
Каждый модуль инкапсулирует функциональную область со своими роутами, application-сервисами,
rich-моделями и репозиториями.

**Почему выбран:** проект — Laravel-монолит на стадии pre-MVP с одним разработчиком и средней
доменной сложностью (e-commerce: товары, заказы, корзина, прайсинг, отзывы). Архитектура даёт
баланс между структурой для роста и низким порогом входа, оставаясь совместимой с конвенциями
Laravel. Layered быстро деградирует в «толстые» сервисы, Explicit Architecture (DDD) избыточна.

## Текущее состояние (pre-MVP)

Витрина остаётся **frontend-first прототипом** (closure-роуты, mock-данные), но первый
MVC-модуль уже реален — **Content: страницы, блоки, языки, настройки**:

- `PageController` + catch-all `/{slug}` и `/{locale}/{slug}` (в конце `routes/web.php`,
  фиксированные роуты не перекрываются)
- Модели `Page`, `PageTranslation`, `Setting`, `Language` + сервисы `LanguageService`,
  `SettingService`
- Остальное (каталог, корзина, заказы, кабинет) — по-прежнему mock в closure-роутах,
  ждёт CRM-склад

> Миграция прототипа → полноценного MVC происходит постепенно: каждый роут переносится
> в Controller → Service → Model только когда заменяется реальной функциональностью.

## Модуль Content: страницы и блоки

### Схема данных

| Таблица | Назначение |
|---------|-----------|
| `languages` | Активные языки: `code` (unique), `name`, `sort_order`, `is_default` |
| `pages` | Страница: `slug` (unique), `is_published`, `sort_order` |
| `page_translations` | Перевод per-locale: `title`, SEO-поля (`meta_title/description/keywords`, `meta_robots`, `canonical_url`, `og_image`), `content` (JSON блоков, `_type`-ключи), **unique(page_id, locale)** |
| `settings` | Шапка/подвал: `key` (header/footer) × `locale`, `value` (JSON блоков), **unique(key, locale)** |

### Конвейер рендеринга

```
БД (JSON блоки) → BlockRenderer::render($blocks, context)
                → view components.page-blocks.{page|header|footer}.{type}
                → вёрстка pb-* (BEM, PostCSS)
```

Неизвестный или падающий блок — WARN в лог и пропуск: страница не ломается.
Динамический блок `featured-products` берёт данные из `App\Support\CatalogMock`;
при появлении CRM-склада (поддомен + API) источник подменяется без изменения блоков.

### Переводы и языки

- Локаль — **строка в отдельной строке таблицы** (не JSON в колонке): у каждой локали свой
  набор блоков, состав и порядок могут отличаться.
- Список активных языков — `LanguageService` (кэш `Cache::rememberForever`, сброс на
  `saved`/`deleted` модели `Language`). Fallback перевода: запрошенная локаль →
  **дефолтный язык** → первый доступный. `config('app.available_locales')` — bootstrap-фолбэк,
  пока в БД пусто.
- Языки редактируются в админке (`LanguageResource`); система всегда держит один дефолт.
- ⚠️ Кавеат: с `route:cache` префиксы локалей заморожены — после добавления языка нужен
  `php artisan optimize:clear`.

### SEO

Полный набор пер-локали (partial `resources/views/partials/seo.blade.php`): title/description/
keywords/robots/canonical, Open Graph и Twitter Card с fallback-цепочками
(og:title ← meta_title ← title; canonical ← текущий URL), hreflang-альтернативы для всех
локалей с переводом + `x-default`.

### Шапка/подвал из настроек

View composer в `AppServiceProvider` рендерит блоки из `SettingService::get('header'|'footer')`;
если настройки пусты/отсутствуют — показывается статическая вёрстка прототипа (ничего не ломается).

## Модули

| Модуль | Ответственность | Сущности |
|--------|----------------|----------|
| **Catalog** | Витрина товаров, категории, бренды, поиск/фильтры | Product, Category, Brand *(ждёт CRM-склад)* |
| **Orders** | Корзина, оформление заказа, оплата, статусы | Order, OrderItem |
| **Reviews** | Отзывы на товары, рейтинги, модерация | Review |
| **Content** | Блог, статьи, статичные страницы (FAQ, контакты, о нас) | Article, **Page, PageTranslation, Setting, Language** ✅ |

## Целевая структура каталогов

Модули группируются через подкаталоги внутри стандартных Laravel-слоёв (`app/Http/Controllers/{Module}/`,
`app/Services/{Module}/`) — это сохраняет совместимость с `php artisan make:` и PSR-4 autoloading.

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Catalog/          # ProductController, CategoryController
│   │   ├── Orders/           # CartController, CheckoutController
│   │   ├── Content/          # BlogController, PageController
│   │   └── Controller.php    # Абстрактный базовый
│   ├── Requests/{Module}/    # Form Requests (валидация)
│   └── Middleware/
│       └── SetLocale.php     # Существующий i18n middleware
│
├── Services/{Module}/        # Application Services (оркестраторы use-case)
│
├── Models/                   # Rich Domain Models (Eloquent)
│   ├── Product.php, Category.php, Brand.php
│   ├── Order.php, OrderItem.php
│   ├── Review.php
│   └── User.php              # Существующая модель
│
├── Repositories/             # Опционально — только для сложных запросов
│   └── Contracts/            # Интерфейсы (Ports) для dependency inversion
│
├── MoonShine/                # Админ-панель (отдельный delivery-mechanism)
│   ├── Resources/{Resource}/
│   ├── Layouts/, Pages/
│
├── Support/                  # Инфраструктура витрины (без бизнес-логики)
│   ├── PageBlocks/           # PageBlockLibrary (конфиг блоков), BlockRenderer
│   └── CatalogMock.php       # Mock-источник товаров (в будущем → API CRM)
│
└── Providers/
    └── MoonShineServiceProvider.php
```

## Правила зависимостей

Строгий нисходящий поток. Внутренние слои **НИКОГДА** не зависят от внешних.

```
Routes → Controllers → Services → Models
                       Services → Repositories (опционально)
Controllers → Form Requests (валидация)
Models → ничего (чистый домен, только Eloquent)
```

| Правильно | Неправильно |
|-----------|-------------|
| `ProductController` → `ProductService` → `Product` | `Product` → `ProductService` (восходящая) |
| `ProductService` → `ProductRepositoryInterface` | `ProductController` → Repository напрямую |
| `CheckoutService` → `ProductService::decrementStock()` | `CheckoutService` → `Product::find()` напрямую |

### Изоляция модулей

Модули зависят от общего root-уровня (Models, Infrastructure), но **не от внутренностей друг друга**.
Cross-module доступ — только через public API Services.

- ✅ `Orders\CheckoutService` → `Catalog\ProductService::decrementStock($productId, $qty)`
- ❌ `Orders\CheckoutService` → `Product::find($id)->decrement('stock')` (прямой доступ к чужой модели)

## Ключевые принципы

### 1. Rich Domain Models

Бизнес-правила и инварианты живут **внутри моделей**, а не в сервисах. Сервисы только оркестрируют
(загрузить → вызвать метод модели → сохранить).

```php
// ✅ Rich Model: логика инкапсулирована
class Order extends Model
{
    public function addItem(Product $product, int $quantity): void
    {
        if ($this->status !== OrderStatus::DRAFT) {
            throw new \DomainException('Нельзя изменить завершённый заказ');
        }
        // ... инварианты внутри модели
    }
}

// ❌ Anemic Model + Fat Service: правила утекли наружу
class CheckoutService
{
    public function addItem(Order $order, Product $product, int $qty): void
    {
        if ($order->status !== 'draft') throw new \Exception('...'); // ← правило в сервисе
    }
}
```

### 2. Тонкие контроллеры

Контроллер валидирует ввод, вызывает 1–2 Service-метода, формирует ответ. Бизнес-логика в контроллере
(`if/else` на доменном состоянии, расчёты) — нарушение; она belongs в Model или Service.

### 3. Dependency Inversion (облегчённая)

Сервисы получают зависимости через constructor injection (Laravel DI). Repository-интерфейсы —
для сложных запросов (поиск с фильтрами, агрегации) и подготовки к будущему Infrastructure-split.

### 4. MoonShine — отдельный delivery-mechanism

MoonShine-ресурсы напрямую используют Eloquent-модели, **не вызывая** Services публичной части.
Это допустимо: админка и витрина — разные способы доставки одних и тех же моделей.

```
Public:  Controller → Service → Model
Admin:   MoonShine Resource → Model (напрямую, без Service)
```

Критичные бизнес-правила (например, списание остатков) инкапсулированы в Model и срабатывают
в обоих случаях через метод модели или Eloquent observer.

## Антипаттерны

- ❌ **Anemic Domain Models** — модели без методов, только `$fillable` + геттеры/сеттеры
- ❌ **Пропуск Service-слоя** — контроллер дёргает Eloquent напрямую
- ❌ **Восходящие зависимости** — Model импортирует Service
- ❌ **Cross-module через модели** — `Orders\Service` лезет в `Product::find()` напрямую
- ❌ **Бизнес-логика в контроллере или middleware**

## Где искать детали

- [`.ai-factory/ARCHITECTURE.md`](../.ai-factory/ARCHITECTURE.md) — полная спецификация с примерами кода
- [`.ai-factory/rules/base.md`](../.ai-factory/rules/base.md) — конвенции (именование, namespace split, PHP-правила)
- [`.ai-factory/DESCRIPTION.md`](../.ai-factory/DESCRIPTION.md) — обзор проекта и требования

## See Also

- [Разработка](development.md) — повседневный рабочий процесс и инструменты качества кода
- [Админ-панель](admin-panel.md) — как MoonShine вписывается в архитектуру
