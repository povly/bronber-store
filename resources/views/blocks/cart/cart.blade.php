@push('block-styles')
    @vite(['resources/css/blocks/cart/style.css'])
@endpush

@push('block-scripts')
    @vite(['resources/js/blocks/cart/index.js'])
@endpush

<section class="cart" x-data="cart({{ Js::from(['items' => $items]) }})">
    <div class="container">
        <x-breadcrumbs class="cart__breadcrumb" :items="[['label' => 'Главная', 'url' => '/'], ['label' => 'Корзина']]" />

        <h1 class="cart__title">Корзина</h1>
    </div>

    {{-- Filled state: body lives OUTSIDE container so cards can be full-width on mobile --}}
    <div class="cart__body">

        <div class="cart__grid">
            <div class="cart__grid-block">
                <div class="cart__items-head">
                    <span class="cart__head-item cart__head-title">Товар</span>
                    <span class="cart__head-item cart__head-price cart__head--center">Цена</span>
                    <span class="cart__head-item cart__head-qty cart__head--center">Кол-во</span>
                    <span class="cart__head-item cart__head-sum cart__head--center">Сумма</span>
                    <span class="cart__head-item cart__head-action" aria-hidden="true"></span>
                </div>

                <div class="cart__items">

                    @foreach ($items as $item)
                        <div class="cart__item" x-show="hasItem({{ $item['id'] }})">
                            <div class="cart__item-image img--full">
                                <x-img :lazy="false" :path="$item['image']" :alt="$item['title']" width="80"
                                    height="80" />
                            </div>

                            <div class="cart__item-info">
                                <p class="cart__item-title">{{ $item['title'] }}</p>
                                <p class="cart__item-article">Артикул: <span>{{ $item['article'] }}</span></p>
                                <p class="cart__item-brand">Бренд: <a href="#!">{{ $item['brand'] }}</a></p>
                            </div>

                            <p class="cart__item-price-mobile">Цена: <span>{{ $formatPrice($item['price']) }}</span></p>

                            <div class="cart__item-unitprice">
                                <span>{{ $formatPrice($item['price']) }}</span>
                            </div>

                            <div class="cart__item-qty">
                                <x-qty
                                    :data="'qty(' . $item['qty'] . ')'"
                                    :watch="'setQty(' . $item['id'] . ', v)'"
                                />
                            </div>

                            <div class="cart__item-total">
                                <span x-text="itemTotal({{ $item['id'] }})"></span>
                            </div>

                            <button type="button" class="cart__item-remove" @click="remove({{ $item['id'] }})"
                                aria-label="Удалить товар">
                                <svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M5.20312 7.29167H19.7865M10.4115 10.4167V18.75M14.5781 10.4167V18.75M10.4115 3.125H14.5781C14.8544 3.125 15.1193 3.23475 15.3147 3.4301C15.51 3.62545 15.6198 3.8904 15.6198 4.16667V7.29167H9.36979V4.16667C9.36979 3.8904 9.47954 3.62545 9.67489 3.4301C9.87024 3.23475 10.1352 3.125 10.4115 3.125ZM6.24479 7.29167H18.7448V20.8333C18.7448 21.1096 18.635 21.3746 18.4397 21.5699C18.2443 21.7653 17.9794 21.875 17.7031 21.875H7.28646C7.01019 21.875 6.74524 21.7653 6.54989 21.5699C6.35454 21.3746 6.24479 21.1096 6.24479 20.8333V7.29167Z"
                                        stroke="#7212BC" stroke-width="1.6" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>

                <button type="button" class="cart__clear">Очистить корзину</button>
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

                <a href="{{ route('checkout') }}" type="button" class="cart__checkout btn--primary btn">Оформить заказ</a>

                <a href="{{ route('catalog') }}" class="cart__back">
                    <svg width="22" height="12" viewBox="0 0 22 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M20.75 6.27295C21.1642 6.27295 21.5 5.93716 21.5 5.52295C21.5 5.10874 21.1642 4.77295 20.75 4.77295L20.75 5.52295L20.75 6.27295ZM0.219669 4.99262C-0.0732231 5.28551 -0.0732231 5.76038 0.219669 6.05328L4.99264 10.8262C5.28553 11.1191 5.76041 11.1191 6.0533 10.8262C6.34619 10.5334 6.34619 10.0585 6.0533 9.76559L1.81066 5.52295L6.0533 1.28031C6.34619 0.987414 6.34619 0.512541 6.0533 0.219647C5.76041 -0.0732464 5.28553 -0.0732464 4.99264 0.219647L0.219669 4.99262ZM20.75 5.52295L20.75 4.77295L0.75 4.77295L0.75 5.52295L0.75 6.27295L20.75 6.27295L20.75 5.52295Z" fill="#7212BC" />
                    </svg>
                    <span>Вернуться в каталог</span>
                </a>
            </aside>

        </div>

    </div>
</section>
