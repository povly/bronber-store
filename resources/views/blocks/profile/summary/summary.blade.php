@push('block-styles')
    @vite(['resources/css/blocks/profile/summary/style.css'])
@endpush

@php
    $mailIcon = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 5.83333C3 5.3731 3.3731 5 3.83333 5H16.1667C16.6269 5 17 5.3731 17 5.83333V14.1667C17 14.6269 16.6269 15 16.1667 15H3.83333C3.3731 15 3 14.6269 3 14.1667V5.83333Z" stroke="#797878" stroke-width="1.5" stroke-linejoin="round"/><path d="M3.5 6.5L10 11L16.5 6.5" stroke="#797878" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
@endphp

<section class="profile-summary">
    <div class="container">
        <div class="profile-summary__greeting">
            <h1 class="profile-summary__hello">{{ __('profile.greeting', ['name' => $user['name']]) }}</h1>
            <p class="profile-summary__welcome">{{ __('profile.welcome') }}</p>
        </div>

        <div class="profile-summary__card">
            <div class="profile-summary__name">{{ $user['name'] }}</div>
            <div class="profile-summary__email-row">
                <span class="profile-summary__email-icon">{!! $mailIcon !!}</span>
                <span class="profile-summary__email">{{ $user['email'] }}</span>
            </div>
            <a href="#" class="profile-summary__edit">{{ __('profile.edit_profile') }}</a>
        </div>
    </div>
</section>
