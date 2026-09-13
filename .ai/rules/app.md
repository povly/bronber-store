---
paths:
  - 'app/**'
---

# App

## Заявки — хранение и доставка во внешней CRM, без почты
Хранение/доставка заявок — только через API внешней CRM: в приложении НЕ заводить модели/таблицы/ресурсы админки/письма для заявок (стек Lead и почтовый интерим удалены осознанно). Сейчас: форма контактов только валидируется (StoreContactRequest → POST /contacts, throttle) и отвечает success. ПРИМЕР отправки в CRM закомментирован в ContactFormController (Http::post + services.crm.url/token из env CRM_URL/CRM_API_TOKEN). При подключении API — раскомментировать, добавить конфиг services.crm, валидацию не трогать.

## MoonShine Text/Textarea: сырой текст в БД, escapeOnApply(false) обязательно
MoonShine v4 по умолчанию кодирует значения Text/Textarea при сохранении: prepareRequestValue → htmlspecialchars(doubleEncode: true) — каждый сейв админки добавляет слой (& → &amp; → &amp;amp;…), включая поля внутри flexible-layouts блоков. Конвенция проекта: в БД храним сырой текст, экранирование на рендере (Blade {{ }}). Всем контентным Text/Textarea (блоки, SEO-поля, настройки шапки/подвала) ставить ->escapeOnApply(static fn (): bool => false). Поле без модификатора — скрытый баг для текстов с &/'/".
