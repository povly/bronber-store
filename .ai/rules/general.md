---
paths:
  - '*'
---

# General

## Dev-окружение: pint/vite запускать через php/node напрямую
В этом окружении бинарники без exec-бита: vendor/bin/pint и node_modules/.bin/vite падают «Отказано в доступе». Запускать: php vendor/bin/pint --dirty --format agent; node node_modules/vite/bin/vite.js build. Смоук локальных страниц: curl http://bronber_store.test/… (после изменений JS обязателен npm-билд, иначе Vite-манифест не увидит файл).
