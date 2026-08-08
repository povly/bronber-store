@push('block-styles')
    @vite(['resources/css/blocks/loyalty/bottom/style.css'])
@endpush

@php
    $faqs = [
        ['question' => __('loyalty.faq_1_question'), 'answer' => __('loyalty.faq_1_answer')],
        ['question' => __('loyalty.faq_2_question'), 'answer' => __('loyalty.faq_2_answer')],
        ['question' => __('loyalty.faq_3_question'), 'answer' => __('loyalty.faq_3_answer')],
        ['question' => __('loyalty.faq_4_question'), 'answer' => __('loyalty.faq_4_answer')],
    ];

    $icons = [
        'star' => '<svg width="55" height="55" viewBox="0 0 55 55" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M27.5 16.0409V48.1242" stroke="#7212BC" stroke-width="4.58333" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M45.8307 25.2091V43.5424C45.8307 44.758 45.3478 45.9238 44.4883 46.7833C43.6288 47.6429 42.463 48.1258 41.2474 48.1258H13.7474C12.5318 48.1258 11.366 47.6429 10.5065 46.7833C9.64695 45.9238 9.16406 44.758 9.16406 43.5424V25.2091" stroke="#7212BC" stroke-width="4.58333" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M17.1901 16.0434C15.6706 16.0434 14.2134 15.4398 13.139 14.3654C12.0645 13.291 11.4609 11.8337 11.4609 10.3143C11.4609 8.79481 12.0645 7.33757 13.139 6.26314C14.2134 5.18872 15.6706 4.58511 17.1901 4.58511C19.4008 4.54659 21.5672 5.61925 23.4068 7.66319C25.2463 9.70713 26.6737 12.6275 27.5026 16.0434C28.3316 12.6275 29.7589 9.70713 31.5984 7.66319C33.438 5.61925 35.6044 4.54659 37.8151 4.58511C39.3346 4.58511 40.7918 5.18872 41.8662 6.26314C42.9407 7.33757 43.5443 8.79481 43.5443 10.3143C43.5443 11.8337 42.9407 13.291 41.8662 14.3654C40.7918 15.4398 39.3346 16.0434 37.8151 16.0434" stroke="#7212BC" stroke-width="4.58333" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M45.8333 16.0409H9.16667C7.90101 16.0409 6.875 17.0669 6.875 18.3326V22.9159C6.875 24.1815 7.90101 25.2076 9.16667 25.2076H45.8333C47.099 25.2076 48.125 24.1815 48.125 22.9159V18.3326C48.125 17.0669 47.099 16.0409 45.8333 16.0409Z" stroke="#7212BC" stroke-width="4.58333" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>',
    ];
@endphp

<section class="loyalty-bottom section">
    <div class="container loyalty-bottom__container">
        <div class="loyalty-bottom__example">
            <h2 class="loyalty-bottom__title section__title">{{ __('loyalty.example_title') }}</h2>
            <div class="loyalty-bottom__example-card">
                <div class="loyalty-bottom__example-row">
                    <span class="loyalty-bottom__example-label">{{ __('loyalty.order_amount') }}</span>
                    <span class="loyalty-bottom__example-value">10,000 ₽</span>
                </div>
                <div class="loyalty-bottom__example-row loyalty-bottom__example-row--accent">
                    <span class="loyalty-bottom__example-label">{{ __('loyalty.bonus_accrued') }}</span>
                    <span class="loyalty-bottom__example-value">100 ₽</span>
                </div>
                <div class="loyalty-bottom__banner">
                    <div class="loyalty-bottom__banner-left">
                        <span class="loyalty-bottom__banner-icon">{!! $icons['star'] !!}</span>
                        <div class="loyalty-bottom__banner-title loyalty-bottom__banner-title--mb">
                            {{ __('loyalty.banner_title') }}
                        </div>
                    </div>
                    <div class="loyalty-bottom__banner-text loyalty-bottom__banner-text--mb">{{ __('loyalty.banner_text') }}</div>

                    <div class="loyalty-bottom__banner-body">
                        <div class="loyalty-bottom__banner-title">{{ __('loyalty.banner_title') }}</div>
                        <div class="loyalty-bottom__banner-text">{{ __('loyalty.banner_text') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="loyalty-bottom__faq">
            <h2 class="loyalty-bottom__title section__title">{{ __('loyalty.faq_title') }}</h2>
            <div class="loyalty-bottom__faq-list" x-data="{ open: 0 }">
                @foreach ($faqs as $i => $faq)
                    <div class="loyalty-bottom__faq-item" :class="{ 'is-open': open === {{ $i }} }">
                        <button type="button" class="loyalty-bottom__faq-question"
                            @click="open = open === {{ $i }} ? null : {{ $i }}">
                            <span>{{ $faq['question'] }}</span>
                            <svg class="loyalty-bottom__faq-icon" width="15" height="20" viewBox="0 0 15 20"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path class="loyalty-bottom__faq-icon-h" d="M2 10H13" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" />
                                <path class="loyalty-bottom__faq-icon-v" d="M7.5 4V16" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </button>
                        <div class="loyalty-bottom__faq-answer-wrap" x-show="open === {{ $i }}" x-collapse
                            {{ $i !== 0 ? 'x-cloak' : '' }}>
                            <p class="loyalty-bottom__faq-answer">{{ $faq['answer'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
