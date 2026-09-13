---
paths:
  - 'database/seeders/**'
---

# Seeders

## Сидеры DB-страниц: fill-when-empty для переводов
Страницные сидеры (Home/Faq/будущие contacts-* и т.п.) кроме firstOrCreate должны заполнять демо-контентом существующие переводы с ПУСТЫМ content (`content === null || []`) — стаб-страница, созданная из админки до сидера, иначе навсегда блокирует демо-данные. Написанный админом контент никогда не перезаписывать. Пример: FaqPageSeeder::isEmptyContent() + attributes(). Логировать результат per-locale (`:created`/`:exists`/`:filled`).
