# Implementation Plan: FAQ — настраиваемые блоки страницы через админку

Branch: none
Created: 2026-09-13

## Original Request

http://bronber_store.test/faq теперь тут блоки настраиваем

## Settings

- Testing: yes
- Logging: verbose
- Docs: yes  # обязательный docs-чекпоинт в /aif-implement через /aif-docs

## Контекст и решение (из разведки кодовой базы)

Страница `/faq` сейчас — статический прототип: `routes/web.php:18` (closure → `view('faq')`) → `resources/views/faq.blade.php` → `resources/views/blocks/faq/faq.blade.php` с захардкоженным массивом `$items` (6 пар вопрос/ответ), Alpine-аккордеоном (`resources/js/blocks/faq/index.js`, `x-data="faq()"`), breadcrumbs и h1 захардкожены.

В проекте уже есть устоявшийся паттерн «настраиваемые блоки страницы» — так сделана главная:

- DB-страница (`pages` + `page_translations`, content — flexible-layouts JSON с `_type`), рендер `BlockRenderer` (context `page`) → вью `resources/views/components/page-blocks/page/{type}.blade.php`.
- Блоки — по одному классу на тип в `app/Support/PageBlocks/Blocks/{Page}/`, регистрация в `PageBlockLibrary::blocks()`, имена типов с префиксом страницы (`home-*`; по докблоку библиотеки следующие — `category-*`, `contact-*`, …). Legacy-типы (`faq`, `hero`, …) не регистрируются, их вью остаются для старого контента.
- **Маршрут с fallback**: главная — фиксированный роут `''` → `PageController::index` ищет опубликованную DB-страницу slug `index`; нет страницы/перевода/таблицы → статический прототип `home`. Тот же подход применяем к FAQ (а не удаление роута под catch-all, который отдаст 404 без сидера).
- Списки в блоках — вложенный `FlexibleLayouts` (паттерн `HomeAdvsBlock`); изображения — `MediaManagerPicker` (паттерн `HomePartnersBlock`/`HomeAdvsBlock`). В прототипе FAQ изображений нет → в блоке `faq-items` полей-изображений нет; если появятся позже — только через `MediaManagerPicker`.
- Демо-контент — идемпотентный сидер по образцу `HomePageSeeder` (`firstOrCreate`, ru+en, `Log::info`).
- Тесты — Pest-конвенция `tests/Feature/PageRenderingTest.php` (RefreshDatabase, `Language::factory()` в beforeEach, `PageTranslationFactory` со стейтами `ru()`/`en()`/`withBlocks()`).

**Решение:** новый блок `faq-items` (заголовок + вложенный список вопрос/ответ), роут `/faq` → `PageController::faq()` с fallback на статический прототип, идемпотентный `FaqPageSeeder` с текущим контентом прототипа. Имя роута `faq` сохраняется (на него ссылаются `top-bar`, `footer`, `mobile-menu` через `route('faq')`; ссылки настроек шапки/подвала используют custom URL `/faq`).

Ограничения: `declare(strict_types=1)`, PHP 8.5, namespace split MoonShine v4, `vendor/bin/pint --dirty --format agent` после каждого PHP-изменения, никакого рефакторинга вне зоны задачи, legacy-вью `components/page-blocks/page/faq.blade.php` и статический прототип `blocks/faq/*` не трогаем (fallback + старый контент).

## Commit Plan

- **Commit 1** (после задач 1–4): `feat(content): FAQ page as admin-configurable flexible-layout blocks`
- **Commit 2** (после задач 5–6): `test(content): FAQ page rendering tests + docs checkpoint`

## Tasks

### Phase 1: Блок faq-items (пикер MoonShine)

- [x] Task 1: Класс блока `FaqItemsBlock` + регистрация в `PageBlockLibrary`
  - Создать `app/Support/PageBlocks/Blocks/Faq/FaqItemsBlock.php` (final class, implements `PageBlock`, `declare(strict_types=1)`) по образцу `HomeHeroBlock` (limit: 1, category) и `HomeAdvsBlock` (вложенный Flexible):
    - `$layouts->block('faq-items', 'FAQ — вопросы и ответы', [...], limit: 1, category: 'FAQ', description: 'Аккордеон вопрос-ответ с заголовком (прототип /faq)', icon: 'question-mark-circle')`
    - Поля: `Text::make('Заголовок', 'title')`; вложенный `FlexibleLayouts::make('Вопросы', 'items')->block('item', 'Вопрос', [Text::make('Вопрос', 'question'), Textarea::make('Ответ', 'answer')])`
    - Изображений в прототипе нет → MediaManagerPicker не добавляется (пользовательское правило: если появятся — только через `MediaManagerPicker`)
  - Зарегистрировать в `app/Support/PageBlocks/PageBlockLibrary::blocks()` (добавить `FaqItemsBlock::class` после Home-блоков)
  - Логирование: не требуется (декларативное определение полей); рантайм-логи уже в `BlockRenderer` (unknown/failed block → WARN)
  - Verify: `vendor/bin/pint --dirty --format agent`; `php artisan test --compact tests/Feature/BlockRendererTest.php` (не сломан)

- [ ] Task 2: Blade-вью блока `faq-items` (depends on 1)
  - Создать `resources/views/components/page-blocks/page/faq-items.blade.php` — перенос разметки прототипа `resources/views/blocks/faq/faq.blade.php`:
    - `@push('block-styles')` + `@once` + `@vite(['resources/css/blocks/faq/style.css'])` — переиспользуем существующий CSS прототипа, классы `faq__*` сохраняются (нулевая визуальная регрессия; паттерн `@once` — как в `home-partners.blade.php`)
    - Breadcrumbs: `<x-breadcrumbs>` с `[['label' => 'Главная', 'url' => route('home')], ['label' => 'FAQ']]` — как в прототипе
    - h1: `$block['title'] ?? 'Часто задаваемые вопросы'`
    - Аккордеон: `@foreach ($block['items'] ?? [] ...)` разметка `faq__item/faq__question/faq__answer` из прототипа, Alpine inline `x-data="{ open: 0 }"` и toggle-выражения инлайн (без зависимости от `js/blocks/faq/index.js`, как в legacy `pb-faq`-вью; первый пункт открыт — поведение прототипа `open: 0`)
    - `{!! nl2br(e($item['answer'] ?? '')) !!}` — экранирование с переносами, как в прототипе
  - Статический прототип `resources/views/blocks/faq/*` и legacy `page/faq.blade.php` НЕ трогать
  - Логирование: не требуется (вью); битые блоки логирует `BlockRenderer`
  - Verify: ручной smoke через tinker не нужен — покрыто тестом Task 5

### Phase 2: Роут и контроллер (fallback-паттерн главной)

- [x] Task 3: `PageController::faq()` + роут `/faq` (depends on 1, 2)
  - `routes/web.php`: заменить `Route::get('/faq', fn () => view('faq'))->name('faq')` на `Route::get('/faq', [PageController::class, 'faq'])->name('faq')` — имя `faq` обязательно сохранить (`top-bar`, `footer`, `mobile-menu`)
  - `app/Http/Controllers/PageController.php`:
    - Отрефакторить без изменения поведения: общая логика `index()` (поиск published-страницы по slug → translation → renderPage | fallback-вью, QueryException → fallback) выносится в приватный хелпер, например `renderSlugOrFallback(string $slug, string $fallbackView): Response`; `index()` вызывает его с (`index`, `home`)
    - Добавить `private const FAQ_SLUG = 'faq'` и метод `faq(): Response` → хелпер с (`faq`, `faq`)
    - Логирование (verbose, паттерн `index()`): DEBUG `[PageController.faq] locale={locale} source=db|fallback` в обеих ветках; WARN не добавлять — 404 для FAQ невозможен (fallback), QueryException уже покрыт fallback-веткой
  - После изменения роутов: если включён route/config cache — `php artisan optimize:clear` (замечание из `routes/web.php`)
  - Verify: `vendor/bin/pint --dirty --format agent`; `php artisan test --compact tests/Feature/PageRenderingTest.php` (не сломан fallback `/`)

### Phase 3: Демо-контент (идемпотентный сидер)

- [x] Task 4: `FaqPageSeeder` + регистрация в `DatabaseSeeder` (depends on 1)
  - Создать `database/seeders/FaqPageSeeder.php` зеркально `HomePageSeeder`:
    - `Page::firstOrCreate(['slug' => 'faq'], ['is_published' => true, 'sort_order' => 0])`
    - `PageTranslation::firstOrCreate(['page_id', 'locale'])` для `ru` и `en`: title («Часто задаваемые вопросы» / «Frequently Asked Questions»), meta_title/meta_description per locale, content = `[['_type' => 'faq-items', 'title' => …, 'items' => [['_type' => 'item', 'question' => …, 'answer' => …], …]]]` — 6 вопросов из текущего прототипа `blocks/faq/faq.blade.php` (ru дословно, en — переведённые)
    - `Log::info('[FaqPageSeeder] faq page seeded, locales={locales}', …)` — как в HomePageSeeder
  - `database/seeders/DatabaseSeeder.php`: `$this->call(FaqPageSeeder::class);` после `HomePageSeeder`
  - Логирование: INFO о созданных/существующих локалях (паттерн HomePageSeeder)
  - Verify: pint; `php artisan db:seed --class=FaqPageSeeder` на dev-окружении дважды (идемпотентность)

<!-- Commit checkpoint: задачи 1–4 → Commit 1 -->

### Phase 4: Тесты (Pest)

- [x] Task 5: Feature-тест `tests/Feature/FaqPageTest.php` (depends on 3, 4)
  - Создать через `php artisan make:test --pest FaqPageTest` с конвенциями `PageRenderingTest` (RefreshDatabase, `resolve(LanguageService::class)->clearCache()` + Language factories в beforeEach, `Page::factory()->has(PageTranslation::factory()->ru()->withBlocks(...))`):
    1. DB-рендер: опубликованная страница slug `faq` с ru-переводом (`faq-items`: title + 2 вопроса) → `GET /faq` → assertOk, assertSee вопроса/ответа, классов `faq__title`/`faq__question`, заголовка блока
    2. Fallback без страницы: чистая БД → `GET /faq` → assertOk + assertSee прототипной разметки (статическое «Как оформить заказ?» из вью-прототипа)
    3. Fallback для черновика: `Page::factory()->draft()` slug `faq` → статический прототип
    4. EN-локаль: `/en/faq` → en-перевод блока (переведённые вопрос/ответ)
    5. SEO из перевода: `<title>` = meta_title, meta description (паттерн SEO-теста из PageRenderingTest)
  - Логирование: в тестах не нужно; проверяем рендер-ветки, логи уже в контроллере
  - Verify: `php artisan test --compact tests/Feature/FaqPageTest.php`; затем весь смежный набор: `php artisan test --compact tests/Feature/PageRenderingTest.php tests/Feature/BlockRendererTest.php tests/Feature/PageTranslationFallbackTest.php`

### Phase 5: Финализация

- [x] Task 6: Pint, полный смежный прогон, docs-чекпоинт (depends on 5)
  - `vendor/bin/pint --dirty --format agent` по всем изменённым PHP-файлам
  - Прогон: `php artisan test --compact tests/Feature` (смежные файлы); полный suite предложить пользователю (`php artisan test --compact`)
  - Docs-чекпоинт (Docs: yes → обязательный, через /aif-docs): зафиксировать конвенцию «префиксные блоки страницы» на примере `faq-items` — README ссылается на `docs/*.md`, которых нет на диске; актуальные правки определить на чекпоинте (как минимум упомянуть блок `faq-items` в описании Content-модуля в `.ai-factory/DESCRIPTION.md` → раздел «Реализованный модуль Content»)
  - Финальная ручная проверка: `php artisan db:seed --class=FaqPageSeeder` → `http://bronber_store.test/faq` рендерит блоки из БД; без сидера — статический прототип

<!-- Commit checkpoint: задачи 5–6 → Commit 2 -->

## Ссылки на эталонные файлы (для имплементатора)

| Что | Файл-образец |
|---|---|
| Блок с вложенным списком | `app/Support/PageBlocks/Blocks/Home/HomeAdvsBlock.php` |
| Блок limit:1 + category | `app/Support/PageBlocks/Blocks/Home/HomeHeroBlock.php` |
| MediaManagerPicker (если появятся изображения) | `app/Support/PageBlocks/Blocks/Home/HomePartnersBlock.php` |
| Вью блока (@once + @vite, разметка прототипа) | `resources/views/components/page-blocks/page/home-partners.blade.php` |
| Fallback-контроллер + SEO | `app/Http/Controllers/PageController.php` (`index()`) |
| Идемпотентный сидер | `database/seeders/HomePageSeeder.php` |
| Тест-конвенции | `tests/Feature/PageRenderingTest.php` |
| Фабрика переводов | `database/factories/PageTranslationFactory.php` (`ru()`, `en()`, `withBlocks()`) |
