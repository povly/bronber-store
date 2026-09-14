# Implementation Plan: Страница 404 — DB-блок в настройках

Branch: none (git.create_branches = false, работа на текущей ветке `main`)
Created: 2026-09-14

## Original Request

http://bronber_store.test/404 теперь этот новый блок!

## Settings

- Testing: yes
- Logging: verbose
- Docs: yes

## Текущее состояние

404 — не DB-страница: рендерится Laravel через `resources/views/errors/404.blade.php` (HTTP 404; locale сниффится из первого URL-сегмента, т.к. `SetLocale` middleware на несопоставленных роутах не выполняется). Контент — статический прототип:

- `resources/views/blocks/error-404/error-404.blade.php` — код «404» (статик), заголовок, описание (HTML с `<br>`), две кнопки `x-btn` (`variant="primary"` → главная, `variant="white-border"` → каталог)
- CSS: `resources/css/blocks/error-404/style.css` — не трогаем, новая вью подключает его же
- Тексты: `lang/{ru,en}/store.php` ключи `error_404_title` / `error_404_desc` / `error_404_home_btn` / `error_404_catalog_btn`

Поэтому паттерн конвертации — **settings** (как шапка/подвал/мобильные меню), а не DB-страница: таблица `settings` (key × locale, value = JSON flexible-блоки), редактирование через `SettingResource`, чтение `SettingService::get()` (со встроенным ru-fallback), пусто → статика прототипа.

**Важно:** slug-страницу `404` НЕ заводить — catch-all `/{slug}` начал бы отдавать `/404` с HTTP 200.

## Дизайн блока

Новый ключ настроек: **`error-404`**. Тип блока: **`error-404`** → `BlockRenderer` режет по первому тире: page=`error`, name=`404` → вью `resources/views/blocks/error/404.blade.php` (плоский файл; rule `.ai/rules/page-blocks.md`).

### Поля блока (категория «Служебное», icon пикера: `x-circle` — существует в vendor-наборе ✓, limit: 1)

| Поле | Тип | Notes |
|---|---|---|
| `title` | Text | h1; `escapeOnApply(false)` |
| `text` | Textarea | описание; HTML (`<br>`) допустим; `escapeOnApply(false)` |
| `buttons` | FlexibleLayouts «Кнопки» | элемент: `label` + `type` (page\|custom) + `page` (Select `PageOptions::published()`) + `url` — reuse `BuildsLinkFields::linkFields()`; плюс `Select` `variant` («Тип кнопки»): `primary` → «Основная», `white-border` → «С белой обводкой» |

Код «404» остаётся статикой во вью (это код ошибки, не контент). Медиа-полей нет → `MEDIA_SCHEMAS` / `MediaFallback` не затрагиваются.

### Рендер

`errors/404.blade.php` (после существующего locale-сниффа):

```blade
@php $errorBlocks = resolve(SettingService::class)->get('error-404'); @endphp
<main class="error-404-page">
    @if ($errorBlocks !== [])
        {!! resolve(BlockRenderer::class)->render($errorBlocks, 'error-404') !!}
    @else
        @include('blocks.error-404.error-404')  {{-- статика прототипа, fallback --}}
    @endif
</main>
```

- Статический include-прототип **сохраняется** как fallback (паттерн шапки/подвала; в отличие от page-конверсий не удаляется)
- Новая вью `blocks/error/404.blade.php`: разметка прототипа, `$block`-данные, кнопки циклом — `x-btn :href` через `LinkResolver::href($button)` (null → кнопка пропускается), `variant` из поля, escape: title `{{ }}`, text `{!! !!}`
- «Другой язык берёт от основного»: `SettingService::get` сам делает fallback locale → дефолтный язык

## Commit Plan

- **Commit 1** (после задач 1–3): `feat(content): error 404 block in settings with static fallback`
- **Commit 2** (после задач 4–6): `test(content): cover error 404 settings rendering and fallback`

## Tasks

### Phase 1: Блок и админка

- [x] Task 1: Класс блока + библиотека + регистрация в админке (depends: —)
  - `app/Support/PageBlocks/Blocks/Error/Error404Block.php` (implements `PageBlock`, трейт `BuildsLinkFields`): title/text + `FlexibleLayouts::make('Кнопки', 'buttons')->block('item', 'Кнопка', [...self::linkFields(), Select::make('Тип кнопки', 'variant')->options([...])->default('primary')])`
  - `app/Support/PageBlocks/Error404BlockLibrary.php` — по образцу `HeaderBlockLibrary`: `FlexibleLayouts::make('Значение', 'value')` + `$layouts->block(...)` через `Error404Block::register()`
  - `SettingFormPage::fields()` — match-arm `'error-404' => Error404BlockLibrary::error404()`; добавить библиотеку в `unionField()` (имя блока `error-404` уникально — коллизий с union нет)
  - `SettingIndexPage`: formatted-match `'error-404' => 'Ошибка 404'` + опция в Select-фильтре
  - Все Text/Textarea: `->escapeOnApply(static fn (): bool => false)` (rule `.ai/rules/app.md`)
  - LOGGING: без новой логики; `PageOptions` DEBUG уже есть

### Phase 2: Вью

- [x] Task 2: Вью + wiring ошибки (depends: 1)
  - `resources/views/blocks/error/404.blade.php`: разметка/классы прототипа; `@push('block-styles')` + тот же `@vite` css; код «404» статик; кнопки циклом (`x-btn :variant :href :text`, битые ссылки — `LinkResolver::href()` null → skip)
  - `resources/views/errors/404.blade.php`: settings → BlockRenderer / пусто → статический include (см. дизайн)
  - LOGGING: падение блока логируется `BlockRenderer` (WARN + skip); страница остаётся 404

### Phase 3: Сидер и фабрика

- [x] Task 3: `SettingsSeeder` + `SettingFactory` (depends: 1)
  - `SettingsSeeder`: ключ `'error-404'` в список ключей + `content()`: блок с текстами из `trans("store.error_404_{$key}", [], $locale)`; кнопки custom с локальным префиксом (`$prefix = $ru ? '' : '/en'`): «На главную» → `$prefix./`, «Перейти в каталог» → `$prefix./catalog`; варианты primary / white-border; fill-only-null (контент админа не перезаписывать)
  - `SettingFactory::error404()` state (key `error-404`) по образцу `header()`
  - Прогнать `php artisan db:seed --class=SettingsSeeder`
  - LOGGING: seeder не логирует по-строчно — добавить INFO per-key при создании (`error-404.{locale}:created/:filled`) по аналогии с page-сидерами? — Нет: `SettingsSeeder` сейчас без логов; оставить как есть, верификация через БД/smoke
- [x] Task 4: Тесты `tests/Feature/Error404PageTest.php` (Pest) (depends: 2, 3)
  - `php artisan make:test --pest Error404PageTest`; beforeEach: языки ru/en + clearCache (rule `.ai/rules/tests.md`)
  - Кейсы: (1) без настроек → 404 + статика (тексты прототипа видны); (2) settings ru → 404 + тексты блока + `btn--primary`/`btn--white-border` + href кнопок; (3) битая ссылка → кнопки нет; (4) `/en/несуществующий` → en-тексты; (5) нет en-строки → ru-тексты (fallback); (6) всегда `assertNotFound()` (никогда не 200)
  - LOGGING: без новой логики

### Phase 4: Финализация

- [x] Task 5: Качество и регресс (depends: 4)
  - `php vendor/bin/pint --dirty --format agent`; прогон `Error404PageTest` + смежных (`SettingServiceTest`, `SettingResourceTest`, `LayoutSettingsTest`, `BlockRendererTest`)
  - Smoke: `curl -s -o /dev/null -w "%{http_code}" http://bronber_store.test/404` (404, не 200) + проверка HTML на тексты блока; `/en/404`
  - LOGGING: отчёт прогона
- [x] Task 6: Docs checkpoint (Settings: Docs = yes) (depends: 5)
  - `README.md` (строка про управляемые блоки) + `.ai-factory/DESCRIPTION.md`: пункт «Страница 404 из настроек (ключ error-404): блок error-404 (заголовок, описание, flexible-кнопки с типом ссылки и вариантом оформления); пусто → статика прототипа»
  - LOGGING: без изменений

## Правила-инварианты (проверить перед завершением)

1. Все Text/Textarea блока имеют `escapeOnApply(false)`
2. HTTP-статус /404 остаётся 404 в обоих режимах (DB и fallback); published-страница со slug `404` не создаётся
3. Иконка пикера `x-circle` существует в vendor-наборе MoonShine (проверено на этапе планирования)
4. `SettingsSeeder` не перезаписывает непустые значения; ключ добавлен в список и в `content()`
5. Тесты: beforeEach сеет языки + `LanguageService::clearCache()`
6. Статический fallback (`blocks.error-404/error-404.blade.php`) сохранён — это settings-паттерн, не page-конверсия
7. Ссылки в en-сидере — с префиксом `/en` (кастомный тип), ru — без
