# Modal Plugin

Alpine.js plugin для управления модальными окнами. Стек модалок + magic `$modal`.

## Подключение

Плагин зарегистрирован в `resources/js/app.js`:

```js
import modal from './alpine/plugins/modal'
Alpine.plugin(modal)
```

## API

### `$modal` (magic) — использование в шаблонах

| Метод | Описание |
|---|---|
| `$modal.show(id)` | Открыть модалку (добавить в стек) |
| `$modal.show(id, { replace: true })` | Закрыть все, открыть только эту |
| `$modal.hide()` | Закрыть верхнюю модалку |
| `$modal.hide('id')` | Закрыть конкретную модалку |
| `$modal.hideAll()` | Закрыть все модалки |
| `$modal.isOpen(id)` | Модалка открыта? |
| `$modal.isTop(id)` | Модалка на вершине стека? |
| `$modal.depth(id)` | Позиция в стеке (1, 2, 3...) |

### `$store.modal` — прямой доступ к store

То же самое, но через store. Полезно в JS-коде вне шаблонов:

```js
Alpine.store('modal').show('login')
Alpine.store('modal').show('confirm', { replace: true })
Alpine.store('modal').hide()
Alpine.store('modal').hide('login')
Alpine.store('modal').hideAll()
```

## Стек модалок

Модалки хранятся в массиве `stack`. Открытие новой добавляет её поверх предыдущей — без закрытия.

```
$modal.show('catalog')   → stack: ['catalog']
$modal.show('filters')   → stack: ['catalog', 'filters']
$modal.hide()            → stack: ['catalog']         (закрылась верхняя)
$modal.hide('catalog')   → stack: []                  (закрылась конкретная)
```

### Replace — закрыть все и открыть одну

```html
<button @click="$modal.show('success', { replace: true })">
    Оплатить
</button>
```

Закроет catalog, filters, что угодно — и покажет только success.

## Примеры

### Простая модалка

```html
<button @click="$modal.show('login')">Войти</button>

<div x-show="$modal.isOpen('login')"
     x-transition
     @keydown.escape.window="$modal.hide()"
     class="fixed inset-0 z-50 flex items-center justify-center">

    <div class="absolute inset-0 bg-black/50" @click.self="$modal.hide()"></div>

    <div x-trap="$modal.isTop('login')"
         class="relative bg-white rounded-lg p-6 z-10">
        <h2>Вход</h2>
        <button @click="$modal.hide()">Закрыть</button>
    </div>
</div>
```

### Стек: каталог → фильтры

```html
<!-- Каталог -->
<button @click="$modal.show('catalog')">Каталог</button>

<div x-show="$modal.isOpen('catalog')" ...>
    <button @click="$modal.show('filters')">Фильтры</button>
</div>

<!-- Фильтры поверх каталога -->
<div x-show="$modal.isOpen('filters')" ...>
    <button @click="$modal.hide()">← Назад к каталогу</button>
</div>
```

### Две модалки с разными z-index

```html
<!-- Первая модалка (нижний слой) -->
<div x-show="$modal.isOpen('catalog')"
     :style="{ zIndex: 50 + $modal.depth('catalog') }">

<!-- Вторая модалка (верхний слой) -->
<div x-show="$modal.isOpen('filters')"
     :style="{ zIndex: 50 + $modal.depth('filters') }">
```

### x-trap только на верхней модалке

```html
<div x-trap="$modal.isTop('login')">
```

Фокус не уйдёт за пределы верхней модалки, но нижняя остаётся видимой.

## Body overflow

Управляется через `:class` на `<body>` в `layouts/app.blade.php`:

```html
<body :class="{ 'overflow-hidden': $store.modal.stack.length }">
```

Скролл блокируется пока в стеке есть хотя бы одна модалка.

## Директивы

| Директива | Зачем |
|---|---|
| `@click="$modal.show('id')"` | Открыть (добавить в стек) |
| `@click="$modal.show('id', { replace: true })"` | Открыть, закрыв все остальные |
| `@click="$modal.hide()"` | Закрыть верхнюю |
| `@click="$modal.hide('id')"` | Закрыть конкретную |
| `@click.self="$modal.hide()"` | Закрыть по клику на оверлей |
| `@keydown.escape.window="$modal.hide()"` | Закрыть по Escape (верхнюю) |
| `x-trap="$modal.isTop('id')"` | Trap фокуса только на верхней |
| `x-show="$modal.isOpen('id')"` | Показать/скрыть |
| `:style="{ zIndex: 50 + $modal.depth('id') }"` | Z-index по позиции в стеке |
| `x-transition` | Плавная анимация |

## Структура файла

```
resources/js/
├── app.js
└── alpine/plugins/
    ├── modal.js
    └── README.md
```
