---
paths:
  - 'app/MoonShine/**'
---

# Moon Shine

## MoonShine #[Icon]: только существующие иконки, иначе 500 всей админки
Имя в #[Icon('…')] ресурса/меню должно существовать в vendor/moonshine/moonshine/src/UI/resources/views/icons/ (без префикса heroicons.): иначе View [icons.X] not found — 500 на ВСЕХ страницах админки, т.к. меню рендерит все ресурсы сразу. Проверять наличие: ls vendor/moonshine/moonshine/src/UI/resources/views/icons/ | grep имя.
