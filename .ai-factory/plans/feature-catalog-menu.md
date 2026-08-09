# Plan: Каталог-меню (mega-menu desktop + accordion mobile)

- **Ветка:** нет (git.create_branches=false) — работа в текущей ветке
- **Дата создания:** 2026-08-09
- **Режим:** Full
- **Figma:** узлы `1010:7203` (моб), `1010:7890` (планшет), `1010:6708` (десктоп), `1010:81` (контейнер)

## Original Request

такое же меню похожее, но другое [figma-узлы] В телефоне до 1200, там показывается под себя подссылки, а от 1200 уже показываются справа

## Settings

- **Testing:** Нет — без тестов (pre-MVP)
- **Logging:** Verbose — `console.debug` в Alpine-обработчиках open/close/hover
- **Docs:** Да — обновить `docs/frontend.md`

## Область реализации (scope)

Новое **каталог-меню** с категориями и подкатегориями (НЕ навигационный drawer — то отдельный `mobile-menu`):

- **≤1199px (моб/планшет):** панели-оверлей, категории списком, клик по категории → подкатегории раскрываются **аккордеоном вниз** ("под себя")
- **≥1200px (десктоп):** mega-menu dropdown из кнопки "Каталог" в хедере — категории **слева**, подкатегории выбранной категории **справа** ("справа"). Открытие по hover + click, закрытие по `@click.away` / `@mouseleave`.

**Триггер:** кнопка "Каталог" в `header.blade.php` (сейчас мёртвая, `href="#"`).

## Содержание (по Figma)

**10 основных категорий** (Manrope ExtraBold): Чип тюнинг, Тормозная система, Диски, Оптика, Впускная система, Подвеска, Приемные трубы и даунпайпы, Выхлопные системы, Карбоновые элементы, Масла и жидкости (+Топливная система, Охлаждение — из home/categories).

**Подкатегории** (Manrope Medium, раскрыты в Figma для "Тормозная система"):
- Тормозная система → Комплект тормозной системы, Комплекты карбон-керамической тормозной системы, Тормозные суппорты, Армированные тормозные шланги, Крепления и адаптеры суппортов

Для категорий без явных подкатегорий в Figma — использовать данные из `catalog/filters/_content.blade.php` (там 4 категории с подкатегориями) + заглушки/пустые массивы для остальных (pre-MVP).

## Архитектурное решение

- **Новый блок:** `resources/views/blocks/common/catalog-menu/` (Blade + CSS + JS)
- **Данные:** View composer в `AppServiceProvider` → шарит `$catalogCategories` (nested array: `[['name'=>..., 'slug'=>..., 'href'=>..., 'children'=>[['name'=>...,'href'=>...],...]]]`) на все views
- **Alpine:** новый компонент `catalogMenu` (отдельный от `storeHeader`) — `isOpen`, `activeCategory`, `openMobileCategories` {}, методы `open/close/toggle/toggleMobile/hoverCategory`
- **Триггер:** кнопка "Каталог" в хедере → hover/click открывает dropdown (десктоп) / оверлей (моб)
- **Не используем `$modal`** для каталог-меню — оно управляется локальным `isOpen` + `@click.away`/`@mouseleave` (hover-поведение несовместимо с modal-стеком)

## Контекст кода (найдено)

| Что | Где | Значение |
|-----|-----|----------|
| Кнопка "Каталог" | `header.blade.php:169-192` | `.header__catalog-btn` — **мёртвая**, `href="#"`, без dropdown |
| `storeHeader` | `resources/js/blocks/common/header/index.js` | имеет `searchDropdownOpen` (dropdown-паттерн) — референс для `@click.away` |
| Accordion-референс | `catalog/filters/_content.blade.php` + `filters/index.js` | `catalogFilters`: `openCategories` {}, `toggleCategory()`, `activeCategory`, chevron `is-open`/`is-hidden` |
| Категории home | `blocks/home/categories.blade.php` | хардкод: `['name'=>..., 'slug'=>...]`, 12 шт., ссылка `?category=slug` |
| View composer | `AppServiceProvider.php` | сейчас шарит `$searchTypes`, `$availableLocales` — **добавить `$catalogCategories`** |
| CSS-токены | `base.css` :root | `--color-white`, `--color-purple`, `--color-black-soft`, `--color-gray-divider`, `--shadow-dropdown`, `--font-manrope` |
| Breakpoints | проект | mobile-first, `@media (min-width:1200px)` — десктоп |
| Modal плагин | `modal.js` | `$modal` — **не для этого меню** (hover несовместим со стеком) |

**Конвенции:** BEM (`block__element--mod`), PostCSS-nested `&`, `fluid-type(minVw,maxVw,minVal,maxVal)`, raw `@media`, inline SVG, `route()`/`__('store.*')`.

---

## Tasks

### Фаза 1 — Данные и триггер

#### Task 1: View composer для категорий
**Файл:** `app/Providers/AppServiceProvider.php`

В методе `boot()` добавить View composer, шарящий `$catalogCategories` на все views (или на `blocks.common.*`):
```php
view()->composer('*', function (\Illuminate\View\View $view) {
    $view->with('catalogCategories', [
        ['name' => 'Тормозная система', 'slug' => 'brake-system', 'href' => route('catalog', ['category' => 'brake-system']),
         'children' => [
             ['name' => 'Комплект тормозной системы', 'href' => '#'],
             ['name' => 'Комплекты карбон-керамической тормозной системы', 'href' => '#'],
             ['name' => 'Тормозные суппорты', 'href' => '#'],
             ['name' => 'Армированные тормозные шланги', 'href' => '#'],
             ['name' => 'Крепления и адаптеры суппортов', 'href' => '#'],
         ]],
        ['name' => 'Чип тюнинг', 'slug' => 'chip-tuning', 'href' => ..., 'children' => [...]],
        // ... остальные 8-10 категорий (slug'и взять из home/categories.blade.php)
    ]);
});
```
- Slug'и категорий взять из `blocks/home/categories.blade.php` (12 шт.).
- Подкатегории: для "Тормозная система" — из Figma (5 шт. выше); для chiptuning/discs/optics — из `catalog/filters/_content.blade.php`; для остальных — пустой `children => []` (заглушка pre-MVP).
- НЕ создавать модель Category/миграцию (pre-MVP, хардкод в composer).
- `php artisan view:clear` после изменения (composer кэшируется).

**Логирование:** `Log::debug('[catalog-categories] shared ' . count($catalogCategories) . ' categories')` — опционально, только если composer вызывается (не на каждый view).

**Ожидаемый результат:** `{{ $catalogCategories }}` доступна в любом Blade-шаблоне.

---

#### Task 2: Привязать кнопку "Каталог" к Alpine-компоненту
**Файлы:** `resources/views/blocks/common/header/header.blade.php`, новый `resources/js/blocks/common/catalog-menu/index.js`

**JS (новый `catalog-menu/index.js`):** Alpine-компонент `catalogMenu`:
```js
function catalogMenu() {
    return {
        isOpen: false,
        activeCategory: null,        // slug выбранной категории (десктоп, правая панель)
        openMobileCategories: {},    // { slug: bool } — аккордеон на моб
        open() { this.isOpen = true; if (!this.activeCategory) this.activeCategory = window.__firstCategorySlug; console.debug('[catalog-menu] open'); },
        close() { this.isOpen = false; console.debug('[catalog-menu] close'); },
        toggle() { this.isOpen ? this.close() : this.open(); },
        hoverCategory(slug) { this.activeCategory = slug; console.debug('[catalog-menu] hover ' + slug); },
        toggleMobile(slug) {
            this.openMobileCategories[slug] = !this.openMobileCategories[slug];
            console.debug('[catalog-menu] mobile toggle ' + slug);
        },
        isMobileOpen(slug) { return !!this.openMobileCategories[slug]; },
    }
}
window.__firstCategorySlug = null; // установится из Blade при рендере
```
- Зарегистрировать в `resources/js/app.js`: `import './blocks/common/catalog-menu'` + `Alpine.data('catalogMenu', catalogMenu)` (или объявить глобально через `window.catalogMenu`).

**Header (кнопка "Каталог"):** обернуть кнопку в контейнер с `x-data="catalogMenu()"`:
- Обёртка-триггер: `@mouseenter="open()" @click.prevent="toggle()"` + `aria-haspopup="true"` + `:aria-expanded="isOpen ? 'true' : 'false'"`.
- Сама кнопка остаётся стилизованной (`.header__catalog-btn`), убрать `href="#"`.
- Dropdown-разметку (Task 3) разместить ВНУТРИ обёртки, чтобы `@click.away` и `@mouseleave` работали корректно.

**Ожидаемый результат:** hover/click по "Каталог" открывает `isOpen=true`, `@click.away` закрывает.

---

### Фаза 2 — Разметка

#### Task 3: Создать Blade-паршл каталог-меню
**Файл (новый):** `resources/views/blocks/common/catalog-menu/catalog-menu.blade.php`

Структура с двумя рендерингами (десктоп mega-menu + мобильный accordion) в одном компоненте, переключаемыми CSS:

```blade
<div class="catalog-menu"
     x-data="catalogMenu()"
     @click.away="close()"
     @mouseleave="close()">
    <!-- Триггер (кнопка "Каталог" — переносится сюда ИЛИ обёртка в header) -->
    {{-- см. Task 2: триггер в header.blade.php обёрнут x-data="catalogMenu()" --}}

    <!-- Dropdown / Overlay panel -->
    <div class="catalog-menu__panel"
         x-show="isOpen"
         x-cloak
         x-transition:enter="catalog-menu__panel--enter"
         x-transition:enter-end="catalog-menu__panel--enter-end"
         x-transition:leave="catalog-menu__panel--leave"
         x-transition:leave-end="catalog-menu__panel--leave-end"
         role="menu"
         aria-label="{{ __('store.catalog_menu_title') }}">

        <!-- Десктоп: левая колонка категорий -->
        <div class="catalog-menu__categories">
            @foreach($catalogCategories as $i => $cat)
                @php if($i===0) window: первый slug для activeCategory default @endphp
                <a class="catalog-menu__category"
                   href="{{ $cat['href'] }}"
                   @mouseenter="hoverCategory('{{ $cat['slug'] }}')"
                   :class="{ 'catalog-menu__category--active': activeCategory === '{{ $cat['slug'] }}' }">
                    {{ $cat['name'] }}
                </a>
            @endforeach
        </div>

        <!-- Десктоп: правая колонка подкатегорий (меняется по activeCategory) -->
        <div class="catalog-menu__subcategories">
            <template x-for="cat in ({{ \Illuminate\Support\Js::from($catalogCategories) }})" :key="cat.slug">
                <div x-show="activeCategory === cat.slug" class="catalog-menu__sub-list">
                    <template x-for="sub in cat.children" :key="sub.name">
                        <a class="catalog-menu__subcategory" :href="sub.href" x-text="sub.name"></a>
                    </template>
                    <a class="catalog-menu__all" :href="cat.href">{{ __('store.catalog_all_in_category') }}</a>
                </div>
            </template>
        </div>

        <!-- Мобильный аккордеон (≤1199px, скрывается на десктопе) -->
        <div class="catalog-menu__accordion">
            @foreach($catalogCategories as $cat)
                <div class="catalog-menu__acc-item">
                    <button class="catalog-menu__acc-trigger"
                            @click="toggleMobile('{{ $cat['slug'] }}')"
                            :class="{ 'is-open': isMobileOpen('{{ $cat['slug'] }}') }"
                            :aria-expanded="isMobileOpen('{{ $cat['slug'] }}') ? 'true' : 'false'">
                        {{ $cat['name'] }}
                        <svg class="catalog-menu__chevron"><!-- chevron --></svg>
                    </button>
                    <div class="catalog-menu__acc-content"
                         x-show="isMobileOpen('{{ $cat['slug'] }}')"
                         x-collapse>
                        @foreach($cat['children'] ?? [] as $sub)
                            <a class="catalog-menu__subcategory" href="{{ $sub['href'] }}">{{ $sub['name'] }}</a>
                        @endforeach
                        <a class="catalog-menu__all" href="{{ $cat['href'] }}">{{ __('store.catalog_all_in_category') }}</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
```

**Важно:**
- Десктоп-колонки (`__categories` + `__subcategories`) видны ≥1200px, мобильный аккордеон (`__accordion`) — ≤1199px (через CSS `display:none`).
- Десктоп правую панель рендерить через Alpine `x-for` по JSON-данным `$catalogCategories` (передать через `@js()` или `Js::from`), чтобы `hoverCategory` мгновенно переключал содержимое без запроса.
- `window.__firstCategorySlug` установить первым slug'ом для default `activeCategory` (или `x-init="activeCategory = '{{ $catalogCategories[0]['slug'] }}'"`).
- Chevron SVG — inline, stroke-based, поворот на `is-open` (как в `catalog-filters`).
- `x-collapse` требует Alpine Collapse plugin — проверить подключён ли; если нет, заменить на `x-show` + `x-transition` (height-анимация опциональна).
- `catalog-menu.blade.php` подключить в `app.blade.php` после `header` (внутри области `x-data`).
- Альтернатива: НЕ выносить триггер, а оставить кнопку в `header.blade.php` обёрнутой `x-data="catalogMenu()"`, а `catalog-menu.blade.php` содержит только `.catalog-menu__panel`. Решение за исполнителем — главное чтобы `@click.away`/`@mouseleave` закрывали корректно.

**Ожидаемый результат:** hover категории (десктоп) обновляет правую панель; клик категории (моб) раскрывает подкатегории аккордеоном.

---

### Фаза 3 — Стили

#### Task 4: CSS каталог-меню
**Файл (новый):** `resources/css/blocks/common/catalog-menu/style.css`

По конвенциям (BEM, `fluid-type()`, переменные `base.css`):

**База (mobile-first, ≤1199px = аккордеон в оверлее):**
- `.catalog-menu` — `position: relative;` (обёртка).
- `.catalog-menu__panel` — `position: absolute; top: 100%; left: 0; width: 100%; background: var(--color-white); box-shadow: var(--shadow-dropdown); z-index: 100;` На моб = full-width оверлей-панель под кнопкой.
- `.catalog-menu__categories`, `.catalog-menu__subcategories` — `display: none;` (только десктоп).
- `.catalog-menu__accordion` — `display: flex; flex-direction: column;` (моб).
- `.catalog-menu__acc-trigger` — Manrope ExtraBold, `font-size: fluid-type(320px,1200px,16px,18px)`, padding, `background:none; border:none; cursor:pointer; width:100%; display:flex; justify-content:space-between; align-items:center;`.
- `.catalog-menu__chevron` — поворот на `.is-open`: `transform: rotate(180deg); transition: transform 0.2s ease;`.
- `.catalog-menu__acc-content` — `display: flex; flex-direction: column; gap: ...; padding-left: ...;`.
- `.catalog-menu__subcategory` — Manrope Medium, `font-size: fluid-type(320px,1200px,14px,16px)`, `color: var(--color-black-soft)`, hover → `var(--color-purple)`.
- `.catalog-menu__all` — отдельно, SemiBold, `color: var(--color-purple)`.

**Десктоп (≥1200px) — mega-menu две колонки:**
```css
@media screen and (min-width: 1200px) {
    .catalog-menu__panel {
        position: absolute;
        top: 100%;
        left: 0;
        width: fluid-type(1200px, 1920px, 600px, 800px);  /* широкая, под 2 колонки */
        display: grid;
        grid-template-columns: 1fr 1.5fr;  /* категории | подкатегории */
        max-height: 70vh;
        overflow-y: auto;
    }
    .catalog-menu__categories { display: flex; flex-direction: column; border-right: 1px solid var(--color-gray-divider); }
    .catalog-menu__subcategories { display: block; padding: ...; }
    .catalog-menu__accordion { display: none; }
    .catalog-menu__category {
        font-family: var(--font-manrope); font-weight: 700;
        font-size: fluid-type(1200px,1440px,15px,16px);
        padding: fluid-type(1200px,1440px,10px,14px) fluid-type(1200px,1440px,16px,20px);
        color: var(--color-black-soft); text-decoration: none; transition: color/background 0.2s ease;
    }
    .catalog-menu__category:hover,
    .catalog-menu__category--active { color: var(--color-purple); background: var(--color-off-white); }
}
```

**Transition classes:**
```css
.catalog-menu__panel--enter,
.catalog-menu__panel--leave-end { opacity: 0; transform: translateY(-8px); }
.catalog-menu__panel--enter-end,
.catalog-menu__panel--leave { opacity: 1; transform: translateY(0); transition: opacity 0.2s ease, transform 0.2s ease; }
```

**z-index:** panel `100` (под хедером z-index 100 — вынести выше, `101`, ИЛИ panel absolute внутри хедера). Свериться с существующим z-index стека (header=100, mobile-nav=100, modal=1000). Catalog-menu panel `z-index: 101` чтобы быть над контентом но под модалками.

---

#### Task 5: Зарегистрировать CSS-импорт
**Файл:** `resources/css/app.css`
- Добавить `@import 'blocks/common/catalog-menu/style.css';` рядом с остальными common-блоками.

---

#### Task 6: Подключить JS и паршл
**Файлы:** `resources/js/app.js`, `resources/views/layouts/app.blade.php`
- В `app.js`: `import './blocks/common/catalog-menu'` + зарегистрировать `Alpine.data('catalogMenu', catalogMenu)`.
- В `app.blade.php`: `@include('blocks.common.catalog-menu.catalog-menu')` после `@include('blocks.common.header.header')`.

---

### Фаза 4 — i18n, верификация, доки

#### Task 7: i18n-ключи
**Файлы:** `lang/ru/store.php`, `lang/en/store.php`
- `catalog_menu_title` — ru "Каталог" / en "Catalog"
- `catalog_all_in_category` — ru "Все товары в категории" / en "All in category"

---

#### Task 8: Сборка и верификация
- `php artisan view:clear` (composer кэш)
- `node node_modules/vite/bin/vite.js build` — exit 0
- `vendor/bin/pint --dirty --format agent` (AppServiceProvider изменился)
- Визуальная проверка (375px / 768px / 1280px):
  - Десктоп: hover "Каталог" → dropdown с 2 колонками; hover категории слева → правая панель обновляется; click away закрывает.
  - Моб: click "Каталог" → оверлей с аккордеоном; click категории → подкатегории раскрываются вниз.

---

#### Task 9: Документация (чекпойнт)
**Файл:** `docs/frontend.md`
- Добавить подраздел `catalog-menu`: назначение, файлы, данные (`$catalogCategories` via View composer), поведение (десктоп mega-menu hover / моб accordion), интеграция с кнопкой "Каталог" в хедере.

---

## Commit Plan

2 коммита:

### Commit 1: feat(frontend): add catalog mega-menu (desktop hover + mobile accordion)
Задачи 1-7: AppServiceProvider, header.blade.php, catalog-menu.blade.php, catalog-menu/index.js, style.css, app.css, app.js, app.blade.php, lang/{ru,en}/store.php

### Commit 2: docs(frontend): document catalog-menu block
Задачи 8-9: docs/frontend.md

---

## Risks & Notes

- **Hover на touch-устройствах:** `@mouseenter` не сработает на тачскрине ≥1200px (редко, но бывает). Click-toggle (`@click.prevent="toggle()"`) покрывает этот кейс.
- **`@mouseleave` на границах:** если между кнопкой и панелью зазор, mouseleave закроет меню преждевременно. Решение: `@mouseleave` на обёртке (включает и кнопку, и панель), без зазора в CSS (panel `top: 100%`).
- **`x-collapse` plugin:** если не подключён — использовать `x-show` + `x-transition` (без height-анимации). Проверить в `app.js` импорты Alpine plugins.
- **Производительность `x-for` по JSON:** категорий мало (10-12), рендер дешёвый. ОК.
- **Доступность:** `role="menu"`, `aria-haspopup`, `aria-expanded`, keyboard (Enter/Space на триггере). Focus-trap опционален (pre-MVP).
- **Дублирование с `catalog/filters`:** filters-sidebar уже имеет похожий аккордеон. Это другой компонент (sidebar страницы каталога vs global mega-menu). Не объединять — разные контексты. Но переиспользовать визуальный паттерн.
- **z-index конфликт:** header=100. Panel должен быть ≥101. Modal=1000 — catalog-menu НЕ модалка, конфликтов нет.
