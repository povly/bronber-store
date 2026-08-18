[← Админ-панель](admin-panel.md) · [Back to README](../README.md) · [Деплой →](deployment.md)

# Фронтенд

Фронтенд bronber-store построен на Blade + Alpine.js + PostCSS, собирается через Vite 8.
Это **не** Livewire и **не** Inertia — кастомный block-based паттерн, ортогональный backend-архитектуре.

## Block-based архитектура

CSS, JS и Blade-шаблоны организованы **постранично** в параллельных каталогах. Каждая страница —
это «блок» со своими стилями, скриптами и шаблонами:

```
resources/
├── views/
│   ├── blocks/{page}/          # Blade-паршлы страницы
│   │   ├── home/               # hero, categories, products, advs, news, partners
│   │   ├── catalog/            # hero, filters/, products/, pagination/
│   │   ├── product/            # product.blade.php
│   │   ├── cart/, checkout/
│   │   ├── error-404/          # Контентный блок страницы 404
│   │   ├── favorites/          # Страница Избранное (4-col grid, без фильтров)
│   │   └── common/             # header, footer, top-bar, mobile-nav (cross-page)
│   ├── components/             # Переиспользуемые: btn, img, breadcrumbs, product-card, qty, slider/
│   ├── errors/404.blade.php    # Laravel error view (авто-рендер при NotFoundHttpException)
│   ├── layouts/app.blade.php   # Базовый layout
│   └── {page}.blade.php        # Корневые шаблоны страниц
├── css/
│   ├── blocks/{page}/          # Стили страницы (style.css — точка входа)
│   ├── common/                 # Общие стили
│   ├── components/             # Стили компонентов
│   └── app.css                 # Глобальная точка входа
└── js/
    ├── blocks/{page}/          # Скрипты страницы (index.js — точка входа)
    ├── alpine/                 # Alpine-компоненты
    ├── app.js                  # Инициализация Alpine
    └── lazyload.js             # vanilla-lazyload инициализация
```

### Точки входа Vite

Vite автоматически собирает все точки входа через `globSync`:

```js
// vite.config.js
const blockStyles = globSync('resources/css/blocks/**/style.css');
const blockScripts = globSync('resources/js/blocks/**/index.js');
```

Каждый блок подключает свои стили/скрипты в корневом Blade-шаблоне страницы через `@vite`.

> При добавлении новой страницы создавайте `style.css` в `css/blocks/{page}/` и `index.js` в
> `js/blocks/{page}/` — Vite подхватит их автоматически.

### Блок `common/mobile-menu` — выезжающее меню

Slide-out drawer для мобильных/планшетов (viewport ≤1199px), ортогональный к нижней панели
`common/mobile-nav`. Скрывается на ≥1200px через `@media screen and (min-width: 1200px)`.

**Файлы:**

| Файл | Назначение |
|------|------------|
| `resources/views/blocks/common/mobile-menu/mobile-menu.blade.php` | Blade-разметка drawer'а |
| `resources/css/blocks/common/mobile-menu/style.css` | BEM + PostCSS-nested стили |

CSS подключается в `resources/css/app.css` (общий бандл), Blade-паршл — через `@include`
в `resources/views/layouts/app.blade.php`.

**Интеграция с `$modal`-плагином**

Состояние drawer'а управляется модальным плагином (`resources/js/alpine/plugins/modal.js`),
НЕ через `storeHeader`:

| Действие | Вызов |
|----------|-------|
| Открыть | `$modal.show('mobile-menu')` |
| Закрыть (current) | `$modal.hide('mobile-menu')` или `$modal.hide()` |
| Проверить открыт ли | `$modal.isOpen('mobile-menu')` |
| Z-index в стеке | `1000 + $modal.depth('mobile-menu')` |

Бургер-кнопка в `blocks/common/header/header.blade.php` (`.header__menu`) вызывает
`$modal.show('mobile-menu')` по клику и по Enter.

**Поведение:**

- Панель выезжает слева (`transform: translateX(-100%) → translateX(0)`, 0.3s ease)
- Overlay затемняет экран (`var(--color-overlay)`, opacity 0↔1, 0.3s)
- Закрытие: ✕ в панели, клик по overlay, или Escape (`@keydown.escape.window`)
- Скролл body блокируется автоматически через `<body :class="{ 'overflow-hidden': $store.modal.stack.length }">` в `layouts/app.blade.php` — никаких отдельных обработчиков не требуется
- При клике по любой nav-ссылке drawer закрывается (`@click="$modal.hide('mobile-menu')"`)

Z-index паттерн зеркалирует `blocks/catalog/filters` (overlay = 1000, panel выше overlay внутри
враппера). CSS использует кастомные properties из `common/base.css` (`--color-overlay`,
`--color-white`, `--color-purple`, `--color-black-soft`, `--color-gray-divider`, `--font-manrope`)
и `fluid-type()` для всех адаптивных размеров.

### Блок `common/catalog-menu` — каталог-меню (mega-menu)

Mega-menu категорий, открывающийся по кнопке «Каталог» в `header__nav`. **Отдельный компонент**
от `common/mobile-menu` (который — burger drawer навигации). Не использует `$modal`-плагин:
состоянием управляет локальный Alpine-компонент `catalogMenu` (`isOpen` + `@click.away` /
`@mouseleave`), т.к. hover-поведение несовместимо с modal-стеком.

**Файлы:**

| Файл | Назначение |
|------|------------|
| `resources/views/blocks/common/catalog-menu/catalog-menu.blade.php` | Blade-разметка (триггер + десктоп панель + мобильный аккордеон) |
| `resources/css/blocks/common/catalog-menu/style.css` | BEM + PostCSS-nested стили (mobile-first, `fluid-type()`) |
| `resources/js/blocks/common/catalog-menu/index.js` | Alpine-компонент `catalogMenu` |

CSS подключается в `resources/css/app.css` (общий бандл, как и другие common-блоки), JS — в
`resources/js/app.js`. Blade-паршл — через `@include` из
`blocks/common/header/header.blade.php` (заменил мёртвую кнопку `.header__catalog-btn`).

**Источник данных:** View composer в `AppServiceProvider::boot()` шарит `$catalogCategories`
(массив `[['name', 'slug', 'href', 'children']]`) на все views. В Blade передаётся в Alpine через
`x-data="catalogMenu(@js($catalogCategories))"`. Этот же массив переиспользуется `home/categories`.

**Поведение:**

- **Десктоп (≥1200px):** mega-menu dropdown — 2 колонки (категории слева, подкатегории активной
  категории справа). Открытие по `@mouseenter` на триггере, переключение правой панели по
  `@mouseenter` на категории (getter `activeChildren`). Закрытие: `@mouseleave` на враппере или
  `@click.away`.
- **Мобильный/планшет (≤1199px):** полноэкранный оверлей с аккордеоном категорий. Клик по категории
  раскрывает подкатегории (`x-collapse`). Явный ✕ для закрытия.
- Z-index panel = `101` (выше хедера 100, ниже модалок 1000+).

## Alpine.js

Единственный JS-фреймворк. Директивы прямо в Blade: `x-data`, `x-show`, `x-on:`, `x-collapse`.

- Пакет: `alpinejs` 3.15 + `@alpinejs/collapse`
- Инициализация: `resources/js/app.js`
- Кастомные компоненты: `resources/js/alpine/`

### Ленивая загрузка изображений

Используется `vanilla-lazyload`. Для lazy-элементов — `data-src` вместо `src`:

```html
<img data-src="/images/photo.png" alt="..." class="lazy">
```

Инициализация — в `resources/js/lazyload.js`.

## PostCSS

Конфигурация в `postcss.config.js`. Пайплайн:

| Плагин | Назначение |
|--------|------------|
| `postcss-mixins` | Миксины для переиспользуемого CSS |
| `postcss-nested` / `postcss-nested-import` | Вложенность селекторов |
| `postcss-simple-vars` | Переменные (`$var`) |
| `autoprefixer` | Автопрефиксы вендоров |
| `cssnano` | Минификация (production) |
| `postcss-functions` | Кастомные функции |

### Кастомные функции

В `postcss/js/functions/`:

| Функция | Назначение |
|---------|------------|
| `fluidType()` | Адаптивная типографика (fluid typography) |
| `pxToVw()` | Конвертация px → vw для responsive-вёрстки |

## Страницы ошибок

Laravel авто-рендерит `resources/views/errors/404.blade.php` при `NotFoundHttpException`.
`Route::fallback()` не нужен — достаточно создать view по convention.

Страница 404 расширяет `layouts.app` (header с поиском + footer автоматически) и подключает
контентный блок `blocks/error-404/error-404.blade.php`. Поскольку `SetLocale` middleware
route-level и не срабатывает на unmatched-роутах, локаль для error-страниц определяется
по URL-сегменту через `@php`-блок в начале `errors/404.blade.php`.

## Совместимость браузеров

Проект нацелен на **консервативные таргеты** — критичное требование для целевой аудитории:

| Целевой браузер | Версия |
|-----------------|--------|
| Internet Explorer | 11 |
| iOS Safari | 9+ |
| Android Browser | 4.4+ |

Транспиляция обеспечивается двумя механизмами в `vite.config.js`:

1. **LightningCSS** — минификация CSS с учётом `browserslist` таргетов (`IE 11`, `android 4.4`, `ios 9`)
2. **Babel** (`@babel/preset-env`) — транспиляция JS для IE 11 / iOS 9 (`useBuiltIns: 'entry'`, `corejs: 3`)

```js
// vite.config.js (фрагмент)
babel({
    babelHelpers: 'bundled',
    presets: [['@babel/preset-env', { targets: { ie: '11', ios: '9' }, modules: false }]],
}),
```

> ⚠️ Избегайте современного JS-API (optional chaining, `Object.fromEntries`, `Array.flat`, и т.д.)
> без полифиллов. Babel + corejs обрабатывают большую часть, но проверяйте caniuse.

## i18n (локализация)

### Локали

| Локаль | Роль | URL |
|--------|------|-----|
| `ru` | Основная (дефолтная) | без префикса (`/catalog`) |
| `en` | Дополнительная | с префиксом (`/en/catalog`) |

Список доступных локалей — в `config/app.php`: `'available_locales' => ['ru', 'en']`.

### Паттерн роутов

Роуты регистрируются один раз в `$register`-замыкании, затем дублируются для каждой локали:

```php
// routes/web.php
$register = function () {
    Route::get('/catalog', fn () => view('main'))->name('catalog');
    // ... остальные роуты
};

// Дефолтная локаль (ru) — без префикса
Route::middleware('locale:ru')->group($register);

// Остальные локали — с /{locale} префиксом
foreach (['en'] as $locale) {
    Route::prefix($locale)->name("{$locale}.")
        ->middleware("locale:{$locale}")
        ->group($register);
}
```

### SetLocale middleware

`app/Http/Middleware/SetLocale.php` вызывает `app()->setLocale()` по параметру роута `{locale}`.

### Тексты

Переводы в шаблонах — через `__('key')` с ключами в `lang/{locale}/`:

```
lang/
├── ru/store.php    # русские переводы
└── en/store.php    # английские переводы
```

## Сборка

| Команда | Режим |
|---------|-------|
| `npm run dev` | Vite HMR (горячая перезагрузка при разработке) |
| `npm run build` | Продакшн-сборка (минификация LightningCSS, target es2017) |
| `composer run dev` | Полная dev-среда (server + queue + logs + vite) |

> Если изменение фронтенда не отображается — возможно нужна пересборка:
> `npm run build` или `npm run dev` (или `composer run dev`).

## See Also

- [Разработка](development.md) — общий рабочий процесс
- [Архитектура](architecture.md) — block-based фронтенд ортогонален backend-модулям
- [`.ai-factory/rules/base.md`](../.ai-factory/rules/base.md) — секция «Frontend-конвенции»
