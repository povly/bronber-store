# Implementation Plan: Программа лояльности — 4 блока + i18n + 1920px

Branch: feature/loyalty-page
Created: 2026-08-07

## Original Request

тут 4 блока в целом! [4 Figma nodes 934:9166, 934:9637, 934:9408, 925:681]

## Settings

- Testing: no
- Logging: minimal
- Docs: no

## Контекст дизайна (Figma 925:681, 1920px)

**4 блока страницы:**

### Block 1 — Hero
- Title: «Программа лояльности» (ExtraBold 80px)
- Subtitle: «Покупайте запчасти и получайте бонусы за каждый заказ» (Medium 24px)
- Image (874×491px, справа от текста на десктопе)
- Button: «Зарегистрироваться» (gradient bg, SemiBold 14px)

### Block 2 — Benefits bar (white card)
- 3 колонки с разделителями
- Col 1: «1 бонус = 1 ₽» (ExtraBold 32px) + «Бонусами можно оплатить до 30%» (Regular 18px)
- Col 2: «Бонусы начисляются с каждой покупки» / «Бонусы не сгорают и действуют всегда»
- Col 3: «Эксклюзивные акции и предложения»

### Block 3 — «Как это работает?» (3 white cards)
- Title: «Как это работает?» (Bold 40px)
- Card 1: icon + «Совершайте покупки» (ExtraBold 32px) + «Покупайте запчасти на сайте или в наших магазинах» (Medium 18px)
- Card 2: icon + «Получайте бонусы» + «Мы начисляем бонусы на ваш счет за каждый заказ»
- Card 3: icon + «Оплачивайте бонусами» + «Оплачивайте бонусы для оплаты до 30% следующих заказов»

### Block 4 — Bottom (2 cards side by side)
- Left: «Пример начисления бонусов» (Bold 40px) — Сумма заказа: 10,000 ₽ / Начислено бонусов (1%): 100 ₽ + purple banner «Чем больше покупок — тем больше бонусов!»
- Right: «Частые вопросы» (Bold 40px) — 4 FAQ (accordion): Когда доступны? / Срок действия? / Где баланс? / Оплатить весь заказ?

## Tasks

### Phase 1: Route + i18n
- [x] **Task 1:** Добавить роут `/loyalty`
- [x] **Task 2:** Создать `lang/ru/loyalty.php` и `lang/en/loyalty.php` (~25 ключей)

### Phase 2: Blade
- [x] **Task 3:** Создать `views/loyalty.blade.php` + `blocks/loyalty/loyalty.blade.php` (4 блока)

### Phase 3: CSS
- [x] **Task 4:** Создать `css/blocks/loyalty/style.css` (375/768/1200/1920px)

### Phase 4: Build
- [x] **Task 5:** `npm run build` + Pint verification
