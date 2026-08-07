# Implementation Plan: Contacts — пересборка по Figma (4 брейкпоинта)

Branch: feature/contacts-page
Created: 2026-08-07

## Original Request

https://www.figma.com/design/vfXiZV4QOG9DrZTXGDsoCf/Bronber-%D0%92%D0%B5%D1%80%D1%81%D1%82%D0%BA%D0%B0--Copy-?node-id=863-5375&t=TiR4cFDNlgFbGJoW-4 мобилка, 375, потом 768 https://www.figma.com/design/vfXiZV4QOG9DrZTXGDsoCf/Bronber-%D0%92%D0%B5%D1%80%D1%81%D1%82%D0%BA%D0%B0--Copy-?node-id=863-5075&t=TiR4cFDNlgFbGJoW-4, потом 1200 https://www.figma.com/design/vfXiZV4QOG9DrZTXGDsoCf/Bronber-%D0%92%D0%B5%D1%80%D1%81%D1%82%D0%BA%D0%B0--Copy-?node-id=863-5226&t=TiR4cFDNlgFbGJoW-4 и 1920 https://www.figma.com/design/vfXiZV4QOG9DrZTXGDsoCf/Bronber-%D0%92%D0%B5%D1%80%D1%81%D1%82%D0%BA%D0%B0--Copy-?node-id=863-4927&t=TiR4cFDNlgFbGJoW-4 Connected on port 3055!
Copy the channel ID: wbe9v11z у нас есть contacts route, но ты проверь!

## Settings

- Testing: no (только вёрстка, форма без бэкенда)
- Logging: minimal (frontend-only, нет backend-логики)
- Docs: no

## Контекст дизайна (Figma)

### Структура страницы «Контакты»

4 фрейма Figma (node-id → ширина):
- `863:5375` → 375px (mobile, height=2642, single column)
- `863:5075` → 768px (tablet, height=2159, wider single column)
- `863:5226` → 1200px (desktop, height=1581, two-column layout)
- `863:4927` → 1920px (wide desktop, height=1962, two-column + full-width map)

### Секции страницы (контент-specific, header/footer — shared, уже реализованы)

1. **Заголовок:** «Открыты к деловому диалогу» (крупный текст, multi-line)
2. **Подзаголовок:** «Оставьте свою заявку и наш менеджер свяжется с вами в ближайшее время»
3. **Контактная информация:** телефон `+7 (985) 449-8000`, email + адрес «Москва, Пресненская набережная 12»
4. **Форма обратной связи** (5 полей, underline-стиль ввода):
   - «Ваше имя*» (required)
   - «Ваш номер телефона*» (required)
   - «Ваш e-mail*» (required)
   - «Тема обращения» (optional)
   - Textarea: placeholder-текст сообщения
   - Consent: «Нажимая кнопку «Отправить» вы соглашаетесь с условиями обработки данных»
   - Кнопка: «Отправить»
5. **Яндекс Карта:** full-width контейнер-плейсхолдер (на pre-MVP — стилизованный div, не реальный API)

### Трансформация лейаута

| Breakpoint | Лейаут | Карта |
|---|---|---|
| 375px (base) | 1 колонка, всё стопкой | под формой, ~329px wide |
| 768px | 1 колонка, шире поля ввода | ~720×326 |
| 1200px | 2 колонки: инфо слева, форма справа | под колонками, full-width |
| 1920px | 2 колонки (шире), карта 1440×530 | max-width контент |

### CSS-методология проекта

- Mobile-first base (375px), без media query
- `fluid-type(minVw, maxVw, minVal, maxVal)` — кастомная PostCSS-функция → `clamp()`
- Breakpoints: `@media (min-width: 768px)`, `@media (min-width: 1200px)`
- **НЕТ** `@media (min-width: 1920px)` — значения масштабируются через `fluid-type(1200px, 1920px, val1200, val1920)` внутри 1200px-блока
- BEM: `&__element` nesting via `postcss-nested`
- CSS custom properties: `--color-purple`, `--color-black`, `--font-manrope` и др. (из `resources/css/common/base.css`)
- Form inputs: underline-стиль (только `border-bottom: 1px solid`)

## Существующее состояние (что переписываем)

| Файл | Статус | Действие |
|---|---|---|
| `resources/views/contacts.blade.php` | ✅ exists (7 строк, wrapper) | Без изменений |
| `resources/views/blocks/contacts/contacts.blade.php` | ⚠️ exists (55 строк, hardcoded RU) | **Переписать** под новый Figma + i18n |
| `resources/css/blocks/contacts/style.css` | ⚠️ exists (249 строк, 3 брейкпоинта) | **Переписать** под 4 брейкпоинта + 2-column |
| `resources/js/blocks/contacts/index.js` | ❌ не существует | Не нужен (только вёрстка) |
| `routes/web.php:16` | ✅ route exists | Без изменений |
| `lang/ru/contacts.php`, `lang/en/contacts.php` | ❓ проверить | Создать если нет |

## Commit Plan

- **Commit 1** (после Task 1-2): `feat(contacts): rewrite blade template + i18n translations`
- **Commit 2** (после Task 3-4): `feat(contacts): rewrite CSS for 4 breakpoints (375/768/1200/1920)`
- **Commit 3** (после Task 5): `feat(contacts): form UX polish + build verification`

## Tasks

### Phase 1: Структура и контент

- [x] **Task 1: i18n-файлы для страницы контактов**
  Создать/обновить файлы переводов `lang/ru/contacts.php` и `lang/en/contacts.php`.
  Включить все строки из Figma:
  - `title`: «Открыты к деловому диалогу» / «Open to business dialogue»
  - `subtitle`: «Оставьте свою заявку и наш менеджер свяжется с вами в ближайшее время»
  - `phone_label`, `email_label`, `address`
  - Поля формы: `name`, `phone`, `email`, `subject`, `message_placeholder`, `consent`, `submit`
  - Проверить существующий формат в `lang/ru/` и `lang/en/` (другие страницы) — следовать тому же паттерну ключей.
  Файлы: `lang/ru/contacts.php`, `lang/en/contacts.php`

- [x] **Task 2: Переписать Blade-шаблон blocks/contacts/contacts.blade.php**
  Полная переработка HTML-структуры под Figma-дизайн. BEM-нейминг.
  Структура:
  ```blade
  @push('block-styles')
      @vite(['resources/css/blocks/contacts/style.css'])
  @endpush

  <section class="contacts">
      <div class="container">
          <div class="contacts__body">
              <div class="contacts__info">
                  <h1 class="contacts__title">{{ __('contacts.title') }}</h1>
                  <p class="contacts__subtitle">{{ __('contacts.subtitle') }}</p>
                  <div class="contacts__contacts-list">
                      <a href="tel:..." class="contacts__phone">+7 (985) 449-8000</a>
                      <div class="contacts__email">email · адрес</div>
                  </div>
              </div>
              <form class="contacts__form" action="" method="POST">
                  @csrf
                  <div class="contacts__field">
                      <label>{{ __('contacts.name') }}*</label>
                      <input type="text" name="name" required>
                  </div>
                  <!-- phone, email, subject, textarea — аналогично -->
                  <div class="contacts__consent">{{ __('contacts.consent') }}</div>
                  <button type="submit" class="btn btn--primary contacts__submit">{{ __('contacts.submit') }}</button>
              </form>
          </div>
          <div class="contacts__map">{{ __('contacts.map_placeholder') }}</div>
      </div>
  </section>
  ```
  Все тексты через `__()`. Form inputs — underline-стиль (без рамки, только `border-bottom`). Плейсхолдеры textarea из Figma.
  Файлы: `resources/views/blocks/contacts/contacts.blade.php`
  Зависит от: Task 1

### Phase 2: Стилизация

- [x] **Task 3: Переписать CSS — mobile-first base (375px)**
  Полная переработка `resources/css/blocks/contacts/style.css`. Базовые стили для mobile (375px):
  - `.contacts` — отступы через `fluid-type(375px, 768px, ...)`
  - `.contacts__title` — крупный font-size, `font-weight: 700`, multi-line (Figma: «Открыты / к деловому / диалогу»)
  - `.contacts__subtitle` — серый текст, `fluid-type` для font-size
  - `.contacts__info` / `.contacts__form` — single column (mobile base)
  - `.contacts__field input` / `textarea` — underline-стиль: `border: none; border-bottom: 1px solid var(--color-gray); background: transparent; padding: ...`
  - `.contacts__field label` — мелкий текст над инпутом
  - `.contacts__consent` — мелкий серый текст
  - `.contacts__submit` — кнопка (`btn btn--primary`), full-width на mobile
  - `.contacts__map` — контейнер-плейсхолдер с min-height, `background: var(--color-gray-bg)`, centered text
  - Использовать CSS custom properties из `base.css` (`--color-*`, `--font-manrope`, `--shadow-*`)
  Файлы: `resources/css/blocks/contacts/style.css`
  Зависит от: Task 2

- [x] **Task 4: Добавить responsive-брейкпоинты (768px, 1200px, 1920px fluid)**
  Дописать media queries в `style.css`:
  - `@media (min-width: 768px)`:
    - Форма шире, отступы больше через `fluid-type(768px, 1200px, ...)`
    - Кнопка отправки — inline (не full-width)
    - Карта шире (~720px)
  - `@media (min-width: 1200px)`:
    - **Two-column layout**: `.contacts__body { display: flex; gap: ... }` — info слева, form справа
    - Значения через `fluid-type(1200px, 1920px, val1200, val1920)` — масштабирование до wide desktop
    - Карта: full-width под колонками, `fluid-type(1200px, 1920px, 400px, 530px)` для height
    - `.contacts__title` font-size увеличивается
  - Проверить соответствие Figma-данным для каждого брейкпоинта (размеры элементов из node info)
  Файлы: `resources/css/blocks/contacts/style.css`
  Зависит от: Task 3

### Phase 3: Полировка

- [x] **Task 5: Form UX + build verification**
  Финальная полировка и проверка сборки:
  - Input focus states: `border-bottom-color: var(--color-purple)` при `:focus`
  - Consent checkbox: стилизованный кастомный чекбокс через CSS `:checked` (или `<input type="checkbox">` + `label`)
  - Submit button hover: `opacity` / `transform` микро-анимация
  - Textarea: `resize: vertical`, `min-height`
  - Запустить `npm run build` — проверить что нет ошибок, CSS скомпилирован
  - Визуальная проверка на 375/768/1200/1920 (через DevTools responsive mode)
  Файлы: `resources/css/blocks/contacts/style.css`, `resources/views/blocks/contacts/contacts.blade.php`
  Зависит от: Task 4
