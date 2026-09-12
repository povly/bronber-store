# Implementation Plan: Настройки шапки/подвала — блоки под реальную статику + ссылки (страница/кастом)

Branch: main (без создания ветки — `git.create_branches: false`)
Created: 2026-09-12
Supersedes: частично Task 8 плана feature-pages-blocks-settings (настройки шапки/подвала переработаны с generic-блоков на поля реального макета)

## Original Request

теперь давай настраивай настройки для шапки, и для подвала http://bronber_store.test/ у нас там header.blade.php footer.blade.php вроде! и настрой все туда нужные поля и данные! и если там есть ссылки, сделать по возможности типо список ссылок(пока из страниц) или кастомная ссылка. И разделяй логику, допустим если блоки добавим - сохрани куда-то Blocks Moonshine чтобы не засирать в одном коде) типо принцип solid

## Settings

- Testing: yes (Pest, feature-тесты)
- Logging: verbose (DEBUG на разработке; только ключи/локали/slug, без PII)
- Docs: yes → обязательный docs-чекпоинт через `/aif-docs`

## Архитектура (итог)

### Ключевые решения

1. **Подстановка данных вместо полной замены вёрстки.** Текущий механизм (`header--custom` + pb-* вьюхи) заменял шапку целиком и терял поиск/каталог-меню/Alpine. Новый подход: `SettingsResolver` превращает JSON настроек в плоские структуры (списки ссылок с готовыми href), а `header.blade.php` / `top-bar.blade.php` / `footer.blade.php` подставляют их в существующую вёрстку через `@if(!empty(...))` со статикой как fallback. Дизайн и JS не трогаем.
2. **Составная ссылка двух типов** (ядро запроса): `{label, type: 'page'|'custom', page: slug|null, url: string|null}`.
   - `page` → href строится локале-осознанно: дефолтная локаль `/{slug}`, прочие `/{locale}/{slug}` (локаль из текущего контекста рендера);
   - `custom` → URL как есть (относительные пути админ пишет сам; кавеат задокументировать);
   - в админке без showWhen внутри Json (надёжность): видны оба поля + hint «заполните страницу ИЛИ URL».
3. **SOLID-разделение библиотек блоков** (явное требование): вместо одного `PageBlockLibrary` — по классу на контекст + переиспользуемый строитель полей ссылки:
   ```
   app/Support/PageBlocks/
   ├── PageBlockLibrary.php        # только page-блоки (hero/text/gallery/faq/contacts/featured)
   ├── HeaderBlockLibrary.php      # top-bar, nav
   ├── FooterBlockLibrary.php      # contacts, socials, links-column, bottom
   ├── Concerns/BuildsLinkFields.php  # trait: linkFields(string $prefix) → [label, type, page, url]
   ├── LinkResolver.php            # JSON-ссылка → href (page|custom, локаль)
   ├── SettingsResolver.php        # settings JSON → структуры для вьюх (topBar, nav, footer...)
   └── BlockRenderer.php           # без изменений (страничные блоки)
   ```
4. **Блоки шапки** (`settings.key=header`): `top-bar` (limit 1: phone + links), `nav` (limit 1: links). Кнопка «Каталог», поиск, действия (избранное/профиль/корзина), логотип, переключатель языков — остаются статическими (не настройки).
5. **Блоки подвала** (`settings.key=footer`): `contacts` (limit 1: phone, email), `socials` (limit 1: список {platform, url}), `links-column` (многократно: title + links — для 3 колонок), `bottom` (limit 1: privacy {label,url}, terms {label,url}, copyright, developer {label,url}). Оплата/кнопка входа — статические.
6. **Опции страниц** для поля `page`: `PageOptions::published()` — опубликованные страницы `slug → title` (локаль текущая → fallback дефолтного языка), per-request статический кэш. Удаление/снятие с публикации → ссылка не резолвится → пункт пропускается с WARN.
7. **Старый generic-механизм удаляется**: `header()/footer()` из `PageBlockLibrary`, pb-nav/pb-header-contacts/pb-footer-col/pb-socials/pb-copyright вьюхи, `header--custom`/`footer--custom` ветки в blade, полная замена в composer. Страничные pb-* блоки остаются.

### Формат ссылки (JSON)

```json
{"label": "Доставка", "type": "page", "page": "delivery", "url": null}
{"label": "Вакансии", "type": "custom", "page": null, "url": "#!"}
```

## Commit Plan

- **Commit 1** (после задач 1–2): `feat: link fields and header footer block libraries`
- **Commit 2** (после задач 3–4): `feat: settings resolver and static markup wiring`
- **Commit 3** (после задач 5–6): `feat: default header footer seeder and docs`

## Tasks

### Phase 1: Поля и библиотеки

- [x] Task 1: LinkResolver + BuildsLinkFields + PageOptions (depends: —)
  `LinkResolver::href(array $link, ?string $locale = null): ?string` — page → `url("/{slug}")` / `url("/{locale}/{slug}")`; custom → url; некорректная ссылка (нет type/нет данных) → null + `Log::warning('[LinkResolver] invalid link skipped')`. `Concerns/BuildsLinkFields::linkFields(string $prefix = '')` — MoonShine-поля: label (Text, required), type (Select: Страница/Кастом), page (Select из `PageOptions::published()`, hint «Опубликованные страницы»), url (Text, hint «Для кастомной ссылки; относительные пути пишите с учётом локали»). `PageOptions::published(): array<string,string>` со статическим per-request кэшем. Тесты: href для page/custom/кривых ссылок, локали, options из published-страниц.
  LOGGING: WARN на невалидную ссылку, DEBUG `[PageOptions] loaded n={count}`.
  Files: `app/Support/PageBlocks/LinkResolver.php`, `app/Support/PageBlocks/Concerns/BuildsLinkFields.php`, `app/Support/PageBlocks/PageOptions.php`, `tests/Feature/LinkResolverTest.php`.

- [x] Task 2: HeaderBlockLibrary + FooterBlockLibrary (depends: 1)
  `HeaderBlockLibrary::header(): FlexibleLayouts` → блоки `top-bar` (phone, links Json через linkFields, limit 1, category «Шапка») и `nav` (links, limit 1). `FooterBlockLibrary::footer(): FlexibleLayouts` → `contacts` (phone, email; limit 1), `socials` (список {platform, url}; limit 1), `links-column` (title, links; многократно, limit 3), `bottom` (privacy label+url, terms label+url, copyright, developer label+url; limit 1). Обновить `SettingFormPage` → новые классы. Удалить `header()/footer()` из `PageBlockLibrary` (page() остаётся). Тесты: состав блоков каждой библиотеки, форма настроек рендерится (header/footer ключи), сохранение ссылок через форму.
  LOGGING: n/a (конфигурация полей).
  Files: `app/Support/PageBlocks/HeaderBlockLibrary.php`, `app/Support/PageBlocks/FooterBlockLibrary.php`, `app/MoonShine/Resources/Setting/Pages/SettingFormPage.php`, `app/Support/PageBlocks/PageBlockLibrary.php`, `tests/Feature/SettingResourceTest.php` (дополнить).

### Phase 2: Резолвинг и вёрстка

- [x] Task 3: SettingsResolver (depends: 2)
  `SettingsResolver::header(): array{topBar: ?array, nav: ?array}` и `::footer(): array{contacts, socials, columns: list, bottom}` — читают `SettingService::get`, прогоняют ссылки через `LinkResolver` (локаль текущая), пропускают пустые, отдают плоские структуры для вьюх: `topBar = ['phone' => ..., 'links' => [['label','href'],...]]`, `columns[] = ['title', 'links']` и т.д. Пусто/нет настроек → все ключи null/[] (вьюхи показывают статику). Тесты: полный резолв, fallback, пропуск битых ссылок, page-ссылка локализуется.
  LOGGING: `Log::debug('[SettingsResolver] context={header|footer} blocks={n} links={m}')`.
  Files: `app/Support/PageBlocks/SettingsResolver.php`, `tests/Feature/SettingsResolverTest.php`.

- [x] Task 4: Подстановка в статику + демонтаж старого механизма (depends: 3)
  `AppServiceProvider` composer: вместо `headerBlocksHtml/footerBlocksHtml` отдавать `SettingsResolver` структуры (`$headerSettings`, `$footerSettings`). `top-bar.blade.php`: телефон + цикл ссылок из `topBar`, `@else` статика. `header.blade.php` nav: цикл из `nav` (ссылка «Каталог» остаётся статической перед циклом), `@else` статика. `footer.blade.php`: контакты, соцсети, колонки (аккордеон-разметка сохраняется, цикл по `columns`), низ (privacy/terms/copyright/developer) — аналогично с `@else` статикой. Удалить: `header--custom`/`footer--custom` ветки, вьюхи `components/page-blocks/header/**`, `footer/**`, соответствующие pb-классы из `page-blocks/style.css` (страничные pb-* не трогать). Обновить `LayoutSettingsTest`: настройка задана → данные из БД в статической вёрстке; не задана → статика; page-ссылка → локализованный href.
  LOGGING: composer логирует source=settings|fallback (как раньше).
  Files: `app/Providers/AppServiceProvider.php`, `resources/views/blocks/common/top-bar/top-bar.blade.php`, `resources/views/blocks/common/header/header.blade.php`, `resources/views/blocks/common/footer/footer.blade.php`, `resources/views/components/page-blocks/header/**` (удалить), `.../footer/**` (удалить), `resources/css/blocks/page-blocks/style.css`, `tests/Feature/LayoutSettingsTest.php`.

### Phase 3: Данные и финализация

- [x] Task 5: SettingsSeeder с дефолтами из статики (depends: 4)
  Наполнить `value` для header/footer × активные языки контентом, эквивалентным текущей статике: top-bar (телефон + доставка/гаранция/FAQ/контакты как custom `/delivery` и т.д., вакансии `#!`), nav (новинки/блог → custom `/blog`, акции `#`, бонусы `/loyalty`, о нас `/about`), footer: контакты (телефон, email), соцсети (Instagram/YouTube `#`), 3 колонки (Каталог — custom-ссылки `/catalog`; Покупателям; Компания), bottom (privacy/terms `#`, копирайт, разработчик). EN-варианты — те же структуры с английскими label. Re-runnable (upsert не перезатирает непустые value — upsert только update updated_at, как сейчас). Тесты: сидер наполняет значения, повторный запуск не дублирует/не затирает.
  LOGGING: n/a.
  Files: `database/seeders/SettingsSeeder.php`, `tests/Feature/SettingResourceTest.php` (дополнить).

- [ ] Task 6: Docs-чекпоинт + финальные проверки (depends: all)
  `/aif-docs`: обновить `lang/docs/admin-panel.md` (новые блоки шапки/подвала, поле ссылки двух типов, SOLID-структура библиотек) и `lang/docs/architecture.md` (SettingsResolver-конвейер, подстановка в статику вместо полной замены). Финальный прогон: `vendor/bin/pint --dirty --format agent`, `vendor/bin/pest`, **`vendor/bin/rector process --dry-run` → 0 изменений** (CI-требование!), `php artisan moonshine:resources`.
  LOGGING: n/a.
  Files: `lang/docs/admin-panel.md`, `lang/docs/architecture.md`.

## Что НЕ входит в этот план (осознанно)

- **Каталог-меню, поиск, действия (избранное/профиль/корзина), логотип, переключатель языков** — остаются кодом: это функциональные элементы, не контент.
- **Колонка «Каталог» подвала из категорий CRM** — сейчас custom-ссылки на `/catalog`; заменится на источник категорий вместе с CRM-складом.
- **Локализация custom-ссылок** — page-ссылки локализуются автоматически; custom-URL админ задаёт явно (per-locale настройки уже это поддерживают).
- **showWhen-условия в Json** — не используем (надёжность внутри flexible-layouts); поля page и url видны одновременно с hint.
