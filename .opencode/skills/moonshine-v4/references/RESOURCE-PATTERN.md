# Resource Creation Pattern (bronber-store conventions)

Step-by-step guide to creating a new MoonShine v4 CRUD section that matches the existing `MoonShineUserResource` layout exactly.

---

## Step 0 — Prerequisites

You need:
1. An Eloquent model (e.g. `App\Models\Product`).
2. A migration for the table.
3. (Recommended) A factory + seeder for testing.

If the model doesn't exist yet, create it with the standard Laravel stack:

```bash
php artisan make:model Product --migration --factory
php artisan migrate
```

> **bronber-store note:** the project uses PHP 8.5 + Laravel 13.8 — models use
> PHP 8 attributes (`#[Fillable]`, `#[Hidden]` etc.), see `app/Models/User.php`.

---

## Step 1 — Generate the MoonShine resource

```bash
php artisan moonshine:resource Product --no-interaction \
  --model="App\\Models\\Product" --title="Products" --pest
```

The generator creates files flat in `app/MoonShine/Resources/`. **bronber-store nests by model**, so move the generated files into the `{Model}/` + `{Model}/Pages/` layout:

```
app/MoonShine/Resources/
└── Product/
    ├── ProductResource.php          # moved from Resources/
    └── Pages/
        ├── ProductIndexPage.php     # moved + renamed from Resources/ProductIndexPage.php
        ├── ProductFormPage.php      # moved + renamed
        └── ProductDetailPage.php    # moved + renamed (delete if not needed)
```

Then **fix the namespaces** to match the new paths:

```php
// ProductResource.php
namespace App\MoonShine\Resources\Product;

// ProductIndexPage.php
namespace App\MoonShine\Resources\Product\Pages;
```

Update the resource's `pages()` method to reference the moved page classes:

```php
use App\MoonShine\Resources\Product\Pages\ProductIndexPage;
use App\MoonShine\Resources\Product\Pages\ProductFormPage;

protected function pages(): array
{
    return [
        ProductIndexPage::class,
        ProductFormPage::class,
    ];
}
```

---

## Step 2 — The Resource class (full template)

```php
<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Product;

use App\Models\Product;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;
use App\MoonShine\Resources\Product\Pages\ProductIndexPage;
use App\MoonShine\Resources\Product\Pages\ProductFormPage;

/**
 * @extends ModelResource<Product, ProductIndexPage, ProductFormPage, null>
 */
#[Icon('heroicons.cube')]
#[Group('catalog', 'catalog')]
#[Order(10)]
class ProductResource extends ModelResource
{
    protected string $model = Product::class;

    protected string $column = 'name';

    protected array $with = ['category'];

    protected bool $simplePaginate = true;

    public function getTitle(): string
    {
        return __('admin.resources.product.title');
    }

    /**
     * Disable the detail (VIEW) action — bronber-store default for most sections.
     * Allow only CREATE + UPDATE + DELETE. Remove this method to enable VIEW.
     */
    protected function activeActions(): ListOf
    {
        return parent::activeActions()->except(Action::VIEW);
    }

    protected function pages(): array
    {
        return [
            ProductIndexPage::class,
            ProductFormPage::class,
        ];
    }

    /**
     * Columns searched from the index "Search" box.
     */
    protected function search(): array
    {
        return ['id', 'name', 'sku'];
    }
}
```

**The `@extends` PHPDoc generic** matters for IDE support. Its 4 type params are:
`<Model, IndexPage, FormPage, DetailPage>`. Use `null` when there is no DetailPage.

**`activeActions()`** accepts `Action::CREATE`, `Action::VIEW`, `Action::UPDATE`,
`Action::DELETE`, `Action::MASS_DELETE`. Use `->except(...)` to remove, or
`->only(...)` to whitelist.

---

## Step 3 — The IndexPage

```php
<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Product\Pages;

use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use App\MoonShine\Resources\Category\CategoryResource;
use App\MoonShine\Resources\Product\ProductResource;
use MoonShine\Support\Enums\Color;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;

/**
 * @extends IndexPage<ProductResource>
 */
final class ProductIndexPage extends IndexPage
{
    /** @return list<FieldContract> */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),

            Image::make(__('admin.fields.image'), 'image')
                ->modifyRawValue(fn (?string $raw): string => $raw ?? ''),

            Text::make(__('admin.fields.name'), 'name')
                ->sortable(),

            BelongsTo::make(
                __('admin.fields.category'),
                'category',
                formatted: static fn ($model) => $model->name,
                resource: CategoryResource::class,
            )->badge(Color::PURPLE),

            Number::make(__('admin.fields.price'), 'price')
                ->sortable(),

            Date::make(__('admin.fields.created_at'), 'created_at')
                ->format('d.m.Y')
                ->sortable(),
        ];
    }

    /** @return list<FieldContract> */
    protected function filters(): iterable
    {
        return [
            BelongsTo::make(
                __('admin.fields.category'),
                'category',
                formatted: static fn ($model) => $model->name,
                resource: CategoryResource::class,
            )->valuesQuery(static fn (Builder $q) => $q->select(['id', 'name'])),

            Text::make(__('admin.fields.name'), 'name'),
        ];
    }

    /**
     * @param  TableBuilder  $component
     */
    protected function modifyListComponent(ComponentContract $component): TableBuilder
    {
        return $component->columnSelection();
    }
}
```

### IndexPage override points

| Method | Purpose |
|--------|---------|
| `fields()` | Column list (each field renders as preview value) |
| `filters()` | Filter form fields (same field types) |
| `modifyListComponent(ComponentContract $c): TableBuilder` | Mutate the table (column selection, row attrs, bulk actions) |
| `metrics()` | Top-of-page stat cards (`Metric::make(...)`) |
| `queryString()` | Default URL params preserved on navigation |

---

## Step 4 — The FormPage

```php
<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Product\Pages;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Pages\Crud\FormPage;
use App\Models\Product;
use App\MoonShine\Resources\Category\CategoryResource;
use App\MoonShine\Resources\Product\ProductResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;

/**
 * @extends FormPage<ProductResource, Product>
 */
final class ProductFormPage extends FormPage
{
    /** @return list<ComponentContract|FieldContract> */
    protected function fields(): iterable
    {
        return [
            Box::make([
                Tabs::make([
                    Tab::make(__('admin.tabs.main'), [
                        BelongsTo::make(
                            __('admin.fields.category'),
                            'category',
                            formatted: static fn ($model) => $model->name,
                            resource: CategoryResource::class,
                        )
                            ->creatable()
                            ->valuesQuery(static fn (Builder $q) => $q->select(['id', 'name'])),

                        Flex::make([
                            Text::make(__('admin.fields.name'), 'name')->required(),
                            Number::make(__('admin.fields.price'), 'price')->min(0)->required(),
                        ]),

                        Textarea::make(__('admin.fields.description'), 'description'),

                        Date::make(__('admin.fields.published_at'), 'published_at')
                            ->format('d.m.Y')
                            ->default(now()->toDateTimeString()),
                    ])->icon('heroicons.cube'),
                ]),
            ]),
        ];
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'published_at' => ['nullable', 'date'],
        ];
    }
}
```

### FormPage override points

| Method | Purpose |
|--------|---------|
| `fields()` | Form layout (Box / Grid / Column / Tabs / Flex + Fields) |
| `rules(DataWrapperContract $item): array` | Laravel validation rules. Use `$item->getOriginal()` for the underlying model (e.g. for unique ignores) |
| `beforeCreate()` / `afterCreate()` / `beforeUpdate()` / `afterUpdate()` | Lifecycle hooks |
| `modifyFormComponent(ComponentContract $c)` | Mutate the FormBuilder |

### Validation pattern for unique fields

```php
use Illuminate\Validation\Rule;

public function rules(DataWrapperContract $item): array
{
    return [
        'sku' => [
            'sometimes',
            'bail',
            'required',
            'string',
            Rule::unique($item->getOriginal()::class)->ignoreModel($item->getOriginal()),
        ],
    ];
}
```

---

## Step 5 — Register the resource

Edit `app/Providers/MoonShineServiceProvider.php`:

```php
use App\MoonShine\Resources\Product\ProductResource;

public function boot(CoreContract $core): void
{
    $core
        ->resources([
            MoonShineUserResource::class,
            MoonShineUserRoleResource::class,
            ProductResource::class,  // <-- add
        ])
        ->pages([
            ...$core->getConfig()->getPages(),
        ])
    ;
}
```

If a related resource is referenced by `BelongsTo::make(..., resource: OtherResource::class)`, that resource MUST also be registered or the panel throws HTTP 500.

---

## Step 6 — Add to the menu

Either use attributes on the resource class (bronber-store default):

```php
#[Group('catalog', 'catalog')]
#[Order(10)]
class ProductResource extends ModelResource
```

or declare in `MoonShineLayout::menu()`:

```php
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\MenuItem;

protected function menu(): array
{
    return [
        MenuGroup::make(__('admin.menu.catalog'), [
            MenuItem::make(ProductResource::class),
            MenuItem::make(CategoryResource::class),
        ]),
        ...parent::menu(),
    ];
}
```

---

## Step 7 — Optional DetailPage

Add only if you need a read-only overview. To enable:

1. Delete the `activeActions()->except(Action::VIEW)` override in the resource.
2. Update the `@extends` generic 4th param: `<Product, ProductIndexPage, ProductFormPage, ProductDetailPage>`.
3. Add `ProductDetailPage::class` to the resource's `pages()` array.
4. Create `Pages/ProductDetailPage.php` extending `DetailPage` with a `fields()` method (same field types, usually preview-only).

---

## Step 8 — Verify

```bash
composer dump-autoload
php artisan moonshine:resources          # confirm ProductResource shows up
php artisan optimize                     # Laravel 11+ (or moonshine:optimize on L10)
```

Then visit `/admin`, log in, and check the new menu item appears and CRUD works.

---

## Checklist

- [ ] `declare(strict_types=1);` at the top of every file
- [ ] Namespace matches directory: `App\MoonShine\Resources\{Model}` and `App\MoonShine\Resources\{Model}\Pages`
- [ ] All `use` statements use v4 namespaces (`MoonShine\Laravel\*` or `MoonShine\UI\*`)
- [ ] Resource registered in `MoonShineServiceProvider::boot()` under `resources([...])`
- [ ] `pages()` method on the resource lists all page classes
- [ ] `@extends` PHPDoc generics on resource + page classes (IDE support)
- [ ] `BelongsTo`/`BelongsToMany` declare `resource:` and the related resource is registered
- [ ] Validation rules in `FormPage::rules()`, not on the model
- [ ] Run `vendor/bin/pint --dirty` after PHP edits (bronber-store convention)
