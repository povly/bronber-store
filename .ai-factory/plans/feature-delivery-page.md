# Implementation Plan: Доставка и оплата — payment cards + i18n + 1920px

Branch: feature/delivery-page
Created: 2026-08-07

## Original Request

новый блок отсюда [4 Figma nodes] Доставка и оплата

## Settings

- Testing: no
- Logging: minimal
- Docs: no

## Контекст дизайна (Figma 4 брейкпоинта)

Страница с 4 карточками способов оплаты + контакты.

**Структура:**
1. Заголовок: «Для наших клиентов доступны следующие варианты оплаты заказа:» (ExtraBold 25→40px)
2. 4 карточки (white bg, radius 5px) — каждая: иконка (gradient circle 50→65px) + title (ExtraBold 18→24px) + description (Medium 16→18px)
3. Подзаголовок: «По вопросам доставки и оплаты:» (ExtraBold 25→40px)
4. Контакты: phone + email (Medium 24→30px) с иконками

**4 способа оплаты:**
1. Оплата наличными — «Вы всегда можете выбрать вариант оплаты наличными при самовывозе»
2. Перевод на банковскую карту — «После оформления и согласования заказа менеджеры предоставят реквизиты»
3. Оплата по СБП — QR Code — «После подтверждения заказа вы получите QR-код»
4. Оплата в других валютах — «Просто попросите менеджера и он расскажет все детально»

**Layout:**
- Mobile (375): 1 колонка, карточки 329×244px
- Tablet (768): 2 колонки
- Desktop (1200): 4 в ряд, карточки ~280×334px
- Desktop (1920): 4 в ряд, карточки 340×334px

## Tasks

### Phase 1: Route + i18n
- [ ] **Task 1:** Добавить роут `/delivery` в `routes/web.php`
- [ ] **Task 2:** Создать `lang/ru/delivery.php` и `lang/en/delivery.php`

### Phase 2: Blade
- [ ] **Task 3:** Создать `views/delivery.blade.php` + `blocks/delivery/delivery.blade.php`

### Phase 3: CSS
- [ ] **Task 4:** Создать `css/blocks/delivery/style.css` (375/768/1200/1920px)

### Phase 4: Build
- [ ] **Task 5:** `npm run build` + Pint verification
