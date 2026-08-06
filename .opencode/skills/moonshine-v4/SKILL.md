---
name: moonshine-v4
description: >-
  MoonShine v4 admin panel conventions for Laravel. Use whenever creating or
  modifying MoonShine admin resources, ModelResource classes, CRUD pages
  (Index/Form/Detail), fields, or registration in MoonShineServiceProvider.
  Covers the v4 namespace split (MoonShine\Laravel\* vs MoonShine\UI\*), field
  imports, artisan commands, and bronber-store resource layout patterns.
  Triggers: MoonShine, admin panel, ModelResource, admin CRUD, IndexPage,
  FormPage, DetailPage, moonshine:resource, MoonShine resource.
user-invocable: true
metadata:
  author: bronber-store
  version: "1.0"
  category: admin-panel
  moonshine-version: "4.x"
---

# MoonShine v4 Admin Panel (bronber-store)

Reference for building MoonShine **v4** admin sections in this project.
All namespaces below are **verified against the official v4 docs**
(https://getmoonshine.app/en/docs/4.x) and the existing bronber-store code.

> **WHY THIS SKILL EXISTS:** MoonShine v3 → v4 introduced a namespace split.
> Public skills/recipes often use v3 (`MoonShine\Fields\*`,
> `MoonShine\Resources\ModelResource`) which **do not exist in v4** and will
> cause fatal `Class not found` errors. Always use the namespaces in this file.

---

## 1. The v4 Namespace Split (CRITICAL — read first)

v4 split classes into two roots:

| Root | Holds | Example |
|------|-------|---------|
| `MoonShine\UI\*` | Framework-agnostic: generic fields, components, contracts | `MoonShine\UI\Fields\Text` |
| `MoonShine\Laravel\*` | Laravel/Eloquent-specific: resources, pages, relationship fields, DI | `MoonShine\Laravel\Resources\ModelResource` |

**Field rule of thumb:**
- Generic fields → `MoonShine\UI\Fields\*` (Text, ID, Email, Date, Image, Number, Select, Password, Textarea, Url, Slug-ish, Switcher, Enum, Json, Code, Color, Range, …)
- Relationship fields (Eloquent-aware) → `MoonShine\Laravel\Fields\Relationships\*` (BelongsTo, BelongsToMany, HasMany, HasOne, MorphTo, MorphMany, MorphOne, MorphToMany, HasManyThrough, HasOneThrough) and `MoonShine\Laravel\Fields\Slug` (Slug is Laravel-specific in v4).

See [references/NAMESPACE-MIGRATION.md](references/NAMESPACE-MIGRATION.md) for the full v3→v4 mapping table.

### Core v4 namespaces used in bronber-store

```php
// Resources & pages (Laravel root)
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Laravel\Pages\Page;
use MoonShine\Laravel\Layouts\AppLayout;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\Models\MoonshineUserRole;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;

// Generic fields (UI root)
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Password;
use MoonShine\UI\Fields\PasswordRepeat;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Switcher;

// Relationship fields (Laravel root)
use MoonShine\Laravel\Fields\Slug;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Fields\Relationships\HasOne;

// Components (UI root)
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\Tabs;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Components\Collapse;
use MoonShine\UI\Components\FlexibleRender;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Table\TableBuilder;

// Menu / attributes / support (root packages, NOT UI/Laravel)
use MoonShine\MenuManager\MenuItem;
use MoonShine\MenuManager\MenuGroup;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\Enums\Color;
use MoonShine\Support\Enums\PageType;
use MoonShine\Support\Enums\SortDirection;
use MoonShine\Support\ListOf;
use MoonShine\AssetManager\Css;
use MoonShine\AssetManager\Js;
use MoonShine\ColorManager\ColorManager;
use MoonShine\ColorManager\Palettes\PurplePalette;

// Contracts (for type hints / modifier methods)
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ModalContract;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
```

---

## 2. bronber-store Resource Layout (canonical pattern)

Every CRUD section follows this directory structure (matches `MoonShineUserResource`):

```
app/MoonShine/Resources/
└── Product/
    ├── ProductResource.php          # extends ModelResource
    └── Pages/
        ├── ProductIndexPage.php     # extends IndexPage
        ├── ProductFormPage.php      # extends FormPage
        └── ProductDetailPage.php    # extends DetailPage (optional — bronber-store disables VIEW by default)
```

**Naming rules (MUST follow):**
- Resource class: `{Model}Resource` (e.g. `ProductResource`).
- Page classes: `{Model}{Index|Form|Detail}Page` (e.g. `ProductIndexPage`).
- Namespace: `App\MoonShine\Resources\{Model}` for the resource, `App\MoonShine\Resources\{Model}\Pages` for pages.
- Always start the file with `declare(strict_types=1);`.
- Final classes for pages (`final class ProductIndexPage extends IndexPage`).

See [references/RESOURCE-PATTERN.md](references/RESOURCE-PATTERN.md) for a full step-by-step walkthrough + copy-paste templates.

---

## 3. Resource skeleton (v4)

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

    protected string $column = 'name'; // shown in relations, breadcrumbs

    protected array $with = ['category']; // eager-load relations

    protected bool $simplePaginate = true;

    public function getTitle(): string
    {
        return __('admin.resources.product.title');
    }

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

    protected function search(): array
    {
        return ['id', 'name', 'sku'];
    }
}
```

**Common resource properties** (all optional, override defaults):

| Property | Type | Purpose |
|----------|------|---------|
| `$model` | `string` | Eloquent model class (REQUIRED) |
| `$title` | `string` | Section heading (or override `getTitle()`) |
| `$column` | `string` | Column used to represent a row in relations/breadcrumbs |
| `$with` | `array` | Eloquent eager-load relations |
| `$sortColumn` / `$sortDirection` | `string` / `SortDirection` | Default index sort |
| `$simplePaginate` / `$cursorPaginate` | `bool` | Pagination strategy |
| `$isAsync` | `bool` | Async table/form (default `true` in v4) |
| `$createInModal` / `$editInModal` / `$detailInModal` | `bool` | Open CRUD in modal instead of dedicated page |
| `$alias` | `?string` | URL alias (kebab-case auto from class name) |
| `$redirectAfterSave` | `?PageType` | `PageType::FORM` / `INDEX` / `DETAIL` |

---

## 4. Registering resources

In `app/Providers/MoonShineServiceProvider.php` → `boot()`. The bronber-store convention uses `CoreContract` injection (NOT `ConfiguratorContract`):

```php
public function boot(CoreContract $core): void
{
    $core
        ->resources([
            MoonShineUserResource::class,
            MoonShineUserRoleResource::class,
            ProductResource::class, // <-- add here
        ])
        ->pages([
            ...$core->getConfig()->getPages(),
        ])
    ;
}
```

`php artisan moonshine:resource` auto-registers. When creating a resource by hand, you MUST add the class string to the `resources([...])` array manually.

Menu items: declare in `app/MoonShine/Layouts/MoonShineLayout.php` → `menu()` using `MenuItem::make(ProductResource::class)` or `MenuGroup::make(...)`, OR use the `#[Group]` / `#[Order]` attributes on the resource class (bronber-store prefers attributes).

---

## 5. Fields API basics

Field creation signature (v4):

```php
Field::make(
    Closure|string|null $label = null,
    ?string $column = null,        // defaults to snake_case($label)
    ?Closure $formatted = null,    // preview formatter
)
```

Common chainable methods (full list in [references/FIELDS.md](references/FIELDS.md)):

```php
Text::make('Title', 'title')
    ->required()
    ->sortable()
    ->hint('Shown in catalog')
    ->default('New product')
    ->nullable()
    ->placeholder('Enter title')
    ->badge(Color::PURPLE)            // preview only
    ->link('/help', 'Help', blank: true)
    ->insideLabel()                   // or beforeLabel()
    ->horizontal()
    ->customAttributes(['autocomplete' => 'off'])
    ->wrapperClass('my-input')
    ->translatable()                  // label via lang file
    ->canSee(fn() => auth()->user()->isAdmin());
```

**Modes** (control visual state across contexts):

```php
$field->defaultMode();  // always render as <input>
$field->previewMode();  // always render as preview value
$field->rawMode();      // always render raw value (good for export)
```

**Lifecycle hooks** (override save/fill logic):

```php
$field->onApply(fn(Model $item, $value, Field $ctx) => ...)
      ->onBeforeApply(...)
      ->onAfterApply(...)
      ->changePreview(fn($value, Field $ctx) => ...)
      ->changeFill(fn(Model $item, Field $ctx) => ...)
      ->modifyRawValue(fn($raw, Model $item, Field $ctx) => ...)
      ->fromRaw(fn(string $name) => User::where('name', $name)->value('id'));
```

---

## 6. Artisan commands

| Command | Purpose |
|---------|---------|
| `php artisan moonshine:resource Product` | Create a ModelResource + Pages (`--type=1` ModelResource default, `2` CrudResource, `3` blank) |
| `php artisan moonshine:resource Product --model=App\\Models\\Product --title="Products" --pest` | With options |
| `php artisan moonshine:page` | Standalone page (`--crud` for index+form+detail group, `--extends=IndexPage`) |
| `php artisan moonshine:layout` | Layout class (`--palette=`, `--default`) |
| `php artisan moonshine:field` | Custom field class |
| `php artisan moonshine:component` | Custom component class |
| `php artisan moonshine:handler` | Handler (import/export, etc.) |
| `php artisan moonshine:policy` | Policy tied to MoonShine user |
| `php artisan moonshine:type-cast` | Custom TypeCast |
| `php artisan moonshine:user` | Create superuser |
| `php artisan moonshine:install` | Initial install |
| `php artisan moonshine:publish` | Publish assets / system resources / pages / forms |
| `php artisan moonshine:optimize` | Cache resources/pages (Laravel 10; Laravel 11+ uses `optimize`) |
| `php artisan moonshine:optimize-clear` | Clear the cache |
| `php artisan moonshine:resources` | List registered resources (`--json`) |
| `php artisan moonshine:pages` | List standalone pages (`--json`) |

**bronber-store `moonshine:resource` invocation** (recommended to match conventions):

```bash
php artisan moonshine:resource Product --no-interaction \
  --model=App\\Models\\Product --title="Products" --pest
```

Then move/rename generated files to the `app/MoonShine/Resources/Product/` directory layout (the generator places them flat by default — bronber-store nests by model).

After creating or editing resource classes by hand, run `composer dump-autoload` if the panel returns 500 / class not found.

---

## 7. Pages (Index, Form, Detail)

All three extend base classes from `MoonShine\Laravel\Pages\Crud\*` and override `fields()`. bronber-store splits field definitions per page (not on the resource), which is the v4 preferred style.

### IndexPage

```php
<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Product\Pages;

use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use App\MoonShine\Resources\Product\ProductResource;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\ID;
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
            Text::make(__('admin.fields.name'), 'name')->sortable(),
            BelongsTo::make(
                __('admin.fields.category'),
                'category',
                formatted: static fn ($model) => $model->name,
                resource: \App\MoonShine\Resources\Category\CategoryResource::class,
            ),
            Date::make(__('admin.fields.created_at'), 'created_at')
                ->format('d.m.Y')
                ->sortable(),
        ];
    }

    protected function filters(): iterable
    {
        return [ /* FieldContract[] — same field types, used as filter inputs */ ];
    }

    protected function modifyListComponent(ComponentContract $component): TableBuilder
    {
        return $component->columnSelection(); // enable column visibility toggle
    }
}
```

### FormPage

```php
<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Product\Pages;

use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Pages\Crud\FormPage;
use App\MoonShine\Resources\Product\ProductResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Text;

/**
 * @extends FormPage<ProductResource, \App\Models\Product>
 */
final class ProductFormPage extends FormPage
{
    /** @return list<ComponentContract|FieldContract> */
    protected function fields(): iterable
    {
        return [
            Box::make([
                Flex::make([
                    Text::make(__('admin.fields.name'), 'name')->required(),
                    Number::make(__('admin.fields.price'), 'price')->min(0)->required(),
                ]),
                BelongsTo::make(
                    __('admin.fields.category'),
                    'category',
                    resource: \App\MoonShine\Resources\Category\CategoryResource::class,
                )->creatable(),
            ]),
        ];
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
        ];
    }
}
```

### DetailPage (optional)

Extends `MoonShine\Laravel\Pages\Crud\DetailPage`. bronber-store disables the detail action by default via `activeActions()->except(Action::VIEW)`; only add a DetailPage when a record needs a read-only overview distinct from the form.

---

## 8.bronber-store extras (already installed)

These ship with the project — do NOT reinstall:

- `povly/moonshine-image-editor` — adds the Filerobot image editor to `Image` fields. Assets registered in `MoonShineLayout::assets()` (`/vendor/image-editor/*`). The modal is injected via `ImageEditorRenderer` in `getContentComponents()`.
- `yurizoom/moonshine-media-manager` — file/media manager. The off-canvas panel (`MediaManagerOffCanvas::make()`) is added to `getContentComponents()` in `MoonShineLayout`.

Layout customization lives in `app/MoonShine/Layouts/MoonShineLayout.php` (extends `AppLayout`, uses `PurplePalette`).

---

## 9. Quick checks before finishing

- [ ] Every `use` statement uses a v4 namespace (`MoonShine\Laravel\*` or `MoonShine\UI\*`). No `MoonShine\Fields\*`, no `MoonShine\Resources\*`.
- [ ] Resource registered in `MoonShineServiceProvider::boot()` under `resources([...])`.
- [ ] Pages listed in the resource's `pages()` method.
- [ ] Relationship `BelongsTo`/`BelongsToMany` declare `resource:` (or relation name must match a registered resource).
- [ ] File starts with `declare(strict_types=1);` and the namespace matches the directory.
- [ ] Run `php artisan moonshine:resources` to confirm the resource shows up.
- [ ] Run `php artisan optimize` (Laravel 11+) or `php artisan moonshine:optimize` (Laravel 10) after adding resources in production.
