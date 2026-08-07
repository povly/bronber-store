@push('block-styles')
    @vite(['resources/css/blocks/loyalty/benefits/style.css'])
@endpush

@php
    $benefits = [
        ['icon' => 'coin', 'title' => __('loyalty.benefit_1_title'), 'text' => __('loyalty.benefit_1_text'), 'main' => true],
        ['icon' => 'cart', 'title' => null, 'text' => __('loyalty.benefit_2'), 'main' => false],
        ['icon' => 'infinity', 'title' => null, 'text' => __('loyalty.benefit_3'), 'main' => false],
        ['icon' => 'star', 'title' => null, 'text' => __('loyalty.benefit_4'), 'main' => false],
    ];

    $icons = [
        'coin' => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="16" cy="16" r="14" stroke="#7212bc" stroke-width="2"/><path d="M16 8C12.7 8 10 10.7 10 14s2.7 6 6 6 6-2.7 6-6-2.7-6-6-6zm0 10c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4-1.8 4-4 4z" fill="#7212bc"/></svg>',
        'cart' => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 7h3l3 14h15l2-9H10" stroke="#7212bc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="13" cy="25" r="2" fill="#7212bc"/><circle cx="22" cy="25" r="2" fill="#7212bc"/></svg>',
        'infinity' => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 11c-3.3 0-6 2.2-6 5s2.7 5 6 5c4 0 6-5 6-5s2-5 6-5c3.3 0 6 2.2 6 5s-2.7 5-6 5c-4 0-6-5-6-5" stroke="#7212bc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        'star' => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16 4l3.5 7.5L28 13l-6 5.5L23.5 27 16 23l-7.5 4L10 18.5 4 13l8.5-1.5L16 4z" stroke="#7212bc" stroke-width="2" stroke-linejoin="round"/></svg>',
    ];
@endphp

<section class="loyalty-benefits">
    <div class="container">
        <div class="loyalty-benefits__list">
            @foreach ($benefits as $benefit)
                <div class="loyalty-benefits__item{{ $benefit['main'] ? ' loyalty-benefits__item--main' : '' }}">
                    <span class="loyalty-benefits__icon">{!! $icons[$benefit['icon']] !!}</span>
                    @if ($benefit['main'])
                        <div class="loyalty-benefits__body">
                            <div class="loyalty-benefits__title">{{ $benefit['title'] }}</div>
                            <div class="loyalty-benefits__text">{{ $benefit['text'] }}</div>
                        </div>
                    @else
                        <div class="loyalty-benefits__text">{{ $benefit['text'] }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
