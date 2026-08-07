@push('block-styles')
    @vite(['resources/css/blocks/delivery/style.css'])
@endpush

@php
    $methods = [
        [
            'title' => __('delivery.method_cash_title'),
            'text' => __('delivery.method_cash_text'),
            'icon' => 'cash',
        ],
        [
            'title' => __('delivery.method_card_title'),
            'text' => __('delivery.method_card_text'),
            'icon' => 'card',
        ],
        [
            'title' => __('delivery.method_sbp_title'),
            'text' => __('delivery.method_sbp_text'),
            'icon' => 'qr',
        ],
        [
            'title' => __('delivery.method_currency_title'),
            'text' => __('delivery.method_currency_text'),
            'icon' => 'currency',
        ],
    ];
@endphp

<section class="delivery">
    <div class="container">
        <h1 class="delivery__title section__title">{{ __('delivery.title') }}</h1>

        <div class="delivery__methods">
            @foreach ($methods as $method)
                <div class="delivery__method">
                    <span class="delivery__icon delivery__icon--{{ $method['icon'] }}"></span>
                    <h2 class="delivery__method-title">{!! nl2br(e($method['title'])) !!}</h2>
                    <p class="delivery__method-text">{{ $method['text'] }}</p>
                </div>
            @endforeach
        </div>

        <h2 class="delivery__contact-title section__title">{{ __('delivery.contact_title') }}</h2>

        <div class="delivery__contacts">
            <a href="tel:{{ preg_replace('/[^+\d]/', '', __('delivery.contact_phone')) }}" class="delivery__contact">
                <span class="delivery__contact-icon">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 2.5L7.5 2.5L8.75 6.25L6.875 7.5C7.5 9.375 9.0625 10.9375 10.9375 11.5625L12.1875 9.6875L15.9375 10.9375L15.9375 13.4375C15.9375 14.2322 15.2947 14.875 14.5 14.875C8.37586 14.875 2.5625 9.06164 2.5625 2.9375C2.5625 2.14282 3.20532 1.5 4 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" transform="translate(1 1)"/>
                    </svg>
                </span>
                {{ __('delivery.contact_phone') }}
            </a>
            <a href="mailto:{{ __('delivery.contact_email') }}" class="delivery__contact">
                <span class="delivery__contact-icon">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="1.5" y="3.5" width="17" height="13" rx="2" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M2 5L10 11L18 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                {{ __('delivery.contact_email') }}
            </a>
        </div>
    </div>
</section>
