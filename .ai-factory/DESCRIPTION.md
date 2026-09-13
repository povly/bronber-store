# bronber-store

## Обзор

Русскоязычный интернет-магазин автозапчастей (топливные насосы, компоненты для BMW/Audi/VW). Проект находится на стадии **pre-MVP**: фронтенд реализован как статический прототип на Blade + Alpine.js с захардкоженными данными в `routes/web.php`, MoonShine-админка заскаффолжена, но доменных моделей и ресурсов пока нет.

Главная локаль — русская (`ru`), дополнительная — английская (`en`). Валюта — российский рубль (₽).

## Текущие возможности (frontend-прототип)

- Витрина: главная, каталог, карточка товара, отзывы
- Корзина и оформление заказа (mock-данные)
- Контентные страницы: блог, статьи, FAQ, контакты, о компании
- i18n через кастомный `SetLocale` middleware + дублирование роутов по локали
- Ленивая загрузка изображений (vanilla-lazyload)

## Реализованный модуль Content (MVC)

- **DB-страницы**: slug + публикации + переводы per-locale (таблицы `pages`, `page_translations`),
  контент — блоки flexible-layouts (JSON, `_type`), рендер `BlockRenderer` →
  `blocks/{page}/{name}.blade.php` (тип `{page}-{name}`: `home-partners` ↔ `blocks/home/partners`;
  legacy-demo типы → `blocks/legacy/`);
  единственный фиксированный роут — `/` (главная, slug `index`); остальные страницы
  (faq, delivery, …) обслуживает catch-all `/{slug}` / `/{locale}/{slug}`: нет
  опубликованной страницы/перевода → 404 (fallback на статический прототип убран)
- **Иерархия страниц и хлебные крошки**: `pages.parent_id` (self-reference, админка — селект
  «Родительская страница» с защитой от циклов); автоматические крошки на уровне вью страницы —
  `PageBreadcrumbs` строит «Главная → опубликованные предки → текущая» через `LinkResolver`
  (автолокализация `/en/...`), JSON-LD BreadcrumbList; на главной крошки скрыты
- **Страница FAQ из БД**: фиксированный роут `/faq` → `PageController::faq` (slug `faq`), блок
  `faq-items` (заголовок + аккордеон вопрос/ответ);
  демо-контент — `FaqPageSeeder` (ru/en, идемпотентный, заполняет только пустые переводы)
- **Страница «Доставка и оплата» из БД**: фиксированный роут `/delivery` → `PageController::delivery`
  (slug `delivery`), блоки `delivery-methods` (заголовок + карточки:
  иконка/заголовок/описание) и `contact-list` (заголовок + контакты: иконка/текст/ссылка tel:|mailto:|URL);
  демо-контент — `DeliveryPageSeeder` (ru/en, идемпотентный, тексты из lang-файлов прототипа)
- **Полный SEO-набор** пер-локали: meta title/description/keywords, robots, canonical,
  OG/Twitter, hreflang-альтернативы + x-default
- **Редактируемые языки** (`languages`): `LanguageService` (кэш) — источник локалей для
  middleware, роутов, автосоздания переводов и fallback (дефолтный язык, не хардкод ru)
- **Шапка/подвал/мобильные меню из настроек** (`settings` key × locale): блоки редактируются в MoonShine
  (`HeaderBlockLibrary`/`FooterBlockLibrary`/`MobileMenuBlockLibrary`/`MobileNavBlockLibrary`), ссылки двух
  типов — страница сайта (автолокализация `/en/...`) или кастомный URL (`LinkResolver`), изображения —
  через MediaManagerPicker; `SettingsResolver` подставляет данные в статическую вёрстку шапки/подвала и
  мобильных меню (drawer `mobile-menu` + нижняя панель `mobile-nav`; функциональные элементы — кнопка
  «Каталог», переключатель языка — остаются в коде), пусто → статика прототипа
- Catch-all `/{slug}` / `/{locale}/{slug}` в конце `routes/web.php` (фиксированные роуты не перекрыты)

## Запланированные возможности

- Доменные модели: Product, Category, Brand, Order, Review
- Перенос mock-данных в БД (SQLite/MySQL)
- CRUD-ресурсы MoonShine для управления каталогом
- Реальная логика корзины и оформления заказа
- Аутентификация клиентов и история заказов

## Технический стек

- **Язык:** PHP 8.5
- **Фреймворк:** Laravel 13.8
- **Админ-панель:** MoonShine 4.15 (+ `povly/moonshine-flexible-layouts` — блоки контента страниц, `sckatik/moonshine-editorjs` — текстовый редактор, `povly/moonshine-image-editor` (VCS dev-main) — оптимизация/конвертация изображений, `yurizoom/moonshine-media-manager`)
- **Обработка изображений:** Intervention Image 4.3 + `intervention/image-laravel` 4.1 (обязательна v4: `laravel/framework` 13.x `Illuminate\Image` требует `ImageManager::usingDriver()`)
- **БД:** SQLite (default) / MySQL (сконфигурирован)
- **Frontend JS:** Alpine.js 3.15 + vanilla-lazyload
- **CSS:** PostCSS (mixins, nested, simple-vars, кастомные функции `fluidType`, `pxToVw`)
- **Сборка:** Vite 8 + LightningCSS + Babel (`@babel/preset-env` для совместимости с IE11/iOS9)
- **Тестирование:** Pest 4.7 + `pestphp/pest-plugin-laravel` 4.1
- **Качество кода:** Laravel Pint 1.27, Rector Laravel 2.5 (`driftingly/rector-laravel`)
- **Отладка:** Laravel Debugbar 4.3, Laravel Pail 1.2, Laravel PAO 1.0
- **AI-интеграция:** Laravel Boost 2.2 (MCP + гайдлайны + скиллы)
- **Тинкер:** Laravel Tinker 3.0

## Архитектурные заметки

- **Frontend-first MVC**: переход от статического прототипа к полному MVC. Следующая фаза — доменные модели и миграции.
- **Block-based фронтенд**: CSS, JS и Blade-шаблоны организованы постранично в каталогах `resources/{css,js,views}/blocks/{page}/`. Это кастомный component-подобный паттерн, не Livewire/Inertia.
- **Closure-based роуты**: вся логика страниц — в `routes/web.php` через замыкания (без классов-контроллеров). При миграции на MVC заменить на ресурс-контроллеры.
- **i18n-паттерн**: роуты регистрируются один раз в `$register`-замыкании, затем дублируются для каждой локали через префикс `/{locale}` (кроме дефолтной `ru`).
- **MoonShine v4 namespace split**: ресурсы используют `MoonShine\Laravel\Resources\ModelResource`, поля — `MoonShine\UI\Fields\*`. Не путать с v3-неймспейсами.
- **Совместимость браузеров**: консервативные таргеты (IE11, iOS 9+, Android 4.4+) через Babel + LightningCSS. Избегать современного JS-API без полифиллов.

## Нефункциональные требования

- **Локализация**: двуязычность ru/en через `lang/{locale}/` + middleware; тексты в шаблонах через `__()`
- **Совместимость с браузерами**: IE11+, iOS 9+, Android 4.4+ — критичное требование для целевой аудитории
- **Валюта**: рубль (₽), цены в целых числах, форматирование с пробелом как разделителем тысяч
- **Безопасность**: следовать глобальным security-правилам (whitelist полей в API, no mass-assignment, no secrets в коде, rate limiting на публичных эндпоинтах)
- **Качество кода**: `vendor/bin/pint --dirty --format agent` после каждого изменения PHP; `declare(strict_types=1)` во всех новых классах
- **Логирование**: логи не должны содержать токены, пароли, PII; использовать структурированные error responses без stack traces в production

## Архитектура

Подробные архитектурные принципы и правила зависимостей — в `.ai-factory/ARCHITECTURE.md`.
**Паттерн:** Structured Modules (Technical Layer) — модули по фичам (Catalog, Orders, Reviews, Content) с техническими слоями внутри (Controllers → Services → Models), rich domain models и облегчённой dependency inversion.
