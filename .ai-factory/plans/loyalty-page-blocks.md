# Implementation Plan: Страница лояльности — 4 DB-блока вместо прототипа

Branch: none (git.create_branches = false, работа на текущей ветке `main`)
Created: 2026-09-14

## Original Request

новые блоки тут отсюда http://bronber_store.test/loyalty если что там куча изображений, не забывай что другой язык берет от основного языка! 1 блок - заголовок, описание, тип ссылки (кастом или список готовых страниц), и изображение. 2 блок - список (где иконка, заголовок и описание) но на первом иконка большая, ну там класс стоит, все окей. А в других нет заголовка. Думаю заметишь! 3 блок заголовок и список flexible иконка, заголовок и описание! 4. блок слева заголовок, и там список (слева и справа текст и цвет), потом подарок иконка, заголовок и текст) и справа элемент (заголовок и список вопросов flexible (заголовок и описание)

## Settings

- Testing: yes
- Logging: verbose
- Docs: yes

## Текущее состояние

`/loyalty` — последний непереведённый контентный прототип:

- Фиксированный роут: `Route::get('/loyalty', fn () => view('loyalty'))->name('loyalty')` (`routes/web.php:22`, внутри i18n-замыкания → есть и `/en/loyalty` дубль)
- Прототипные вью: `resources/views/loyalty.blade.php` + `resources/views/blocks/loyalty/{hero,benefits,how-works,bottom}/{name}.blade.php` (подкаталоги!)
- CSS: `resources/css/blocks/loyalty/{hero,benefits,how-works,bottom}/style.css` — **остаются без изменений**, новые вью подключают их через `@vite`
- Тексты: `lang/{ru,en}/loyalty.php` — **остаются**, сидер берёт из них тексты (паттерн DeliveryPageSeeder)
- Изображения: `public/images/loyalty/1.{png,webp,avif}` (hero). Иконки — inline-SVG, захардкожены в вью прототипа → надо выгрузить в файлы
- `route('loyalty')` используется в 3 layout-партиалах: `blocks/common/header/header.blade.php:197`, `blocks/common/footer/footer.blade.php:185`, `blocks/common/mobile-menu/mobile-menu.blade.php:78`

Рецепт конвертации (DESCRIPTION.md «Рецепт конвертации прототип-страницы в блоки»): класс блока + регистрация в `PageBlockLibrary` (+ `mediaSchemas()`) → плоское вью `blocks/loyalty/{name}.blade.php` → идемпотентный сидер fill-when-empty → фиксированный роут удалить (catch-all подхватит) → тесты по образцу `ReturnsPageTest` → прототипные вью удалить.

## Дизайн блоков

Имена типов: `loyalty-hero`, `loyalty-benefits`, `loyalty-how-works`, `loyalty-bottom`. `BlockRenderer` режет по первому тире: `loyalty-how-works` → вью `blocks.loyalty.how-works` (плоский файл, не подкаталог прототипа). Категория в пикере: «Лояльность». Всем блокам `limit: 1`.

### Блок 1 — `loyalty-hero` (иконка пикера: `photo`)

| Поле | Тип | Notes |
|---|---|---|
| `title` | Text | h1 |
| `text` | Textarea | подзаголовок |
| `label`, `type`, `page`, `url` | `BuildsLinkFields::linkFields()` | label = текст кнопки; type: `page` (Select из `PageOptions::published()`) \| `custom` (URL) |
| `image_pc` | MediaManagerPicker | десктопное изображение |
| `image_mb` | MediaManagerPicker | мобильное; пусто → рендерим `image_pc` |

Media schema: `'loyalty-hero' => ['image_pc', 'image_mb']` (ключи уровня блока).

Во вью кнопка рендерится только если `LinkResolver::href($block)` ≠ null (битые ссылки → null, паттерн SettingsResolver::links). SVG-иконка кнопки остаётся inline во вью.

### Блок 2 — `loyalty-benefits` (иконка пикера: `star`)

Список `items` (flexible), поля элемента: `icon` (MediaManagerPicker), `title` (Text, опционален), `text` (Textarea, допускается HTML `<strong>`/`&nbsp;` как в прототипе).

Логика «главного» пункта прототипа: **`title` заполнен → элементу вешается класс `loyalty-benefits__item--main` (большая иконка) и рендерится body с заголовком; пустой `title` → только иконка + текст**. Media schema: `'loyalty-benefits' => ['items' => ['icon']]`.

### Блок 3 — `loyalty-how-works` (иконка пикера: `queue-list`)

`title` (Text, h2 секции) + список `items` (flexible): `icon` (MediaManagerPicker), `title`, `text` (Textarea). Структурно = `delivery-methods`, вёрстка `loyalty-how`. Media schema: `'loyalty-how-works' => ['items' => ['icon']]`.

### Блок 4 — `loyalty-bottom` (иконка пикера: `gift`)

Две колонки в одном блоке:

| Поле | Тип | Notes |
|---|---|---|
| `title` | Text | h2 левой колонки («Пример начисления бонусов») |
| `rows` | flexible | элемент: `left_text` (Text), `right_text` (Text), `color` (Color, `MoonShine\UI\Fields\Color`, опц.) |
| `gift_icon` | MediaManagerPicker | иконка подарка |
| `gift_title` | Text | заголовок баннера |
| `gift_text` | Textarea | текст баннера |
| `faq_title` | Text | h2 правой колонки («Частые вопросы») |
| `faqs` | flexible | элемент: `question` (Text), `answer` (Textarea) |

Цвет (решение пользователя — color picker): при непустом `color` во вью вешаем inline `style="color: {color}"` на label/value строки — обобщение прототипного `--accent` (тот красил оба спана в `var(--color-purple)`). CSS не трогаем. Media schema: `'loyalty-bottom' => ['gift_icon']` (ключ уровня блока).

FAQ-аккордеон: Alpine `x-data="{ open: 0 }"` + `x-collapse` по индексу, как в прототипе.

### i18n / «другой язык берёт от основного»

- Тексты: per-locale переводы (`page_translations.content`), сидер заполняет ru + en из lang-файлов; отсутствие en-перевода → PageController сам делает fallback на дефолтный язык (уже реализовано)
- Медиа: пустые медиа-поля en-перевода наследуются из ru при рендере через `MediaFallback::apply` + `PageBlockLibrary::mediaSchemas()` — поэтому схемы обязательны (rule `.ai/rules/page-blocks.md`)

## Commit Plan

- **Commit 1** (после задач 1–3): `feat(loyalty): DB-блоки страницы лояльности — классы, вью, иконки`
- **Commit 2** (после задач 4–5): `feat(loyalty): сидер страницы, переход на catch-all, удаление прототипа`
- **Commit 3** (после задач 6–8): `test(loyalty): фич-тесты страницы лояльности + docs`

## Tasks

### Phase 1: Подготовка ассетов

- [x] Task 1: Выгрузить inline-SVG прототипа в файлы `public/images/loyalty/icons/` (depends: —)
  - Из `resources/views/blocks/loyalty/benefits/benefits.blade.php`: `benefits-coin.svg` (111×111), `benefits-cart.svg` (35×35), `benefits-calendar.svg` (35×35, ключ `$icons['infinity']`), `benefits-star.svg` (35×35)
  - Из `how-works.blade.php`: `how-cart.svg` (80×80), `how-coin.svg` (76×76), `how-card.svg` (83×83)
  - Из `bottom/bottom.blade.php`: `bottom-gift.svg` (55×55, ключ `$icons['star']`)
  - SVG копировать дословно из php-строк прототипа; проверить `curl -I` каждого файла (200)
  - LOGGING: без изменений логики; проверка — только HTTP-статусы

### Phase 2: Блоки MoonShine

- [x] Task 2: Создать 4 класса блоков + регистрация (depends: —)
  - `app/Support/PageBlocks/Blocks/Loyalty/LoyaltyHeroBlock.php` — поля по таблице выше; подключить трейт `Concerns\BuildsLinkFields` (label/type/page/url); два MediaManagerPicker с `allowedExtensions(['jpg','jpeg','png','webp','svg','avif'])`
  - `LoyaltyBenefitsBlock.php`, `LoyaltyHowWorksBlock.php`, `LoyaltyBottomBlock.php` — по дизайну выше; в `LoyaltyBottomBlock` — `Color::make('Цвет', 'color')` из `MoonShine\UI\Fields\Color`
  - Всем Text/Textarea: `->escapeOnApply(static fn (): bool => false)` (rule `.ai/rules/app.md` — сырой текст в БД)
  - Иконки пикера только проверенные: `photo`, `star`, `queue-list`, `gift` (существуют в `vendor/moonshine/.../icons/` — rule `.ai/rules/moon-shine.md`)
  - `PageBlockLibrary::blocks()` — добавить 4 класса; `mediaSchemas()` — добавить 4 схемы (точно в дизайн выше)
  - LOGGING: без новой логики; `PageOptions` уже пишет DEBUG
- [x] Task 3: Создать 4 плоских вью (depends: 2)
  - `resources/views/blocks/loyalty/{hero,benefits,how-works,bottom}.blade.php`
  - Разметка/классы дословно из прототипа; `@push('block-styles')` + `@once` + те же `@vite`-пути css
  - Нормализация медиа-путей как в `blocks/delivery/methods.blade.php` (disk-relative → `/storage/…`, абсолютные/URL — pass-through), рендер через `<x-img>`
  - hero: alt = title; `image_mb` пуст → использовать `image_pc`; кнопка — `LinkResolver::href($block)`, null → не рендерить `<a>`
  - benefits: класс `--main` + заголовок при непустом `title`; тексты с HTML — `{!! !!}`, заголовки — `{{ }}`
  - bottom: inline `style="color: …"` при непустом `color`; Alpine-аккордеон faq по индексу (`x-cloak` для всех, кроме первого)
  - LOGGING: без новой логики; падение блока уже логируется `BlockRenderer` (WARN + skip)

### Phase 3: Сидер

- [x] Task 4: `database/seeders/LoyaltyPageSeeder.php` + регистрация (depends: 1, 2)
  - Паттерн `DeliveryPageSeeder`: `firstOrCreate` страница `slug=loyalty, is_published=true`; переводы ru/en `firstOrCreate` + fill-when-empty (`isEmptyContent`); **контент админа не перезаписывать** (rule `.ai/rules/seeders.md`)
  - Тексты — `trans("loyalty.{$key}", [], $locale)` из `lang/{ru,en}/loyalty.php`; SEO title/meta — по образцу других сидеров
  - Контент: все 4 блока; иконки — абсолютные пути `/images/loyalty/icons/*.svg`; hero — `image_pc`/`image_mb` = `/images/loyalty/1.png`; акцентная строка бонусов — `color = '#7212BC'` (значение `--color-purple`)
  - Регистрация в `DatabaseSeeder` (после `ReturnsPageSeeder`); прогнать `php artisan db:seed --class=LoyaltyPageSeeder`
  - LOGGING: INFO per-locale `[LoyaltyPageSeeder] loyalty page seeded, locales=ru:created, en:created` (`:created`/`:exists`/`:filled`)

### Phase 4: Переход на catch-all, удаление прототипа

- [x] Task 5: Убрать фиксированный роут и прототип (depends: 3, 4)
  - `routes/web.php`: удалить `Route::get('/loyalty', …)` (i18n-замыкание — уйдут и `/en/loyalty` дубли; новый роут НЕ заводить — rule `.ai/rules/routes.md`)
  - Заменить `route('loyalty')` → `url('/loyalty')` в 3 партиалах: header:197, footer:185, mobile-menu:78 (паттерн соседних `url('/faq')`, `url('/delivery')`)
  - Удалить `resources/views/loyalty.blade.php` и каталоги `resources/views/blocks/loyalty/{hero,benefits,how-works,bottom}/`; css/js НЕ трогать
  - Smoke: `php artisan route:list --path=loyalty` (только catch-all), `curl -s http://bronber_store.test/loyalty` и `/en/loyalty` (200, секции на месте)
  - LOGGING: DEBUG `[PageController.show]` уже пишет found/locale — убедиться в логах smoke-запроса

### Phase 5: Тесты

- [x] Task 6: `tests/Feature/LoyaltyPageTest.php` (Pest) (depends: 3, 4)
  - Создать `php artisan make:test --pest LoyaltyPageTest`
  - beforeEach: `resolve(LanguageService::class)->clearCache()` + фабрики языков ru/en (rule `.ai/rules/tests.md`)
  - Хелпер `loyaltyPage()`: фабрика Page slug=loyalty + ru/en переводы с 4 блоками (по образцу `returnsPage()`)
  - Ассерты: `/loyalty` 200 + видны hero title/подзаголовок/кнопка с `href` кастомной ссылки; benefits: первый пункт с классом `loyalty-benefits__item--main` и заголовком, второй — без; how-works: заголовок + шаг; bottom: строки label/value, акцентная строка с `style="color:`, баннер, faq-вопрос; `/en/loyalty` 200 + en-тексты; 404 для черновика и отсутствующей страницы
  - LOGGING: тесты не добавляют своей логики; падение рендера блока видно через WARN `BlockRenderer` в логах

### Phase 6: Финализация

- [x] Task 7: Качество и регресс (depends: 5, 6)
  - `php vendor/bin/pint --dirty --format agent` (rule `.ai/rules/general.md` — через `php`, не напрямую)
  - `php artisan test --compact tests/Feature/LoyaltyPageTest.php`; смежные: `tests/Feature/BlockRendererTest.php`, `tests/Feature/LinkResolverTest.php`, `tests/Feature/MediaFallbackTest.php`
  - Smoke: `curl` `/loyalty` + `/en/loyalty` после всех правок
  - LOGGING: отчёт прогона в чат, без новых лог-вызовов
- [x] Task 8: Docs checkpoint (Settings: Docs = yes) (depends: 7)
  - Через `/aif-docs`: обновить список DB-страниц (docs/admin-panel.md, docs/frontend.md) и `.ai-factory/DESCRIPTION.md` — добавить пункт «Страница "Программа лояльности" из БД (slug loyalty, catch-all): блоки loyalty-hero/-benefits/-how-works/-bottom»
  - LOGGING: без изменений

## Правила-инварианты (проверить перед завершением)

1. Все контентные Text/Textarea имеют `->escapeOnApply(static fn (): bool => false)`
2. `mediaSchemas()` синхронны с MediaManagerPicker-полями всех 4 блоков
3. Сидер идемпотентен, fill-when-empty, контент админа не перезаписывается
4. Фиксированный роут `/loyalty` удалён; ссылок `route('loyalty')` в коде не осталось
5. Иконки пикера (`photo`, `star`, `queue-list`, `gift`) существуют в vendor-наборе MoonShine
6. Тесты: beforeEach сеет языки + clearCache
7. Никаких новых веток/каталогов вне конвенций; css/js прототипа переиспользуются без правок
