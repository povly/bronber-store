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
    $class .= ' order-card--' . $status;
@endphp

<{{ $tagEl }} {!! $tagAttrs !!} {{ $attributes->merge(['class' => "order-card {$class}"]) }}>
    <div class="order-card__image img--full">
        <img src="{{ $image }}" alt="{{ $orderNumber }}" loading="lazy" />
    </div>
    <div class="order-card__body">
        <span class="order-card__status">{{ $statusText }}</span>
        <h3 class="order-card__number">{{ $orderNumber }}</h3>
        <div class="order-card__meta">
            <span class="order-card__date">{{ __('profile.order_date') }} {{ $date }}</span>
            <span class="order-card__total">{{ __('profile.order_total') }} {{ $total }}</span>
        </div>
    </div>
</{{ $tagEl }}>
