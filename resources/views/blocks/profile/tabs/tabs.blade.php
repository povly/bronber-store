@push('block-styles')
    @vite(['resources/css/blocks/profile/tabs/style.css'])
@endpush

@php
    // Sidebar nav icons (20x20, currentColor stroke). Hidden on mobile via CSS,
    // shown only inside the desktop (>=1200px) vertical sidebar.
    $icons = [
        'cabinet' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5L12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1V10.5z"/></svg>',
        'orders' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 4h6a1 1 0 0 1 1 1v1H8V5a1 1 0 0 1 1-1z"/><path d="M6 6h12v15H6z"/><path d="M9 11h6"/><path d="M9 15h6"/></svg>',
        'favorites' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-7-4.5-7-10a4 4 0 0 1 7-2.5A4 4 0 0 1 19 11c0 5.5-7 10-7 10z"/></svg>',
        'data' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>',
        'password' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="1"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/><circle cx="12" cy="16" r="1.5"/></svg>',
        'logout' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 12H4"/><path d="M8 17l-4-5 4-5"/><path d="M10 4h9a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-9"/></svg>',
    ];
@endphp

<section class="profile-tabs" x-data="{ tab: 'cabinet' }">
    <div class="container">
        <h1 class="profile-tabs__title section__title">{{ __('profile.title') }}</h1>

        <div class="profile-tabs__user">
            <div class="profile-tabs__user-name">{{ $user['name'] }}</div>
            <div class="profile-tabs__user-email">{{ $user['email'] }}</div>
        </div>

        <div class="profile-tabs__nav"
             x-scrollable="{ orientation: 'horizontal', thumbColor: '#7212bc', thumbWidth: 4, thumbRadius: 4, trackOffset: 2 }">
            <button type="button" class="profile-tabs__btn" @click="tab = 'cabinet'" :class="{ 'is-active': tab === 'cabinet' }">
                <span class="profile-tabs__btn-icon">{!! $icons['cabinet'] !!}</span>
                <span class="profile-tabs__btn-text">{{ __('profile.tab_cabinet') }}</span>
            </button>
            <button type="button" class="profile-tabs__btn" @click="tab = 'orders'" :class="{ 'is-active': tab === 'orders' }">
                <span class="profile-tabs__btn-icon">{!! $icons['orders'] !!}</span>
                <span class="profile-tabs__btn-text">{{ __('profile.tab_orders') }}</span>
            </button>
            <button type="button" class="profile-tabs__btn" @click="tab = 'favorites'" :class="{ 'is-active': tab === 'favorites' }">
                <span class="profile-tabs__btn-icon">{!! $icons['favorites'] !!}</span>
                <span class="profile-tabs__btn-text">{{ __('profile.tab_favorites') }}</span>
            </button>
            <button type="button" class="profile-tabs__btn" @click="tab = 'data'" :class="{ 'is-active': tab === 'data' }">
                <span class="profile-tabs__btn-icon">{!! $icons['data'] !!}</span>
                <span class="profile-tabs__btn-text">{{ __('profile.nav_data') }}</span>
            </button>
            <button type="button" class="profile-tabs__btn" @click="tab = 'password'" :class="{ 'is-active': tab === 'password' }">
                <span class="profile-tabs__btn-icon">{!! $icons['password'] !!}</span>
                <span class="profile-tabs__btn-text">{{ __('profile.nav_password') }}</span>
            </button>
        </div>

        <div class="profile-tabs__divider"></div>

        <a href="#" class="profile-tabs__logout">
            <span class="profile-tabs__btn-icon">{!! $icons['logout'] !!}</span>
            <span class="profile-tabs__btn-text">{{ __('profile.nav_logout') }}</span>
        </a>
    </div>
</section>
