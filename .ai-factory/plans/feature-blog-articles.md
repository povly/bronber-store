# Implementation Plan: Блог → Статьи (DB-статьи с блоками, отдельно от pages)

Branch: — (git.create_branches=false, работа в текущей ветке)
Created: 2026-09-14

## Original Request

теперь нужно настроить Блог -> Статьи, это тоже отдельно от page сделаем? http://bronber_store.test/blog тоже отдельно от page сделаем? http://bronber_store.test/blog ну и сделаем наподобие сстраницы, только там Блог просто со списком статьи и сама статья с блоками

## Settings

- Testing: yes (Pest-фич-тесты по образцу FaqPageTest)
- Logging: verbose (Log::debug в контроллере/сервисе по образцу PageController, INFO в сидере/ресурсе)
- Docs: yes (обязательный чекпоинт: обновить DESCRIPTION.md через /aif-docs)

## Решение архитектуры (ответ на вопрос пользователя)

**Да, статьи — отдельно от `pages`.** Блог — это коллекция сущностей (листинг + пагинация-подгрузка + даты публикации + обложки + «другие новости»), а DB-страница — одна сущность без концепции списка. При этом **контент статьи делается по образцу страниц**: flexible-layouts блоки в JSON, рендер через существующий `BlockRenderer` (`article-{name}` → `blocks/article/{name}.blade.php`).

Соответствует `.ai-factory/ARCHITECTURE.md`: модуль Content планирует `BlogController`, `BlogService`, сущность `Article`.

Ключевые решения:

1. **Сущности**: `articles` (slug, is_published, published_at, cover_pc, cover_mb) + `article_translations` (locale, title, tag, excerpt, SEO-поля, content-блоки) — зеркально паре `pages`/`page_translations`.
2. **Роуты фиксированные**: `/blog` → `BlogController::index`, `/blog/{slug}` → `BlogController::show` (заменяют прототипные замыкания, стоят над catch-all). Обоснованное отступление от правила «страницы только через catch-all» (.ai/rules/routes.md): блог — entity-модуль с листингом (как будущий catalog), а не контентная страница. Slug `blog` для DB-страниц не резервировать.
3. **Листинг без серверной пагинации**: все опубликованные статьи + существующий Alpine `blog()` show-more (6 → +3). Для pre-MVP достаточно; серверная пагинация — будущее улучшение.
4. **Блоки статьи** (`article-*`, категория «Статья»): `article-content` (EditorJS-документ, как `returns-content`), `article-gallery` (слайдер изображений), `article-cta` (кнопка с двухтиповой ссылкой), `article-related` (динамические «Другие новости»: редактируется только заголовок, статьи тянет `BlogService` — паттерн `home-categories`).
5. **Обложки** (`cover_pc`, `cover_mb`) — на статье, не в переводе (картинки не переводятся); OG-изображение — в переводе, как у страниц.
6. **MediaFallback** для контента переводов статей с `ArticleBlockLibrary::mediaSchemas()` — пустые медиа непереведённой локали наследуются из дефолтной.
7. **SEO** — копия подхода `PageController::seo()` (hreflang `/blog/{slug}` ↔ `/en/blog/{slug}`), для листинга — lang-ключи.

## Существующее состояние

| Файл | Статус | Действие |
|---|---|---|
| `routes/web.php:19,115` | ✅ прототипные роуты `/blog`, `/blog/{slug}` (замыкания) | **Заменить** на `[BlogController::class, ...]` |
| `resources/views/blog.blade.php` | ✅ wrapper → `blocks.blog.blog` | Оставить (wrapper) |
| `resources/views/blocks/blog/blog.blade.php` | ⚠️ захардкоженный `$news` (9 карточек) | **Переписать** на `$articles` из БД |
| `resources/views/article.blade.php` | ✅ wrapper → статический include | **Переписать** по образцу `page.blade.php` (hero + блоки + крошки) |
| `resources/views/blocks/article/article.blade.php` | ⚠️ статический прототип статьи | **Удалить** (заменяют блоки `article-*`) |
| `resources/css/blocks/blog/style.css`, `resources/css/blocks/article-page/style.css` | ✅ готовы | Без изменений (переиспользуем) |
| `resources/js/blocks/blog/index.js` | ✅ Alpine show-more | Без изменений |
| `lang/{ru,en}/store.php` (`article_*`) | ⚠️ тексты прототипа | Тексты уйдут в сидер; ключи **удалить** после |
| `app/Support/PageBlocks/BlockRenderer.php` | ✅ универсальный | Без изменений (`article-content` → `blocks.article.content` уже работает) |
| MoonShine-админка | ❌ ресурсов статей нет | **Создать** ArticleResource + переводы |

Эталоны для копирования: `PageController` (поиск/404/SEO/MediaFallback), `FaqPageSeeder` (fill-when-empty), `FaqPageTest` (beforeEach), `PageResource`/`PageTranslationResource` (форма), `HomeNewsBlock`/`FaqItemsBlock` (классы блоков), `ReturnsContentBlock` (EditorJS-блок), `LoyaltyHeroBlock` (кнопка-ссылка через BuildsLinkFields).

## Commit Plan

- **Commit 1** (после задач 1–2): `feat(blog): articles and article_translations migrations, models, factories`
- **Commit 2** (после задач 3–4): `feat(blog): article content blocks and storefront views`
- **Commit 3** (после задач 5–6): `feat(blog): BlogController with listing and article routes`
- **Commit 4** (после задачи 7): `feat(blog): MoonShine ArticleResource admin CRUD`
- **Commit 5** (после задач 8–10): `feat(blog): demo seeder, feature tests, prototype cleanup`

## Tasks

### Phase 1: Данные

- [x] **Task 1: Миграции `articles` + `article_translations`**
  Создать через `php artisan make:migration --no-interaction`:
  - `create_articles_table`: `id`, `slug` (unique), `is_published` (bool, default false), `published_at` (date, NOT NULL), `cover_pc` (string, nullable), `cover_mb` (string, nullable), `timestamps`.
  - `create_article_translations_table`: `id`, `foreignId('article_id')->constrained()->cascadeOnDelete()`, `locale` (string(12), index), `title`, `tag` (string, nullable), `excerpt` (text, nullable), SEO-поля и типы — точно как в `2026_09_12_144317_add_seo_fields_to_page_translations_table.php` (meta_title, meta_description, meta_keywords, meta_robots, canonical_url, og_image) + `content` (json, nullable), `timestamps`, `unique(['article_id', 'locale'])`.
  Файлы: `database/migrations/*_create_articles_table.php`, `database/migrations/*_create_article_translations_table.php`
  Логирование: не требуется (миграции). Проверка: `php artisan migrate --pretend` + `php artisan migrate`.

- [x] **Task 2: Модели `Article` + `ArticleTranslation` + фабрики**
  `app/Models/Article.php` — копия паттерна `Page`: `#[Fillable([...])]`, `translations(): HasMany`, `scopePublished`, `translation(?string $locale)` с фолбэком requested → default → first (как `Page::translation()`), `casts()` (is_published bool, published_at date).
  `app/Models/ArticleTranslation.php` — копия паттерна `PageTranslation`: `article(): BelongsTo`, `metaRobots()`, `casts()` (content array).
  Фабрики `ArticleFactory` (+ state `draft()`, как у `PageFactory`) и `ArticleTranslationFactory` (+ states `ru()`/`en()`/`withBlocks()`, как у `PageTranslationFactory`; в definition — tag/excerpt/SEO-поля).
  Файлы: `app/Models/Article.php`, `app/Models/ArticleTranslation.php`, `database/factories/ArticleFactory.php`, `database/factories/ArticleTranslationFactory.php`
  Логирование: не требуется (пассивные модели). Проверка: `php -l` на каждом файле.

### Phase 2: Блоки статьи

- [x] **Task 3: Классы блоков `article-*` + `ArticleBlockLibrary`** (depends on 2)
  Каталог `app/Support/PageBlocks/Blocks/Article/`, каждый класс `implements PageBlock` (по образцу `FaqItemsBlock`/`HomeNewsBlock`; все Text/Textarea — `->escapeOnApply(static fn (): bool => false)`):
  - `ArticleContentBlock` → тип `article-content`: поле `Sckatik\MoonshineEditorJs\Fields\EditorJs` («Текст статьи», `text`). Без limit (можно чередовать с галереей). Категория «Статья».
  - `ArticleGalleryBlock` → `article-gallery`: `FlexibleLayouts::make('Изображения', 'items')->block('item', ...)` с `MediaManagerPicker` (`image`, allowedExtensions jpg/jpeg/png/webp/svg).
  - `ArticleCtaBlock` → `article-cta`: текст кнопки + двухтиповая ссылка «страница/кастом» через `BuildsLinkFields` — скопировать механику из `LoyaltyHeroBlock` (условная видимость полей уже обеспечена `/vendor/bronber/link-type-visibility.js`). limit 1.
  - `ArticleRelatedBlock` → `article-related`: только `Text` «Заголовок секции» (по умолчанию «Другие новости»); список статей динамический. limit 1.
  `app/Support/PageBlocks/ArticleBlockLibrary.php` — брат-близнец `PageBlockLibrary`: `article(): FlexibleLayouts` регистрирует 4 блока; `mediaSchemas(): array` → `['article-gallery' => ['items' => ['image']]]`.
  Файлы: `app/Support/PageBlocks/Blocks/Article/{ArticleContentBlock,ArticleGalleryBlock,ArticleCtaBlock,ArticleRelatedBlock}.php`, `app/Support/PageBlocks/ArticleBlockLibrary.php`
  Логирование: не требуется (декларации полей). Проверка: `php -l`.

- [x] **Task 4: Вьюхи статьи** (depends on 3)
  - **Переписать** `resources/views/article.blade.php` по образцу `page.blade.php`: крошки (`x-breadcrumbs`), hero (`cover_mb`/`cover_pc` через `<x-img>` из полей статьи, как в прототипе: два `<x-img>` с классами `article-page-block__image`), дата (`published_at->format('d/m/Y')`), `<h1>` из перевода, затем `{!! resolve(\App\Support\PageBlocks\BlockRenderer::class)->render($translation->content ?? [], 'article') !!}`. Рендер блоков именно во вью (не в контроллере) — @push('block-styles') выживают только так (см. комментарий в `page.blade.php`).
  - **Создать** `resources/views/blocks/article/content.blade.php` — EditorJS-рендер по образцу вью блока `returns-content` (`RenderEditorJs`), разметка/классы прототипа (`article-page-block__content`).
  - **Создать** `resources/views/blocks/article/gallery.blade.php` — `<x-slider>` perView 1/2 (скопировать конфиг из прототипа), элементы `items` c `<x-img :path="$item['image']">`.
  - **Создать** `resources/views/blocks/article/cta.blade.php` — `<x-btn variant="primary">` со ссылкой, разрешённой через `LinkResolver` (тип page/custom, как в блоках loyalty).
  - **Создать** `resources/views/blocks/article/related.blade.php` — секция «Другие новости»: `<x-slider>` perView 1/2/3 с карточками `.article` из `$related` (передаёт контроллер); пустой `$related` → секция не выводится.
  - **Удалить** `resources/views/blocks/article/article.blade.php` (статический прототип).
  Ссылки в карточках — локализованные (правило .ai/rules/views.md): default → `url('/blog/'.$slug)`, прочие → `url('/'.$locale.'/blog/'.$slug)`; default-локаль через `LanguageService::defaultCode()`, не хардкод ru.
  Файлы: `resources/views/article.blade.php` (rewrite), `resources/views/blocks/article/{content,gallery,cta,related}.blade.php` (create), `resources/views/blocks/article/article.blade.php` (delete)
  Логирование: не требуется (вью). Проверка: смоук `curl http://bronber_store.test/blog/{slug}` после задачи 5; npm-билд не нужен (новых CSS/JS-энтри нет).

### Phase 3: Публичная часть

- [x] **Task 5: `BlogService` + `BlogController` + роуты** (depends on 2)
  `app/Services/Content/BlogService.php` (каталог Content — по ARCHITECTURE.md):
  - `published()`: `Article::query()->published()->with('translations')->orderByDesc('published_at')->get()` + `Log::debug('[BlogService.published] count={count}')`.
  - `related(Article $exclude, int $limit = 3)`: опубликованные кроме текущей, top по дате + `Log::debug`.
  `app/Http/Controllers/BlogController.php` (рядом с `PageController`, root-уровень Controllers — по фактическому расположению sibling):
  - `index()`: `$articles = resolve(BlogService::class)->published();` → `view('blog', ['articles' => $articles])`; SEO через `view()->share('seo', ...)` — title/description из новых lang-ключей `store.blog_title` / `store.blog_meta_description`, hreflang `/blog` ↔ `/en/blog` (копия структуры `PageController::seo()`); `Log::debug('[BlogController.index] locale={locale} articles={count}')`.
  - `show(string $slug)`: опубликованная статья по slug c translations; нет статьи/перевода → `Log::debug('[BlogController.show] slug={slug} found=false')` + `abort(404)`; контент через `MediaFallback::apply($translation->content ?? [], $defaultContent, ArticleBlockLibrary::mediaSchemas())` (фолбэк контента дефолтной локали — копия `PageController::defaultContent()`); SEO по переводу (hreflang `/blog/{slug}` ↔ `/en/blog/{slug}`); крошки: Главная → Блог (локализованный url) → заголовок статьи; `view('article', ['article' => ..., 'translation' => ..., 'related' => BlogService::related($article)])`.
  `routes/web.php` — в `$register` заменить два замыкания:
  ```php
  Route::get('/blog', [BlogController::class, 'index'])->name('blog');
  // ...
  Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('article');
  ```
  (позиции над catch-all сохранить; имена `blog`/`article` — как в прототипе).
  Файлы: `app/Services/Content/BlogService.php`, `app/Http/Controllers/BlogController.php`, `routes/web.php`
  Логирование: DEBUG-логи в index/show/related по формату `[BlogController.show] slug={slug} locale={locale} found={bool}` (образец — PageController).

- [x] **Task 6: Листинг `/blog` из БД** (depends on 5)
  Переписать `resources/views/blocks/blog/blog.blade.php`: убрать `@php $news = [...] @endphp`; цикл по `$articles` — карточка `.article` (разметка как сейчас): `<x-img :path="$article->cover_pc"` (альт — title перевода), tag/date (`published_at->format('d/m/y')`)/title/desc (excerpt) из `$article->translation()`; href — локализованная ссылка на статью (правило views.md). Alpine `blog()` show-more и `@push` vite-энтри не трогать. Заголовок `<h1>` — `__('store.blog_title')`.
  Файлы: `resources/views/blocks/blog/blog.blade.php` (rewrite), `lang/ru/store.php` + `lang/en/store.php` (+`blog_title`, `blog_meta_title`, `blog_meta_description`)
  Логирование: не требуется (вью).

### Phase 4: Админка MoonShine

- [ ] **Task 7: `ArticleResource` + ресурсы переводов + регистрация** (depends on 3)
  - `app/MoonShine/Resources/Article/ArticleResource.php` — копия `PageResource`: `#[Icon('newspaper')]` (**предварительно проверить**: `ls vendor/moonshine/moonshine/src/UI/resources/views/icons/ | grep newspaper`, иначе 500 всей админки — .ai/rules/moon-shine.md; запасной вариант `document-text`), `#[Group('content')]`, `#[Order(15)]`, `$column = 'slug'`, `search(): ['slug']`, `activeActions()->except(Action::VIEW)`, `afterSave` — создать недостающие переводы на каждый язык (`title = $article->slug`) + `Log::info('[ArticleResource] article_id={id} saved by moonshine_user_id={uid}')`.
  - `app/MoonShine/Resources/Article/Pages/ArticleFormPage.php` — Tabs по образцу `PageFormPage`: таб «Статья» (slug с regex-валидацией как у страниц, `published_at` — `MoonShine\UI\Fields\Date`, Switcher «Опубликована», `cover_pc`/`cover_mb` — `MediaManagerPicker` allowedExtensions jpg/jpeg/png/webp); таб «Переводы» (`HasMany` → `ArticleTranslationResource`, `creatable()`).
  - `app/MoonShine/Resources/Article/Pages/ArticleIndexPage.php` — по образцу `PageIndexPage` (колонка slug, статус публикации, published_at).
  - `app/MoonShine/Resources/ArticleTranslation/ArticleTranslationResource.php` + `Pages/ArticleTranslationFormPage.php` — копия `PageTranslationResource`/`PageTranslationFormPage`: locale (unique на статью), title, tag, excerpt, SEO-поля + og_image, в конце — `ArticleBlockLibrary::article()` вместо `PageBlockLibrary::page()`; все Text/Textarea с `escapeOnApply(false)` (.ai/rules/app.md).
  - Регистрация: `app/Providers/MoonShineServiceProvider.php` (+`ArticleResource::class`, `ArticleTranslationResource::class`), `app/MoonShine/Layouts/MoonShineLayout.php` меню — `MenuItem::make(ArticleResource::class, 'Статьи')` после «Страницы» (итог в меню: «Блог → Статьи» как пункт раздела контента).
  Файлы: `app/MoonShine/Resources/Article/**`, `app/MoonShine/Resources/ArticleTranslation/**`, `app/Providers/MoonShineServiceProvider.php`, `app/MoonShine/Layouts/MoonShineLayout.php`
  Логирование: INFO в afterSave (id, uid) — как PageResource.

### Phase 5: Контент, тесты, очистка

- [ ] **Task 8: `BlogArticlesSeeder`** (depends on 7)
  `database/seeders/BlogArticlesSeeder.php` — по образцу `FaqPageSeeder` (.ai/rules/seeders.md): `firstOrCreate` 6 статей (3 уникальные × 2 для show-more, даты по убыванию, обложки `/images/blog/{1,2,3}.jpg`, hero `/images/blog/hero.jpg` + `hero-mb.jpg`); переводы ru/en: `firstOrCreate` по (article_id, locale), fill-when-empty для пустого content, авторский контент не перезаписывать; тексты — из текущих `lang/{ru,en}/store.php` `article_*`; контент-блоки: `article-content` (EditorJS-документ: lead + подзаголовки + абзацы из прототипа `blocks/article/article.blade.php`), `article-gallery` (2–4 изображения), `article-cta` (кнопка «Перейти к направлению»), в одной статье — `article-related`. `Log::info('[BlogArticlesSeeder] seeded, locales={locales}')` per-locale (`:created`/`:exists`/`:filled`). Зарегистрировать в `DatabaseSeeder` (посмотреть, как зарегистрированы FaqPageSeeder и др.).
  Файлы: `database/seeders/BlogArticlesSeeder.php`, `database/seeders/DatabaseSeeder.php`

- [ ] **Task 9: Фич-тесты `BlogTest`** (depends on 5, 8)
  `tests/Feature/BlogTest.php` (Pest, `php artisan make:test --pest --no-interaction BlogTest`), beforeEach по .ai/rules/tests.md: `resolve(LanguageService::class)->clearCache()` + фабрики языков ru(default)/en. Хелпер `blogArticle()` по образцу `faqPage()`: Article + переводы ru/en c блоками (`article-content`, `article-gallery`, `article-cta`, `article-related`):
  - `/blog` рендерит листинг из БД (карточка: title, tag, дата, excerpt);
  - `/en/blog` — en-перевод карточек;
  - `/blog/{slug}` рендерит блоки (текст EditorJS, галерея, кнопка) + «Другие новости»;
  - несуществующий slug → 404; черновая статья (`ArticleFactory::draft()`) → 404 и не попадает в листинг;
  - en-перевод без контента → рендер с ru-контентом (MediaFallback/translation fallback);
  - SEO-теги статьи (title, description, canonical, hreflang ru/en).
  Файлы: `tests/Feature/BlogTest.php`
  Прогон: `php artisan test --compact tests/Feature/BlogTest.php`.

- [ ] **Task 10: Очистка прототипа + финализация** (depends on 9)
  - Удалить неиспользуемые ключи `article_*` из `lang/ru/store.php` и `lang/en/store.php` (тексты теперь в сидере; перед удалением grep по репозиторию на каждый ключ).
  - Смоук: `php artisan db:seed --class=BlogArticlesSeeder`, `curl http://bronber_store.test/blog`, `curl http://bronber_store.test/blog/{slug-seeded}`, `curl http://bronber_store.test/en/blog`.
  - `php vendor/bin/pint --dirty --format agent` (правило .ai/rules/general.md — бинарники без exec-бита, запускать через `php`).
  - Полный прогон затронутых тестов: BlogTest + существующие страницы (`php artisan test --compact tests/Feature/`).
  - Docs-чекпоинт (Docs: yes): обновить `.ai-factory/DESCRIPTION.md` — модуль блога в «Реализованные возможности» (сущности, блоки article-*, роуты, ресурс админки, сидер), при необходимости `docs/` через /aif-docs.

## Риски и заметки

- **Иконка админки**: несуществующее имя в `#[Icon]` → 500 всех страниц админки. Проверка `ls .../icons/ | grep newspaper` обязательна до регистрации (правило .ai/rules/moon-shine.md).
- **escapeOnApply(false)**: каждый контентный Text/Textarea в блоках и форме переводов — иначе многослойное HTML-экранирование в БД (правило .ai/rules/app.md).
- **Порядок роутов**: `/blog*` обязательно выше catch-all `/{slug}` (внутри `$register` позиции сохранить).
- **Reserved slug**: не создавать DB-страницу со slug `blog` (фиксированный роут её перекроет).
- **npm-билд не нужен**: новые вью переиспользуют уже собранные CSS/JS-энтри (`blocks/blog/style.css`, `blocks/article-page/style.css`, `blocks/blog/index.js`).
- **В IE11-совместимости** ничего не меняется (нового JS нет).
