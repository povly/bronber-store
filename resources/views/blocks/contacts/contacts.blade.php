@push('block-styles')
    @vite(['resources/css/blocks/contacts/style.css'])
@endpush

<section class="contacts">
    <div class="container">
        <div class="contacts__top">
            <div class="contacts__info">
                <h1 class="contacts__title">{!! __('store.contacts_title') !!}</h1>
                <p class="contacts__subtitle">{{ __('store.contacts_subtitle') }}</p>
                <a href="tel:+79854498000" class="contacts__phone">{{ __('store.contacts_phone') }}</a>
                <div class="contacts__address">
                    <a href="mailto:{{ __('store.contacts_email') }}">{{ __('store.contacts_email') }}</a>
                    <span>{{ __('store.contacts_address') }}</span>
                </div>
            </div>

            <form class="contacts__form" action="" method="POST">
                @csrf
                <div class="contacts__field">
                    <label class="contacts__label" for="name">{{ __('store.contacts_form_name') }}*</label>
                    <input class="contacts__input" type="text" id="name" name="name"
                        placeholder="{{ __('store.contacts_form_name_placeholder') }}" required>
                </div>
                <div class="contacts__row">
                    <div class="contacts__field">
                        <label class="contacts__label" for="phone">{{ __('store.contacts_form_phone') }}*</label>
                        <input class="contacts__input" type="tel" id="phone" name="phone"
                            placeholder="{{ __('store.contacts_form_phone_placeholder') }}" required>
                    </div>
                    <div class="contacts__field">
                        <label class="contacts__label" for="email">{{ __('store.contacts_form_email') }}*</label>
                        <input class="contacts__input" type="email" id="email" name="email"
                            placeholder="{{ __('store.contacts_form_email_placeholder') }}" required>
                    </div>
                </div>
                <div class="contacts__field">
                    <label class="contacts__label" for="message">{{ __('store.contacts_form_subject') }}</label>
                    <textarea class="contacts__textarea" id="message" name="message" rows="1"
                        placeholder="{{ __('store.contacts_form_message_placeholder') }}"></textarea>
                </div>
                <div class="contacts__bottom">
                    <button type="submit" class="contacts__submit btn btn--primary">{{ __('store.contacts_form_submit') }}</button>
                    <p class="contacts__privacy">{!! __('store.contacts_form_consent') !!}</p>
                </div>
            </form>
        </div>

        <div class="contacts__map">
            <iframe src="https://yandex.ru/map-widget/v1/?ll=37.539822%2C55.748792&z=15" width="100%" height="100%"
                frameborder="0" loading="lazy"></iframe>
        </div>
    </div>
</section>
