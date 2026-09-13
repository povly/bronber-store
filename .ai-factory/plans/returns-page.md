# План: страница «Возврат и обмен» (/returns) — контент через EditorJS

> INFO [aif-plan] resolved plan file: .ai-factory/plans/returns-page.md (format=slug)

- **Дата:** 2026-09-14
- **Ветка:** не создаётся (`git.create_branches: false`), работа в текущей ветке
- **Тип:** конвертация прототип-страницы в DB-страницу (рецепт из DESCRIPTION.md), контент — EditorJS

## Original Request

http://bronber_store.test/returns новая страница - Возврат и обмен и тут все через editorJS делаем

## Settings

- **Testing:** yes — feature-тест `ReturnsPageTest` по образцу `AboutPageTest`/`FaqPageTest`
- **Logging:** verbose — детальные логи там, где есть поверхность (сидер логирует per-locale `:created`/`:exists`/`:filled` по конвенции; рендер и блоки уже покрыты логами `BlockRenderer`)
- **Docs:** yes — обязательный чекпоинт документации в конце через `/aif-docs`

## Контекст исследования (факты кодовой базы)

- Прототип `/returns` уже существует: фиксированный роут `routes/web.php:118` (`->name('returns')`), вью `resources/views/returns.blade.php` → `blocks/returns/returns.blade.php` (захардкоженный ru-текст), мёртвый дубль `components/returns/returns.blade.php` (нигде не подключён), CSS `resources/css/blocks/returns/style.css`, тексты `lang/{ru,en}/returns.php`.
- EditorJS уже интегрирован: поле `Sckatik\MoonshineEditorJs\Fields\EditorJs` (пример — `AboutTimelineBlock`), серверный рендер `app(\Sckatik\MoonshineEditorJs\RenderEditorJs::class)->render($json)` (примеры — `blocks/about/timeline.blade.php`, `blocks/legacy/text.blade.php`), конфиг `config/moonshine-editor-js.php` (paragraph/header/list/… включены).
- DB-страницы: `pages` + `page_translations.content` (JSON flexible-блоки), catch-all `/{slug}` / `/{locale}/{slug}` отдаёт опубликованные страницы; фиксированный страничный роут только `/`.
- Конвенции (`.ai/rules`): новые хардкод-роуты для DB-страниц не заводить; Text/Textarea/EditorJs — `->escapeOnApply(static fn (): bool => false)`; сидеры fill-when-empty; тесты — beforeEach сеет языки + `clearCache`; иконка блока должна существовать в `vendor/moonshine/moonshine/src/UI/resources/views/icons/` (для returns проверена `receipt-refund`).
- Ссылки-фолбэки `route('returns')` в шапке/подвале/мобильном меню сломаются после удаления именованного роута — заменить на `url('/returns')`.
- Паттерн источника демо-текстов: как у delivery — сидер читает `lang/{ru,en}/returns.php`, lang-файлы остаются (потребитель — только сидер).

## Архитектурное решение

Один flexible-блок **`returns-content`** (→ вью `blocks/returns/content.blade.php`) с двумя полями:

- `title` (Text) — h1 «Возврат и обмен» (класс `section__title`, как в прототипе);
- `body` (EditorJs) — весь rich-контент страницы одним документом EditorJS (paragraph / header level 2 / list).

Рендер: `{!! app(\Sckatik\MoonshineEditorJs\RenderEditorJs::class)->render((string) $block['body']) !!}` — без кастомных blade-шаблонов пакета, разметка EditorJS (`<p>`, `<h2>`, `<ul><li>`) совпадает с тегами прототипа и наследует существующие стили `.returns__block`. H1 остаётся отдельным полем блока (как `title` в `about-timeline`) — гарантирует прототипную вёрстку без кастомизации рендера заголовков. `mediaImage` из media-manager доступен в редакторе опционально, схема `mediaSchemas()` не нужна (нет MediaManagerPicker-полей уровня блока).

## Tasks

### Фаза 1. Блок и вью

- [x] **T1. Класс блока `ReturnsContentBlock`**
  - Создать `app/Support/PageBlocks/Blocks/Returns/ReturnsContentBlock.php` по образцу `AboutTimelineBlock`: `$layouts->block('returns-content', 'Возврат — контент (EditorJS)', [Text::make('Заголовок', 'title')->escapeOnApply(false), EditorJs::make('Контент (EditorJS)', 'body')->escapeOnApply(false)], limit: 1, category: 'Возврат', description: 'Заголовок h1 + весь текст страницы одним EditorJS-документом (прототип /returns)', icon: 'receipt-refund')`.
  - Зарегистрировать в `PageBlockLibrary::blocks()` (после `AboutTimelineBlock`).
  - `mediaSchemas()` не трогать. `declare(strict_types=1)`, final class, PHPDoc.
  - Логирование: не требуется (декларативный класс); проверка — страница админки открывает форму перевода с новым блоком в пикере.

- [x] **T2. Вью `blocks/returns/content.blade.php`**
  - Создать `resources/views/blocks/returns/content.blade.php`: `@push('block-styles') @vite(['resources/css/blocks/returns/style.css']) @endpush`; разметка прототипа `section.returns.section > div.container > div.returns__block`, `<h1 class="section__title">{{ $block['title'] }}</h1>`, дальше `@if(!empty($block['body'])) {!! app(\Sckatik\MoonshineEditorJs\RenderEditorJs::class)->render((string) $block['body']) !!} @endif`.
  - Breadcrumbs не добавлять (уровень страницы, `PageBreadcrumbs`).
  - Логирование: не требуется; проверка — BlockRenderer подхватит тип `returns-content` → `blocks.returns.content`.

### Фаза 2. Сидер

- [x] **T3. `ReturnsPageSeeder` + регистрация**
  - Создать `database/seeders/ReturnsPageSeeder.php` по образцу `AboutPageSeeder`/`DeliveryPageSeeder`: `Page::firstOrCreate(['slug' => 'returns'], ['is_published' => true, 'sort_order' => 0])`; переводы ru/en `firstOrCreate` + fill-when-empty (`isEmptyContent`: `content === null || []`); `title`/`meta_title`/`meta_description` из текстов, `content = [['_type' => 'returns-content', 'title' => __('returns.title'), 'body' => $editorJs]]`.
  - Хелпер `editorJs()` строит JSON `{time: 0, blocks: [...], version}` из lang-ключей: `paragraph(body_1)`, `paragraph(body_2)`, `header(2, subtitle_non_returnable)`, `paragraph(вступление body_3)`, `list(unordered, 4 позиции)`, `paragraph(хвост body_3: «Данные товары…» + адрес)`, `header(2, subtitle_defective)`, `paragraph(body_4)` — строки `\n` в body_3 разбить. Источник — `__('returns.*')` из `lang/{ru,en}/returns.php`.
  - Зарегистрировать в `DatabaseSeeder` после `AboutPageSeeder`.
  - Логирование: `Log::info('[ReturnsPageSeeder] returns page seeded, locales={locales}', ...)` с per-locale статусами `:created`/`:exists`/`:filled` (конвенция `.ai/rules/seeders.md`).

### Фаза 3. Переключение на DB-страницу

- [x] **T4. Убрать фиксированный роут и починить ссылки**
  - `routes/web.php`: удалить строку 118 `Route::get('/returns', ...)->name('returns');` — catch-all `/{slug}` подхватит DB-страницу (роут `page`), `/en/returns` — locale-группа catch-all.
  - Заменить фолбэк-ссылки `route('returns')` → `url('/returns')` в: `resources/views/blocks/common/top-bar/top-bar.blade.php:38`, `blocks/common/footer/footer.blade.php:184`, `blocks/common/mobile-menu/mobile-menu.blade.php:92` (конвенция `.ai/rules/routes.md`: `url('/slug')`, не route()-имена).
  - Логирование: не требуется; проверка — `php artisan route:list --path=returns` не показывает фиксированный роут, ссылки рендерятся.

- [x] **T5. Удалить прототип**
  - Удалить: `resources/views/returns.blade.php`, `resources/views/blocks/returns/returns.blade.php`, `resources/views/components/returns/returns.blade.php` (мёртвый дубль).
  - **Оставить:** `lang/{ru,en}/returns.php` (источник сида, как у delivery), `resources/css/blocks/returns/style.css` (используется новым блоком), ключи `store.top_guarantee` / `store.footer_buy_guarantee` (лейблы ссылок шапки/подвала).
  - Логирование: не требуется.

### Фаза 4. Тесты и верификация

- [x] **T6. Feature-тест `ReturnsPageTest`** (после T4 — иначе фиксированный роут затеняет catch-all и тесты видят прототип)
  - Создать `tests/Feature/ReturnsPageTest.php` (`php artisan make:test --pest ReturnsPageTest`) по образцу `AboutPageTest`: `uses(RefreshDatabase::class)`; beforeEach — `resolve(LanguageService::class)->clearCache()` + фабрики языков ru/en; хелпер `returnsEditorJs(...)` (paragraph/header/list — покрыть все три типа).
  - Кейсы: рендер ru из DB-блоков (`/returns` → h1, body_1-фрагмент, `<h2>`-подзаголовок, элемент списка); 404 без страницы; 404 черновик (`Page::factory()->draft()`); `/en/returns` → en-перевод; SEO-теги (title, description, canonical, hreflang ru/en); рендер EditorJS-разметки (`<h2>`, `<ul>`/`<li>` в HTML).
  - Логирование: тесты не логируют; прогон — `php artisan test --compact tests/Feature/ReturnsPageTest.php`.

- [x] **T7. Верификация и сборка**
  - `php vendor/bin/pint --dirty --format agent` (обязательно после PHP-изменений; бинарники без exec-бита — запуск через `php`, см. `.ai/rules/general.md`).
  - Прогнать узкий набор: `php artisan test --compact tests/Feature/ReturnsPageTest.php tests/Feature/BlockRendererTest.php tests/Feature/PageRenderingTest.php` — затем попросить пользователя прогнать полный `php artisan test --compact`.
  - Smoke локально: `php artisan db:seed --class=ReturnsPageSeeder` → `curl http://bronber_store.test/returns` и `http://bronber_store.test/en/returns` (200, контент, крошки); `npm run build` не нужен (новых entrypoints нет — css уже в манифесте).
  - Docs-чекпоинт: обновить `docs/frontend.md` / `.ai-factory/DESCRIPTION.md` (страница /returns теперь DB-страница с EditorJS-блоком) через `/aif-docs`.

## Commit Plan

| Чекпоинт | Задачи | Сообщение |
|---|---|---|
| 1 | T1–T3 | `feat(content): returns page as DB page with editorjs content block` |
| 2 | T4–T5 | `refactor(routes): serve /returns from catch-all, drop prototype views` |
| 3 | T6–T7 | `test(content): cover returns page rendering, seo and en locale` |

Коммиты — только по явной просьбе пользователя (глобальное правило безопасности).
