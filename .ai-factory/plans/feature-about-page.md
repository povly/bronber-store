# Implementation Plan: About page — slider timeline + i18n + 1920px

Branch: feature/about-page
Created: 2026-08-07

## Original Request

About page route with Figma nodes 870:1168, 870:986, 870:848, 870:704.
Timeline лет (2017+) как горизонтальный слайдер через slider.js.

## Settings

- Testing: no
- Logging: minimal
- Docs: no

## Tasks

### Phase 1: i18n
- [x] **Task 1:** Создать `lang/ru/about.php` и `lang/en/about.php` (отдельные файлы, не store.php)

### Phase 2: Blade + Slider
- [x] **Task 2:** Переписать `blocks/about/about.blade.php`: timeline → slider.js (perView 2/3/5), хардкод → `__('about.*')`, удалить more-list

### Phase 3: CSS
- [x] **Task 3:** `css/blocks/about/style.css`: годы-слайдер на всех брейкпоинтах, `--slider-slide` per perView, `--slider-gap` fixed px, extend 1200px→1920px

### Phase 4: JS
- [x] **Task 4:** `js/blocks/about/index.js`: убрать markerLeft (маркер per-slide через CSS)

### Phase 5: Build
- [x] **Task 5:** `npm run build` + Pint + visual verification (мобайл perView=2, десктоп perView=5, клик переключает панель)
