@push('block-styles')
    @vite(['resources/blocks/common/mobile-nav/style.css'])
@endpush

<nav class="mobile-nav">
    <a href="/" class="mobile-nav__item">
        <svg class="mobile-nav__icon" width="27" height="27" viewBox="0 0 24 24" fill="none"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span class="mobile-nav__label">{{ __('store.mobile_home') }}</span>
    </a>
    <a href="/catalog" class="mobile-nav__item mobile-nav__item--active">
        <svg class="mobile-nav__icon" width="24" height="24" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="8" height="8" rx="1.5" stroke="currentColor" stroke-width="1.5"/><rect x="13" y="3" width="8" height="8" rx="1.5" stroke="currentColor" stroke-width="1.5"/><rect x="3" y="13" width="8" height="8" rx="1.5" stroke="currentColor" stroke-width="1.5"/><rect x="13" y="13" width="8" height="8" rx="1.5" stroke="currentColor" stroke-width="1.5"/></svg>
        <span class="mobile-nav__label">{{ __('store.mobile_catalog') }}</span>
    </a>
    <a href="/cart" class="mobile-nav__item">
        <svg class="mobile-nav__icon" width="25" height="25" viewBox="0 0 24 24" fill="none"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4zM3 6h18M16 10a4 4 0 01-8 0" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span class="mobile-nav__label">{{ __('store.mobile_cart') }}</span>
    </a>
    <a href="/profile" class="mobile-nav__item">
        <svg class="mobile-nav__icon" width="27" height="27" viewBox="0 0 24 24" fill="none"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span class="mobile-nav__label">{{ __('store.mobile_profile') }}</span>
    </a>
</nav>
