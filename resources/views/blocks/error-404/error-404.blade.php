@push('block-styles')
    @vite(['resources/css/blocks/error-404/style.css'])
@endpush

<section class="error-404">
    <div class="container">
        <div class="error-404__inner">
            <div class="error-404__code">404</div>
            <h1 class="error-404__title section__title">{{ __('store.error_404_title') }}</h1>
            <p class="error-404__desc">{!! __('store.error_404_desc') !!}</p>
            <div class="error-404__actions">
                <x-btn variant="primary error-404__btn" href="{{ route('home') }}" :text="__('store.error_404_home_btn')" />
                <x-btn variant="white-border error-404__btn" href="{{ route('catalog') }}" :text="__('store.error_404_catalog_btn')" />
            </div>
        </div>
    </div>
</section>
