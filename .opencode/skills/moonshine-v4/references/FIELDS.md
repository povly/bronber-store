# MoonShine v4 Fields Reference

All namespaces **verified against MoonShine v4 docs** (https://getmoonshine.app/en/docs/4.x/fields/).
v3 used `MoonShine\Fields\*` — **does not exist in v4**, will fatal.

## Golden rule

| Field type | Namespace root |
|------------|----------------|
| Generic fields (no Eloquent awareness) | `MoonShine\UI\Fields\*` |
| Relationship fields (Eloquent-aware) | `MoonShine\Laravel\Fields\Relationships\*` |
| Slug (special — needs Laravel) | `MoonShine\Laravel\Fields\Slug` |

---

## 1. Basic / scalar fields — `MoonShine\UI\Fields\*`

```php
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\Password;
use MoonShine\UI\Fields\PasswordRepeat;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Checkbox;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Color;
use MoonShine\UI\Fields\Url;
use MoonShine\UI\Fields\Phone;
use MoonShine\UI\Fields\Code;
use MoonShine\UI\Fields\Markdown;
use MoonShine\UI\Fields\TinyMce;
use MoonShine\UI\Fields\Hidden;
use MoonShine\UI\Fields\Preview;
```

Examples:

```php
ID::make()->sortable();                       // primary key, auto-column
Text::make('Title', 'title')->required();
Textarea::make('Body', 'body');
Email::make('Email', 'email')->sortable();
Password::make('Password', 'password')->eye()->customAttributes(['autocomplete' => 'new-password']);
PasswordRepeat::make('Repeat', 'password_confirmation')->eye();
Number::make('Price', 'price')->min(0)->max(999999)->step(0.01)->stars();
Checkbox::make('Active', 'is_active');
Switcher::make('Published', 'is_published');  // toggle UI variant
Color::make('Accent', 'accent_color');
Url::make('Website', 'website');
Phone::make('Phone', 'phone');
Code::make('Snippet', 'snippet')->language('php');
Markdown::make('Description', 'description');
TinyMce::make('Content', 'content');          // requires tinymce assets
Hidden::make('Token', 'token');
Preview::make('Computed', formatted: fn($item) => strtoupper($item->name));
```

## 2. Selection / enum fields — `MoonShine\UI\Fields\*`

```php
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\Range;
use MoonShine\UI\Fields\RangeSlider;
use MoonShine\UI\Fields\DateRange;
```

```php
Select::make('Status', 'status')
    ->options(['draft' => 'Draft', 'published' => 'Published'])
    ->multiple();                              // multi-select

Enum::make('State', 'state')
    ->attach(\App\Enums\OrderState::class);    // PHP 8.1+ backed enum

Range::make('Quantity', 'quantity')->min(0)->max(100);
RangeSlider::make('Range', 'range')->fromTo(0, 100);
DateRange::make('Created between', 'created_at');
```

## 3. Date / time fields — `MoonShine\UI\Fields\*`

```php
use MoonShine\UI\Fields\Date;
```

```php
Date::make('Created at', 'created_at')->format('d.m.Y')->sortable();
Date::make('Published at', 'published_at')
    ->withTime()
    ->default(now()->toDateTimeString());
```

## 4. File / image fields — `MoonShine\UI\Fields\*`

```php
use MoonShine\UI\Fields\File;
use MoonShine\UI\Fields\Image;
```

```php
Image::make('Avatar', 'avatar')
    ->disk(moonshineConfig()->getDisk())             // MoonShine disk (bronber-store pattern)
    ->dir(moonshineConfig()->getUserAvatarsDir())
    ->allowedExtensions(['jpg', 'png', 'jpeg', 'gif'])
    ->multiple();                                     // gallery

File::make('Document', 'document')
    ->disk('public')
    ->dir('documents')
    ->allowedExtensions(['pdf', 'docx'])
    ->multiple();

// bronber-store has povly/moonshine-image-editor — Image fields automatically
// get the Filerobot editor button via the layout asset registration.
```

## 5. Composite / structural fields — `MoonShine\UI\Fields\*`

```php
use MoonShine\UI\Fields\Json;
use MoonShine\UI\Fields\Fieldset;
use MoonShine\UI\Fields\Position;
use MoonShine\UI\Fields\Template;
use MoonShine\UI\Fields\HiddenIds;
```

```php
Json::make('Attributes', 'attributes')->fields([
    Text::make('Key'),
    Text::make('Value'),
]);

Fieldset::make('Settings', fn() => [Switcher::make('Active'), Text::make('Note')]);

Position::make();                                  // for reorderable HasMany rows
Template::make('Section', 'admin.partials._custom'); // Blade partial
```

## 6. Relationship fields — `MoonShine\Laravel\Fields\Relationships\*`

**These require Eloquent and live under the Laravel root.** All require the related `ModelResource` to be registered in `MoonShineServiceProvider`.

```php
use MoonShine\Laravel\Fields\Slug;                                  // special: Laravel-specific
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\BelongsToMany;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\Laravel\Fields\Relationships\HasOne;
use MoonShine\Laravel\Fields\Relationships\HasManyThrough;
use MoonShine\Laravel\Fields\Relationships\HasOneThrough;
use MoonShine\Laravel\Fields\Relationships\MorphTo;
use MoonShine\Laravel\Fields\Relationships\MorphOne;
use MoonShine\Laravel\Fields\Relationships\MorphMany;
use MoonShine\Laravel\Fields\Relationships\MorphToMany;
use MoonShine\Laravel\Fields\Relationships\RelationRepeater;
```

### BelongsTo (foreign key, e.g. `category_id`)

```php
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;

BelongsTo::make(
    __('admin.fields.category'),
    'category',                                                     // relation name on the model
    formatted: static fn ($model) => $model->name,                  // preview formatter
    resource: \App\MoonShine\Resources\Category\CategoryResource::class,
)
    ->badge(Color::PURPLE)
    ->sortable('category_id')
    ->creatable()                                                   // modal to create new related
    ->searchable()                                                  // tom-select search
    ->asyncSearch('name', limit: 15)                                // server-side search
    ->valuesQuery(static fn (Builder $q) => $q->select(['id', 'name']))
    ->nullable()
    ->placeholder('Choose category');
```

**Bronber-store convention:** always pass `resource:` explicitly and `formatted:` closure for clean previews.

### BelongsToMany (many-to-many, e.g. `tags`)

```php
BelongsToMany::make('Tags', 'tags', resource: TagResource::class)
    ->selectMode()                  // simple multi-select
    ->asyncSearch('name')
    ->creatable()
    ->reorderable();                // drag-to-reorder pivot
```

### HasMany (e.g. `Product hasMany Variants`) — shown as inline table on form/detail

```php
HasMany::make('Variants', 'variants', resource: VariantResource::class)
    ->creatable()
    ->relatedLink()                 // link to the related resource index
    ->async();                      // lazy-load the rows
```

### HasOne (e.g. `Product hasOne Meta`)

```php
HasOne::make('Meta', 'meta', resource: ProductMetaResource::class);
```

### MorphTo / MorphOne / MorphMany / MorphToMany

```php
MorphTo::make('Owner', 'owner')->types([
    UserResource::class,
    OrganizationResource::class,
]);

MorphMany::make('Comments', 'comments', resource: CommentResource::class);
MorphOne::make('Image', 'image', resource: ImageResource::class);
MorphToMany::make('Tags', 'tags', resource: TagResource::class);
```

### Slug (Laravel-specific)

```php
use MoonShine\Laravel\Fields\Slug;

Slug::make('Slug', 'slug')
    ->from('title')                 // auto-generate from another field
    ->unique();
```

### RelationRepeater (repeater bound to a relation)

```php
RelationRepeater::make('Attributes', 'attributes', resource: AttributeResource::class)
    ->fields([
        Text::make('Key'),
        Text::make('Value'),
    ]);
```

---

## Common chain methods (apply to most fields)

```php
->required([Closure|bool|null $condition = null])
->disabled([Closure|bool|null $condition = null])
->readonly([Closure|bool|null $condition = null])
->nullable([Closure|bool|null $condition = null])
->default(mixed $value)
->hint(string $hint)
->badge(string|Color|Closure|null $color = null, string|Closure|null $icon = null)
->link(string|Closure $url, string|Closure $name = '', ?string $icon = null, bool $blank = false)
->sortable([Closure|string|null $callback = null])           // callback for custom sort
->searchable()
->placeholder(string $value)
->customAttributes(array $attributes, bool $override = false)
->wrapperClass(string|array $classes)
->wrapperStyle(string|array $styles)
->customWrapperAttributes(array $attributes)
->setNameAttribute(string $name)
->translatable([string $key = ''])
->insideLabel() | beforeLabel()
->horizontal()
->withoutWrapper([mixed $condition = null])
->canSee(Closure $callback)                                  // conditional display
->defaultMode() | previewMode() | rawMode()                  // force visual state
```

## Field lifecycle hooks (override save/fill/export)

```php
->onApply(Closure $cb)            // fn(Model $item, $value, Field $ctx): Model
->onBeforeApply(Closure $cb)
->onAfterApply(Closure $cb)
->changePreview(Closure $cb)      // fn($value, Field $ctx): mixed
->changeFill(Closure $cb)         // fn(Model $item, Field $ctx): mixed
->changeRender(Closure $cb)       // fn($value, Field $ctx): mixed
->modifyRawValue(Closure $cb)     // fn(mixed $raw, Model $item, Field $ctx): mixed
->fromRaw(Closure $cb)            // import: fn(mixed $raw): mixed
```

## Export / import helpers

```php
->showOnExport()                  // include in export handler
->hideOnExport()
```

(Export/import itself is configured on the resource via `indexExportHandler` / `importHandler` — see MoonShine `import-export` docs.)

## Colors enum (used by `->badge()`)

```php
use MoonShine\Support\Enums\Color;
// primary, secondary, success, warning, error, info,
// purple, pink, blue, green, yellow, red, gray
Text::make('Status')->badge(Color::SUCCESS, 'check');
```

## Field modes recap

| Method | Always renders as |
|--------|-------------------|
| `defaultMode()` | HTML form input |
| `previewMode()` | Formatted preview value |
| `rawMode()` | Original raw value (good for export) |

## Custom field

```bash
php artisan moonshine:field PriceRange
# creates app/MoonShine/Fields/PriceRange.php + resources/views/admin/fields/price-range.blade.php
```

Extends `MoonShine\UI\Fields\Field` (or any existing field class) and overrides `prepareRequest()` / view path as needed.
