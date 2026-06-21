@push('block-styles')
    @vite(['resources/css/blocks/cart/style.css'])
@endpush

@push('block-scripts')
    @vite(['resources/js/blocks/cart/index.js'])
@endpush

<section class="cart" x-data="cart({{ Js::from(['items' => $items]) }})">
    <div class="container">
        <x-breadcrumbs
            class="cart__breadcrumb"
            :items="[['label' => 'Главная', 'url' => '/'], ['label' => 'Корзина']]"
        />

        <h1 class="cart__title">Корзина</h1>
    </div>

    {{-- Empty state --}}
    <div class="container">
        <div class="cart__empty" x-show="items.length === 0" x-cloak>
            <p class="cart__empty-text">В корзине нет товаров</p>
            <a href="{{ route('catalog') }}" class="cart__empty-link">Вернуться в каталог</a>
        </div>
    </div>

    {{-- Filled state: body lives OUTSIDE container so cards can be full-width on mobile --}}
    <div class="cart__body" x-show="items.length > 0" x-cloak>

        <div class="cart__grid">

            {{-- Left column: items --}}
            <div class="cart__items">
                <div class="cart__items-head">
                    <span class="cart__head-title">Товар</span>
                    <span class="cart__head-price">Цена</span>
                    <span class="cart__head-qty">Кол-во</span>
                    <span class="cart__head-sum">Сумма</span>
                    <span class="cart__head-action" aria-hidden="true"></span>
                </div>

                <template x-for="item in items" :key="item.id">
                    <div class="cart__item">
                        <img class="cart__item-image" :src="item.image" alt="" loading="lazy">

                        <div class="cart__item-info">
                            <p class="cart__item-title" x-text="item.title"></p>
                            <p class="cart__item-article">Артикул: <span x-text="item.article"></span></p>
                            <p class="cart__item-brand">Бренд: <span x-text="item.brand"></span></p>
                        </div>

                        <p class="cart__item-price-mobile">Цена: <span x-text="formatPrice(item.price)"></span></p>

                        <div class="cart__item-unitprice">
                            <span x-text="formatPrice(item.price)"></span>
                        </div>

                        <div class="cart__item-qty">
                            <div class="cart__qty">
                                <button type="button" class="cart__qty-btn" @click="dec(item.id)" aria-label="Уменьшить количество">
                                    <span class="cart__qty-icon cart__qty-icon--minus"></span>
                                </button>
                                <span class="cart__qty-value" x-text="item.qty"></span>
                                <button type="button" class="cart__qty-btn" @click="inc(item.id)" aria-label="Увеличить количество">
                                    <span class="cart__qty-icon cart__qty-icon--plus"></span>
                                </button>
                            </div>
                        </div>

                        <div class="cart__item-total">
                            <span x-text="formatPrice(item.price * item.qty)"></span>
                        </div>

                        <button type="button" class="cart__item-remove" @click="remove(item.id)" aria-label="Удалить товар">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 6H21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                <path d="M8 6V4.5C8 4.10218 8.15804 3.72064 8.43934 3.43934C8.72064 3.15804 9.10218 3 9.5 3H14.5C14.8978 3 15.2794 3.15804 15.5607 3.43934C15.842 3.72064 16 4.10218 16 4.5V6M19 6V20C19 20.3978 18.842 20.7794 18.5607 21.0607C18.2794 21.342 17.8978 21.5 17.5 21.5H6.5C6.10218 21.5 5.72064 21.342 5.43934 21.0607C5.15804 20.7794 5 20.3978 5 20V6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M10 11V16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                <path d="M14 11V16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </button>
                    </div>
                </template>
            </div>

            {{-- Right column: summary --}}
            <aside class="cart__summary">
                <h2 class="cart__summary-title">Ваш заказ</h2>

                <div class="cart__summary-row">
                    <span class="cart__summary-label">Товаров</span>
                    <span class="cart__summary-value" x-text="itemsCount"></span>
                </div>
                <div class="cart__summary-row">
                    <span class="cart__summary-label">Сумма</span>
                    <span class="cart__summary-value" x-text="formatPrice(subtotal)"></span>
                </div>
                <div class="cart__summary-row">
                    <span class="cart__summary-label">Доставка</span>
                    <span class="cart__summary-value">Уточняется</span>
                </div>

                <div class="cart__summary-separator"></div>

                <div class="cart__summary-row cart__summary-row--total">
                    <span class="cart__summary-label">К оплате</span>
                    <span class="cart__summary-value" x-text="formatPrice(total)"></span>
                </div>

                <a href="{{ route('checkout') }}" type="button" class="cart__checkout">Оформить заказ</a>

                <a href="{{ route('catalog') }}" class="cart__back">Вернуться в каталог</a>
            </aside>

        </div>

        <div class="cart__clear-wrap">
            <button type="button" class="cart__clear" @click="clear()">Очистить корзину</button>
        </div>

    </div>
</section>
