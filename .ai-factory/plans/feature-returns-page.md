# Implementation Plan: Возврат и обмен — text page + i18n + 1920px

Branch: feature/returns-page
Created: 2026-08-07

## Original Request

https://www.figma.com/design/vfXiZV4QOG9DrZTXGDsoCf/Bronber-Вёрстка--Copy-?node-id=925-8390 https://www.figma.com/design/vfXiZV4QOG9DrZTXGDsoCf/Bronber-Вёрстка--Copy-?node-id=925-8119 https://www.figma.com/design/vfXiZV4QOG9DrZTXGDsoCf/Bronber-Вёрстка--Copy-?node-id=925-8258 https://www.figma.com/design/vfXiZV4QOG9DrZTXGDsoCf/Bronber-Вёрстка--Copy-?node-id=925-357 Возрат в обмен!

## Settings

- Testing: no
- Logging: minimal
- Docs: no

## Контекст дизайна (Figma 4 брейкпоинта)

Текстовая страница «Возврат и обмен». Только текст — без форм, слайдеров, интерактива.

**Шрифты (Manrope):**
- Заголовок: ExtraBold 25px (mobile) → 32px (1200px) → 40px Bold (1920px)
- Подзаголовок: ExtraBold 25px → 32px → 40px
- Параграф: Regular 16px → 18px, lineHeight 20.8px → 23.4px

**Контент (7 блоков):**
1. Заголовок: «Возврат и обмен»
2. Параграф: 14 дней на возврат, причины возврата
3. Параграф: условия — товарный вид, упаковка, без следов установки
4. Подзаголовок: перечень товаров не подлежащих возврату (Постановление №2463)
5. Параграф: масла, присадки, технически сложные товары + способы возврата (адрес магазина)
6. Подзаголовок: возврат товара ненадлежащего качества
7. Параграф: гарантия производителя, предпродажная проверка, правильный монтаж

## Tasks

### Phase 1: Route + i18n
- [ ] **Task 1:** Добавить роут `/returns` в `routes/web.php` (RU + EN duplicate)
- [ ] **Task 2:** Создать `lang/ru/returns.php` и `lang/en/returns.php`

### Phase 2: Blade
- [ ] **Task 3:** Создать `views/returns.blade.php` + `blocks/returns/returns.blade.php`

### Phase 3: CSS
- [ ] **Task 4:** Создать `css/blocks/returns/style.css` (375/768/1200/1920px)

### Phase 4: Build
- [ ] **Task 5:** `npm run build` + Pint verification
