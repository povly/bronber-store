# Implementation Plan: Главная страница как DB-страница «index» + блоки главной + локализованный переключатель языков

Branch: main (без создания ветки — `git.create_branches: false`)
Created: 2026-09-13
Продолжение: `feature-pages-blocks-settings.md` (контентный модуль Pages/FlexibleLayouts/BlockRenderer реализован)

## Original Request

создай новый блок в moonshine resource pages отсюда http://bronber_store.test/ это отсюда @resources/views/home.blade.php и в web route настрой так что главная страница это с индексом index! и в шапке где есть переключение языков, надо автоматом ставить /en если это другой язык и еще в мобильном меню! .mobile-menu

## Settings

- Testing: **no** (по явному выбору пользователя — тест-задачи НЕ создаём; существующий suite прогоняем в финальном чеке как регрессию)
- Logging: verbose (DEBUG на разработке; логируем slug/локали/источник рендера, без PII)
- Docs: yes → обязательный docs-чекпоинт в `/aif-implement` через `/aif-docs`

## Контекст (что уже есть)

- **Контентный модуль Pages** (`feature-pages-blocks-settings.md`, выполнен): таблицы `pages`/`page_translations`, `PageController::show()` → `BlockRenderer` → вьюхи `components/page-blocks/page/{type}.blade.php`, catch-all `/{slug}` в конце `$register`-замыкания, SEO c hreflang через `view()->share('seo', ...)`.
- **PageBlockLibrary::page()**: блоки `hero`, `text`, `gallery`, `faq`, `contacts`, `featured-products` (сетка `pb-featured`, данные `CatalogMock::featured()`).
- **Каталог блоков шапки/подвала**: `HeaderBlockLibrary`/`FooterBlockLibrary` + `SettingsResolver` (fallback на статику) — паттерн «настройка есть → рендерим из БД, нет → статический прототип».
- **Главная сейчас**: `routes/web.php` → `Route::get('', fn () => view('home'))->name('home')` (путь `''` вместо `'/'` — уже правлен вручную в рабочем дереве, это база плана; имя `home` используется в `error-404`, breadcrumbs `faq`/`product-reviews` — НЕ переименовывать). `home.blade.php` инклюдит `blocks.home.{hero,categories,advs,products×3,partners,news}`.
- **Переключатели языков**: `blocks/common/top-bar/top-bar.blade.php` (строки 51–59) и `blocks/common/mobile-menu/mobile-menu.blade.php` (строки 97–106) — оба всегда ведут на главную (`/` или `/en`), теряя текущую страницу, и читают `config('app.available_locales')` вместо DB-источника `LanguageService` (языки редактируются в админке с прошлого плана).
- **LanguageService**: `codes()` / `defaultCode()` — единый источник локалей (кэш), fallback на config.

## Ключевые решения

1. **Главная = DB-страница со slug `index`**. `Route::get('', [PageController::class, 'index'])->name('home')`. Метод `index()`: published-страница `index` → рендер через `BlockRenderer` (как `show()`), нет страницы/перевода → fallback `view('home')` (статический прототип, ничего не ломается — тот же паттерн, что у шапки/подвала).
2. **Slug `index` резервируется за главной**: `/index` и `/en/index` → 301-редирект на `/` и `/en` (в `show()`), canonical/hreflang для `index` указывают на корень, а не на `/index`.
3. **Новые типы блоков для состава главной** (директивы пользователя: имена «чёткие», префикс страницы входит в тип — `home-hero` ↔ `blocks/home/hero`, в будущем блоки других страниц будут `category-*`, `contact-*`; повторяющиеся структуры — НЕ Json, а вложенные `FlexibleLayouts` из `povly/moonshine-flexible-layouts`). Определения блоков разносятся по отдельным классам `App\Support\PageBlocks\Blocks\{Home\*}` (интерфейс `PageBlock::register(FlexibleLayouts)`), `PageBlockLibrary` — тонкий агрегатор; вьюхи переиспользуют CSS прототипа через `@push` и их же классы (`home-hero`, `home-categories`, …):
   - `home-hero` — слайдер промо (Json-слайды: title, text, текст кнопки, href кнопки), limit 1;
   - `home-categories` — динамический: редактируется только заголовок, категории из `CatalogMock::homeCategories()` (12 штук как на главной);
   - `home-advs` — преимущества: Json-пункты (title, image, text);
   - `home-products` — динамический: заголовок + количество, товары из `CatalogMock::featured()` (слайдер как на главной, НЕ сетка `featured-products`);
   - `home-partners` — заголовок + логотипы (MediaManagerPicker multiple);
   - `home-news` — заголовок + Json-пункты (tag, date, title, desc, image) + кнопка «Все новости» → `route('blog')`.
4. **Сид `HomePageSeeder`**: страница slug=`index`, published, переводы ru+en с блоками, повторяющими текущую главную (hero 3 слайда, категории, 4 преимущества, 3 секции товаров-slider, партнёры brembo/bosch/akrapovic, 3 новости) + meta_title. Идемпотентно (`firstOrCreate` по slug), подключается в `DatabaseSeeder` после `LanguageSeeder`.
5. **Переключатель языков сохраняет текущий путь**: новый `App\Support\Locales\LocaleSwitcher::href(string $target): string` — берёт `request()->path()`, срезает префикс текущей недефолтной локали, подставляет префикс целевой (дефолт — без префикса). Оба паршла (top-bar + mobile-menu) переходят с `config('app.available_locales')` на `LanguageService::codes()`/`defaultCode()` + `LocaleSwitcher::href()`. Query-string не сохраняется (осознанно).
6. **Без новых зависимостей, без контроллеров-классов для остальных роутов** — меняется только главная (arch: route → controller, `PageController` уже существует).

## Tasks

### Phase 1: Роут и контроллер главной

- [x] Task 1: `PageController::index()` + роут главной + редиректы `/index` (depends: —)
  В `app/Http/Controllers/PageController.php` добавить `index(): Response|View`-метод: `Page::published()->where('slug', 'index')` + `translation()` (fallback дефолтный язык) → есть: `BlockRenderer` + `view()->share('seo', ...)` + `view('page', ...)` (вынести общий рендер/SEO из `show()` в private-метод, DRY); нет: `Log::debug` + `view('home')` (статический fallback). В `show()`: ранний `redirect()` для `slug === 'index'` → дефолтная локаль `url('/')`, иначе `url("/{$locale}")` (301). В `seo()`: для slug `index` альтернативы/canonical → `/` и `/{$locale}` (не `/index`). В `routes/web.php`: `Route::get('', [PageController::class, 'index'])->name('home');` (имя `home` сохранить — ссылаются error-404/faq/product-reviews).
  LOGGING: `Log::debug('[PageController.index] locale={locale} source={db|fallback}')`; в show-редиректе `Log::debug('[PageController.show] slug=index redirected to={url}')`.
  Files: `app/Http/Controllers/PageController.php`, `routes/web.php`.
  CHECK: `php artisan route:list --name=home` показывает `home` и `en.home`; `/` без страницы index рендерит прежнюю статику.

### Phase 2: Блоки главной в библиотеке

- [x] Task 2: `CatalogMock::homeCategories()` (depends: —)
  Метод `homeCategories(): array` — 12 категорий из `blocks/home/categories.blade.php` (Тормозная система…Охлаждение), каждая: `name`, `slug`, `image` = `/images/home/categories/{n}` (порядок как в паршле, картинки по итерации), `href` = `route('catalog', ['category' => $slug])`. PHPDoc array-shape. Существующий `AppServiceProvider::catalogCategories()` (меню шапки) НЕ трогаем — у него другая форма/назначение.
  LOGGING: n/a (статический mock).
  Files: `app/Support/CatalogMock.php`.

- [x] Task 3: Блоки главной в отдельных классах `Blocks/Home/*` (depends: —)
  Новый интерфейс `App\Support\PageBlocks\Blocks\PageBlock` (`public static function register(FlexibleLayouts $layouts): void`) + 6 классов в `app/Support/PageBlocks/Blocks/Home/`: `HomeHeroBlock` (`home-hero`, «Промо-слайдер (главная)», вложенные FlexibleLayouts slides: title, text, btn_text, btn_href; limit 1), `HomeCategoriesBlock` (`home-categories`, «Категории (динамический)», title с дефолтом, данные из CatalogMock), `HomeAdvsBlock` (`home-advs`, «Преимущества», вложенные FlexibleLayouts items: title, image через MediaManagerPicker, text), `HomeProductsBlock` (`home-products`, «Товары слайдером (динамический)», title + Number count 1–12 default 4), `HomePartnersBlock` (`home-partners`, «Партнёры», title + MediaManagerPicker images multiple), `HomeNewsBlock` (`home-news`, «Новости», title + вложенные FlexibleLayouts items: tag, date, title, desc, image через MediaManagerPicker). `PageBlockLibrary::page()` — агрегатор по списку классов. Директива пользователя: повторяющиеся структуры — вложенные FlexibleLayouts (`povly/moonshine-flexible-layouts`), НЕ Json; старые универсальные блоки (hero-баннер, text, gallery, faq, contacts, featured-products) УДАЛЕНЫ из библиотеки — в пикере только `home-*`; их pb-вьюхи остаются как слой рендера для уже сохранённого/demo-контента (тесты BlockRendererTest/PageRenderingTest продолжают работать).
  LOGGING: n/a (конфигурация полей).
  Files: `app/Support/PageBlocks/Blocks/PageBlock.php`, `app/Support/PageBlocks/Blocks/Home/HomeHeroBlock.php`, `.../HomeCategoriesBlock.php`, `.../HomeAdvsBlock.php`, `.../HomeProductsBlock.php`, `.../HomePartnersBlock.php`, `.../HomeNewsBlock.php`, + классы для существующих блоков, `app/Support/PageBlocks/PageBlockLibrary.php`.

- [x] Task 4: Вьюхи новых блоков (depends: 3)
  `resources/views/components/page-blocks/page/{home-hero,home-categories,home-advs,home-products,home-partners,home-news}.blade.php` — перенос разметки из `resources/views/blocks/home/{hero,categories,advs,products,partners,news}.blade.php` с заменой хардкода на поля `$block[...]` и данные `CatalogMock::homeCategories()` / `CatalogMock::featured(count)`. Сохранить `@push('block-styles')` с теми же CSS-файлами (`resources/css/blocks/home/{...}/style.css`) и оригинальные классы (`home-hero__*` и т.д.) — пиксельная идентичность прототипу. Кнопки блога/каталога — `route('blog')` / `route('catalog')`. Изображения из MediaManagerPicker — с префиксом `/storage/` по образцу `hero.blade.php`. Мобильная/десктоп дубли разметки (x-slider + `__pc`) сохранить как в оригинале.
  LOGGING: отсутствующие поля — тихий пропуск (`@if`), как в существующих вьюхах.
  Files: `resources/views/components/page-blocks/page/home-hero.blade.php`, `.../home-categories.blade.php`, `.../home-advs.blade.php`, `.../home-products.blade.php`, `.../home-partners.blade.php`, `.../home-news.blade.php`.
  CHECK: временная DB-страница с блоками рендерится идентично статике (визуальная сверка).

### Phase 3: Сид главной

- [x] Task 5: `HomePageSeeder` (depends: 2, 3, 4)
  Идемпотентный сидер (`firstOrCreate` по slug `index`, переводы `firstOrCreate` по (page_id, locale)): ru + en переводы, `is_published = true`, контент = последовательность блоков текущей главной: home-hero (3 слайда «Найдите нужные товары по категориям»…, кнопка → `/catalog`), home-categories, home-advs (4 пункта), home-products ×3 («Рекомендованные товары», «Топливные насосы», «Тормозные диски»), home-partners (brembo/bosch/akrapovic, повторы как в прототипе), home-news (3 новости из паршла). EN-переводы заголовков/текстов — короткие маркетинговые строки. meta_title: «BRONBER — автозапчасти…» / «BRONBER — Auto parts…». Подключить в `DatabaseSeeder` после `LanguageSeeder` (после LanguageSeeder, чтобы локали существовали).
  LOGGING: `Log::info('[HomePageSeeder] index page seeded, locales={list}')`.
  Files: `database/seeders/HomePageSeeder.php`, `database/seeders/DatabaseSeeder.php`.
  CHECK: `php artisan db:seed --class=HomePageSeeder` дважды — без дублей; `/` рендерит блоки, `/en` — английскую версию.

### Phase 4: Переключатель языков

- [x] Task 6: `App\Support\Locales\LocaleSwitcher` (depends: —)
  Статический класс (`declare(strict_types=1)`, PHPDoc array-shape): `href(string $target): string` — path = `request()->path()`; если path целиком равен коду недефолтной активной локали → strip; если начинается с `{code}/` любой недефолтной активной локали → срезать префикс; результат: `$target === defaultCode()` → `url($path === '' ? '/' : '/'.$path)`, иначе → `url('/'.$target.($path === '' ? '' : '/'.$path))`. Активные коды/дефолт — из `LanguageService` (не config). Query-string отбрасывается (зафиксировать в PHPDoc).
  LOGGING: `Log::debug('[LocaleSwitcher] from={from} to={to} path={path}')`.
  Files: `app/Support/Locales/LocaleSwitcher.php`.

- [x] Task 7: Переключатели в top-bar и mobile-menu (depends: 6)
  `blocks/common/top-bar/top-bar.blade.php` (строки 51–59) и `blocks/common/mobile-menu/mobile-menu.blade.php` (строки 97–106): цикл по `LanguageService::codes()` (через `@php app(...)`, стиль паршлов уже такой), `$isDefault = $locale === defaultCode()`, `href="{{ \App\Support\Locales\LocaleSwitcher::href($locale) }}"` вместо хардкода `/` и `/{$locale}`. Активный класс и разметка — без изменений.
  LOGGING: n/a (вёрстка).
  Files: `resources/views/blocks/common/top-bar/top-bar.blade.php`, `resources/views/blocks/common/mobile-menu/mobile-menu.blade.php`.
  CHECK: на `/contacts` клик EN → `/en/contacts`; на `/en/catalog` клик RU → `/catalog`; на `/` → `/en`; на `/en` → `/`; на DB-странице `/o-kompanii` → `/en/o-kompanii`.

### Phase 5: Финализация

- [ ] Task 8: Docs-чекпоинт + финальные проверки (depends: all)
  Обязательный чекпоинт через `/aif-docs`: `docs/admin-panel.md` — блоки главной в PageBlockLibrary, страница «index» как главная, сид; `docs/frontend.md` — переключатель языков сохраняет путь, список локалей из LanguageService. Финальный прогон: `vendor/bin/pint --dirty --format agent`, `php artisan test --compact` (регрессия существующего suite), `php artisan route:list` (home/en.home на месте), визуальная проверка `/` vs статика и переключение языков в шапке + `.mobile-menu`.
  LOGGING: n/a.
  Files: `docs/admin-panel.md`, `docs/frontend.md`.

## Commit Plan

- **Commit 1** (после Task 1): `feat: render home page from index DB page with prototype fallback`
- **Commit 2** (после Tasks 2–4): `feat: home page flexible-layout block types`
- **Commit 3** (после Task 5): `feat: seed editable index home page`
- **Commit 4** (после Tasks 6–7): `feat: locale switcher preserves current path in header and mobile menu`
- **Commit 5** (после Task 8): `docs: home index page and locale switcher`

## Что НЕ входит в этот план (осознанно)

- **Тесты** — пользователь явно отказался; существующий suite используется только как регрессионный чек.
- **`home.blade.php` и `blocks/home/*`** — остаются нетронутыми как fallback; удалять после миграции будем отдельным решением.
- **Логотип шапки (`href="/"`) и прочие внутренние ссылки** — остаются как есть (в запросе только переключатель языков); локализацию логотипа можно сделать позже тем же `LocaleSwitcher`.
- **Сохранение query-string при смене языка** — отброшено (прототип; категории `?category=` переживут переход через fallback главной).
- **Массовый перенос других статических страниц в DB** — отдельные планы (механизм готов).
