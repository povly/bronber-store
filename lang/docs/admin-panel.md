[← Разработка](development.md) · [Back to README](../README.md) · [Фронтенд →](frontend.md)

# Админ-панель (MoonShine v4)

Проект использует **MoonShine 4.15** как админ-панель. Дополнительно установлены:
`povly/moonshine-flexible-layouts` (блоки контента), `sckatik/moonshine-editorjs`
(редактор текста), `povly/moonshine-image-editor` (редактирование изображений)
и `yurizoom/moonshine-media-manager` (управление медиафайлами).

## Текущее состояние

Админка доступна по адресу `/admin`. Зарегистрированы ресурсы:

- `MoonShineUserResource` / `MoonShineUserRoleResource` — администраторы и роли
- `PageResource` — **страницы сайта** с переводами, блоками и SEO
- `LanguageResource` — **активные языки** (редактируются в админке)
- `SettingResource` — **шапка/подвал** с переводами (блоки)
- `PageTranslationResource` — технический ресурс для HasMany переводов (в меню скрыт)

> Доменные ресурсы каталога (`ProductResource`, `OrderResource`, ...) появятся вместе
> с CRM-складом на поддомене.

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

## Контентные ресурсы

### Страницы (PageResource)

CRUD страниц сайта: slug (kebab-case, уникальный), сортировка, публикация и **переводы** —
HasMany на ресурс `PageTranslationResource` (таблица + модальная форма редактирования).

- При сохранении страницы **автоматически создаются переводы** для всех активных языков
  (хук `afterSave`); новые языки, добавленные позже, добавляются при следующем сохранении.
- Форма перевода (модалка): заголовок, полный набор SEO-полей (meta title/description/keywords,
  robots, canonical URL, OG-изображение через media-manager) и **контент** — блоки
  `FlexibleLayouts` из `PageBlockLibrary::page()` (блоки главной `home-*`, см. ниже).
- Форма страницы разбита на табы «Страница» / «Переводы».

**Главная страница = страница со slug `index`** (сидируется `HomePageSeeder`): роут `/`
рендерит её блоки; если страница/перевод отсутствуют — витрина падает обратно на статический
прототип `home.blade.php` (паттерн шапки/подвала). `/index` и `/en/index` — 301-редирект на
`/` и `/en`; canonical/hreflang главной указывают на корень, а не на `/index`.

> ⚠️ Не вкладывайте `FlexibleLayouts` в inline-поля `HasMany` (`->fields([...])`) —
> возникает бесконечная рекурсия генерации имён полей (OOM). Поле должно жить на верхнем
> уровне формы связанного ресурса (как в `PageTranslationFormPage`) — проверено тестом
> `edit page renders for a page with translations without running out of memory`.

### Языки (LanguageResource)

Управление активными языками: код (двухбуквенный, ключ связей — после создания не меняется),
название, сортировка, флаг «По умолчанию».

- Система **всегда хранит ровно один дефолтный язык**: переключение флага понижает прежний
  дефолт, снятие флага с последнего дефолта молча откатывается.
- Сохранение языка сбрасывает кэш `LanguageService` — middleware, роуты и fallback
  подхватывают изменения сразу.
- ⚠️ При включённом `route:cache` список локалей в роутах заморожен — после добавления
  языка выполните `php artisan optimize:clear`.

### Настройки шапки/подвала (SettingResource)

Записи `key × locale` (сидируются `SettingsSeeder` контентом прототипа, создание отключено —
только редактирование). Ключ и локаль залочены (`readonly` + `canApply(false)` — не меняются
и при подмене запроса). Значение — блоки `FlexibleLayouts`, конфигурация по ключу:
`header` → `HeaderBlockLibrary::header()` (top-bar: телефон + сервисные ссылки; nav: основное
меню), `footer` → `FooterBlockLibrary::footer()` (contacts, socials, links-column ×3, bottom).

**Ссылки двух типов** (переиспользуемый trait `BuildsLinkFields`): у каждой ссылки `label`,
`type` («Страница сайта» | «Кастомная ссылка»), `page` (Select только из опубликованных
страниц — `PageOptions::published()`) и `url`. Page-ссылка автоматически локализуется
(`/{slug}` ↔ `/{locale}/{slug}`), custom-URL выводится как есть. Битые ссылки (страница
удалена/снята с публикации, пустой URL) пропускаются с WARN — меню не ломается.

**SOLID-структура библиотек блоков** — по классу на контекст вместо одного файла:

```
app/Support/PageBlocks/
├── PageBlockLibrary.php        # агрегатор page-блоков (список классов Blocks/*)
├── Blocks/
│   ├── PageBlock.php           # интерфейс: static register(FlexibleLayouts)
│   └── Home/                   # блоки главной (типы home-*)
│       ├── HomeHeroBlock.php         # home-hero — промо-слайдер
│       ├── HomeCategoriesBlock.php   # home-categories — динамическая сетка категорий
│       ├── HomeAdvsBlock.php         # home-advs — преимущества
│       ├── HomeProductsBlock.php     # home-products — слайдер товаров (динамический)
│       ├── HomePartnersBlock.php     # home-partners — логотипы партнёров
│       └── HomeNewsBlock.php         # home-news — карточки новостей
├── HeaderBlockLibrary.php      # блоки шапки (top-bar, nav)
├── FooterBlockLibrary.php      # блоки подвала (contacts, socials, links-column, bottom)
├── Concerns/BuildsLinkFields.php  # переиспользуемые поля двухтипной ссылки
├── LinkResolver.php            # JSON-ссылка → href (page|custom, локаль)
├── SettingsResolver.php        # settings JSON → структуры для вьюх
├── PageOptions.php             # опции опубликованных страниц (кэш per-request)
└── BlockRenderer.php           # рендер страничных блоков
```

### Блоки flexible-layouts

Определения блоков живут **по одному классу на тип** в `App\Support\PageBlocks\Blocks\`
(интерфейс `PageBlock::register(FlexibleLayouts)`), `PageBlockLibrary::page()` — тонкий
агрегатор по списку классов: новый блок = новый класс + строка в списке.

- **Именование с префиксом страницы**: тип блока зеркалит прототип-паршл —
  `home-hero` ↔ `blocks/home/hero.blade.php`. Блоки будущих страниц следуют правилу:
  `category-*`, `contact-*` и т.д. — так типы разных страниц не конфликтуют.
- **Повторяющиеся структуры** (слайды, пункты, новости) — вложенные `FlexibleLayouts`
  из `povly/moonshine-flexible-layouts`, **не** `Json`-поля: единый UX добавления,
  drag-n-drop и формат данных `[{_type: 'item', ...}]`.
- Блоки объявляются через `->block($name, $title, $fields, limit:, category:, icon:)`,
  хранятся в JSON как `[{_type: 'home-hero', ...}]`. Изображения — `MediaManagerPicker`
  (public-диск). Витрина рендерит их через `BlockRenderer` (см. [архитектуру](architecture.md)).
- **Динамические блоки** (`home-categories`, `home-products`) не хранят данные в JSON —
  тянут их из `CatalogMock` (позже CRM-склад); в админке редактируется только заголовок
  и количество.
- Старые универсальные блоки (`hero`, `text`, `gallery`, `faq`, `contacts`,
  `featured-products`) **удалены из пикера** — их pb-вьюхи остаются, `BlockRenderer`
  всё ещё рендерит их для ранее сохранённого/demo-контента.
- Состав главной для свежих установок задаёт `HomePageSeeder` (страница `index`,
  ru+en, зеркалит статический прототип).

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
