@push('block-styles')
    @vite(['resources/css/blocks/loyalty/hero/style.css'])
@endpush

<section class="loyalty-hero">
    <div class="container">
        <div class="loyalty-hero__content">
            <h1 class="loyalty-hero__title">{{ __('loyalty.title') }}</h1>
            <p class="loyalty-hero__subtitle">{{ __('loyalty.subtitle') }}</p>
            <a href="#" class="btn btn--primary">{{ __('loyalty.register_button') }}</a>
        </div>
        <div class="loyalty-hero__image">
            <x-img path="/images/loyalty/1.png" :lazy="false" :alt="__('loyalty.title')" class="loyalty-hero__img" />
        </div>
    </div>
</section>
