@push('block-styles')
    @vite(['resources/css/blocks/profile/stats/style.css'])
@endpush

@php
    $icons = [
        'bonuses' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="9" stroke="#7212BC" stroke-width="1.8"/><path d="M9 15H14.5" stroke="#7212BC" stroke-width="1.8" stroke-linecap="round"/><path d="M9 12H14.5C15.3284 12 16 11.3284 16 10.5C16 9.67157 15.3284 9 14.5 9H11.5V15" stroke="#7212BC" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        'orders' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 7C3 5.89543 3.89543 5 5 5H19C20.1046 5 21 5.89543 21 7V18C21 19.1046 20.1046 20 19 20H5C3.89543 20 3 19.1046 3 18V7Z" stroke="#7212BC" stroke-width="1.8" stroke-linejoin="round"/><path d="M3 9H21" stroke="#7212BC" stroke-width="1.8" stroke-linecap="round"/><path d="M8 13H12" stroke="#7212BC" stroke-width="1.8" stroke-linecap="round"/></svg>',
        'favorites' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 21C12 21 4 15.5 4 9.5C4 6.46243 6.46243 4 9.5 4C10.9 4 12 5 12 5C12 5 13.1 4 14.5 4C17.5376 4 20 6.46243 20 9.5C20 15.5 12 21 12 21Z" stroke="#7212BC" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    ];
@endphp

<section class="profile-stats">
    <div class="container">
        <div class="profile-stats__list">
            @foreach ($stats as $stat)
                <div class="profile-stats__item">
                    <span class="profile-stats__icon">{!! $icons[$stat['icon']] !!}</span>
                    <div class="profile-stats__value">{{ $stat['value'] }}</div>
                    <div class="profile-stats__title">{{ $stat['title'] }}</div>
                    <div class="profile-stats__subtitle">{{ $stat['subtitle'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>
