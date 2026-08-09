# Plan: Мобильное меню (hamburger + drawer) ≤1200px

- **Ветка:** нет (git.create_branches=false) — работа в текущей ветке
- **Дата создания:** 2026-08-09
- **Режим:** Full
- **Figma:** `bwkgx55t` → узлы `1010:13972` (Мобильная версия) и `1010:8340` (Планшет)

## Original Request

Connect to Figma, channel bwkgx55t https://www.figma.com/design/vfXiZV4QOG9DrZTXGDsoCf/Bronber-%D0%92%D0%B5%D1%80%D1%81%D1%82%D0%BA%D0%B0--Copy-?node-id=1010-13972&t=haXqZIbjQP5cukJq-4 https://www.figma.com/design/vfXiZV4QOG9DrZTXGDsoCf/Bronber-%D0%92%D0%B5%D1%80%D1%81%D1%82%D0%BA%D0%B0--Copy-?node-id=1010-8340&t=haXqZIbjQP5cukJq-4 Нужно создать меню для телефона и до 1200 пикселей!

## Settings

- **Testing:** Нет — тесты не пишем (pre-MVP прототип)
- **Logging:** Verbose — `console.debug` в Alpine-обработчиках open/close для разработки
- **Docs:** Да — обязательный чекпойнт: обновить `docs/frontend.md`

## Область реализации (scope)

- ✅ Hamburger + выезжающая боковая панель (drawer) с навигацией
- ✅ Overlay/затемнение фона + закрытие по клику и Escape
- ❌ Bottom nav bar — **не в скоупе** (существует отдельно как `blocks/common/mobile-nav/`, не трогаем)
- ❌ Полный редизайн хедера — **не в скоупе** (только привязка существующей кнопки-бургера)

**Состав drawer (по Figma):**
1. Кнопка закрытия (✕)
2. Навигация (Manrope Bold 20px): Новинки, Акции, Блог, Бонусы, О компании, Карьера, FAQ, Контакты, Доставка и оплата, Гарантия и возврат
3. Телефон: `+7 (985) 449-8000` (Manrope Medium 18px)
4. Переключатель языка: RU / EN (Manrope Medium 20px)

**Поведение:** виден на viewport ≤1199px, скрыт ≥1200px. Slide-in слева (соответствует расположению бургера в шапке). Body scroll lock при открытии (уже есть в `closeMenu()`).

## Контекст кода (найдено агентами)

| Что | Где | Значение |
|-----|-----|----------|
| Alpine store | `resources/js/blocks/common/header/index.js` | `storeHeader`: `menuOpen`, `toggleMenu()`, `closeMenu()` (с блокировкой скролла) — **готово** |
| Бургер-кнопка | `resources/views/blocks/common/header/header.blade.php` | `.header__menu` — существует, **нет `@click`** |
| Эталон overlay+panel | `resources/css/blocks/catalog/filters/style.css` (строки 2-29) | `z-index: 1000` overlay / `1001` panel, `.is-open`, `transform` + `transition: 0.3s ease` |
| Layout | `resources/views/layouts/app.blade.php` | `<body x-data>` + `@include` header/mobile-nav/footer |
| CSS-токены | `resources/css/common/base.css` :root | `--color-overlay`, `--color-purple`, `--gradient-black`, `--font-manrope`, `--shadow-overlay` |
| Точка входа CSS | `resources/css/app.css` | импортирует блоки постранично — добавить импорт нового блока |
| `[x-cloak]` | `base.css:99` | `{ display: none }` — уже работает |
| i18n ru/en | `lang/{ru,en}/store.php` | `nav_new`, `nav_promo`, `nav_blog`, `nav_bonus`, `nav_about`, `top_career`, `top_faq`, `top_contacts`, `top_delivery`, `top_guarantee` — **существуют** |
| Шрифты | `resources/css/common/fonts.css` | Manrope 400-800 подключён |

**Конвенции:** BEM (`block__element--modifier`), mobile-first, raw `@media`, `fluid-type(minVw, maxVw, minVal, maxVal)` для адаптивных размеров, inline SVG в Blade, `route()` для ссылок, `__('store.*')` для текстов.

---

## Tasks

### Фаза 1 — Разметка и привязка бургера

#### Task 1: Привязать кнопку-бургер к Alpine `toggleMenu()`
**Файл:** `resources/views/blocks/common/header/header.blade.php`

**Что сделать:**
- Найти элемент `.header__menu` (SVG-иконка бургера).
- Добавить `@click="toggleMenu()"` и `aria-label="{{ __('store.mobile_menu_open') }}"`.
- Добавить `aria-expanded="false"` с привязкой `:aria-expanded="menuOpen ? 'true' : 'false'"`.
- Убедиться, что кнопка видна ≤1199px (проверить CSS — должна быть `display: none` ≥1200px, видна ниже; по данным агента она уже скрыта на десктопе).

**Ожидаемый результат:** клик по бургеру меняет `storeHeader.menuOpen = true`. В логе (verbose): `console.debug('[mobile-menu] open')` — добавить в `toggleMenu()` если его нет.

**Логирование:** в `resources/js/blocks/common/header/index.js` внутри `toggleMenu()`/`closeMenu()` добавить `console.debug('[storeHeader] menu ' + (this.menuOpen ? 'opened' : 'closed'))`.

---

#### Task 2: Создать Blade-паршл drawer-меню
**Файл (новый):** `resources/views/blocks/common/mobile-menu/mobile-menu.blade.php`

**Что сделать:**
Создать структуру drawer + overlay, обёрнутую в Alpine-директивы `storeHeader`:

```blade
<div class="mobile-menu"
     x-data
     x-cloak
     @keydown.escape.window="closeMenu()">
    <!-- Overlay -->
    <div class="mobile-menu__overlay"
         x-show="menuOpen"
         x-transition:enter="mobile-menu__overlay--enter"
         x-transition:enter-end="mobile-menu__overlay--enter-end"
         x-transition:leave="mobile-menu__overlay--leave"
         x-transition:leave-end="mobile-menu__overlay--leave-end"
         @click="closeMenu()"></div>

    <!-- Panel -->
    <aside class="mobile-menu__panel"
           x-show="menuOpen"
           x-transition:enter="mobile-menu__panel--enter"
           x-transition:enter-end="mobile-menu__panel--enter-end"
           x-transition:leave="mobile-menu__panel--leave"
           x-transition:leave-end="mobile-menu__panel--leave-end"
           role="dialog"
           aria-modal="true"
           aria-label="{{ __('store.mobile_menu_title') }}">
        <button class="mobile-menu__close" @click="closeMenu()" aria-label="{{ __('store.mobile_menu_close') }}">
            <!-- inline SVG ✕ -->
        </button>

        <nav class="mobile-menu__nav">
            <a class="mobile-menu__link" href="{{ route('new') }}" @click="closeMenu()">{{ __('store.nav_new') }}</a>
            <a class="mobile-menu__link" href="{{ route('promo') }}" @click="closeMenu()">{{ __('store.nav_promo') }}</a>
            <a class="mobile-menu__link" href="{{ route('blog') }}" @click="closeMenu()">{{ __('store.nav_blog') }}</a>
            <a class="mobile-menu__link" href="{{ route('bonus') }}" @click="closeMenu()">{{ __('store.nav_bonus') }}</a>
            <a class="mobile-menu__link" href="{{ route('about') }}" @click="closeMenu()">{{ __('store.nav_about') }}</a>
            <a class="mobile-menu__link" href="{{ route('career') }}" @click="closeMenu()">{{ __('store.top_career') }}</a>
            <a class="mobile-menu__link" href="{{ route('faq') }}" @click="closeMenu()">{{ __('store.top_faq') }}</a>
            <a class="mobile-menu__link" href="{{ route('contacts') }}" @click="closeMenu()">{{ __('store.top_contacts') }}</a>
            <a class="mobile-menu__link" href="{{ route('delivery') }}" @click="closeMenu()">{{ __('store.top_delivery') }}</a>
            <a class="mobile-menu__link" href="{{ route('guarantee') }}" @click="closeMenu()">{{ __('store.top_guarantee') }}</a>
        </nav>

        <div class="mobile-menu__footer">
            <a class="mobile-menu__phone" href="tel:+79854498000">+7 (985) 449-8000</a>
            <div class="mobile-menu__lang">
                @foreach($availableLocales ?? [] as $code => $name)
                    <a class="mobile-menu__lang-link {{ app()->getLocale() === $code ? 'mobile-menu__lang-link--active' : '' }}"
                       href="{{ ... }}">{{ strtoupper($code) }}</a>
                @endforeach
            </div>
        </div>
    </aside>
</div>
```

**Важно:**
- Имена роутов (`new`, `promo`, `blog` и т.д.) **взять из существующей десктоп-навигации** в `header.blade.php` (раздел `.header__nav`) и `top-bar.blade.php` — не выдумывать. Реальные route names определить по `routes/web.php`.
- Переключатель языка реализовать по образцу из `top-bar.blade.php` (использовать тот же механизм `$availableLocales` из AppServiceProvider view composer).
- `@click="closeMenu()"` на каждой ссылке — закрывать меню при переходе.
- SVG ✕ для кнопки закрытия — inline, stroke-based, в стиле существующих иконок (stroke `currentColor` или `#7212BC`).

**Ожидаемый результат:** паршл рендерит drawer + overlay, управляется через `storeHeader.menuOpen`.

---

#### Task 3: Подключить drawer в layout
**Файл:** `resources/views/layouts/app.blade.php`

**Что сделать:**
- После `@include('blocks.common.header.header')` (или сразу после открывающего тега контента) добавить:
  ```blade
  @include('blocks.common.mobile-menu.mobile-menu')
  ```
- Убедиться, что Alpine-контекст `storeHeader` доступен (drawer читает `menuOpen`/`closeMenu()` из того же `x-data`, что и хедер). Если `storeHeader` объявлен только на `.header`, перенести объявление `x-data="storeHeader(...)"` на общий родительский элемент (например `<body>` или обёртку layout), либо использовать Alpine `$store` (глобальный store). **Решение принять по структуре app.blade.php** — предпочтительно поднять `x-data` на `<body>`, чтобы и хедер, и drawer делили состояние.

**Ожидаемый результат:** drawer появляется в DOM на всех страницах, разделяет состояние `menuOpen` с бургером в хедере.

---

### Фаза 2 — Стили

#### Task 4: Создать CSS блока mobile-menu
**Файл (новый):** `resources/css/blocks/common/mobile-menu/style.css`

**Что сделать:**
Создать стили по конвенциям проекта (BEM, `fluid-type()`, переменные из `base.css`):

- `.mobile-menu` — `position: fixed; inset: 0; z-index: 1000; display: none;` (виден ≤1199px).
- `.mobile-menu__overlay` — фон `var(--color-overlay)` (rgba(0,0,0,0.5)), `position: absolute; inset: 0;`. Переход непрозрачности.
- `.mobile-menu__panel` — `position: absolute; top: 0; left: 0; height: 100%;` ширина `fluid-type(320px, 768px, 280px, 360px)`. Фон `var(--color-white)`. Slide-in слева: по умолчанию `transform: translateX(-100%); transition: transform 0.3s ease;`. Активное состояние (через Alpine transition classes ИЛИ `x-show`) — `transform: translateX(0)`.
- `.mobile-menu__close` — кнопка ✕ в правом верхнем углу панели, размер `fluid-type(375px, 1200px, 20px, 24px)`.
- `.mobile-menu__nav` — flex column, `gap: fluid-type(320px, 768px, 24px, 32px)`, padding.
- `.mobile-menu__link` — Manrope Bold, `font-size: fluid-type(320px, 1200px, 18px, 20px)`, цвет `var(--color-black-soft)`, hover → `var(--color-purple)`.
- `.mobile-menu__footer` — margin-top auto (прижать вниз), border-top.
- `.mobile-menu__phone` — Manrope Medium, `font-size: fluid-type(320px, 768px, 16px, 18px)`, цвет `var(--color-black-soft)`.
- `.mobile-menu__lang` — flex row, gap; `.mobile-menu__lang-link` Manrope Medium 20px; `--active` → `var(--color-purple)` + bold.

**Responsive:**
```css
.mobile-menu { display: block; } /* base = mobile */

@media screen and (min-width: 1200px) {
    .mobile-menu { display: none; } /* скрываем на десктопе */
}
```

**Transition classes** (для Alpine `x-transition`):
```css
.mobile-menu__overlay--enter,
.mobile-menu__overlay--leave-end { opacity: 0; }
.mobile-menu__overlay--enter-end,
.mobile-menu__overlay--leave { opacity: 1; transition: opacity 0.3s ease; }

.mobile-menu__panel--enter,
.mobile-menu__panel--leave-end { transform: translateX(-100%); }
.mobile-menu__panel--enter-end,
.mobile-menu__panel--leave { transform: translateX(0); transition: transform 0.3s ease; }
```

**z-index:** overlay=1000, panel=1001 (соответствует паттерну catalog-filters).

**Ожидаемый результат:** drawer выезжает слева за 0.3s, overlay затемняет фон, всё скрывается ≥1200px. На 375px и 768px выглядит как в Figma.

**Логирование:** `console.debug('[mobile-menu] CSS loaded')` не нужен — CSS без логов. Verbose-логирование только в JS (Task 1).

---

#### Task 5: Зарегистрировать CSS-импорт
**Файл:** `resources/css/app.css`

**Что сделать:**
- Добавить импорт нового блока рядом с остальными common-блоками:
  ```css
  @import "./blocks/common/mobile-menu/style.css";
  ```
  (после импорта `header` и `mobile-nav`, в секции common-блоков).

**Ожидаемый результат:** стили попадают в сборку Vite, drawer стилизован.

---

### Фаза 3 — i18n и финализация

#### Task 6: Добавить недостающие i18n-ключи
**Файлы:** `lang/ru/store.php`, `lang/en/store.php`

**Что сделать:**
Проверить и добавить ключи (если отсутствуют):
- `mobile_menu_title` — "Меню" / "Menu"
- `mobile_menu_open` — "Открыть меню" / "Open menu"
- `mobile_menu_close` — "Закрыть меню" / "Close menu"

**Существующие ключи (переиспользуем, не дублируем):** `nav_new`, `nav_promo`, `nav_blog`, `nav_bonus`, `nav_about`, `top_career`, `top_faq`, `top_contacts`, `top_delivery`, `top_guarantee`.

**Ожидаемый результат:** `php artisan test` (или ручная проверка) — все `__('store.*')` в drawer возвращают переводы для ru и en.

---

#### Task 7: Документация (обязательный чекпойнт)
**Файл:** `docs/frontend.md`

**Что сделать:**
- В раздел со структурой блоков добавить описание нового блока `mobile-menu`:
  - Назначение: выезжающее меню-drawer для viewport ≤1199px
  - Файлы: Blade (`blocks/common/mobile-menu/`), CSS (`blocks/common/mobile-menu/style.css`)
  - Интеграция: Alpine `storeHeader.menuOpen`, управляется бургером в `header`, overlay + panel
  - Поведение: slide-in слева, закрытие по ✕/клику на overlay/Escape, body scroll lock
  - Связь с существующим `mobile-nav` (нижняя панель) — ортогональны, не конфликтуют

**Ожидаемый результат:** блок задокументирован, будущие агенты/разработчики понимают назначение и точки интеграции.

---

#### Task 8: Сборка и верификация
**Команды:**
- `npm run build` — убедиться, что CSS собирается без ошибок, импорт `mobile-menu/style.css` резолвится.
- `vendor/bin/pint --dirty --format agent` — PHP не менялся, но запустить для консистентности (Blade не форматируется Pint, пропустить если грязно только Blade).
- Визуальная проверка (делегировать visual-engineering агенту или пользователь проверяет): на 375px и 768px открыть меню, проверить slide-анимацию, закрытие по Escape/overlay/✕, переключатель языка, переходы по ссылкам.

**Ожидаемый результат:** `npm run build` exit 0, drawer работает на мобильном и планшетном размерах.

---

## Commit Plan

6+ задач — разбиваем на 2 коммита:

### Commit 1: feat(frontend): add mobile slide-out menu drawer ≤1200px
После задач 1-5 (разметка + CSS):
- `resources/views/blocks/common/header/header.blade.php` (бургер @click)
- `resources/views/blocks/common/mobile-menu/mobile-menu.blade.php` (новый)
- `resources/views/layouts/app.blade.php` (include)
- `resources/js/blocks/common/header/index.js` (verbose logging)
- `resources/css/blocks/common/mobile-menu/style.css` (новый)
- `resources/css/app.css` (импорт)
- `lang/ru/store.php`, `lang/en/store.php` (i18n — задача 6 включена сюда)

### Commit 2: docs(frontend): document mobile-menu block
После задач 7-8:
- `docs/frontend.md`

---

## Risks & Notes

- **Имена роутов:** в задачах использованы предположительные имена (`new`, `promo`, `blog`...). Исполнитель ОБЯЗАН свериться с `routes/web.php` и существующей десктоп-навигацией в `header.blade.php` / `top-bar.blade.php` — использовать те же route names и href.
- **Область видимости Alpine:** если `x-data="storeHeader(...)"` объявлена только на `.header`, drawer её не увидит. Решение — поднять `x-data` на `<body>` в layout (Task 3). Альтернатива — Alpine global store (`Alpine.store('header', {...})`). Выбрать по минимальным изменениям.
- **Доступность:** `role="dialog"`, `aria-modal="true"`, фокус-ловушка. Минимум — Escape + aria-label (реализовано в Task 2). Полная фокус-ловушка опциональна (pre-MVP), отметить как future enhancement.
- **Существующий `mobile-nav` (нижняя панель 768-1199px):** не трогаем. Drawer и нижняя панель работают параллельно — это соответствует Figma-макету планшета.
- **iOS Safari `position: fixed` + body scroll lock:** существующий `closeMenu()` уже блокирует скролл через `overflow: hidden` на body. Проверить корректную работу на iOS (возможен баг с `position: fixed` — может потребоваться `overflow: hidden` + `position: fixed; top: -scrollY` трюк). Отметить как риск, проверить при QA.
