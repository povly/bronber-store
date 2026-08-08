@push('block-styles')
    @vite(['resources/css/blocks/profile/data/style.css'])
@endpush

<section class="profile-data">
    <div class="container">
        <div class="profile-data__header">
            <h2 class="profile-data__title section__title">{{ __('profile.data_title') }}</h2>
            <p class="profile-data__subtitle">{{ __('profile.data_subtitle') }}</p>
        </div>

        <div class="profile-data__card">
            <h3 class="profile-data__section-title">{{ __('profile.data_section_contacts') }}</h3>
            <div class="profile-data__fields">
                <label class="profile-data__field">
                    <span class="profile-data__label">{{ __('profile.data_field_name') }}</span>
                    <input class="profile-data__input" type="text" value="{{ $user['name'] }}">
                </label>
                <label class="profile-data__field">
                    <span class="profile-data__label">{{ __('profile.data_field_phone') }}</span>
                    <input class="profile-data__input" type="tel" value="{{ $user['phone'] }}">
                </label>
                <label class="profile-data__field">
                    <span class="profile-data__label">{{ __('profile.data_field_email') }}</span>
                    <input class="profile-data__input" type="email" value="{{ $user['email'] }}">
                </label>
            </div>
        </div>

        <div class="profile-data__card">
            <h3 class="profile-data__section-title">{{ __('profile.data_section_address') }}</h3>
            <div class="profile-data__fields">
                <label class="profile-data__field profile-data__field--wide">
                    <span class="profile-data__label">{{ __('profile.data_field_city') }}</span>
                    <input class="profile-data__input" type="text" value="{{ $user['city'] }}">
                </label>
                <label class="profile-data__field profile-data__field--wide">
                    <span class="profile-data__label">{{ __('profile.data_field_address') }}</span>
                    <input class="profile-data__input" type="text" value="{{ $user['address'] }}">
                </label>
                <label class="profile-data__field">
                    <span class="profile-data__label">{{ __('profile.data_field_zip') }}</span>
                    <input class="profile-data__input" type="text" value="{{ $user['zip'] }}">
                </label>
                <label class="profile-data__field">
                    <span class="profile-data__label">{{ __('profile.data_field_extra') }}</span>
                    <input class="profile-data__input" type="text" placeholder="{{ __('profile.data_field_extra_placeholder') }}">
                </label>
            </div>
        </div>

        <div class="profile-data__actions">
            <x-btn variant="primary" class="profile-data__btn" :text="__('profile.data_save')" />
            <x-btn variant="white-border" class="profile-data__btn profile-data__cancel" :text="__('profile.data_cancel')" />
        </div>
    </div>
</section>
