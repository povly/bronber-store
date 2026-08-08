# Implementation Plan: Личный кабинет (главная) — mobile-first ≤1200px

Branch: feature/profile-main-page
Created: 2026-08-08

## Original Request

создай новый блок и страницу Connect to Figma, channel go47qjzz https://www.figma.com/design/vfXiZV4QOG9DrZTXGDsoCf/Bronber-%D0%92%D0%B5%D1%80%D1%81%D1%82%D0%BA%D0%B0--Copy-?node-id=1010-20208&t=zzDhkvYBRCF4JNn2-4 https://www.figma.com/design/vfXiZV4QOG9DrZTXGDsoCf/Bronber-%D0%92%D0%B5%D1%80%D1%81%D1%82%D0%BA%D0%B0--Copy-?node-id=1010-20404&t=zzDhkvYBRCF4JNn2-4 https://www.figma.com/design/vfXiZV4QOG9DrZTXGDsoCf/Bronber-%D0%92%D0%B5%D1%80%D1%81%D1%82%D0%BA%D0%B0--Copy-?node-id=1010-19995&t=zzDhkvYBRCF4JNn2-4 https://www.figma.com/design/vfXiZV4QOG9DrZTXGDsoCf/Bronber-%D0%92%D0%B5%D1%80%D1%81%D1%82%D0%BA%D0%B0--Copy-?node-id=1010-18531&t=zzDhkvYBRCF4JNn2-4 адаптив с телефона! и поизучай готовые классы которые у меня есть в теме css и скрипт! тут в телефоне до 1200 пикслей, где Мой кабинет это блок со скроллом! там где заказы - сама карточка надо сделать как компонент, много где повторяется! Шапка и подвал у нас и так есть, без иземенений! Это личный кабинет главная!

## Settings

- Testing: no
- Logging: verbose
- Docs: no

## Контекст дизайна (Figma, channel go47qjzz)

**Важно:** 4 ноды (1010:20208, 1010:20404, 1010:19995, 1010:18531) — это **одна страница** «Личный кабинет (главная)» на 4 брейкпоинтах (375 / 768 / 1200 / 1920), а не 4 разных экрана. Пользователь явно просит **mobile-first ≤1200px** (телефон/планшет), где вкладка «Мой кабинет» — скроллимый блок.

### Структура mobile (375px, нода 1010:20208 — PRIMARY REFERENCE)
Сверху вниз между существующими header и mobile-nav:
1. **Заголовок + таб-бар**: «Личный кабинет» (ExtraBold 24px) + табы «Мой кабинет» (active, purple underline) / «Мои заказы» / «Избранное»
2. **Profile-summary card** (белая, скруглённая): «Игорь Валерьевич» + email + «Редактировать профиль» (purple #7212bc)
3. **Stats row — 3 карточки** (вертикально на mobile): Бонусы «1250 ₽» / Заказы «32» / Избранное «24», каждая с круговой иконкой 40×40
4. **«Последние заказы»** (заголовок) + 2 order-card
5. footer + mobile-nav — **существующие, без изменений**

### Order card (переиспользуемый паттерн)
`[image 86×86] [status badge 92×20] [order #] [date] [total]`
- image placeholder `#d9d9d9`
- status badge: «Ожидается» bg `#fff9ca` / «Выполнен» (другой цвет)
- «№32843» (SemiBold 16px), «Дата: 20.08.2026», «Сумма:» + «40.000 ₽»

### Цвета (из дизайн-системы)
- `#7212bc` — brand purple (active таб, ссылки)
- `#ffffff` — карточки
- `#f8f8f8` — фон страницы (уже `--color-page-bg`)
- `#fff9ca` — badge «Ожидается»
- `#d9d9d9` — placeholder изображений

### Адаптив (что меняется на >1200px — вне scope, но учитывать)
- 768px+: появляется поиск, табы расширяются («Личные данные»/«Смена пароля»/«Выход»), stats → 3 колонки, order-cards → 2 в ряд
- 1200px+: 2-колоночный layout (sidebar profile + контент)
- **Scope этого плана — mobile-first база ≤1200px.** Десктоп-раскладка (sidebar) — отдельная задача.

## Конвенции проекта (изучено)

- **Block-based**: `resources/{views,css,js}/blocks/{page}/` — каждый блок сам пушит стили через `@push('block-styles') @vite([...]) @endpush`
- **Переиспользуемые Blade-компоненты**: `resources/views/components/{name}.blade.php` + CSS в `resources/css/components/{name}.css` (импортируется в `app.css` глобально). Образец — `x-product-card`.
- **i18n**: `lang/{ru,en}/{page}.php`, в шаблонах `__('{page}.key')`
- **Роуты**: closure-based в `$register`-замыкании `routes/web.php`, дублируются по локали (RU без префикса, EN `/en/`)
- **Существующие CSS-классы для переиспользования**: `.section`, `.section__title`, `.section__top`, `.container`, `.tag` (purple pill badge), `.btn`/`.btn--primary`, `.card`. CSS-переменные: `--color-purple` (#7212bc), `--color-page-bg`, `--font-manrope`, `--shadow-card`. Функции: `fluid-type(min,max,from,to)`.
- **Alpine.js**: `x-data`, `x-show`, `x-collapse` (плагин collapse подключён), `@click`. Inline для простых компонент.
- **mock-данные**: в closure-роуте (как `cart`, `product`) — pre-MVP прототип.
- **mobile-nav** уже ссылается на `/profile` (hardcoded) — регистрируем именно этот URI.

## Решения

- **Route name/URI**: `profile` → `/profile` (совпадает с существующей ссылкой mobile-nav, не требует её правки). Пользователь сказал «Шапка и подвал без изменений» — mobile-nav тоже не трогаем.
- **View/wrapper**: `resources/views/profile.blade.php` (extends `layouts.app`, `<main class="profile-page">`).
- **Блоки**: nested-паттерн (как catalog/loyalty): `blocks/profile/{tabs,summary,stats,orders}/`.
- **order-card**: глобальный компонент `components/order-card.blade.php` + `css/components/order-card.css` (импорт в app.css) — т.к. переиспользуется на будущих страницах (история заказов и т.д.).
- **Табы**: Alpine `x-data="{ tab: 'cabinet' }"` с переключением; в scope — только вкладка «Мой кабинет» (главная), остальные — заглушки/скрыты.

## Tasks

### Phase 1: Route + wrapper + i18n

- [x] **Task 1:** Добавить роут `/profile` в `$register`-замыкание `routes/web.php`
  - `Route::get('/profile', fn () => view('profile', ['user' => [...], 'stats' => [...], 'orders' => [...]]))->name('profile');`
  - mock-данные: user `['name' => 'Игорь Валерьевич', 'email' => 'igor@example.com']`, stats `[['key'=>'bonuses','value'=>'1250 ₽'],...]`, orders `[['number'=>'№32843','status'=>'expected','date'=>'20.08.2026','total'=>'40 000 ₽','image'=>'...'], ...]`
  - Файл: `routes/web.php` (внутрь `$register`, рядом с другими роутами)

- [x] **Task 2:** Создать i18n-файлы
  - `lang/ru/profile.php` и `lang/en/profile.php`
  - Ключи: `title` («Личный кабинет»/«Personal account»), `tab_cabinet`/`tab_orders`/`tab_favorites`, `edit_profile` («Редактировать профиль»), `stat_bonuses`/`stat_orders`/`stat_favorites` + подзаголовки (`stat_bonuses_sub` «Доступно бонусов» и т.д.), `orders_title` («Последние заказы»), `order_date` («Дата:»), `order_total` («Сумма:»), `status_expected` («Ожидается»), `status_done` («Выполнен»), `order_details` («Подробнее о заказе»)

- [x] **Task 3:** Создать обёртку `resources/views/profile.blade.php`
  - `@extends('layouts.app')`, `@section('content') <main class="profile-page"> @include('blocks.profile.tabs.tabs') @include('blocks.profile.summary.summary') @include('blocks.profile.stats.stats') @include('blocks.profile.orders.orders') @endsection`
  - Файл: `resources/views/profile.blade.php`

### Phase 2: Переиспользуемый компонент order-card

- [x] **Task 4:** Создать `x-order-card` компонент
  - `resources/views/components/order-card.blade.php`: `@props(['class'=>'', 'status'=>'expected'])`, неявные props: `$image`, `$status`, `$orderNumber`, `$date`, `$total`, `$href`
  - Разметка по Figma: `.order-card` (белая, скруглённая) → image-wrap + body (status badge `.tag`-стиль + order number + date + total)
  - Статус-цвет через модификатор: `.order-card--expected` (badge `#fff9ca`), `.order-card--done` (badge зелёный/серый)
  - CSS: `resources/css/components/order-card.css` (BEM `.order-card__*`, mobile-first, `fluid-type()` для типографики)
  - Импорт в `resources/css/app.css`: `@import 'components/order-card.css';` (в блок reusable components, после card.css)

### Phase 3: Блоки кабинета (mobile-first)

- [x] **Task 5:** Блок tabs — заголовок + таб-бар
  - `resources/views/blocks/profile/tabs/tabs.blade.php` + `resources/css/blocks/profile/tabs/style.css`
  - `<section class="profile-tabs">`: h1 «Личный кабинет» + список табов (Alpine `x-data="{ tab: 'cabinet' }"` на странице или блоке, `@click="tab='...'"`, active-класс с purple underline)
  - Переиспользовать `.section__title` для h1

- [x] **Task 6:** Блок summary — карточка профиля
  - `resources/views/blocks/profile/summary/summary.blade.php` + `resources/css/blocks/profile/summary/style.css`
  - `<section class="profile-summary">`: белая карточка → имя `{{ $user['name'] }}` + email (иконка+текст) + ссылка «Редактировать профиль» (`--color-purple`)
  - `$user` приходит из роута

- [x] **Task 7:** Блок stats — 3 stat-карточки
  - `resources/views/blocks/profile/stats/stats.blade.php` + `resources/css/blocks/profile/stats/style.css`
  - `<section class="profile-stats">`: `@foreach($stats as $stat)` → `.profile-stats__item` (круглая иконка + title + value + subtitle)
  - Mobile: вертикальный стек; 768px+: 3 колонки (grid)
  - Inline SVG-иконки для бонусов/заказов/избранного (как в loyalty/about — inline в `@php`)

- [x] **Task 8:** Блок orders — «Последние заказы» + order-card
  - `resources/views/blocks/profile/orders/orders.blade.php` + `resources/css/blocks/profile/orders/style.css`
  - `<section class="profile-orders">`: h2 «Последние заказы» (`.section__title`) + `@foreach($orders as $order) <x-order-card :image="..." :status="..." :order-number="..." :date="..." :total="..." /> @endforeach`
  - Mobile: 1 колонка; 768px+: 2 колонки grid

### Phase 4: Build + verify

- [x] **Task 9:** Сборка и проверка
  - `npm run build` — exit 0
  - `php artisan route:list --name=profile` — роут `/profile` и `/en/profile` зарегистрированы
  - `view('profile')->render()` — рендерится без 500
  - Визуальная проверка `/profile` на 375px через `/aif-figma-qa` (опционально, после реализации)
  - `vendor/bin/pint --dirty --format agent`

## Commit Plan

- **Commit 1** (after tasks 1-3): `feat(profile): add route, wrapper view, and i18n`
  - Files: `routes/web.php`, `resources/views/profile.blade.php`, `lang/ru/profile.php`, `lang/en/profile.php`
- **Commit 2** (after task 4): `feat(order-card): add reusable order card component`
  - Files: `resources/views/components/order-card.blade.php`, `resources/css/components/order-card.css`, `resources/css/app.css`
- **Commit 3** (after tasks 5-8): `feat(profile): add cabinet blocks (tabs, summary, stats, orders)`
  - Files: `resources/views/blocks/profile/{tabs,summary,stats,orders}/*`, `resources/css/blocks/profile/{tabs,summary,stats,orders}/*`
- **Commit 4** (after task 9): `build(profile): verify build and formatting` (только если появились правки форматирования; иначе слить в Commit 3)
