# План: Блок «Таймлайн» на странице «О компании» (/about)

**Режим:** Fast
**Ветка:** main (git.create_branches = false — ветка не создаётся)
**Дата:** 2026-09-13

## Original Request

новый блок делай http://bronber_store.test/about там будет заголовок, список (дата и текст через editorjs)

и список через flexible!

## Суть

Конвертация прототипа `/about` в DB-страницу по рецепту проекта: новый блок `about-timeline`
(заголовок + flexible-список элементов «дата + editorjs-текст»), идемпотентный сидер ru/en,
удаление прототип-роута/вью/lang-ключей, перевод ссылок на `url('/about')`, тесты по образцу
`FaqPageTest`.

## Факты разведки

- `/about` — прототип: `Route::get('/about', fn () => view('about'))` (`routes/web.php:22`),
  обёртка `resources/views/about.blade.php` → `blocks/about/about.blade.php` — таймлайн-слайдер
  лет (desktop) + аккордеон (mobile), захардкоженный демо-текст, изображения `bg.jpg`/`bg-mb.jpg`
- Catch-all `/{slug}` / `/{locale}/{slug}` уже обслуживает DB-страницы — новый fixed-роут заводить
  нельзя (правило `.ai/rules/routes.md`)
- Паттерн блока: интерфейс `PageBlock::register(FlexibleLayouts)`; образец вложенного списка —
  `FaqItemsBlock` (`FlexibleLayouts::make('items')->block('item', …)`)
- EditorJs-поле пакета: `Sckatik\MoonshineEditorJs\Fields\EditorJs` — **наследует Textarea**
  (в блоках проекта пока не используется, первое применение); рендер фронта:
  `app(\Sckatik\MoonshineEditorJs\RenderEditorJs::class)->render($json)` — образец
  `blocks/legacy/text.blade.php`
- Конфиг `config/moonshine-editor-js.php` опубликован; инструменты paragraph/header/list/quote/
  code/link/image активированы; ассеты `public/vendor/moonshine-editorjs/` опубликованы
- `route('about')` используется в 3 вью: `blocks/common/header/header.blade.php:198`,
  `blocks/common/footer/footer.blade.php:198`, `blocks/common/mobile-menu/mobile-menu.blade.php:80`
- Иконка `clock` существует в `vendor/moonshine/moonshine/src/UI/resources/views/icons/` (проверено —
  правило `moon-shine.md` satisfied)
- `EditorJs extends Textarea` → обязателен `->escapeOnApply(static fn (): bool => false)`
  (правило `.ai/rules/app.md` — иначе каждый сейв админки портит JSON)
- EditorJs-рендер бросает `Exception` на невалидном JSON; `BlockRenderer` ловит Throwable и
  пропускает блок с `Log::warning` — сидер обязан сеять валидный EditorJs JSON

## Settings

- **Testing:** yes — `AboutPageTest` по образцу `FaqPageTest`
- **Logging:** standard — сидер логирует per-locale (`:created`/`:exists`/`:filled`) по правилу
  `seeders.md`; `BlockRenderer` уже логирует и пропускает битые блоки; новых логгеров не добавлять
- **Docs:** no — WARN-only, обязательный чекпоинт документации не требуется

## Решения

- Поле «дата» — `Text` (гибкий формат: «2017», «2017–2019», «май 2024»), без `Date`-поля и
  принудительного формата; вывод через `{{ }}` (экранирование на рендере)
- Тип блока `about-timeline` ↔ `resources/views/blocks/about/timeline.blade.php` (правило
  `page-blocks.md`: `{page}-{name}`)
- Элемент списка = `date` + `text` (EditorJs). Заголовок события прототипа (`about__subtitle`)
  и изображения (`about__image-wrap`) в блок не входят — ТЗ определяет состав как «дата и текст»;
  CSS/JS блока сохраняются (те же классы `about__*`, Alpine-компонент `about()`, x-slider)
- `PageBlockLibrary::mediaSchemas()` не трогаем — медиа-полей нет
- Сидер: editorjs-текст сеем как валидный JSON через `json_encode(['blocks' => [['type' => 'paragraph', 'data' => ['text' => …]]]])`
- Демо-тексты — собственные компактные ru/en абзацы об истории магазина (демо-мусор «PASHA Holding»
  из прототипа не копировать)

## Tasks

### Task 1: Блок-класс AboutTimelineBlock + регистрация в PageBlockLibrary

- Создать `app/Support/PageBlocks/Blocks/About/AboutTimelineBlock.php`
  (`namespace App\Support\PageBlocks\Blocks\About`, `final class`, `implements PageBlock`,
  `declare(strict_types=1)`, PHPDoc):
  - `$layouts->block('about-timeline', 'О компании — история (таймлайн)', [`
    - `Text::make('Заголовок', 'title')->escapeOnApply(static fn (): bool => false),`
    - `FlexibleLayouts::make('События', 'items')->block('item', 'Событие', [`
      - `Text::make('Дата', 'date')->escapeOnApply(static fn (): bool => false),`
      - `EditorJs::make('Текст', 'text')->escapeOnApply(static fn (): bool => false),` —
        импорт `Sckatik\MoonshineEditorJs\Fields\EditorJs`
      - `]),`
    - `], limit: 1, category: 'О компании', description: 'Заголовок + список событий: дата и editorjs-текст (прототип /about)', icon: 'clock');`
- Зарегистрировать в `app/Support/PageBlocks/PageBlockLibrary.php`: use-импорт
  `App\Support\PageBlocks\Blocks\About\AboutTimelineBlock` + добавить класс в `blocks()` (в конец
  списка)
- MUST: `escapeOnApply(false)` на ВСЕХ текстовых полях, включая EditorJs (это Textarea!)
- MUST NOT: не трогать `mediaSchemas()`; не заводить роут; не менять другие блоки

### Task 2: Вью блока blocks/about/timeline.blade.php

- Создать `resources/views/blocks/about/timeline.blade.php` на основе разметки прототипа
  `blocks/about/about.blade.php` (классы, слайдер, аккордеон — без изменений):
  - Сохранить `@push('block-styles')` → `resources/css/blocks/about/style.css` и
    `@push('block-scripts')` → `resources/js/blocks/about/index.js`
  - `<h1 class="about__title section__title">{{ $block['title'] }}</h1>`
  - `$timeline = $block['items'] ?? []` (каждый элемент: `date`, `text`)
  - Desktop-слайдер лет (`x-slider`, кнопки `about__year` = `{{ $item['date'] }}`) + панели
    `about__panel` (`x-show="active === $i"`) + мобильный аккордеон (`about__acc-item`,
    заголовок аккордеона = `{{ $item['date'] }}`)
  - Текст события (и в панели, и в аккордеоне):
    `@if (! empty($item['text'])) {!! app(\Sckatik\MoonshineEditorJs\RenderEditorJs::class)->render((string) $item['text']) !!} @endif`
  - Удалить из разметки: `about__subtitle` (h2 события) и `about__image-wrap` (нет полей для них)
- Данные приходят как `$block` (BlockRenderer рендерит вью с `['block' => $block]`)
- Логирование: не требуется — падение рендера ловит `BlockRenderer` (warn + пропуск блока)

### Task 3: Сидер AboutPageSeeder + регистрация в DatabaseSeeder

- Создать `database/seeders/AboutPageSeeder.php` по образцу `FaqPageSeeder` (строго:
  `firstOrCreate`, fill-when-empty через `isEmptyContent()`, пер-локальный `Log::info`):
  - `Page`: slug `about`, `is_published => true`, `sort_order => 0`
  - ru: title «О компании», meta_title/meta_description (о магазине автозапчастей BMW/Audi/VW);
    en: «About Us» + мета
  - `content` = один блок `about-timeline`: title «История компании» / «Our Story» + 5 событий
    (2017–2021, как в прототипе; тексты — 1–2 editorjs-абзаца о вехах магазина ru/en)
  - editorjs-текст элемента: `json_encode(['blocks' => [['type' => 'paragraph', 'data' => ['text' => '…']]]])`
- Добавить `$this->call(AboutPageSeeder::class);` в `DatabaseSeeder` после `ContactsPageSeeder`
- Логирование: `Log::info('[AboutPageSeeder] about page seeded, locales={locales}', …)` —
  per-locale статусы (правило `seeders.md`)

### Task 4: Удаление прототипа /about

- `routes/web.php`: удалить строку 22 — `Route::get('/about', fn () => view('about'))->name('about');`
- Удалить файлы: `resources/views/about.blade.php`, `resources/views/blocks/about/about.blade.php`,
  `lang/ru/about.php`, `lang/en/about.php`
- Заменить `route('about')` → `url('/about')` (правило `views.md` — без route()-имён страниц):
  - `resources/views/blocks/common/header/header.blade.php:198`
  - `resources/views/blocks/common/footer/footer.blade.php:198`
  - `resources/views/blocks/common/mobile-menu/mobile-menu.blade.php:80`
- MUST: после правок grep-проверка — не осталось `route('about')`, `route('en.about')` и ключей
  `about.` в `resources/` и `routes/`
- Выполнить `php artisan optimize:clear` (снятие кэша роутов после удаления)

### Task 5: Тесты AboutPageTest

- Создать `tests/Feature/AboutPageTest.php` (Pest, по образцу `FaqPageTest`; правило `tests.md`):
  - `beforeEach`: `resolve(LanguageService::class)->clearCache()` + языки ru (default) / en
  - Хелпер `aboutPage()`: `Page::factory()` slug `about` + переводы ru/en с блоком
    `about-timeline` (title + items: `['_type' => 'item', 'date' => '2017', 'text' => json_encode(paragraph…)]`)
  - Тесты:
    1. рендер ru: `assertSee` заголовок блока, дату события, текст editorjs-абзаца
    2. 404, когда страницы нет
    3. 404, когда страница — черновик (`Page::factory()->draft()`)
    4. рендер `/en/about` с EN-текстом
    5. SEO-теги: `<title>`, description, canonical `url('/about')`, hreflang ru/en
- Использовать фабрики (`PageFactory`, `PageTranslationFactory`), не создавать модели вручную

### Task 6: Верификация

- `php vendor/bin/pint --dirty --format agent` (бинарники без exec-бита — запускать через `php`,
  правило `general.md`)
- `php artisan test --compact tests/Feature/AboutPageTest.php`
- `php artisan db:seed --class=AboutPageSeeder` (идемпотентно; на dev-БД)
- Смоук: `curl -s http://bronber_store.test/about` и `http://bronber_store.test/en/about` — 200,
  заголовок и текст из блока; в логах нет `[BlockRenderer]`-warning
- `npm run build` НЕ требуется — JS/CSS не меняются (переиспользуются существующие
  about-ассеты, уже в Vite-манифесте)
- После прохождения фич-тестов попросить пользователя прогнать полный suite
  (`php artisan test --compact`)

## Commit Plan

6 задач → чекпоинты (коммиты только по явной просьбе пользователя):

- **Чекпоинт 1** (после Task 4): `feat(content): about page timeline block with editorjs items`
- **Чекпоинт 2** (после Task 6): `test(content): cover about page db rendering`

## Риски и edge cases

- **Невалидный editorjs-JSON** (админ вручную испортил значение) → `RenderEditorJs` бросает
  исключение → `BlockRenderer` ловит, блок пропускается с warning — страница не падает
- **`escapeOnApply` пропущен на EditorJs** → каждый сейв админки экранирует JSON (`&` → `&amp;`)
  — скрытый баг, проверить оба поля в Task 1
- **Забытый `route('about')`** → `RouteNotFoundException` на шапке/подвале — grep-проверка в Task 4
  обязательна
- **route:cache** — если кэш роутов был включён, без `optimize:clear` старый роут `/about`
  перекроет catch-all
- **`items` пуст** → вью рендерит заголовок без слайдера/аккордеона (guard `@forelse`/пустой
  `@foreach`) — не падать на пустом списке
