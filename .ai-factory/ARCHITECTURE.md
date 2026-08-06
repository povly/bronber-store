# Архитектура: Structured Modules (Technical Layer)

## Обзор

**Structured Modules** — это облегчённая, доменно-ориентированная модульная архитектура. Каждый модуль инкапсулирует функциональную область (Catalog, Orders, Auth, Content) со своими роутами, application-сервисами (оркестраторами), rich-моделями и репозиториями. Архитектура приносит ключевые преимущества DDD (rich-модели, dependency inversion) без строгой folder-формалистике Explicit Architecture.

**Почему выбрана для bronber-store:** проект — Laravel-монолит на стадии pre-MVP с одним разработчиком и средней доменной сложностью (e-commerce: товары, заказы, корзина, прайсинг, отзывы). Layered Architecture быстро деградирует в «толстые» сервисы при появлении логики заказов/оплаты. Explicit Architecture избыточна для текущего масштаба. Structured Modules даёт нужный баланс: структура для роста, низкий порог входа, естественная совместимость с конвенциями Laravel.

## Обоснование решения

- **Тип проекта:** E-commerce (доменная логика средней сложности: state machine заказов, ценовые правила, остатки)
- **Стек:** PHP 8.5 + Laravel 13.8 + Eloquent + MoonShine 4
- **Ключевой фактор:** средняя сложность домена + 1 разработчик + потребность в структуре без DDD-оверхеда
- **Эволюция:** при росте сложности домена и команды → миграция на Explicit Architecture тривиальна (модули → bounded contexts)

## Структура каталогов

Архитектура адаптирована под стандартную структуру Laravel `app/` (PSR-4 autoloading). Модули группируются через подкаталоги внутри технических слоёв — это сохраняет совместимость с `php artisan make:` командами и не создаёт новых базовых каталогов без необходимости.

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Catalog/                    # ── МОДУЛЬ: Каталог ──
│   │   │   ├── ProductController.php   # Витрина, карточка товара
│   │   │   └── CategoryController.php
│   │   ├── Orders/                     # ── МОДУЛЬ: Заказы ──
│   │   │   ├── CartController.php      # Корзина
│   │   │   └── CheckoutController.php  # Оформление заказа
│   │   ├── Content/                    # ── МОДУЛЬ: Контент ──
│   │   │   ├── BlogController.php
│   │   │   └── PageController.php      # FAQ, контакты, о компании
│   │   └── Controller.php              # Абстрактный базовый
│   ├── Requests/
│   │   ├── Catalog/
│   │   │   └── ProductSearchRequest.php
│   │   └── Orders/
│   │       └── CheckoutRequest.php
│   └── Middleware/
│       └── SetLocale.php               # Существующий i18n middleware
│
├── Services/                           # Application Services (оркестраторы use-case)
│   ├── Catalog/
│   │   ├── ProductService.php          # Use-case: поиск, фильтрация, рекомендации
│   │   └── CategoryService.php
│   ├── Orders/
│   │   ├── CartService.php             # Use-case: добавить/удалить/пересчитать
│   │   └── CheckoutService.php         # Use-case: оформление, оплата, списание остатков
│   └── Content/
│       └── BlogService.php
│
├── Models/                             # Rich Domain Models (Eloquent)
│   ├── Product.php                     # Инкапсулирует прайсинг, статусы, остатки
│   ├── Category.php                    # Дерево категорий, иерархия
│   ├── Brand.php
│   ├── Order.php                       # State machine: draft → finalized → paid → shipped
│   ├── OrderItem.php
│   ├── Review.php                      # Валидация отзыва, рейтинги
│   └── User.php                        # Существующая модель
│
├── Repositories/                       # Слой доступа к данным (опционально, только для сложных запросов)
│   ├── Contracts/                      # Интерфейсы (Ports) для dependency inversion
│   │   ├── Catalog/
│   │   │   └── ProductRepositoryInterface.php
│   │   └── Orders/
│   │       └── OrderRepositoryInterface.php
│   └── Orders/
│       └── OrderRepository.php         # Реализация интерфейса
│
├── MoonShine/                          # Админ-панель (не часть модульной иерархии)
│   ├── Resources/
│   │   ├── Product/
│   │   │   ├── ProductResource.php
│   │   │   └── Pages/{IndexPage,FormPage}.php
│   │   └── ...                         # CatalogResource, OrderResource и т.д.
│   ├── Layouts/MoonShineLayout.php
│   └── Pages/Dashboard.php
│
└── Providers/
    ├── AppServiceProvider.php
    └── MoonShineServiceProvider.php    # Регистрация ресурсов админки

routes/
└── web.php                             # i18n-роуты → указывают на Controllers/{Module}/

database/
├── migrations/                         # Все миграции единым списком (Laravel convention)
├── factories/                          # {Model}Factory для каждого домена
└── seeders/
```

### Распределение модулей

| Модуль | Ответственность | Сущности |
|--------|----------------|----------|
| **Catalog** | Витрина товаров, категории, бренды, поиск/фильтры | Product, Category, Brand |
| **Orders** | Корзина, оформление заказа, оплата, статусы | Order, OrderItem |
| **Reviews** | Отзывы на товары, рейтинги, модерация | Review |
| **Content** | Блог, статьи, статичные страницы (FAQ, контакты, о нас) | Article, Page |

## Правила зависимостей

Строгий нисходящий поток: зависимости направлены **только вниз**. Внутренние слои НИКОГДА не зависят от внешних.

```
Routes → Controllers → Services → Models
                       Services → Repositories (опционально)
                       Services → Models (всегда)
Controllers → Form Requests (валидация)
Models → ничего (чистый домен, только Eloquent)
Repositories → Models (маппинг данных)
```

- ✅ `ProductController` → `ProductService` → `Product` (модель)
- ✅ `CheckoutService` → `OrderService` + `Product` (cross-entity оркестрация внутри Services)
- ✅ `ProductService` → `ProductRepositoryInterface` (dependency inversion)
- ❌ `Product` (модель) → `ProductService` (восходящая зависимость)
- ❌ `ProductController` → `ProductRepository` напрямую (пропуск Service-слоя)
- ❌ `Catalog\ProductController` → `Orders\CheckoutService` (cross-module через Service, не напрямую в чужую модель)

### Изоляция модулей

Модули зависят от общего root-уровня (Models, Infrastructure), но **не от внутренностей друг друга**. Cross-module зависимости — только через определённые public API (методы Services).

- ✅ `Orders\CheckoutService` → `Catalog\ProductService::decrementStock($productId, $qty)`
- ❌ `Orders\CheckoutService` → `Product::find($id)->decrement('stock')` (прямой доступ к чужой модели)

## Взаимодействие слоёв

### Запрос → Ответ (типичный flow)

```
1. HTTP Request попадает в route (routes/web.php)
2. Route → Controller method (app/Http/Controllers/{Module}/)
3. Controller валидирует ввод (Form Request) → вызывает один-два Service-метода
4. Service (app/Services/{Module}/) оркестрирует use-case:
   - Загружает данные через Model/Repository
   - Вызывает бизнес-методы на rich-модели (инкапсулированные правила)
   - Сохраняет результат
   - Возвращает DTO/Model/Mock в Controller
5. Controller формирует ответ (View для Blade, или redirect)
```

### Rich Domain Model (ключевой принцип)

Бизнес-правила и инварианты живут **внутри моделей**, а не в сервисах. Сервисы только оркестрируют (загрузить → вызвать метод модели → сохранить).

```php
// ✅ ХОРОШО: Rich Model — логика внутри модели
class Order extends Model
{
    public function addItem(Product $product, int $quantity): void
    {
        if ($this->status !== OrderStatus::DRAFT) {
            throw new \DomainException('Нельзя изменить завершённый заказ');
        }
        if ($product->stock < $quantity) {
            throw new \DomainException("Недостаточно остатков: {$product->name}");
        }

        $this->items()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price' => $product->currentPrice(),
        ]);
        $this->recalculateTotal();
    }

    public function finalize(): void
    {
        if ($this->items()->doesntExist()) {
            throw new \DomainException('Нельзя завершить пустой заказ');
        }
        $this->status = OrderStatus::FINALIZED;
        $this->save();
    }

    private function recalculateTotal(): void
    {
        $this->total = $this->items->sum(fn (OrderItem $item) => $item->price * $item->quantity);
    }
}

// ❌ ПЛОХО: Anemic Model + Fat Service — логика снаружи
class CheckoutService
{
    public function addItem(Order $order, Product $product, int $qty): void
    {
        if ($order->status !== 'draft') throw new \Exception('Нельзя');  // ← правило утекло в сервис
        if ($product->stock < $qty) throw new \Exception('Нет остатков'); // ← инвариант снаружи модели
        $order->items()->create([...]);
        $order->total = $order->items->sum(...);                         // ← расчёт в сервисе
    }
}
```

### Dependency Inversion (опционально, для сложных запросов)

Для простых CRUD Model + Eloquent достаточно. Для сложных запросов (поиск с фильтрами, агрегации) — интерфейс + реализация:

```php
// Контракт (Port) — в app/Repositories/Contracts/
interface ProductRepositoryInterface
{
    /** @return LengthAwarePaginator<Product> */
    public function search(ProductSearchDto $filters): LengthAwarePaginator;
}

// Реализация (Adapter) — в app/Repositories/Catalog/
class EloquentProductRepository implements ProductRepositoryInterface
{
    public function search(ProductSearchDto $filters): LengthAwarePaginator
    {
        return Product::query()
            ->with(['category', 'brand'])
            ->when($filters->categoryId, fn ($q, $id) => $q->where('category_id', $id))
            ->when($filters->brandId, fn ($q, $id) => $q->where('brand_id', $id))
            ->when($filters->minPrice, fn ($q, $p) => $q->where('price', '>=', $p))
            ->orderBy('sort_order')
            ->paginate(24);
    }
}

// Регистрация в AppServiceProvider
public function register(): void
{
    $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);
}

// Использование в Service
class ProductService
{
    public function __construct(private ProductRepositoryInterface $products) {}

    public function searchCatalog(ProductSearchDto $filters): LengthAwarePaginator
    {
        return $this->products->search($filters);
    }
}
```

## Ключевые принципы

1. **Границы модулей:** каждый модуль инкапсулирует функциональную область. Cross-module доступ — только через public API Services других модулей, никогда напрямую к чужим Models/Repositories.

2. **Rich Domain Models:** модели инкапсулируют свои инварианты и бизнес-правила (`Order::addItem()`, `Product::applyDiscount()`, `Review::approve()`). Сервисы НЕ содержат бизнес-логику — они оркестрируют.

3. **Dependency Inversion (облегчённая):** сервисы получают зависимости через constructor injection (Laravel DI). Repository-интерфейсы поощряются для сложных запросов и подготовки к будущему Infrastructure-split.

4. **Тонкие контроллеры:** контроллер валидирует ввод, вызывает 1-2 Service-метода, формирует ответ. Если в контроллере появилась бизнес-логика (if/else на доменном состоянии, расчёты) — она belongs в Model или Service.

5. **Infrastructure минимальна:** root-level `app/` каталоги (Providers, Middleware) — только cross-cutting concerns. Никаких god-классов.

6. **Совместимость с Laravel:** используем стандартные `php artisan make:` команды. PSR-4 autoloading сохранён (`App\Services\Catalog\ProductService`). Form Requests для валидации, не inline-валидация в контроллерах.

## Политика организации кода

- **Новый код:** весь новый доменный код строго следует архитектуре этого документа. Модели в `app/Models/`, сервисы в `app/Services/{Module}/`, контроллеры в `app/Http/Controllers/{Module}/`.

- **Существующий код (closure-роуты):** текущие closure-роуты в `routes/web.php` с захардкоженными данными — это прототип. При реализации доменной логики переносить логику из замыканий в Controller → Service → Model по мере добавления каждой фичи. Не рефакторить все роуты сразу — только те, что заменяются реальной функциональностью.

- **Block-based фронтенд:** организация `resources/{css,js,views}/blocks/{page}/` ортогональна backend-архитектуре. Сохраняется как есть — Blade-шаблоны продолжают использовать block-структуру, контроллеры отдают те же views через `view('product', $data)`.

- **Совместимость:** новый код вызывает существующий (closure-роуты, mock-данные) через чистые интерфейсы во время постепенной миграции. Не смешивать стили в одной фиче.

## Антипаттерны

- ❌ **Anemic Domain Models:** модели без методов, только геттеры/сеттерки + `$fillable`. Вся логика утекает в «толстые» сервисы → сложность тестирования, дублирование правил.
- ❌ **Пропуск Service-слоя:** контроллер вызывает Repository/Eloquent напрямую, минуя Service.
- ❌ **Восходящие зависимости:** Model импортирует Service; Service импортирует Controller.
- ❌ **Cross-module через модели:** `Orders\CheckoutService` обращается к `Product::find()` напрямую вместо `Catalog\ProductService`.
- ❌ **Бизнес-логика в контроллере:** `if ($order->status === 'draft')` в контроллере — это инвариант `Order`, должен быть методом модели.
- ❌ **Бизнес-логика в middleware:** middleware обрабатывает cross-cutting (auth, locale, rate limiting), не доменные правила.
- ❌ **Циклические зависимости модулей:** модуль A импортирует B, B импортирует A → выносить в shared contracts или domain events.

## Связь с MoonShine

MoonShine-ресурсы (`app/MoonShine/Resources/`) — это слой **админ-презентации**, отдельный от публичной витрины. Они напрямую используют Eloquent Models ( тот же `Product`), но НЕ вызывают Services публичной части. MoonShine-админка может изменять доменные данные напрямую через свои CRUD-ресурсы — это нормально для backoffice.

```
Public:  Controller → Service → Model
Admin:   MoonShine Resource → Model ( напрямую, без Service)
```

Это допустимо, т.к. админка и витрина — разные delivery-mechanisms для одних и тех же моделей. Если бизнес-правило критично (например, списание остатков), оно инкапсулировано в Model и срабатывает в обоих случаях через метод модели или Eloquent observer.
