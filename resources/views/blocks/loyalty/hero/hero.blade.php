@push('block-styles')
    @vite(['resources/css/blocks/loyalty/hero/style.css'])
@endpush

<section class="loyalty-hero">
    <div class="container loyalty-hero__container">
        <div class="loyalty-hero__content">
            <h1 class="loyalty-hero__title section__title">{{ __('loyalty.title') }}</h1>
            <p class="loyalty-hero__subtitle">{{ __('loyalty.subtitle') }}</p>
            <div class="loyalty-hero__image loyalty-hero__image--mb">
                <x-img path="/images/loyalty/1.png" :lazy="false" :alt="__('loyalty.title')" class="loyalty-hero__img" />
            </div>
            <a href="#" class="loyalty-hero__btn btn btn--primary">
                <span>{{ __('loyalty.register_button') }}</span>
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M6.99479 7.58333C8.60562 7.58333 9.91146 6.2775 9.91146 4.66667C9.91146 3.05584 8.60562 1.75 6.99479 1.75C5.38396 1.75 4.07812 3.05584 4.07812 4.66667C4.07812 6.2775 5.38396 7.58333 6.99479 7.58333Z"
                        stroke="white" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                    <path
                        d="M11.6615 12.2526C11.6615 11.0149 11.1698 9.82794 10.2946 8.95277C9.41945 8.0776 8.23247 7.58594 6.99479 7.58594C5.75711 7.58594 4.57013 8.0776 3.69496 8.95277C2.81979 9.82794 2.32813 11.0149 2.32812 12.2526"
                        stroke="white" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
        </div>
        <div class="loyalty-hero__image loyalty-hero__image--pc">
            <x-img path="/images/loyalty/1.png" :lazy="false" :alt="__('loyalty.title')" class="loyalty-hero__img" />
        </div>
    </div>
</section>
