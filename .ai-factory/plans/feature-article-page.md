# Implementation Plan: Article page — вёрстка по Figma (4 брейкпоинта)

Branch: feature/article-page
Created: 2026-08-07

## Original Request

https://www.figma.com/design/vfXiZV4QOG9DrZTXGDsoCf/Bronber-%D0%92%D0%B5%D1%80%D1%81%D1%82%D0%BA%D0%B0--Copy-?node-id=866-6488&t=TiR4cFDNlgFbGJoW-4 https://www.figma.com/design/vfXiZV4QOG9DrZTXGDsoCf/Bronber-%D0%92%D0%B5%D1%80%D1%81%D1%82%D0%BA%D0%B0--Copy-?node-id=866-6131&t=TiR4cFDNlgFbGJoW-4 https://www.figma.com/design/vfXiZV4QOG9DrZTXGDsoCf/Bronber-%D0%92%D0%B5%D1%80%D1%81%D1%82%D0%BA%D0%B0--Copy-?node-id=866-6306&t=TiR4cFDNlgFbGJoW-4 https://www.figma.com/design/vfXiZV4QOG9DrZTXGDsoCf/Bronber-%D0%92%D0%B5%D1%80%D1%81%D1%82%D0%BA%D0%B0--Copy-?node-id=866-5947&t=TiR4cFDNlgFbGJoW-4 адаптивная верстка! это статься, назовем как-то /blog/article В конце есть блок похожий на это home-news или возможно это он и есть

## Settings

- Testing: no (только вёрстка)
- Logging: minimal (frontend-only)
- Docs: no

## Контекст дизайна (Figma)

### Структура страницы статьи (node 866:5947, 1920px)

4 фрейма Figma: `866:6488`, `866:6131`, `866:6306`, `866:5947` (адаптивные брейкпоинты).

### Секции (page-specific, header/footer — shared)

1. **Hero image** — крупное изображение на всю ширину контента (1441×500 на 1920px)
2. **Article header:**
   - Title: «Запуск нового направления Bronber Auto Service» (крупный, multi-line)
   - Subtitle: «Новый этап развития экосистемы Bronber»
   - Date: «09/02/2026»
3. **Article body** (max-width ~1441px на desktop):
   - Lead paragraph: «Bronber объявляет о запуске нового направления...»
   - Body paragraphs: «Bronber Auto Service — это профессиональный автосервис...» (2 параграфа)
   - 2 inline images (710×710 каждая, бок о бок на desktop)
4. **CTA:** кнопка «Перейти к направлению» (240×48) + 2 иконки-share (48×48)
5. **«Другие новости»** — 3 статические карточки `.article` (464×650 каждая) в grid, НЕ slider:
   - Каждая: image, tag «#news», date «25/12/25», title, desc, «Детальнее» button
   - Переиспользует существующий компонент `.article`

### Трансформация лейаута

| Breakpoint | Article body | Images | News grid |
|---|---|---|---|
| 375px (base) | 1 колонка, full-width | стопкой | 1 колонка |
| 768px | шире, отступы больше | стопкой | 2 колонки |
| 1200px | max-width контент | 2 в ряд | 3 колонки |
| 1920px | max-width, крупнее | 2 в ряд | 3 колонки |

## Существующее состояние

| Файл | Статус | Действие |
|---|---|---|
| `routes/web.php:19` | ✅ route `/blog/{slug}` → `view('article')` | Без изменений |
| `resources/views/article.blade.php` | ✅ wrapper, включает `blocks.article.article` | Добавить `@include('blocks.article.news')` |
| `resources/views/blocks/article/article.blade.php` | ❌ не существует | **Создать** |
| `resources/views/blocks/article/news.blade.php` | ❌ не существует | **Создать** (3 карточки) |
| `resources/css/blocks/article-page/style.css` | ⚠️ exists (162 строки, `.article-page-block`) | **Дополнить** — все секции + 4 брейкпоинта |
| `resources/js/blocks/article/index.js` | ❌ | Не нужен (статичная страница) |

## Commit Plan

- **Commit 1** (после Task 1-2): `feat(article): create blade template + i18n`
- **Commit 2** (после Task 3-4): `feat(article): rewrite CSS for 4 breakpoints`
- **Commit 3** (после Task 5): `feat(article): news section + build verification`

## Tasks

### Phase 1: Структура и контент

- [x] **Task 1: i18n-строки для страницы статьи**
  Добавить в `lang/ru/store.php` и `lang/en/store.php` секцию `// Article page`:
  - `article_title`, `article_subtitle`, `article_date`, `article_lead`
  - `article_body_1`, `article_body_2` (параграфы)
  - `article_cta` («Перейти к направлению» / «Go to direction»)
  - `article_news_title` («Другие новости» / «Other news»)
  - Строки для 3 news-карточек (переиспользовать данные из `$news` массива)
  Файлы: `lang/ru/store.php`, `lang/en/store.php`

- [x] **Task 2: Создать Blade-шаблоны**
  **`resources/views/blocks/article/article.blade.php`** — BEM-структура:
  ```blade
  @push('block-styles')
      @vite(['resources/css/blocks/article-page/style.css'])
  @endpush

  <section class="article-page-block">
      <div class="container">
          <div class="article-page-block__hero">
              <img class="article-page-block__hero-img lazy" data-src="..." alt="...">
          </div>
          <div class="article-page-block__content">
              <span class="article-page-block__date">{{ __('store.article_date') }}</span>
              <h1 class="article-page-block__title">{{ __('store.article_title') }}</h1>
              <p class="article-page-block__subtitle">{{ __('store.article_subtitle') }}</p>
              <div class="article-page-block__body">
                  <p>{{ __('store.article_lead') }}</p>
                  <p>{{ __('store.article_body_1') }}</p>
                  <div class="article-page-block__images">
                      <img class="article-page-block__img lazy" ...>
                      <img class="article-page-block__img lazy" ...>
                  </div>
                  <p>{{ __('store.article_body_2') }}</p>
              </div>
              <div class="article-page-block__cta">
                  <a href="#" class="btn btn--primary">{{ __('store.article_cta') }}</a>
              </div>
          </div>
      </div>
  </section>
  ```
  **`resources/views/blocks/article/news.blade.php`** — 3 статические карточки:
  ```blade
  @push('block-styles')
      @vite(['resources/css/blocks/article-page/style.css'])
  @endpush

  <section class="article-news">
      <div class="container">
          <h2 class="article-news__title">{{ __('store.article_news_title') }}</h2>
          <div class="article-news__grid">
              @foreach($relatedNews as $item)
                  <article class="article-news__item">
                      <a href="#" class="article">
                          {{-- переиспользует .article card component --}}
                      </a>
                  </article>
              @endforeach
          </div>
      </div>
  </section>
  ```
  **`resources/views/article.blade.php`** — добавить второй include:
  ```blade
  @include('blocks.article.article')
  @include('blocks.article.news')
  ```
  Все тексты через `__()`. Images через `data-src` + класс `lazy` (vanilla-lazyload).
  Файлы: `blocks/article/article.blade.php` (create), `blocks/article/news.blade.php` (create), `article.blade.php` (modify)
  Зависит от: Task 1

### Phase 2: Стилизация

- [x] **Task 3: CSS — mobile-first base (375px)**
  Дополнить `resources/css/blocks/article-page/style.css` (уже 162 строки). Добавить:
  - `.article-page-block__hero` — full-width image, aspect-ratio для mobile
  - `.article-page-block__content` — single column, padding
  - `.article-page-block__title` — крупный font-size через `fluid-type(375px, 768px, ...)`
  - `.article-page-block__body` — paragraphs, line-height, max-width
  - `.article-page-block__images` — single column на mobile
  - `.article-page-block__cta` — center, margin-top
  - `.article-news` — секция новостей
  - `.article-news__grid` — 1 колонка на mobile
  - Использовать CSS custom properties из `base.css`
  Файлы: `resources/css/blocks/article-page/style.css`
  Зависит от: Task 2

- [x] **Task 4: Responsive-брейкпоинты (768px, 1200px, 1920px)**
  Добавить media queries:
  - `@media (min-width: 768px)`:
    - `.article-news__grid` → 2 колонки
    - Контент шире, отступы через `fluid-type(768px, 1200px, ...)`
  - `@media (min-width: 1200px)`:
    - `.article-page-block__images` → 2 колонки (flex/grid)
    - `.article-news__grid` → 3 колонки
    - Значения через `fluid-type(1200px, 1920px, ...)` — масштабирование до 1920px
    - `.article-page-block__title` font-size увеличивается
  Файлы: `resources/css/blocks/article-page/style.css`
  Зависит от: Task 3

### Phase 3: Полировка

- [x] **Task 5: CTA hover + build verification**
  - CTA button: hover state (использует `.btn--primary` hover)
  - Article images: `lazy` класс + `data-src` (vanilla-lazyload)
  - Hero image: `object-fit: cover`
  - News cards: переиспользуют `.article` hover-эффект (button reveal) — убедиться что `setArticleBtnHeight()` из `app.js` покрывает `.article-news__item .article`
  - Запустить `npm run build` — проверить что нет ошибок
  Файлы: `resources/css/blocks/article-page/style.css`, blade-шаблоны
  Зависит от: Task 4
