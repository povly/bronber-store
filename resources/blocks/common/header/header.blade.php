@push('block-styles')
    @vite(['resources/blocks/common/header/style.css'])
@endpush

<header class="header" x-data="storeHeader(@js($searchTypes))">
    <div class="header__top container">
        <a href="/" class="header__logo" aria-label="{{ __('store.header_logo_alt') }}">
            <svg class="header__logo-icon" viewBox="0 0 207 32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <text x="0" y="26" font-family="Manrope, sans-serif" font-weight="700" font-size="28" fill="#000000">bronber</text>
            </svg>
        </a>

        <div class="header__search">
            <div class="header__search-type" @click.away="closeSearchDropdown()">
                <button type="button" class="header__search-dropdown" @click="toggleSearchDropdown()" :aria-expanded="searchDropdownOpen">
                    <span x-text="searchTypeLabel"></span>
                    <svg width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true" :class="{ 'header__search-chevron--open': searchDropdownOpen }">
                        <path d="M1 1.5L6 6.5L11 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <ul class="header__search-list" x-show="searchDropdownOpen" x-transition x-cloak>
                    @foreach($searchTypes as $type)
                        <li>
                            <button type="button" class="header__search-option" :class="{ 'is-active': searchType === '{{ $type['value'] }}' }" @click="selectSearchType('{{ $type['value'] }}')">{{ $type['label'] }}</button>
                        </li>
                    @endforeach
                </ul>
            </div>
            <input
                type="text"
                class="header__search-input"
                placeholder="{{ __('store.search_placeholder') }}"
                x-ref="searchInput"
                name="search"
            >
            <button type="button" class="header__search-btn" aria-label="{{ __('store.search_button') }}">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                    <path d="M16.5 16.5L21 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <div class="header__actions">
            <a href="#" class="header__action header__action--search" aria-label="{{ __('store.header_search') }}">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                    <path d="M16.5 16.5L21 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </a>
            <a href="#" class="header__action header__action--user" aria-label="{{ __('store.header_profile') }}">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="12" cy="8" r="4.5" stroke="currentColor" stroke-width="2"/>
                    <path d="M3.5 21c0-4.14 3.81-7.5 8.5-7.5s8.5 3.36 8.5 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </a>
            <a href="#" class="header__action header__action--favorites" aria-label="{{ __('store.header_favorites') }}">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" stroke="currentColor" stroke-width="2" fill="none"/>
                </svg>
            </a>
            <a href="#" class="header__action header__action--cart" aria-label="{{ __('store.header_cart') }}">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M9 22a1 1 0 100-2 1 1 0 000 2zM20 22a1 1 0 100-2 1 1 0 000 2zM1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="header__badge">3</span>
            </a>
        </div>
    </div>

    <div class="header__search-mobile container">
        <input type="text" placeholder="{{ __('store.search_placeholder_mobile') }}">
        <button type="button" aria-label="{{ __('store.search_button') }}">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                <path d="M16.5 16.5L21 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
    </div>

    <nav class="header__nav">
        <div class="header__nav-inner container">
            <button type="button" class="header__catalog-btn" aria-label="{{ __('store.header_catalog_btn') }}">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                    <rect x="0" y="0" width="7" height="7" rx="1.5" fill="currentColor"/>
                    <rect x="11" y="0" width="7" height="7" rx="1.5" fill="currentColor"/>
                    <rect x="0" y="11" width="7" height="7" rx="1.5" fill="currentColor"/>
                    <rect x="11" y="11" width="7" height="7" rx="1.5" fill="currentColor"/>
                </svg>
            </button>
            <a href="#" class="header__nav-link header__nav-link--catalog">{{ __('store.nav_catalog') }}</a>
            <a href="#" class="header__nav-link">{{ __('store.nav_new') }}</a>
            <a href="#" class="header__nav-link">{{ __('store.nav_promo') }}</a>
            <a href="#" class="header__nav-link">{{ __('store.nav_blog') }}</a>
            <a href="#" class="header__nav-link">{{ __('store.nav_bonus') }}</a>
            <a href="#" class="header__nav-link">{{ __('store.nav_about') }}</a>
        </div>
    </nav>
</header>
