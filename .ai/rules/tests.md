---
paths:
  - 'tests/**'
---

# Tests

## Тесты страниц/форм: beforeEach сеет языки + clearCache
Фич-тесты DB-страниц/форм обязаны в beforeEach сеять языки и чистить кэш (иначе роуты/локаль не зарегистрируются): resolve(LanguageService::class)->clearCache(); Language::factory()->default()->create(['code'=>'ru','sort_order'=>0]); Language::factory()->create(['code'=>'en','sort_order'=>1]). Образцы: FaqPageTest, ContactsPageTest, ContactFormTest (валидный/невалидный POST, throttle 429, /en-маршрут).
