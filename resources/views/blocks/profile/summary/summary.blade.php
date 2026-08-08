@push('block-styles')
    @vite(['resources/css/blocks/profile/summary/style.css'])
@endpush

@php
    $mailIcon =
        '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 5.83333C3 5.3731 3.3731 5 3.83333 5H16.1667C16.6269 5 17 5.3731 17 5.83333V14.1667C17 14.6269 16.6269 15 16.1667 15H3.83333C3.3731 15 3 14.6269 3 14.1667V5.83333Z" stroke="#797878" stroke-width="1.5" stroke-linejoin="round"/><path d="M3.5 6.5L10 11L16.5 6.5" stroke="#797878" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
@endphp

<section class="profile-summary">
    <div class="container">
        <div class="profile-summary__greeting">
            <h1 class="profile-summary__hello">{{ __('profile.greeting', ['name' => $user['name']]) }}</h1>
            <p class="profile-summary__welcome">{{ __('profile.welcome') }}</p>
        </div>

        <div class="profile-summary__card">
            <div class="profile-summary__name">{{ $user['name'] }}</div>
            <a href="mailto:{{ $user['email'] }}" class="profile-summary__email">{{ $user['email'] }}</a>
            <a href="#" class="profile-summary__edit">

                <span> {{ __('profile.edit_profile') }}</span>

                <svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M6.05377 6.05329C6.34666 5.7604 6.34666 5.28553 6.05377 4.99263L1.2808 0.219664C0.987904 -0.0732298 0.51303 -0.0732298 0.220137 0.219664C-0.0727568 0.512557 -0.0727568 0.987431 0.220137 1.28032L4.46278 5.52296L0.220137 9.7656C-0.0727568 10.0585 -0.0727568 10.5334 0.220137 10.8263C0.51303 11.1192 0.987904 11.1192 1.2808 10.8263L6.05377 6.05329ZM4.52344 5.52296V6.27296H5.52344V5.52296V4.77296H4.52344V5.52296Z"
                        fill="#7212BC" />
                </svg>

            </a>
        </div>
    </div>
</section>
