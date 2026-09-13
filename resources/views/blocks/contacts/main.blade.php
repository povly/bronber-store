@push('block-styles')
    @vite(['resources/css/blocks/contacts/style.css'])
@endpush

@php
    $phone = trim((string) ($block['phone'] ?? ''));
    $email = trim((string) ($block['email'] ?? ''));
    $address = trim((string) ($block['address'] ?? ''));
    $mapHtml = trim((string) ($block['map_html'] ?? ''));
    $submitLabel = trim((string) ($block['submit_label'] ?? ''));
    $consent = trim((string) ($block['consent_text'] ?? ''));
    $successMessage = trim((string) ($block['success_message'] ?? ''));
@endphp

<section class="contacts">
    <div class="container">
        <div class="contacts__top">
            <div class="contacts__info">
                <h1 class="contacts__title">{!! $block['title'] ?? '' !!}</h1>
                <p class="contacts__subtitle">{{ $block['subtitle'] ?? '' }}</p>
                @if ($phone !== '')
                    <a href="tel:{{ preg_replace('/[^+\d]/', '', $phone) }}" class="contacts__phone">{{ $phone }}</a>
                @endif
                <div class="contacts__address">
                    @if ($email !== '')
                        <a href="mailto:{{ $email }}">{{ $email }}</a>
                    @endif
                    @if ($address !== '')
                        <span>{{ $address }}</span>
                    @endif
                </div>
            </div>

            {{-- Валидация — в приложении; доставка заявки — через API CRM (закомментированный пример в ContactFormController, см. .ai/rules/app.md) --}}
            <form class="contacts__form" action="{{ request()->url() }}" method="POST">
                @csrf

                @if (session('success'))
                    <div class="contacts__alert contacts__alert--success" role="status">
                        {{ $successMessage !== '' ? $successMessage : __('store.contacts_form_success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="contacts__alert contacts__alert--error" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="contacts__field">
                    <label class="contacts__label" for="name">{{ __('store.contacts_form_name') }}*</label>
                    <input class="contacts__input @error('name') is-invalid @enderror" type="text" id="name" name="name"
                        value="{{ old('name') }}" placeholder="{{ __('store.contacts_form_name_placeholder') }}" required>
                </div>
                <div class="contacts__row">
                    <div class="contacts__field">
                        <label class="contacts__label" for="phone">{{ __('store.contacts_form_phone') }}*</label>
                        <input class="contacts__input @error('phone') is-invalid @enderror" type="tel" id="phone" name="phone"
                            value="{{ old('phone') }}" placeholder="{{ __('store.contacts_form_phone_placeholder') }}" required>
                    </div>
                    <div class="contacts__field">
                        <label class="contacts__label" for="email">{{ __('store.contacts_form_email') }}*</label>
                        <input class="contacts__input @error('email') is-invalid @enderror" type="email" id="email" name="email"
                            value="{{ old('email') }}" placeholder="{{ __('store.contacts_form_email_placeholder') }}" required>
                    </div>
                </div>
                <div class="contacts__field">
                    <label class="contacts__label" for="message">{{ __('store.contacts_form_subject') }}</label>
                    <textarea class="contacts__textarea @error('message') is-invalid @enderror" id="message" name="message" rows="1"
                        placeholder="{{ __('store.contacts_form_message_placeholder') }}">{{ old('message') }}</textarea>
                </div>
                <div class="contacts__bottom">
                    <button type="submit" class="contacts__submit btn btn--primary">
                        {{ $submitLabel !== '' ? $submitLabel : __('store.contacts_form_submit') }}
                    </button>
                    <p class="contacts__privacy">
                        {{ $consent !== '' ? $consent : __('store.contacts_form_consent') }}
                    </p>
                </div>
            </form>
        </div>

        @if ($mapHtml !== '')
            <div class="contacts__map">{!! $mapHtml !!}</div>
        @endif
    </div>
</section>
