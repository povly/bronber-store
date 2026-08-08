@push('block-styles')
    @vite(['resources/css/blocks/profile/tabs/style.css'])
@endpush

@php
    // Sidebar nav icons (20x20, currentColor stroke). Hidden on mobile via CSS,
    // shown only inside the desktop (>=1200px) vertical sidebar.
    $icons = [
        'cabinet' => '<svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M13.75 19.25V11.9167C13.75 11.6736 13.6534 11.4404 13.4815 11.2685C13.3096 11.0966 13.0764 11 12.8333 11H9.16667C8.92355 11 8.69039 11.0966 8.51849 11.2685C8.34658 11.4404 8.25 11.6736 8.25 11.9167V19.25" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M2.75 9.16776C2.74994 8.90107 2.80806 8.63758 2.92031 8.39567C3.03255 8.15375 3.19623 7.93924 3.39992 7.76709L9.81658 2.26709C10.1475 1.98742 10.5667 1.83398 11 1.83398C11.4333 1.83398 11.8525 1.98742 12.1834 2.26709L18.6001 7.76709C18.8038 7.93924 18.9674 8.15375 19.0797 8.39567C19.1919 8.63758 19.2501 8.90107 19.25 9.16776V17.4178C19.25 17.904 19.0568 18.3703 18.713 18.7141C18.3692 19.0579 17.9029 19.2511 17.4167 19.2511H4.58333C4.0971 19.2511 3.63079 19.0579 3.28697 18.7141C2.94315 18.3703 2.75 17.904 2.75 17.4178V9.16776Z" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>',
        'orders' => '<svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
        <g clip-path="url(#clip0_1010_18861)">
        <path d="M7 19.25C7.48325 19.25 7.875 18.8582 7.875 18.375C7.875 17.8918 7.48325 17.5 7 17.5C6.51675 17.5 6.125 17.8918 6.125 18.375C6.125 18.8582 6.51675 19.25 7 19.25Z" stroke="black" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M16.625 19.25C17.1082 19.25 17.5 18.8582 17.5 18.375C17.5 17.8918 17.1082 17.5 16.625 17.5C16.1418 17.5 15.75 17.8918 15.75 18.375C15.75 18.8582 16.1418 19.25 16.625 19.25Z" stroke="black" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M1.78906 1.79492H3.53906L5.86656 12.6624C5.95194 13.0604 6.1734 13.4162 6.49281 13.6685C6.81222 13.9209 7.2096 14.054 7.61656 14.0449H16.1741C16.5723 14.0443 16.9585 13.9078 17.2687 13.658C17.5789 13.4083 17.7947 13.0601 17.8803 12.6712L19.3241 6.16992H4.47531" stroke="black" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
        </g>
        <defs>
        <clipPath id="clip0_1010_18861">
        <rect width="21" height="21" fill="white"/>
        </clipPath>
        </defs>
        </svg>',
        'favorites' => '<svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M1.75 8.31296C1.75002 7.33926 2.04539 6.38847 2.59712 5.58617C3.14884 4.78386 3.93095 4.16779 4.84016 3.81931C5.74936 3.47084 6.74289 3.40635 7.68951 3.63438C8.63613 3.8624 9.49132 4.37221 10.1421 5.09646C10.188 5.14547 10.2434 5.18455 10.3049 5.21126C10.3665 5.23798 10.4329 5.25176 10.5 5.25176C10.5671 5.25176 10.6335 5.23798 10.6951 5.21126C10.7566 5.18455 10.812 5.14547 10.8579 5.09646C11.5066 4.3675 12.362 3.85341 13.3102 3.62261C14.2583 3.39182 15.2543 3.45527 16.1655 3.80451C17.0767 4.15376 17.86 4.77223 18.411 5.57763C18.962 6.38302 19.2546 7.33713 19.25 8.31296C19.25 10.3167 17.9375 11.813 16.625 13.1255L11.8195 17.7743C11.6565 17.9616 11.4554 18.112 11.2298 18.2156C11.0041 18.3192 10.759 18.3736 10.5108 18.3752C10.2625 18.3767 10.0167 18.3254 9.78975 18.2247C9.56281 18.124 9.3599 17.9761 9.1945 17.791L4.375 13.1255C3.0625 11.813 1.75 10.3255 1.75 8.31296Z" stroke="black" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>',
        'data' => '<svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M10.5 11.375C12.9162 11.375 14.875 9.41625 14.875 7C14.875 4.58375 12.9162 2.625 10.5 2.625C8.08375 2.625 6.125 4.58375 6.125 7C6.125 9.41625 8.08375 11.375 10.5 11.375Z" stroke="black" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M17.5 18.375C17.5 16.5185 16.7625 14.738 15.4497 13.4253C14.137 12.1125 12.3565 11.375 10.5 11.375C8.64348 11.375 6.86301 12.1125 5.55025 13.4253C4.2375 14.738 3.5 16.5185 3.5 18.375" stroke="black" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>',
        'password' => '<svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M10.5 14.875C10.9832 14.875 11.375 14.4832 11.375 14C11.375 13.5168 10.9832 13.125 10.5 13.125C10.0168 13.125 9.625 13.5168 9.625 14C9.625 14.4832 10.0168 14.875 10.5 14.875Z" stroke="black" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M16.625 8.75H4.375C3.4085 8.75 2.625 9.5335 2.625 10.5V17.5C2.625 18.4665 3.4085 19.25 4.375 19.25H16.625C17.5915 19.25 18.375 18.4665 18.375 17.5V10.5C18.375 9.5335 17.5915 8.75 16.625 8.75Z" stroke="black" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M6.125 8.75V6.125C6.125 4.96468 6.58594 3.85188 7.40641 3.03141C8.22688 2.21094 9.33968 1.75 10.5 1.75C11.6603 1.75 12.7731 2.21094 13.5936 3.03141C14.4141 3.85188 14.875 4.96468 14.875 6.125V8.75" stroke="black" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>',
        'logout' => '<svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M7 14.875L2.625 10.5L7 6.125" stroke="black" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M2.625 10.5H13.125" stroke="black" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M13.125 18.375H16.625C17.0891 18.375 17.5342 18.1906 17.8624 17.8624C18.1906 17.5342 18.375 17.0891 18.375 16.625V4.375C18.375 3.91087 18.1906 3.46575 17.8624 3.13756C17.5342 2.80937 17.0891 2.625 16.625 2.625H13.125" stroke="black" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>',
    ];
@endphp

<section class="profile-tabs" x-data="{ tab: 'cabinet' }">

    <h1 class="profile-tabs__title section__title">{{ __('profile.title') }}</h1>

    <div class="profile-tabs__user">
        <div class="profile-tabs__user-name">{{ $user['name'] }}</div>
        <div class="profile-tabs__user-email">{{ $user['email'] }}</div>
    </div>

    <div class="profile-tabs__nav"
        x-scrollable="{ orientation: 'horizontal', thumbColor: '#7212bc', thumbWidth: 4, thumbRadius: 4, trackOffset: 2 }">
        <a href="{{ route('profile') }}" class="profile-tabs__btn is-active" @click="tab = 'cabinet'"
            :class="{ 'is-active': tab === 'cabinet' }">
            <span class="profile-tabs__btn-icon">{!! $icons['cabinet'] !!}</span>
            <span class="profile-tabs__btn-text">{{ __('profile.tab_cabinet') }}</span>
        </a>
        <a href="{{ route('profile') }}#orders" class="profile-tabs__btn" @click="tab = 'orders'"
            :class="{ 'is-active': tab === 'orders' }">
            <span class="profile-tabs__btn-icon">{!! $icons['orders'] !!}</span>
            <span class="profile-tabs__btn-text">{{ __('profile.tab_orders') }}</span>
        </a>
        <a href="{{ route('favorites') }}" class="profile-tabs__btn" @click="tab = 'favorites'"
            :class="{ 'is-active': tab === 'favorites' }">
            <span class="profile-tabs__btn-icon">{!! $icons['favorites'] !!}</span>
            <span class="profile-tabs__btn-text">{{ __('profile.tab_favorites') }}</span>
        </a>
        <a href="{{ route('profile') }}#data" class="profile-tabs__btn" @click="tab = 'data'"
            :class="{ 'is-active': tab === 'data' }">
            <span class="profile-tabs__btn-icon">{!! $icons['data'] !!}</span>
            <span class="profile-tabs__btn-text">{{ __('profile.nav_data') }}</span>
        </a>
        <a href="{{ route('profile') }}#password" class="profile-tabs__btn" @click="tab = 'password'"
            :class="{ 'is-active': tab === 'password' }">
            <span class="profile-tabs__btn-icon">{!! $icons['password'] !!}</span>
            <span class="profile-tabs__btn-text">{{ __('profile.nav_password') }}</span>
        </a>

        <a href="#" class="profile-tabs__btn profile-tabs__logout--mb">
            <span class="profile-tabs__btn-icon">{!! $icons['logout'] !!}</span>
            <span class="profile-tabs__btn-text">{{ __('profile.nav_logout') }}</span>
        </a>
    </div>

    <div class="profile-tabs__divider"></div>

    <a href="#" class="profile-tabs__logout profile-tabs__logout--pc">
        <span class="profile-tabs__btn-icon">{!! $icons['logout'] !!}</span>
        <span class="profile-tabs__btn-text">{{ __('profile.nav_logout') }}</span>
    </a>
</section>
