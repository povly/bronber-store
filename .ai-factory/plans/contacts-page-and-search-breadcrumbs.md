# Implementation Plan: Страница «Контакты» из DB-блоков с универсальными заявками + рабочий поиск и breadcrumbs

Branch: main
Created: 2026-09-13

## Original Request

новый блок  http://bronber_store.test/contacts для страницы, там в целом заголовок, описание, номер телефона, адрес, карта яндекс как html iframe! и еще форма есть! форму тоже настраиваем!

ааа и breadcrumbs сделай чтобы в поиске работали!

заявки храним как модель Заявки, потому что могут быть разные формы!

и данные там могут быть любые в форме если что! тоесть надо как-то сделать так чтобы все работало и не фиксированные данные, все зависит от формы, там потом еще будут вакансии, это потом!

## Settings

- Testing: yes
- Logging: verbose
- Docs: no  # только WARN [docs] в /aif-implement, обязательного чекпойнта нет

## Контекст разведки (факты кодовой базы)

- **Блочный пайплайн**: тип `{page}-{name}` → вью `blocks/{page}/{name}.blade.php` (`BlockRenderer::viewFor`); класс блока в `app/Support/PageBlocks/Blocks/{Page}/`, регистрация в `PageBlockLibrary::blocks()`; медиа-поля — только через слот в `PageBlockLibrary::mediaSchemas()` (у новых блоков медиа-полей НЕТ → не трогаем). Существующий `contact-list` блок не подходит: его разметка (список иконок) ≠ прототип контактов.
- **Роуты**: единственный фиксированный страничный роут — `/`; DB-страницы обслуживает catch-all `/{slug}` / `/{locale}/{slug}` (`routes/web.php`, замыкание `$register`, авто-дублирование по локалям). GET `/contacts` (строка 18) — временный прототип, подлежит удалению. Ссылки в прототипах — `url('/slug')`, не route()-имена.
- **Крошки DB-страниц**: `PageBreadcrumbs::forPage()` (Главная → опубликованные предки → текущая, через `LinkResolver`, JSON-LD) рендерятся во `view('page')` — страница контактов получит их автоматически после конвертации.
- **Прототип контактов**: `resources/views/blocks/contacts/contacts.blade.php` — h1/подзаголовок/телефон/email/адрес, форма (`action=""`, обработчика НЕТ), Яндекс-карта iframe (`yandex.ru/map-widget/v1/?ll=37.539822%2C55.748792&z=15`), стили `resources/css/blocks/contacts/style.css`, тексты в `lang/{ru,en}/store.php` (`contacts_*`).
- **Поиск сломан в корне**: в шапке два инпута `name="search"` БЕЗ `<form>`, кнопка `type="button"` без обработчика; JS (`resources/js/blocks/common/header/index.js`) только переключает тип поиска (типы в `config/search.php`: name/sku). На каталог `/catalog?search=` попасть нельзя. Breadcrumbs в `blocks/catalog/hero/hero.blade.php` захардкожены: `[['label' => 'Главная', 'url' => '/'], ['label' => $title]]` — не отражают поиск, «Главная» не локализована (`/` вместо `/en`). Компонент `x-breadcrumbs` сам строит JSON-LD BreadcrumbList из items.
- **Заявки**: модели заявок нет — создаём. По решению пользователя хранилище универсальное: `form` (дискриминатор) + JSON `payload`, поля формы НЕ фиксированы в таблице. Будущие формы (вакансии и др.) добавляют свою вью + FormRequest + POST-роут, не меняя модель/сервис/письмо/ресурс.
- **Админка**: ресурсы в `app/MoonShine/Resources/{Resource}/` + `Pages/`, регистрация в `MoonShineServiceProvider::boot()` (`->resources([...])`) и меню `MoonShineLayout::menu()` (атрибуты `#[Group('content')]`, `#[Order(N)]`). Namespace split v4: `MoonShine\Laravel\Resources\ModelResource`, поля `MoonShine\UI\Fields\*`.
- **Сидеры**: правило `.ai/rules/seeders.md` — firstOrCreate + fill-when-empty для переводов с пустым content (`isEmptyContent()`), контент админа не перезаписывать, лог per-locale (`:created`/`:exists`/`:filled`). Образцы: `FaqPageSeeder`, `DeliveryPageSeeder` (тексты из lang-файлов прототипа).

## Commit Plan

- **Commit 1** (после задач 1–3): `feat(leads): универсальная модель заявок и обработка формы контактов`
- **Commit 2** (после задач 4–7): `feat(contacts): страница контактов из DB-блоков с картой и формой`
- **Commit 3** (после задачи 8): `feat(admin): ресурс «Заявки» в MoonShine`
- **Commit 4** (после задач 9–10): `feat(search): рабочий поиск в шапке и breadcrumbs каталога`
- **Commit 5** (после задач 11–14): `test: покрытие контактов, заявок и поиска`

## Tasks

### Phase 1 — Домен заявок (универсальное хранилище)

- [ ] Task 1: Миграция + модель `Lead` (+ фабрика)
  - `php artisan make:model Lead -f --no-interaction`; миграция `create_leads_table`:
    - `form` string, index — ключ формы (сейчас `'contacts'`, позже `'vacancies'` и т.п.)
    - `payload` json — ВСЕ поля формы как есть (никаких фиксированных name/phone/email колонок)
    - `locale` string default `'ru'`; `status` string default `'new'`, index
    - timestamps
  - `app/Models/Lead.php`: `declare(strict_types=1)`, `#[Fillable(['form', 'payload', 'locale', 'status'])]`, cast `payload => array`; константы `STATUS_NEW/IN_PROGRESS/DONE`; rich-метод `changeStatus(string $to): void` с guard допустимых переходов (new→in_progress→done, new→done) и `DomainException` на некорректный; scope `status()`. Фабрика: `payload` => ['name' => fake name, 'phone' => '+7 …', 'email' => safe, 'message' => sentence].
  - LOGGING: без логов в модели (чистый домен); логирует сервис (Task 2).

- [ ] Task 2: FormRequest + `LeadService` + контроллер + POST-роут + конфиг (depends on 1)
  - `app/Http/Requests/Content/StoreLeadRequest.php`: правила ТЕКУЩЕЙ формы контактов (поля формы пока фиксированы в коде по решению пользователя): `name` required|string|max:255, `phone` required|string|max:32, `email` required|email|max:255, `message` nullable|string|max:5000; атрибуты ru. Это валидация формы, НЕ модели — будущие формы заведут свои Request-классы.
  - `app/Services/Content/LeadService.php` (модуль Content по ARCHITECTURE.md): `submit(string $form, array $payload, string $locale): Lead` — создаёт Lead, отправляет `LeadSubmittedMail` (Task 3) в try/catch (падение письма НЕ роняет запрос — заявка уже в БД); получатель: `recipient_email` из блока `contacts-main` страницы slug `contacts` (чтение `Page`→`translation($locale)`→`content`, поиск `_type === 'contacts-main'`), fallback `config('leads.recipient')`.
  - `app/Http/Controllers/Content/LeadController.php` (`declare(strict_types=1)`, тонкий): `store(StoreLeadRequest $request)` → `LeadService::submit('contacts', $request->validated(), app()->getLocale())` → `redirect()->back()->with('success', __('store.contacts_form_success'))`.
  - `routes/web.php` внутри `$register`: `Route::post('/contacts', [LeadController::class, 'store'])->middleware('throttle:10,1')->name('leads.store');` — авто-дублируется для `/en/contacts`. GET-прототип строки 18 НЕ трогаем до Task 7 (POST не конфликтует: другой глагол).
  - `config/leads.php`: `['recipient' => env('LEADS_RECIPIENT_EMAIL')]` — секрет только через env.
  - LOGGING (verbose, БЕЗ PII — не логировать имя/телефон/email пользователя!): `[LeadService] lead created {lead_id, form, locale, fields:[keys]}` INFO; `[LeadService] mail queued/sent {lead_id, recipient}` INFO; `[LeadService] mail failed {lead_id, error}` ERROR (только текст исключения); контроллер — вход DEBUG `{form, locale}` без тела.

- [ ] Task 3: Mailable `LeadSubmittedMail` (depends on 1)
  - `php artisan make:mail LeadSubmittedMail --markdown=emails.lead-submitted --no-interaction`; конструктор `(public string $form, public array $payload, public string $locale)`; тема «Новая заявка с сайта — {form}»; `replyTo(payload['email'])` если задан; тело — универсальный рендер: таблица key => value по payload (ключи-humanized), работает для ЛЮБОЙ формы без правок.
  - LOGGING: отправка/ошибка логируется сервисом (Task 2), mailable сам не логирует.

### Phase 2 — Страница контактов из блоков

- [ ] Task 4: Блок `ContactsMainBlock` + регистрация (depends on —)
  - `app/Support/PageBlocks/Blocks/Contacts/ContactsMainBlock.php` (образец: `FaqItemsBlock`/`ContactListBlock`), `final class … implements PageBlock`; тип **`contacts-main`** (первое тире = страница `contacts` → вью `blocks.contacts.main`):
    - `title` Text («Заголовок (h1)»), `subtitle` Textarea («Описание»), `phone` Text, `email` Text, `address` Text
    - `map_html` Textarea («Карта — HTML iframe (Яндекс)», hint: вставить целиком `<iframe …>` из конструктора Яндекс.Карт)
    - настройки формы: `submit_label` Text, `consent_text` Textarea, `recipient_email` Text (hint: пусто → из config), `success_message` Text
    - `limit: 1, category: 'Контакты'`, description, иконка `phone`
  - Регистрация в `PageBlockLibrary::blocks()`. Медиа-полей нет → `mediaSchemas()` НЕ трогаем (правило `.ai/rules/page-blocks.md`).
  - LOGGING: без своего логирования (подхватится BlockRenderer WARN при ошибках рендера).

- [ ] Task 5: Вью `resources/views/blocks/contacts/main.blade.php` + CSS (depends on 4)
  - Разметка прототипа (`section.contacts` → `contacts__top` = инфо-колонка + форма, `contacts__map`), данные из `$block[...]` с fallback `?? ''`; `@push('block-styles') @vite(['resources/css/blocks/contacts/style.css']) @endpush`.
  - Карта: `{!! $block['map_html'] ?? '' !!}` (доверенный ввод администратора; при пустом — блок карты не выводить).
  - Форма: `action="{{ request()->url() }}"` method POST (локале-корректно: /contacts и /en/contacts), `@csrf`; поля ТЕКУЩЕЙ формы контактов зафиксированы в коде (имя/телефон/email/сообщение) с лейблами из `lang store.contacts_form_*`; `old()` подстановка; баннеры: `@if(session('success'))` → текст `success_message` блока, `@if($errors->any())` — сводный список ошибок; inline-ошибки полей (`@error`).
  - CSS: минимальные стили `.contacts__alert--success/--error` в `resources/css/blocks/contacts/style.css` (стили прототипа не ломать).
  - Телефон/email как ссылки tel:/mailto: (нормализация телефона как в `DeliveryPageSeeder`).
  - LOGGING: без JS; серверный лог — Task 2.

- [ ] Task 6: Сидер `ContactsPageSeeder` + DatabaseSeeder (depends on 4)
  - `php artisan make:seeder ContactsPageSeeder --no-interaction`; строго по правилу `.ai/rules/seeders.md` и образцу `DeliveryPageSeeder`: firstOrCreate `Page` slug `contacts` (is_published, sort_order 0) + firstOrCreate переводов ru/en c `isEmptyContent()` fill-when-empty; контент — один блок `contacts-main`: тексты из `lang/{ru,en}/store.php` (`contacts_*`), карта iframe `ll=37.539822,55.748792&z=15` из прототипа, `recipient_email` пусто (config fallback); title/meta_title/meta_description ru/en.
  - Регистрация в `database/seeders/DatabaseSeeder.php` рядом с `FaqPageSeeder`/`DeliveryPageSeeder`.
  - LOGGING: `[ContactsPageSeeder] contacts page seeded, locales={locales}` INFO per-locale `:created`/`:exists`/`:filled`.

- [ ] Task 7: Удаление прототипа контактов (depends on 5, 6)
  - `routes/web.php`: удалить строку GET `/contacts` (прототип); catch-all начнёт обслуживать DB-страницу.
  - Удалить `resources/views/contacts.blade.php` и `resources/views/blocks/contacts/contacts.blade.php`.
  - `lang/{ru,en}/store.php` ключи `contacts_*` ОСТАВИТЬ (источник сидера + лейблы формы), добавить `contacts_form_success`.
  - Проверить `grep -rn "route('contacts')\|route('contacts'"` — заменить на `url('/contacts')` (правило `.ai/rules/routes.md`).
  - LOGGING: `php artisan route:list --path=contacts` для проверки.

### Phase 3 — Админка «Заявки»

- [ ] Task 8: `LeadResource` + Pages + регистрация (depends on 1)
  - `app/MoonShine/Resources/Lead/LeadResource.php` + `Pages/{LeadIndexPage,LeadFormPage}.php` (образец `SettingResource`/`PageResource`): `#[Icon('inbox-arrow-down')]`, `#[Group('content')]`, `#[Order(20)]`; title `__('Заявки')`; `activeActions()->except(Action::CREATE)` — заявки приходят с сайта; column `'created_at'` или `'form'`.
  - Поля (whitelist, только чтение кроме статуса): `form` Badge, `status` Select (new/in_progress/done, русский labels), `locale`, `payload` JSON readonly, `created_at`; `name/phone/email` НЕ выводить отдельными колонками — данные произвольные (при желании в FormPage показать payload pretty).
  - Фильтры/поиск: по `form`, `status`. simplePaginate.
  - Регистрация: `MoonShineServiceProvider::boot()` → `->resources([…, LeadResource::class])`; пункт меню в `MoonShineLayout::menu()`.
  - LOGGING: `afterSave` `[LeadResource] lead_id={id} status={status} saved by moonshine_user_id={uid}` INFO (без PII).

### Phase 4 — Поиск + breadcrumbs

- [ ] Task 9: Рабочий поиск в шапке (depends on —)
  - `resources/views/blocks/common/header/header.blade.php`: обернуть `.header__search` в `<form method="GET" @submit.prevent="submitSearch($event)">`; action НЕ нужен (навигация из JS), добавить `hidden` input `search_type` `:value="searchType"`; кнопку поиска `type="submit"` (иконку/aria-label сохранить). Проблема двух инпутов `name="search"` решается ручной сборкой URL (ниже) — нативный submit предотвращён.
  - `resources/js/blocks/common/header/index.js` (Alpine `storeHeader`): метод `submitSearch(e)` — определить видимый непустой инпут (`$refs.searchInputDesktop`/`searchInputMobile`), собрать URL: база = локализованный каталог (default locale → `{{ url('/catalog') }}`, иначе `{{ url('/'.$locale.'/catalog') }}` — вычислить в Blade и пробросить параметром в Alpine-данные), query: `search=<encodeURIComponent(q)>` + `&search_type=<тип>`; `window.location.assign(url)`; пустой query → не отправлять. IE11/iOS9-safe (никакого optional chaining/fetch).
  - LOGGING: `console.debug('[storeHeader] search submitted, type=' + this.searchType)` — БЕЗ текста запроса.

- [ ] Task 10: Заголовок и breadcrumbs каталога при поиске (depends on —)
  - `resources/views/blocks/catalog/hero/hero.blade.php`: в `@php`-блоке — `$query = trim((string) request()->input('search', ''))`; локализованные URL: `$homeUrl` (default → `url('/')`, иначе `url('/'.$locale.'/')`), `$catalogUrl` (аналогично `/catalog`); при непустом `$query`: `$title = __('store.search_results_title', ['query' => $query])`, items breadcrumbs = `[Главная → $homeUrl], [Каталог → $catalogUrl], [Поиск: query → null (текущая)]`; иначе `[Главная → $homeUrl], [Каталог → null]`. Убрать хардкод `'url' => '/'`.
  - JSON-LD BreadcrumbList построится компонентом `x-breadcrumbs` автоматически из items.
  - `lang/{ru,en}/store.php`: `'search_results_title' => 'Поиск: :query'` / `'Search: :query'`.
  - Фильтрация mock-товаров по запросу — ВНЕ_scope (каталог ещё прототип, доменных моделей нет).
  - LOGGING: без (чистая вью-логика).

### Phase 5 — Тесты (Pest, по образцу FaqPageTest/DeliveryPageTest; читать скилл testing-best-practices)

- [ ] Task 11: `tests/Feature/ContactsPageTest.php` (depends on 5, 7)
  - `php artisan make:test ContactsPageTest --pest --no-interaction`; beforeEach как в FaqPageTest (Language ru/en + clearCache); фабрика Page slug `contacts` + переводы с блоком `contacts-main` (в т.ч. `map_html` с `<iframe … yandex …>`):
    - рендер `/contacts` и `/en/contacts` из блоков (тексты ru/en видны);
    - 404 при отсутствии страницы и для черновика (`draft()` state);
    - в HTML есть iframe Яндекс-карты и форма (`<form` + поле `name="name"`);
    - breadcrumbs: «Главная» + текущий заголовок + JSON-LD `BreadcrumbList`.
  - Дополнительно: сидер — `ContactsPageSeeder` заполняет пустой перевод (`:filled`), не перезаписывает непустой.

- [ ] Task 12: `tests/Feature/LeadSubmissionTest.php` (depends on 2, 3, 7)
  - `Mail::fake()`; страница contacts с блоком (в т.ч. `recipient_email`):
    - валидный POST `/contacts` (и `/en/contacts`) → redirect назад + session success; в БД `leads` строка `form=contacts`, `locale`, `payload` содержит name/phone/email/message; `LeadSubmittedMail` отправлен на адрес из блока (fallback — config);
    - невалидный POST (пустые name/phone, битый email) → errors сессия, БД пуста;
    - throttle: 11-й POST в минуту → 429;
    - сбой почты (fake throwing) → заявка всё равно создана, ответ 302 (проверить try/catch сервиса).

- [ ] Task 13: `tests/Feature/SearchBreadcrumbsTest.php` (depends on 9, 10)
  - GET `/catalog?search=bosch` → 200, содержит «Поиск: bosch», JSON-LD с 3 позициями, ссылки `/` и `/catalog`;
    GET `/en/catalog?search=bosch` → «Search: bosch», home-ссылка `/en`, каталог `/en/catalog`;
    GET `/catalog` без search → обычный след «Главная → Каталог».

### Phase 6 — Финализация

- [ ] Task 14: Качество и сборка (depends on all)
  - `vendor/bin/pint --dirty --format agent`; прогон новых тестов: `php artisan test --compact tests/Feature/ContactsPageTest.php tests/Feature/LeadSubmissionTest.php tests/Feature/SearchBreadcrumbsTest.php`; смежные: `FaqPageTest`, `DeliveryPageTest`, `BlockRendererTest`, `PageBreadcrumbsTest`;
  - `npm run build` (менялись JS шапки) — иначе Vite-манифест не увидит изменения; попросить пользователя при необходимости `npm run dev`;
  - `php artisan migrate` + `php artisan db:seed --class=ContactsPageSeeder` на dev-БД для ручной проверки `/contacts` и админки «Заявки»;
  - Ручной смоук через браузер: форма (успех/ошибки), письмо в log-mailer, каталог с `?search=`, `/en/…`-варианты.

## Верификационные критерии (Definition of Done)

1. `/contacts` и `/en/contacts` рендерятся из DB-блоков (редактируются в MoonShine: тексты, телефон, адрес, iframe карты, настройки формы), прототип-роут и вью удалены.
2. Форма сохраняет заявку (`leads`: form+payload+locale+status) и шлёт письмо администратору; ошибки валидации и успех показываются на странице; 11 запросов в минуту → 429.
3. «Заявки» доступны в админке: список/фильтры/просмотр payload, смена статуса.
4. Поиск из шапки ведёт на `/catalog?search=…&search_type=…` (и `/en/…`), заголовок и breadcrumbs (включая JSON-LD) корректны для ru/en, «Главная» локализована.
5. Все новые тесты зелёные, Pint чистый, `npm run build` прошёл.

## Out of scope

- Фильтрация mock-товаров каталога по поисковому запросу (доменных моделей ещё нет).
- Конструктор полей формы в админке (по решению пользователя — поля форм пока в коде).
- Форма вакансий и прочие будущие формы (инфраструктура под них готова: Lead универсален).
- Перевод админ-интерфейса ресурсов на en.
