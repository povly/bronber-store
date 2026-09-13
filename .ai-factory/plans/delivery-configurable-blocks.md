# Implementation Plan: Доставка и оплата — настраиваемые блоки страницы через админку

Branch: none
Created: 2026-09-13

## Original Request

http://bronber_store.test/delivery теперь 
теперь тут блоки
опишу так, там заголовок, потос список(заголовок, иконка и описание), потом пзаголовок 2 и список контактов (иконка с текстом и типа ссылок)

## Settings

- Testing: yes
- Logging: verbose
- Docs: yes  # обязательный docs-чекпоинт в /aif-implement через /aif-docs

## Контекст и решение (из разведки кодовой базы)

Страница `/delivery` сейчас — статический прототип: `routes/web.php:118` (closure → `view('delivery')`) → `resources/views/delivery.blade.php` → `resources/views/blocks/delivery/delivery.blade.php`: h1 `delivery.title`, 4 карточки способов оплаты (inline-SVG иконки по индексу `$key == 0..3`, заголовок + описание из lang-ключей), h2 `delivery.contact_title`, 2 контакта (tel:/mailto: с inline-SVG иконками). Локали — через lang-файлы `lang/{ru,en}/delivery.php`.

Паттерн «настраиваемые блоки страницы» отработан на главной и FAQ (план `faq-configurable-blocks.md`, реализован в этой же ветке):

- DB-страница (`pages` + `page_translations`, content — flexible-layouts JSON с `_type`), рендер `BlockRenderer` (context `page`) → вью `resources/views/components/page-blocks/page/{type}.blade.php`.
- Блоки — по одному классу на тип в `app/Support/PageBlocks/Blocks/{Page}/`, регистрация в `PageBlockLibrary::blocks()`. По докблоку библиотеки имена типов — с префиксом страницы: `home-*`, `faq-*`, далее `category-*`, `contact-*`, … Для доставки: `delivery-*` и `contact-*`.
- **Маршрут с fallback**: `PageController::renderSlugOrFallback(slug, fallbackView)` — опубликованная DB-страница рендерит блоки; нет страницы/перевода/таблицы → статический прототип. Паттерн `faq()` копируется один-в-один.
- **Breadcrumbs** теперь автоматические на уровне страницы (`PageBreadcrumbs::forPage()` в `renderPage()`, вью `page.blade.php`) — в блоках НЕ хардкодить крошки (в отличие от ранней версии FAQ-плана).
- Иконки/изображения — `MediaManagerPicker` (паттерн `HomeAdvsBlock`); ключ `icon` уже входит в `MediaFallback::SINGLE_MEDIA_KEYS` — пустые иконки не-дефолтной локали наследуются из ru автоматически на рендере. Сидер ссылается на публичные файлы путём (`/images/home/advs/1.svg` — паттерн `HomePageSeeder`).
- Демо-контент — идемпотентный сидер fill-when-empty по образцу `FaqPageSeeder` (`firstOrCreate`; существующий перевод с ПУСТЫМ content заполняется, авторский — не трогается; правило `.ai/rules/seeders.md`).

**Решение:** два новых блока под структуру пользователя —
1. `delivery-methods` — заголовок + список карточек (заголовок, иконка, описание);
2. `contact-list` — заголовок + список контактов (текст, ссылка tel:/mailto:/URL, иконка).

Роут `/delivery` → `PageController::delivery()` с fallback на прототип; идемпотентный `DeliveryPageSeeder` с текущим контентом прототипа. Имя роута `delivery` сохранить (на него ссылается `top-bar`/`footer`/настройки). Ссылка контакта — простое текстовое поле `href` (tel:/mailto:/https:), НЕ `BuildsLinkFields` (тот паттерн — для навигационных ссылок на страницы сайта с label; здесь текст и есть label, а href — внешний протокол).

Ограничения: `declare(strict_types=1)`, PHP 8.5, namespace split MoonShine v4, `php vendor/bin/pint --dirty --format agent` после каждого PHP-изменения, никакого рефакторинга вне зоны задачи. Статический прототип `blocks/delivery/*` и lang-файлы не трогаем (fallback). CSS не меняется: блок-вью переиспользуют существующие классы `delivery__*` и `resources/css/blocks/delivery/style.css` (уже в Vite-инпутах, npm build не нужен). Legacy-вью `page-blocks/page/contacts.blade.php` не трогать (старый контент).

## Commit Plan

- **Commit 1** (после задач 1–5): `feat(content): delivery page as admin-configurable flexible-layout blocks`
- **Commit 2** (после задач 6–7): `test(content): delivery page rendering tests + docs checkpoint`

## Tasks

### Phase 1: Ассеты и блоки (пикер MoonShine)

- [x] Task 1: SVG-иконки в `public/images/delivery/` (нет зависимостей)
  - Извлечь 6 inline-SVG из `resources/views/blocks/delivery/delivery.blade.php` в файлы (размеры/paths 1:1, имена по смыслу):
    - `$key == 0..3` (карточки оплаты) → `public/images/delivery/{cash,card,sbp,currency}.svg` (stroke="white" — оставить как есть, иконка на градиентном круге `.delivery__icon`)
    - телефон и почта (контакты) → `public/images/delivery/{phone,email}.svg` — в прототипе `stroke="currentColor"`; в `<img>` наследование цвета не работает → заменить `currentColor` на фактический цвет из css-правил `.delivery__contact` / `.delivery__contact-icon` в `resources/css/blocks/delivery/style.css` (проверить вычисленное значение, обычно переменная цвета ссылок)
  - Проверить, что каждая svg открывается и визуально совпадает с прототипом (размером управляет css/атрибуты вью)
  - Логирование: не требуется (статические ассеты)

- [x] Task 2: Классы блоков `DeliveryMethodsBlock` + `ContactListBlock` + регистрация (нет зависимостей)
  - `app/Support/PageBlocks/Blocks/Delivery/DeliveryMethodsBlock.php` (final, implements `PageBlock`, `declare(strict_types=1)`, образец `HomeAdvsBlock` + `FaqItemsBlock`):
    - `$layouts->block('delivery-methods', 'Доставка — способы оплаты и доставки', [...], limit: 1, category: 'Доставка', description: 'Заголовок + карточки: иконка, заголовок, описание (прототип /delivery)', icon: 'truck')`
    - Поля: `Text::make('Заголовок', 'title')`; вложенный `FlexibleLayouts::make('Карточки', 'items')->block('item', 'Карточка', [Text::make('Заголовок', 'title'), MediaManagerPicker::make('Иконка', 'icon')->allowedExtensions(['jpg','jpeg','png','webp','svg']), Textarea::make('Описание', 'text')])`
  - `app/Support/PageBlocks/Blocks/Contact/ContactListBlock.php`:
    - `$layouts->block('contact-list', 'Контакты — список', [...], limit: 1, category: 'Доставка', description: 'Заголовок + контакты: иконка, текст, ссылка (tel:/mailto:/URL)', icon: 'phone')`
    - Поля: `Text::make('Заголовок', 'title')`; вложенный `FlexibleLayouts::make('Контакты', 'items')->block('item', 'Контакт', [Text::make('Текст', 'text'), Text::make('Ссылка', 'href')->hint('Протокол: tel:, mailto: или https://; пусто — без ссылки'), MediaManagerPicker::make('Иконка', 'icon')->allowedExtensions(['jpg','jpeg','png','webp','svg'])])`
  - Зарегистрировать оба в `app/Support/PageBlocks/PageBlockLibrary::blocks()` после `FaqItemsBlock::class`
  - Логирование: не требуется (декларативные поля); рантайм-логи уже в `BlockRenderer` (unknown/failed block → WARN)
  - Verify: `php vendor/bin/pint --dirty --format agent`; `php artisan test --compact tests/Feature/BlockRendererTest.php` (не сломан)

### Phase 2: Blade-вью блоков

- [x] Task 3: Вью `delivery-methods.blade.php` + `contact-list.blade.php` (depends on 1, 2)
  - `resources/views/components/page-blocks/page/delivery-methods.blade.php` — разметка из прототипа `blocks/delivery/delivery.blade.php`:
    - `@push('block-styles')` + `@once` + `@vite(['resources/css/blocks/delivery/style.css'])` (паттерн `home-partners.blade.php`)
    - h1: `<h1 class="delivery__title section__title">{{ $block['title'] ?? '' }}</h1>`
    - Сетка: `@foreach ($block['items'] ?? [] as $item)` → `.delivery__methods` → `.delivery__method`: `.delivery__icon` с `<img src="{{ $item['icon'] }}" alt="" ...>` (размеры — из атрибутов извлечённых svg или css), `{!! nl2br(e($item['title'] ?? '')) !!}` в `h2.delivery__method-title`, `{{ $item['text'] ?? '' }}` в `p.delivery__method-text`
  - `resources/views/components/page-blocks/page/contact-list.blade.php`:
    - `@push('block-styles')` + `@once` + тот же `@vite(['resources/css/blocks/delivery/style.css'])`
    - h2: `$block['title']` → `h2.delivery__contact-title.section__title`
    - `.delivery__contacts` → `@foreach ($block['items'] ?? [])`: элемент — `<a href="{{ $item['href'] }}" class="delivery__contact">` когда href непуст, иначе `<div class="delivery__contact">`; внутри `.delivery__contact-icon` с `<img src="{{ $item['icon'] }}">` + `{{ $item['text'] }}`. tel:-номер НЕ дезинфицировать preg_replace как в прототипе — href заполняется админом осознанно (прототипная логика нормализации была нужна из-за форматирования lang-строки)
  - Breadcrumbs в вью НЕ добавлять — автоматические из `PageBreadcrumbs` (страница `delivery` без родителя → «Главная → Доставка и оплата»)
  - Логирование: не требуется (вью); битые блоки логирует `BlockRenderer`
  - Verify: покрыто тестом Task 6; ручной smoke после Task 5

### Phase 3: Роут и контроллер

- [x] Task 4: `PageController::delivery()` + роут `/delivery` (depends on 2, 3)
  - `routes/web.php`: заменить closure `Route::get('/delivery', fn () => view('delivery'))->name('delivery')` (строка ~118, ru-группа) на `Route::get('/delivery', [PageController::class, 'delivery'])->name('delivery')`; найти и аналогично обновить вариант в `/en`-группе (i18n-дублирование роутов — см. паттерн `/faq` в обеих группах). Имя `delivery` сохранить
  - `app/Http/Controllers/PageController.php`: добавить `private const DELIVERY_SLUG = 'delivery'` и метод `delivery(): Response` → `renderSlugOrFallback(self::DELIVERY_SLUG, 'delivery')` (докблок в стиле `faq()`). Хелпер `renderSlugOrFallback` уже существует — не дублировать
  - Логирование: уже в `renderSlugOrFallback` (DEBUG source=db|fallback) — новых логов не нужно
  - Verify: `php vendor/bin/pint --dirty --format agent`; `php artisan test --compact tests/Feature/PageRenderingTest.php` (не сломан fallback `/`)

### Phase 4: Демо-контент (идемпотентный сидер)

- [x] Task 5: `DeliveryPageSeeder` + регистрация в `DatabaseSeeder` (depends on 1, 2)
  - `database/seeders/DeliveryPageSeeder.php` зеркально `FaqPageSeeder` (fill-when-empty, `.ai/rules/seeders.md`):
    - `Page::firstOrCreate(['slug' => 'delivery'], ['is_published' => true, 'sort_order' => 0])`
    - `PageTranslation::firstOrCreate(['page_id', 'locale'])` для `ru` и `en`: title («Доставка и оплата» / из `lang/en/delivery.php`), meta_title/meta_description per locale, content:
      - `[['_type' => 'delivery-methods', 'title' => <delivery.title>, 'items' => [['_type' => 'item', 'title' => <method_cash_title>, 'icon' => '/images/delivery/cash.svg', 'text' => <method_cash_text>], … 4 шт]], ['_type' => 'contact-list', 'title' => <contact_title>, 'items' => [['_type' => 'item', 'text' => <contact_phone>, 'href' => 'tel:+7…', 'icon' => '/images/delivery/phone.svg'], ['_type' => 'item', 'text' => <contact_email>, 'href' => 'mailto:…', 'icon' => '/images/delivery/email.svg']]]]`
      - Тексты ru/en дословно из `lang/{ru,en}/delivery.php` (прототип), tel:-href — нормализованный номер
    - `Log::info('[DeliveryPageSeeder] delivery page seeded, locales={locales}', …)`
  - `database/seeders/DatabaseSeeder.php`: `$this->call(DeliveryPageSeeder::class);` после `FaqPageSeeder`
  - Логирование: INFO о созданных/заполненных/существующих локалях
  - Verify: pint; `php artisan db:seed --class=DeliveryPageSeeder` на dev-окружении дважды (идемпотентность); smoke `http://bronber_store.test/delivery` — рендер из БД

<!-- Commit checkpoint: задачи 1–5 → Commit 1 -->

### Phase 5: Тесты и финализация

- [x] Task 6: Feature-тест `tests/Feature/DeliveryPageTest.php` (depends on 4, 5)
  - Создать через `php artisan make:test --pest DeliveryPageTest` с конвенциями `FaqPageTest` (RefreshDatabase, `resolve(LanguageService::class)->clearCache()` + Language factories в beforeEach, фабрики Page/PageTranslation со стейтами `ru()`/`en()`):
    1. DB-рендер: опубликованная страница slug `delivery` (delivery-methods: title + 2 карточки; contact-list: title + телефон с tel:-href) → `GET /delivery` → assertOk, assertSee заголовков карточек, `delivery__method`, `href="tel:`
    2. Fallback без страницы: чистая БД → `GET /delivery` → assertOk + assertSee статического текста прототипа (из `blocks/delivery/delivery.blade.php`)
    3. Fallback для черновика: draft-страница slug `delivery` → статический прототип
    4. EN-локаль: `/en/delivery` → en-перевод блоков
    5. Breadcrumbs: «Главная» + заголовок страницы на `/delivery` (автоматические крошки), отсутствие на `/`
  - Логирование: в тестах не нужно
  - Verify: `php artisan test --compact tests/Feature/DeliveryPageTest.php`; смежные: `FaqPageTest`, `PageRenderingTest`, `BlockRendererTest`, `PageBreadcrumbsTest`

- [x] Task 7: Pint, полный прогон, docs-чекпоинт (depends on 6)
  - `php vendor/bin/pint --dirty --format agent` по всем изменённым PHP-файлам
  - Полный `php artisan test --compact` (все зелёные); предложить пользователю финальный прогон
  - Docs-чекпоинт (Docs: yes → обязательный, через /aif-docs): обновить перечни «управляемых из админки» страниц — README.md («контент ключевых страниц (главная, FAQ, доставка и оплата)») и `.ai-factory/DESCRIPTION.md` (раздел модуля Content: блоки `delivery-methods`, `contact-list`)
  - Финальный ручной smoke: `/delivery` из БД (карточки с иконками-файлами, контакты-ссылки), `/en/delivery`, мобильный брейкпоинт (375) — иконки-`<img>` не разъехались

<!-- Commit checkpoint: задачи 6–7 → Commit 2 -->

## Риски и нюансы

- **currentColor в `<img>`**: иконки телефона/почты в прототипе красятся через `currentColor` — при извлечении в файлы заменить на фактический цвет из CSS, иначе почернеют. Проверить визуально на smoke.
- **Размеры иконок**: извлечённые svg сохраняют свои width/height (53/37/34/35px у карточек) — вью рендерит `<img>` как есть; если css `.delivery__icon img` не задан, размеры уже в атрибутах svg — совпадает с прототипом.
- **Роуты в обеих локалях**: не пропустить `/en`-группу в `routes/web.php`.
- **tel:-нормализация**: в блоке href пишется админом/сидером уже готовым (`tel:+7…`) — вью не нормализует (убрать preg_replace из прототипной логики).

## Ссылки на эталонные файлы (для имплементатора)

| Что | Файл-образец |
|---|---|
| Блок с вложенным списком + MediaManagerPicker | `app/Support/PageBlocks/Blocks/Home/HomeAdvsBlock.php` |
| Блок limit:1 + category (страничный префикс) | `app/Support/PageBlocks/Blocks/Faq/FaqItemsBlock.php` |
| Вью блока (@once + @vite, разметка прототипа) | `resources/views/components/page-blocks/page/faq-items.blade.php`, `home-partners.blade.php` |
| Fallback-контроллер (`faq()` — копировать) | `app/Http/Controllers/PageController.php` |
| Идемпотентный сидер fill-when-empty | `database/seeders/FaqPageSeeder.php` |
| Сидер с путями публичных svg | `database/seeders/HomePageSeeder.php` |
| Тест-конвенции | `tests/Feature/FaqPageTest.php` |
| Источник разметки/текстов прототипа | `resources/views/blocks/delivery/delivery.blade.php`, `lang/{ru,en}/delivery.php` |
