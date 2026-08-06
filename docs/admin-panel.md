[← Разработка](development.md) · [Back to README](../README.md) · [Фронтенд →](frontend.md)

# Админ-панель (MoonShine v4)

Проект использует **MoonShine 4.15** как админ-панель. Дополнительно установлены:
`povly/moonshine-image-editor` (редактирование изображений) и `yurizoom/moonshine-media-manager`
(управление медиафайлами).

## Текущее состояние

Админка заскаффолжена и доступна по адресу `/admin`. Зарегистрированы только фреймворковые ресурсы:

- `MoonShineUserResource` — администраторы MoonShine
- `MoonShineUserRoleResource` — роли администраторов

> Доменные ресурсы (`ProductResource`, `OrderResource` и т.д.) **предстоит создать** вместе
> с доменными моделями в следующих фазах разработки.

## Создание администратора

```bash
php artisan moonshine:user
```

Команда запросит email, имя и пароль.

## Namespace split (важно!)

MoonShine v4 разделил неймспейсы между пакетами — не путать с v3:

| Компонент | Правильный namespace |
|-----------|---------------------|
| Базовый класс ресурсов | `MoonShine\Laravel\Resources\ModelResource` |
| Поля | `MoonShine\UI\Fields\*` (`Text`, `Image`, `Number`, `BelongsTo`, ...) |
| Атрибуты (Icon, Group, Order) | `MoonShine\MenuManager\Attributes\*`, `MoonShine\Support\Attributes\*` |
| Enums (Action) | `MoonShine\Support\Enums\Action` |
| ListOf | `MoonShine\Support\ListOf` |

> ⚠️ Для Eloquent-ресурсов используйте `ModelResource`, **НЕ** `MoonShine\UI\Resources\CrudResource`.

## Структура ресурсов

Каждый ресурс живёт в своём подкаталоге `app/MoonShine/Resources/{Resource}/` с отдельной папкой `Pages/`:

```
app/MoonShine/Resources/
├── MoonShineUser/
│   ├── MoonShineUserResource.php       # extends ModelResource
│   └── Pages/
│       ├── MoonShineUserIndexPage.php  # список
│       └── MoonShineUserFormPage.php   # создание/редактирование
└── MoonShineUserRole/
    ├── MoonShineUserRoleResource.php
    └── Pages/
        ├── MoonShineUserRoleIndexPage.php
        └── MoonShineUserRoleFormPage.php
```

Эталон реализации — `MoonShineUserResource`. Изучите его перед созданием доменных ресурсов.

## Анатомия ресурса

```php
<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\MoonShineUser;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\MenuManager\Attributes\Group;
use MoonShine\MenuManager\Attributes\Order;
use MoonShine\Support\Attributes\Icon;
use MoonShine\Support\Enums\Action;
use MoonShine\Support\ListOf;

#[Icon('users')]
#[Group('moonshine::ui.resource.system', 'users', translatable: true)]
#[Order(0)]
class MoonShineUserResource extends ModelResource
{
    protected string $model = MoonshineUser::class;
    protected string $column = 'name';
    protected array $with = ['moonshineUserRole'];
    protected bool $simplePaginate = true;

    public function getTitle(): string
    {
        return __('moonshine::ui.resource.admins_title');
    }

    protected function activeActions(): ListOf
    {
        return parent::activeActions()->except(Action::VIEW);
    }

    protected function pages(): array
    {
        return [
            MoonShineUserIndexPage::class,
            MoonShineUserFormPage::class,
        ];
    }

    protected function search(): array
    {
        return ['id', 'name'];
    }
}
```

## Создание нового ресурса

```bash
php artisan moonshine:resource Product
```

Генерирует ресурс в `app/MoonShine/Resources/Product/ProductResource.php`. Для тестирования:

```bash
php artisan moonshine:resource Product --pest
```

После генерации — **зарегистрируйте** ресурс в `app/Providers/MoonShineServiceProvider.php`:

```php
public function boot(CoreContract $core): void
{
    $core
        ->resources([
            MoonShineUserResource::class,
            MoonShineUserRoleResource::class,
            ProductResource::class,       // ← добавить сюда
        ])
        ->pages([
            ...$core->getConfig()->getPages(),
        ]);
}
```

## Связь с архитектурой

MoonShine-ресурсы — это слой **админ-презентации**, отдельный от публичной витрины. Они напрямую
используют Eloquent-модели, но **не вызывают** Services публичной части:

```
Public:  Controller → Service → Model
Admin:   MoonShine Resource → Model (напрямую, без Service)
```

Это допустимо: админка и витрина — разные delivery-mechanisms для одних и тех же моделей.
Критичные бизнес-правила инкапсулированы в Model и срабатывают в обоих случаях.

> Подробнее — в [архитектурной документации](architecture.md#4-moonshine--отдельный-delivery-mechanism)
> и [`.ai-factory/ARCHITECTURE.md`](../.ai-factory/ARCHITECTURE.md).

## Конфигурация

Основные конфиги:

| Файл | Назначение |
|------|------------|
| `config/moonshine.php` | Конфигурация админ-панели (палитра PurplePalette) |
| `app/Providers/MoonShineServiceProvider.php` | Регистрация ресурсов и страниц |
| `app/MoonShine/Layouts/MoonShineLayout.php` | Layout админки (меню, сайдбар) |
| `app/MoonShine/Pages/Dashboard.php` | Главная страница админки |

## See Also

- [Архитектура](architecture.md) — как MoonShine вписывается в общую структуру
- [Разработка](development.md) — Pint после изменений PHP, конвенции кода
- [`.ai-factory/rules/base.md`](../.ai-factory/rules/base.md) — секция «MoonShine v4»
