[← Фронтенд](frontend.md) · [Back to README](../README.md)

# Деплой

> ⚠️ **Проект находится на стадии pre-MVP.** Продакшн-деплой пока не настроен —
> эта страница описывает подготовку и чек-лист для будущей стадии релиза.

## Текущее состояние

- Доменные модели и БД-логика не реализованы (mock-данные в роутах)
- Нет CI/CD-пайплайна
- Нет production `.env`-конфигурации
- Нет Dockerfile / compose-окружения для продакшна

Деплой будет релевантен после реализации доменных моделей, реальной корзины/оформления и аутентификации.

## Подготовка к деплою (чек-лист)

Когда проект приблизится к релизу, необходимо настроить:

### Окружение

| Параметр | Значение для production |
|----------|------------------------|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_LOCALE` | `ru` |
| `DB_CONNECTION` | `mysql` (рекомендуется для production) |
| Кеш-драйвер | `redis` или `database` (не `file`) |
| Очередь | `redis` или `database` |

### Безопасность

- [ ] `APP_DEBUG=false` — без stack traces в error-ответах
- [ ] Сильный `APP_KEY` (32 символа)
- [ ] Секреты — только в environment variables, не в коде
- [ ] CORS — whitelist доменов (не `*`)
- [ ] Rate limiting на публичные эндпоинты
- [ ] Security headers: HSTS, `X-Content-Type-Options`, `X-Frame-Options`, CSP
- [ ] Скрыть информационные заголовки (`Server`, `X-Powered-By`)
- [ ] Отключить debug-эндпоинты в production
- [ ] Whitelist полей в API-ответах (DTO/serializer, не ORM-модель целиком)
- [ ] Mass assignment защищён (`$fillable` / `$guarded` / PHP 8 attributes)

> Полный чеклист безопасности — в скилле `aif-security-checklist` и
> [глобальных security-правилах](https://github.com/povly/.config/blob/main/opencode/security-rules.md).

### Логирование

- [ ] Логи не содержат токенов, паролей, PII
- [ ] Структурированные error responses (code + message, без stack traces)
- [ ] Скраббинг чувствительных данных через маскер перед логированием

## Команды сборки

```bash
composer install --no-dev --optimize-autoloader     # без dev-зависимостей
npm install --omit=dev
npm run build                                         # production-сборка фронта
php artisan migrate --force                           # миграции БД
php artisan config:cache                              # кеширование конфига
php artisan route:cache                               # кеширование роутов
php artisan view:cache                                # компиляция Blade
```

## Варианты деплоя

Laravel можно развернуть через:

- **[Laravel Cloud](https://cloud.laravel.com/)** — рекомендованный путь для production Laravel
- VPS (Forge / Vapor / собственный сервер) + Nginx + PHP-FPM
- Docker (Dockerfile будет создан на стадии релиза — см. скилл `aif-dockerize`)

## CI/CD

CI/CD-пайплайн (GitHub Actions / GitLab CI) будет добавлен на стадии релиза —
см. скилл `aif-ci`. Пайплайн должен включать: linting (Pint), static analysis (Rector dry-run),
тесты (Pest), security checks.

## See Also

- [Установка](getting-started.md) — локальная разработка
- [Разработка](development.md) — инструменты качества кода
- [`.ai-factory/DESCRIPTION.md`](../.ai-factory/DESCRIPTION.md) — нефункциональные требования (безопасность)
