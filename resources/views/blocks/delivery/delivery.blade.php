@push('block-styles')
    @vite(['resources/css/blocks/delivery/style.css'])
@endpush

@php
    $methods = [
        [
            'title' => __('delivery.method_cash_title'),
            'text' => __('delivery.method_cash_text'),
        ],
        [
            'title' => __('delivery.method_card_title'),
            'text' => __('delivery.method_card_text'),
        ],
        [
            'title' => __('delivery.method_sbp_title'),
            'text' => __('delivery.method_sbp_text'),
        ],
        [
            'title' => __('delivery.method_currency_title'),
            'text' => __('delivery.method_currency_text'),
        ],
    ];
@endphp

<section class="delivery">
    <div class="container">
        <h1 class="delivery__title section__title">{{ __('delivery.title') }}</h1>

        <div class="delivery__methods">
            @foreach ($methods as $key => $method)
                <div class="delivery__method">
                    <span class="delivery__icon">
                        @if($key == 0)
                            <svg width="53" height="53" viewBox="0 0 53 53" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_925_661)">
                            <path d="M41.8194 24.1457L19.8031 11.4346C18.2832 10.5571 16.3397 11.0778 15.4622 12.5977L9.1066 23.6059C8.22908 25.1258 8.74984 27.0693 10.2697 27.9468L32.286 40.6579C33.8059 41.5354 35.7494 41.0147 36.627 39.4948L42.9825 28.4866C43.86 26.9667 43.3393 25.0232 41.8194 24.1457Z" stroke="white" stroke-width="2.27933" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M24.4534 28.798C25.9733 29.6756 27.9168 29.1548 28.7944 27.6349C29.6719 26.115 29.1511 24.1715 27.6312 23.294C26.1113 22.4164 24.1678 22.9372 23.2903 24.4571C22.4128 25.977 22.9335 27.9205 24.4534 28.798Z" stroke="white" stroke-width="2.27933" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17.7932 21.2793L17.807 21.2872M34.3054 30.8126L34.3192 30.8206" stroke="white" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </g>
                            <defs>
                            <clipPath id="clip0_925_661">
                            <rect width="38.1333" height="38.1333" fill="white" transform="translate(19.0667) rotate(30)"/>
                            </clipPath>
                            </defs>
                            </svg>

                        @elseif($key == 1)
                        <svg width="37" height="37" viewBox="0 0 37 37" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M30.8398 7.71033H6.17318C4.4703 7.71033 3.08984 9.09078 3.08984 10.7937V26.2103C3.08984 27.9132 4.4703 29.2937 6.17318 29.2937H30.8398C32.5427 29.2937 33.9232 27.9132 33.9232 26.2103V10.7937C33.9232 9.09078 32.5427 7.71033 30.8398 7.71033Z" stroke="white" stroke-width="3.08333" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3.08984 15.4147H33.9232" stroke="white" stroke-width="3.08333" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>

                        @elseif($key == 2)
                        <svg width="34" height="34" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.91667 4.25H5.66667C4.88426 4.25 4.25 4.88426 4.25 5.66667V9.91667C4.25 10.6991 4.88426 11.3333 5.66667 11.3333H9.91667C10.6991 11.3333 11.3333 10.6991 11.3333 9.91667V5.66667C11.3333 4.88426 10.6991 4.25 9.91667 4.25Z" stroke="white" stroke-width="2.83333" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M28.3346 4.25H24.0846C23.3022 4.25 22.668 4.88426 22.668 5.66667V9.91667C22.668 10.6991 23.3022 11.3333 24.0846 11.3333H28.3346C29.117 11.3333 29.7513 10.6991 29.7513 9.91667V5.66667C29.7513 4.88426 29.117 4.25 28.3346 4.25Z" stroke="white" stroke-width="2.83333" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9.91667 22.6672H5.66667C4.88426 22.6672 4.25 23.3015 4.25 24.0839V28.3339C4.25 29.1163 4.88426 29.7506 5.66667 29.7506H9.91667C10.6991 29.7506 11.3333 29.1163 11.3333 28.3339V24.0839C11.3333 23.3015 10.6991 22.6672 9.91667 22.6672Z" stroke="white" stroke-width="2.83333" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M29.7513 22.6672H25.5013C24.7499 22.6672 24.0292 22.9657 23.4978 23.4971C22.9665 24.0285 22.668 24.7491 22.668 25.5006V29.7506" stroke="white" stroke-width="2.83333" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M29.75 29.75V29.7646" stroke="white" stroke-width="2.83333" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M17.0013 9.91724V14.1672C17.0013 14.9187 16.7028 15.6394 16.1714 16.1707C15.6401 16.7021 14.9194 17.0006 14.168 17.0006H9.91797" stroke="white" stroke-width="2.83333" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M4.25 17H4.26458" stroke="white" stroke-width="2.83333" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M17 4.25H17.0146" stroke="white" stroke-width="2.83333" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M17 22.6672V22.6818" stroke="white" stroke-width="2.83333" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M22.668 17H24.0846" stroke="white" stroke-width="2.83333" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M29.75 17V17.0146" stroke="white" stroke-width="2.83333" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M17 29.7494V28.3328" stroke="white" stroke-width="2.83333" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>


                        @elseif($key == 3)
                        <svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.5013 32.0833C25.5555 32.0833 32.0846 25.5541 32.0846 17.5C32.0846 9.44584 25.5555 2.91666 17.5013 2.91666C9.44715 2.91666 2.91797 9.44584 2.91797 17.5C2.91797 25.5541 9.44715 32.0833 17.5013 32.0833Z" stroke="white" stroke-width="2.91667" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M23.3346 11.6667H14.5846C13.8111 11.6667 13.0692 11.974 12.5222 12.521C11.9753 13.0679 11.668 13.8098 11.668 14.5834C11.668 15.3569 11.9753 16.0988 12.5222 16.6457C13.0692 17.1927 13.8111 17.5 14.5846 17.5H20.418C21.1915 17.5 21.9334 17.8073 22.4804 18.3543C23.0273 18.9013 23.3346 19.6431 23.3346 20.4167C23.3346 21.1902 23.0273 21.9321 22.4804 22.4791C21.9334 23.0261 21.1915 23.3334 20.418 23.3334H11.668" stroke="white" stroke-width="2.91667" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M17.5 26.25V8.75" stroke="white" stroke-width="2.91667" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>


                        @endif
                    </span>
                    <h2 class="delivery__method-title">{!! nl2br(e($method['title'])) !!}</h2>
                    <p class="delivery__method-text">{{ $method['text'] }}</p>
                </div>
            @endforeach
        </div>

        <h2 class="delivery__contact-title section__title">{{ __('delivery.contact_title') }}</h2>

        <div class="delivery__contacts">
            <a href="tel:{{ preg_replace('/[^+\d]/', '', __('delivery.contact_phone')) }}" class="delivery__contact">
                <span class="delivery__contact-icon">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 2.5L7.5 2.5L8.75 6.25L6.875 7.5C7.5 9.375 9.0625 10.9375 10.9375 11.5625L12.1875 9.6875L15.9375 10.9375L15.9375 13.4375C15.9375 14.2322 15.2947 14.875 14.5 14.875C8.37586 14.875 2.5625 9.06164 2.5625 2.9375C2.5625 2.14282 3.20532 1.5 4 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" transform="translate(1 1)"/>
                    </svg>
                </span>
                {{ __('delivery.contact_phone') }}
            </a>
            <a href="mailto:{{ __('delivery.contact_email') }}" class="delivery__contact">
                <span class="delivery__contact-icon">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="1.5" y="3.5" width="17" height="13" rx="2" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M2 5L10 11L18 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                {{ __('delivery.contact_email') }}
            </a>
        </div>
    </div>
</section>
