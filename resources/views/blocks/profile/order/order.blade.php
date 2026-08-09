@push('block-styles')
    @vite(['resources/css/blocks/profile/order/style.css'])
@endpush

<section class="profile-order">
    <div class="container">
        <div class="profile-order__header">
            <div class="profile-order__header-top">
                <div class="profile-order__header-top-left">
                    <h2 class="profile-order__title section__title">Заказ №{{ $order['number'] }}</h2>
                    <span class="profile-order__status profile-order__status--{{ $order['status'] }}">{{ $order['statusText'] }}</span>
                </div>
                <p class="profile-order__created">Оформлен {{ $order['created'] }}</p>
            </div>
            <x-btn variant="primary" class="profile-order__button" :text="__('profile.order_detail_cancel')" />
        </div>

        <div class="profile-order__card">
            <div class="profile-order__info-rows">
                <div class="profile-order__info-row">
                    <span class="profile-order__info-label">{{ __('profile.order_detail_status') }}</span>
                    <span class="profile-order__info-value">{{ $order['statusText'] }}</span>
                </div>
                <div class="profile-order__info-row">
                    <span class="profile-order__info-label">{{ __('profile.order_detail_date') }}</span>
                    <span class="profile-order__info-value">{{ $order['date'] }}</span>
                </div>
                <div class="profile-order__info-row">
                    <span class="profile-order__info-label">{{ __('profile.order_detail_payment') }}</span>
                    <span class="profile-order__info-value">{{ $order['payment'] }}</span>
                </div>
                <div class="profile-order__info-row">
                    <span class="profile-order__info-label">{{ __('profile.order_detail_phone') }}</span>
                    <span class="profile-order__info-value">{{ $order['phone'] }}</span>
                </div>
                <div class="profile-order__info-row">
                    <span class="profile-order__info-label">{{ __('profile.order_detail_delivery') }}</span>
                    <span class="profile-order__info-value">{{ $order['delivery'] }}</span>
                </div>
                <div class="profile-order__info-row">
                    <span class="profile-order__info-label">{{ __('profile.order_detail_address') }}</span>
                    <span class="profile-order__info-value">{{ $order['address'] }}</span>
                </div>
                <div class="profile-order__info-row">
                    <span class="profile-order__info-label">{{ __('profile.order_detail_recipient') }}</span>
                    <span class="profile-order__info-value">{{ $order['recipient'] }}</span>
                </div>
                <div class="profile-order__info-row">
                    <span class="profile-order__info-label">{{ __('profile.order_detail_email') }}</span>
                    <span class="profile-order__info-value">{{ $order['email'] }}</span>
                </div>
            </div>
            <div class="profile-order__summary">
                <div class="profile-order__summary-row">
                    <span class="profile-order__info-label">{{ __('profile.order_detail_items_count') }}</span>
                    <span class="profile-order__summary-value">{{ $order['items_count'] }}</span>
                </div>
                <div class="profile-order__summary-row">
                    <span class="profile-order__info-label">{{ __('profile.order_detail_sum') }}</span>
                    <span class="profile-order__summary-value">{{ $order['sum'] }}</span>
                </div>
                <div class="profile-order__summary-row">
                    <span class="profile-order__info-label">{{ __('profile.order_detail_delivery_cost') }}</span>
                    <span class="profile-order__summary-value">{{ $order['delivery_cost'] }}</span>
                </div>
                <div class="profile-order__total-row">
                    <span class="profile-order__total-label">{{ __('profile.order_detail_total') }}</span>
                    <span class="profile-order__total-value">{{ $order['total'] }}</span>
                </div>
            </div>
        </div>

        <h3 class="profile-order__items-title section__title">{{ __('profile.order_detail_items') }}</h3>

        <div class="profile-order__items-card">
            <div class="profile-order__item profile-order__item--header">
                <div class="profile-order__item-th">Товар</div>
                <div class="profile-order__item-th">Цена</div>
                <div class="profile-order__item-th">Кол-во</div>
                <div class="profile-order__item-th">Сумма</div>
            </div>
            @foreach ($items as $item)
            <div class="profile-order__item">
                <div class="profile-order__item-image img--full">
                    <x-img path="{{ $item['image'] }}" :lazy="false" width="80" height="80" />
                </div>
                <div class="profile-order__item-info">
                    <h4 class="profile-order__item-title">{{ $item['title'] }}</h4>
                    <p class="profile-order__item-meta">{{ __('profile.order_detail_article') }} {{ $item['article'] }}</p>
                    <p class="profile-order__item-meta">{!! __('profile.order_detail_brand') !!} {!! $item['brand'] !!}</p>
                </div>
                <div class="profile-order__item-bottom">
                    <div class="profile-order__item-price">
                        <span class="profile-order__item-price-label">{{ __('profile.order_detail_price') }}</span>
                        <span>{{ $item['price'] }}</span>
                    </div>
                    <div class="profile-order__item-qty">
                        <span class="profile-order__item-price-label">{{ __('profile.order_detail_qty') }}</span>
                        <span>{{ $item['qty'] }}</span>
                    </div>
                    <div class="profile-order__item-sum">
                        <span class="profile-order__item-price-label">{{ __('profile.order_detail_item_sum') }}</span>
                        <span>{!! $item['sum'] !!}</span>
                    </div>
                </div>
                
                <div class="profile-order__item-price profile-order__item--pc">
                    <span>{{ $item['price'] }}</span>
                </div>
                <div class="profile-order__item-qty profile-order__item--pc">
                    <span>{{ $item['qty'] }}</span>
                </div>
                <div class="profile-order__item-sum profile-order__item--pc">
                   <span>{{ $item['sum'] }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
