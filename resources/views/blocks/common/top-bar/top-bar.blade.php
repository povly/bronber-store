@push('block-styles')
    @vite(['resources/css/blocks/common/top-bar/style.css'])
@endpush

<div class="top-bar">
    <div class="top-bar__inner">
        <div class="top-bar__left">
            <a href="tel:+79854498000" class="top-bar__phone">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_755_591)">
                        <path d="M10.374 12.426C10.5289 12.4971 10.7034 12.5134 10.8688 12.4721C11.0341 12.4308 11.1805 12.3344 11.2837 12.1987L11.55 11.85C11.6897 11.6637 11.8709 11.5125 12.0792 11.4084C12.2875 11.3042 12.5171 11.25 12.75 11.25H15C15.3978 11.25 15.7794 11.408 16.0607 11.6893C16.342 11.9706 16.5 12.3522 16.5 12.75V15C16.5 15.3978 16.342 15.7794 16.0607 16.0607C15.7794 16.342 15.3978 16.5 15 16.5C11.4196 16.5 7.9858 15.0777 5.45406 12.5459C2.92232 10.0142 1.5 6.58042 1.5 3C1.5 2.60218 1.65804 2.22064 1.93934 1.93934C2.22064 1.65804 2.60218 1.5 3 1.5H5.25C5.64782 1.5 6.02935 1.65804 6.31066 1.93934C6.59196 2.22064 6.75 2.60218 6.75 3V5.25C6.75 5.48287 6.69578 5.71254 6.59164 5.92082C6.4875 6.1291 6.33629 6.31028 6.15 6.45L5.799 6.71325C5.66131 6.81838 5.56426 6.96794 5.52434 7.13651C5.48442 7.30509 5.50409 7.48228 5.58 7.638C6.60501 9.7199 8.29082 11.4036 10.374 12.426Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </g>
                    <defs>
                        <clipPath id="clip0_755_591">
                            <rect width="18" height="18" fill="white" />
                        </clipPath>
                    </defs>
                </svg>
                <span>+7 (985) 449-8000</span>
            </a>

            <nav class="top-bar__links top-bar__links--left">
                <a href="{{ route('delivery') }}" class="top-bar__link">{{ __('store.top_delivery') }}</a>
                <a href="{{route('returns')}}" class="top-bar__link">{{ __('store.top_guarantee') }}</a>
            </nav>
        </div>

        <div class="top-bar__right">
            <nav class="top-bar__links top-bar__links--right">
                <a href="#!" class="top-bar__link">{{ __('store.top_career') }}</a>
                <a href="{{ route('faq') }}" class="top-bar__link">{{ __('store.top_faq') }}</a>
                <a href="{{ route('contacts') }}" class="top-bar__link">{{ __('store.top_contacts') }}</a>
            </nav>

            <div class="top-bar__lang">
                @foreach(config('app.available_locales') as $locale)
                    @php $isDefault = $locale === config('app.available_locales.0'); @endphp
                    <a href="{{ $isDefault ? '/' : '/' . $locale }}" class="top-bar__lang-btn {{ app()->getLocale() === $locale ? 'is-active' : '' }}">{{ strtoupper($locale) }}</a>
                    @if(!$loop->last)
                        <span class="top-bar__lang-divider"></span>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
