# План: Фикс — изображения home-partners не показываются на index (страница «pages → index»)

**Дата:** 2026-09-13
**Ветка:** main (git.create_branches = false — без создания ветки)
**Режим:** fast

## Original Request

home-partners изображении на pages - index не показывается

## Диагноз (подтверждён разведкой)

Цепочка воспроизведения бага на ru-локали страницы `index`:

1. При пересохранении страницы через MoonShine (flexible-layouts + `MediaManagerPicker::multiple()`)
   поле `images` блока `home-partners` записалось в `page_translations.content` как
   **JSON-строка вместо массива** (двойное кодирование):
   `"images":"[\"home/partners/brembo.png\",…]"` (факт из БД, ru).
   EN-версия не пересохранялась — там честный массив, поэтому работает.
2. `resources/views/components/page-blocks/page/home-partners.blade.php`:
   `collect($block['images'] ?? [])` от строки даёт коллекцию из **одного элемента-строки**,
   которая превращается в мусорный путь `/storage/["home/partners/brembo.png",…]`.
3. `<x-img>` (`app/View/Components/Img.php`): `resolveSrc()` → `file_exists(public_path(…))`
   → false → `$found = false` → тег `<img>` **не рендерится вовсе**.

Сопутствующие факты (не баги):
- Пути после пересохранения стали дисково-относительными (`home/partners/brembo.png`) —
  это корректно обрабатывается существующим префиксом `/storage/` (доказательство: ru-блок
  `home-advs` с тем же паттерном относительных путей работает).
- Файлы на месте: `storage/app/public/home/partners/*.{png,webp,avif}`; symlink
  `public/storage → storage/app/public` существует; `x-img` сам подберёт лучший формат
  (avif → webp → png).

Вывод: единственный дефект — неспособность шаблона принять `images` в виде JSON-строки.
Такое состояние будет возникать снова при каждом сохранении страницы через админку,
потоому фикс делаем устойчивым к обоим форматам хранения.

## Settings

- **Testing:** да — регрессионный Pest-тест на рендер блока
- **Logging:** verbose — debug-лог при невалидной строке `images`
- **Docs:** нет — только `WARN [docs]` по завершении, без обязательного чекпоинта
- **Roadmap:** артефакт ROADMAP.md отсутствует — linkage пропущен

## Tasks

### Task 1 — Нормализовать поле `images` в шаблоне блока

**Файл:** `resources/views/components/page-blocks/page/home-partners.blade.php`

В `@php`-блоке перед существующей коллекцией декодировать строковый формат:

```php
@php
    // MediaManagerPicker stores a list of disk-relative paths; a form
    // re-saved via MoonShine may persist them as a JSON-encoded string —
    // decode it. Legacy Json format ({src: path}) is still accepted.
    $rawImages = $block['images'] ?? [];

    if (is_string($rawImages)) {
        $decoded = json_decode($rawImages, true);

        if (! is_array($decoded)) {
            Log::debug('[home-partners] images is a non-JSON string, images skipped');
        }

        $rawImages = is_array($decoded) ? $decoded : [];
    }

    $images = collect($rawImages)
        ->map(…существующая цепочка без изменений…);
```

Требования:
- Существующий pipeline (map → filter → префикс `/storage/`) не менять — он корректен.
- Не трогать `home-advs` и другие блоки — они не затронуты (scope: только home-partners).
- Логирование: `Log::debug` при невалидной строке (verbose-режим); валидные пути не логировать.
- После правки: `vendor/bin/pint --dirty --format agent`.

### Task 2 — Регрессионный тест

**Файл:** `tests/Feature/BlockRendererTest.php` (Pest, паттерн существующих тестов файла)

1. Fixture (самоочищающаяся): в тесте создать `storage/app/public/home/partners-test/logo.png`
   (`File::ensureDirectoryExists` + `File::put`, в `afterEach`/finally — удалить каталог).
2. `it('renders home-partners images stored as a JSON-encoded string')`:
   блок `['_type' => 'home-partners', 'title' => 'Наши партнеры', 'images' => '["home/partners-test/logo.png"]']`
   → `resolve(BlockRenderer::class)->render([$block], 'page')`
   → `expect($html)->toContain('home-partners__title')->toContain('/storage/home/partners-test/logo')`.
3. `it('skips the partners section when images string is not valid JSON')`:
   `'images' => 'not-json'` → `$html` не содержит `home-partners__`.

### Task 3 — Верификация

1. `vendor/bin/pint --dirty --format agent`
2. `php artisan test --compact tests/Feature/BlockRendererTest.php`
3. Проверка на реальных данных БД (контент ru страницы index):
   `php artisan tinker --execute 'echo resolve(App\Support\PageBlocks\BlockRenderer::class)->render(json_decode(App\Models\PageTranslation::query()->whereRelation("page","slug","index")->where("locale","ru")->value("content"), true), "page");'`
   → в выводе присутствуют `/storage/home/partners/brembo` (и другие логотипы), нет `["home/partners`.
4. Попросить пользователя прогнать полный suite: `php artisan test --compact`.
5. Фронтенд-проверка силами пользователя: обновить страницу `/` (при необходимости `npm run build`).

## Commit Plan

Меньше 5 задач — один коммит по завершении (только по явному запросу пользователя):

```
fix(page-blocks): decode JSON-string partner logos in home-partners block
```
