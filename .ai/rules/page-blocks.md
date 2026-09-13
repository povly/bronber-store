---
paths:
  - 'app/Support/PageBlocks/**'
---

# Page Blocks

## Блоки страниц: {page}-{name} → blocks/{page}/{name}.blade.php
Тип блока рендерится вью `resources/views/blocks/{page}/{name}.blade.php` — первое тире = страница (`home-partners` → `blocks/home/partners`, `delivery-methods` → `blocks/delivery/methods`). Legacy-demo типы (hero, text, gallery, contacts, faq, featured-products) живут в `blocks/legacy/` — закрытый список `BlockRenderer::LEGACY_TYPES`. Новые блоки называть только с префиксом страницы; каталог `components/page-blocks/` больше не существует.

## MediaFallback: схемы медиа-полей обязательны для новых блоков
Медиа-поля (MediaManagerPicker) каждого типа блока объявляются в схемах: `PageBlockLibrary::mediaSchemas()` (страницы) / `SettingsResolver::MEDIA_SCHEMAS` (настройки шапки/подвала/меню). Формат: строка = ключ на уровне блока, '<список>' => [ключи] = ключи внутри элементов. Тип без схемы молча не получает наследование медиа из дефолтной локали — при добавлении блока с медиа-полем обязательно обновить схему.
