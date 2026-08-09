# Implementation Plan: Server-side active-класс для табов профиля

Branch: main (без создания новой ветки — `git.create_branches: false`)
Created: 2026-08-08

## Original Request

@resources/views/blocks/profile/tabs/tabs.blade.php сделай так чтобы класс active ставился от бэкенда и по ссылке проверял, что в текущей находимся!

## Settings

- Testing: no
- Logging: minimal (Blade-only изменение, без backend-логики)
- Docs: no

## Постановка

Табы профиля сейчас управляют классом `is-active` через Alpine.js (`x-data="{ tab: 'cabinet' }"`, `@click`, `:class`). Нужно перевести определение активного таба на server-side: класс `is-active` ставится в Blade на основе текущего route name, а не по клиентскому клику.

## Ключевые наблюдения

1. **`x-scrollable` зависит от Alpine scope.** Плагин `resources/js/alpine/plugins/scrollable.js` (стр. 51, 67) вызывает `evaluate()` внутри `Alpine.directive('scrollable', ...)`. Без `x-data` на предке `evaluate` падает — в коде есть try/catch с комментарием «element may not have Alpine scope yet». **Вывод:** `x-data` с `<section class="profile-tabs">` убирать нельзя, но его можно обнулить до `x-data="{}"`, сохранив scope для `x-scrollable` и убрав стейт `tab`.

2. **Laravel-идиома — `request()->routeIs(...)`** Принимает variadic паттерны и возвращает `true`, если любой совпал с `route()->getName()` через `Str::is()`. Поддерживает wildcard (`profile.orders.*`). Используем напрямую в Blade — без промежуточных переменных.

3. **Маршруты и табы (route name → таб):**

   | Таб | route name(s) | URL |
   |-----|---------------|-----|
   | cabinet | `profile` | `/profile` |
   | orders | `profile.orders`, `profile.order` | `/profile/orders`, `/profile/orders/{id}` |
   | favorites | `favorites` | `/favorites` |
   | data | `profile.data` | `/profile/data` |
   | password | `profile.password` | `/profile/password` |

   `profile.order` (детальная страница заказа `/profile/orders/{id}`) тоже должна подсвечивать таб «orders», т.к. это вложенная страница раздела заказов.

4. **logout-ссылки** (`href="#"`) не имеют реального route — их трогать не нужно, `is-active` на них не ставится.

## Подход

В одном Blade-файле заменяем Alpine active-state на server-side проверку через `request()->routeIs()`:

- Убираем `x-data="{ tab: 'cabinet' }"` → `x-data="{}"` (пустой scope для `x-scrollable`)
- Убираем `@click="tab = '...'"` и `:class="{ 'is-active': tab === '...' }"` со всех ссылок-табов
- Убираем захардкоженный `is-active` с первой ссылки (cabinet)
- На каждую ссылку-таб вешаем server-side: `{{ request()->routeIs('profile') ? 'is-active' : '' }}`
- Для таба orders: `{{ request()->routeIs('profile.orders', 'profile.order') ? 'is-active' : '' }}`

## Tasks

### Phase 1: Реализация

- [x] **Task 1: Заменить Alpine active-state на server-side route detection**
  **File:** `resources/views/blocks/profile/tabs/tabs.blade.php`

  Изменения:

  1. **`<section>` (стр. 45):** заменить `x-data="{ tab: 'cabinet' }"` на `x-data="{}"`.
     ```blade
     <section class="profile-tabs" x-data="{}">
     ```

  2. **Cabinet (стр. 56-60):** убрать хардкод `is-active`, `@click`, `:class`. Добавить server-side проверку:
     ```blade
     <a href="{{ route('profile') }}"
        class="profile-tabs__btn {{ request()->routeIs('profile') ? 'is-active' : '' }}">
     ```

  3. **Orders (стр. 61-65):** убрать `@click`, `:class`. Server-side с двумя паттернами (orders list + order detail):
     ```blade
     <a href="{{ route('profile.orders') }}"
        class="profile-tabs__btn {{ request()->routeIs('profile.orders', 'profile.order') ? 'is-active' : '' }}">
     ```

  4. **Favorites (стр. 66-70):** убрать `@click`, `:class`:
     ```blade
     <a href="{{ route('favorites') }}"
        class="profile-tabs__btn {{ request()->routeIs('favorites') ? 'is-active' : '' }}">
     ```

  5. **Data (стр. 71-75):** убрать `@click`, `:class`:
     ```blade
     <a href="{{ route('profile.data') }}"
        class="profile-tabs__btn {{ request()->routeIs('profile.data') ? 'is-active' : '' }}">
     ```

  6. **Password (стр. 76-80):** убрать `@click`, `:class`:
     ```blade
     <a href="{{ route('profile.password') }}"
        class="profile-tabs__btn {{ request()->routeIs('profile.password') ? 'is-active' : '' }}">
     ```

  **НЕ трогать:**
  - `<section>` как таковой — только значение `x-data`
  - logout-ссылки (`href="#"`) — у них нет route name, `is-active` не нужен
  - `x-scrollable` на `.profile-tabs__nav` — работает независимо от стейта `tab`

### Phase 2: Проверка

- [x] **Task 2: Build + визуальная проверка на всех страницах профиля**
  1. `npm run build`
  2. Открыть каждую страницу и проверить активный таб:
     - `/profile` → cabinet подсвечен
     - `/profile/orders` → orders подсвечен
     - `/profile/orders/{id}` → orders подсвечен (страница деталей заказа)
     - `/favorites` → favorites подсвечен
     - `/profile/data` → data подсвечен
     - `/profile/password` → password подсвечен
  3. Проверить, что `x-scrollable` (кастомный скроллбар на десктопе ≥1200px) работает
  4. Проверить, что при клике по табу происходит реальная навигация (full page load), а не JS-переключение

## Риски

- **`routeIs()` на несуществующем route name:** если посетитель оказался на странице вне profile-группы, ни один таб не подсветится — это корректное поведение.
- **i18n-дублирование роутов:** роуты для `en` имеют те же route names (`profile`, `profile.orders` и т.д.) — `routeIs()` будет работать одинаково для обеих локалей.
