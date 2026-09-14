---
paths:
  - 'resources/views/**'
---

# Views

## Вью: локализованные URL-ссылки + breadcrumbs через x-breadcrumbs
Ссылки в прототипах/блоках обязаны учитывать локаль: default → url('/path'), остальные → url('/'.$locale.'/path') (default-локаль — LanguageService::defaultCode(), не хардкод ru). Хардкод 'url' => '/' ломает /en-страницы (пример фикса — catalog/hero breadcrumbs). Breadcrumbs рендерить компонентом x-breadcrumbs :items (JSON-LD BreadcrumbList строится автоматически); на DB-страницах крошки даёт PageBreadcrumbs, в блоках их не дублировать.
