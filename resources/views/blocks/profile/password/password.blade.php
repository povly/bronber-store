@push('block-styles')
    @vite(['resources/css/blocks/profile/password/style.css'])
@endpush

<section class="profile-password" x-data="{ showCurrent: false, showNew: false, showConfirm: false }">
    <div class="container">
        <h2 class="profile-password__title section__title">{{ __('profile.password_title') }}</h2>

        <div class="profile-password__card">
            <div class="profile-password__field">
                <span class="profile-password__label">{{ __('profile.password_field_current') }}</span>
                <div class="profile-password__input-wrap">
                    <svg class="profile-password__icon profile-password__icon--lock" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="5" y="11" width="14" height="10" rx="2" stroke="#a7a7a7" stroke-width="1.5"/>
                        <path d="M8 11V8a4 4 0 0 1 8 0v3" stroke="#a7a7a7" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <input class="profile-password__input" :type="showCurrent ? 'text' : 'password'" name="current_password" placeholder="{{ __('profile.password_placeholder_current') }}">
                    <button type="button" class="profile-password__icon-btn" @click="showCurrent = !showCurrent">
                        <svg x-show="!showCurrent" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" stroke="#a7a7a7" stroke-width="1.5"/>
                            <circle cx="12" cy="12" r="3" stroke="#a7a7a7" stroke-width="1.5"/>
                        </svg>
                        <svg x-show="showCurrent" x-cloak width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none;">
                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" stroke="#a7a7a7" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c6.5 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68" stroke="#a7a7a7" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M6.6 6.6A13.34 13.34 0 0 0 2 12s3.5 7 10 7a10.43 10.43 0 0 0 5-.88" stroke="#a7a7a7" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M2 2l20 20" stroke="#a7a7a7" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="profile-password__field">
                <span class="profile-password__label">{{ __('profile.password_field_new') }}</span>
                <div class="profile-password__input-wrap">
                    <svg class="profile-password__icon profile-password__icon--lock" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="5" y="11" width="14" height="10" rx="2" stroke="#a7a7a7" stroke-width="1.5"/>
                        <path d="M8 11V8a4 4 0 0 1 8 0v3" stroke="#a7a7a7" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <input class="profile-password__input" :type="showNew ? 'text' : 'password'" name="new_password" placeholder="{{ __('profile.password_placeholder_new') }}">
                    <button type="button" class="profile-password__icon-btn" @click="showNew = !showNew">
                        <svg x-show="!showNew" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" stroke="#a7a7a7" stroke-width="1.5"/>
                            <circle cx="12" cy="12" r="3" stroke="#a7a7a7" stroke-width="1.5"/>
                        </svg>
                        <svg x-show="showNew" x-cloak width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none;">
                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" stroke="#a7a7a7" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c6.5 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68" stroke="#a7a7a7" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M6.6 6.6A13.34 13.34 0 0 0 2 12s3.5 7 10 7a10.43 10.43 0 0 0 5-.88" stroke="#a7a7a7" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M2 2l20 20" stroke="#a7a7a7" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="profile-password__field">
                <span class="profile-password__label">{{ __('profile.password_field_confirm') }}</span>
                <div class="profile-password__input-wrap">
                    <svg class="profile-password__icon profile-password__icon--lock" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="5" y="11" width="14" height="10" rx="2" stroke="#a7a7a7" stroke-width="1.5"/>
                        <path d="M8 11V8a4 4 0 0 1 8 0v3" stroke="#a7a7a7" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <input class="profile-password__input" :type="showConfirm ? 'text' : 'password'" name="confirm_password" placeholder="{{ __('profile.password_placeholder_confirm') }}">
                    <button type="button" class="profile-password__icon-btn" @click="showConfirm = !showConfirm">
                        <svg x-show="!showConfirm" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" stroke="#a7a7a7" stroke-width="1.5"/>
                            <circle cx="12" cy="12" r="3" stroke="#a7a7a7" stroke-width="1.5"/>
                        </svg>
                        <svg x-show="showConfirm" x-cloak width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:none;">
                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" stroke="#a7a7a7" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c6.5 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68" stroke="#a7a7a7" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M6.6 6.6A13.34 13.34 0 0 0 2 12s3.5 7 10 7a10.43 10.43 0 0 0 5-.88" stroke="#a7a7a7" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M2 2l20 20" stroke="#a7a7a7" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="profile-password__actions">
            <x-btn variant="primary" class="profile-password__btn" :text="__('profile.password_save')" />
            <x-btn variant="white-border" class="profile-password__btn profile-password__cancel" :text="__('profile.password_cancel')" />
        </div>
    </div>
</section>
