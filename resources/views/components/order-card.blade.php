@props([
    'class' => '',
    'status' => 'expected',
    'image' => '',
    'orderNumber' => '',
    'date' => '',
    'total' => '',
    'href' => null,
])

@php
    $tagEl = $href ? 'a' : 'article';
    $tagAttrs = $tagEl === 'a' ? 'href="' . e($href) . '"' : '';
    $statusText = $status === 'expected' ? __('profile.status_expected') : __('profile.status_done');
@endphp

<{{ $tagEl }} {!! $tagAttrs !!} {{ $attributes->merge(['class' => "order-card {$class}"]) }}>
    <div class="order-card__image img--full">

        <x-img path="{{ $image }}" :lazy="false" width="86" height="86" />
    </div>
    <div class="order-card__body">
        <div class="order-card__body-top">
            <h3 class="order-card__number">{{ $orderNumber }}</h3>
            <span class="order-card__status order-card__status--{{ $status }}">{{ $statusText }}</span>
        </div>
        <div class="order-card__meta">
            <span class="order-card__date">{{ __('profile.order_date') }} {{ $date }}</span>
            <span class="order-card__total">{{ __('profile.order_total') }} <strong>{{ $total }}</strong></span>
        </div>
        <x-btn variant="white-border order-card__btn" text="Подробнее о заказе" />
    </div>
    </{{ $tagEl }}>
