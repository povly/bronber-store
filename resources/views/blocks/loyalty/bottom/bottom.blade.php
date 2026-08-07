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
        'star' => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16 4l3.5 7.5L28 13l-6 5.5L23.5 27 16 23l-7.5 4L10 18.5 4 13l8.5-1.5L16 4z" stroke="#7212bc" stroke-width="2" stroke-linejoin="round"/></svg>',
    ];
@endphp

<section class="loyalty-bottom">
    <div class="container">
        <div class="loyalty-bottom__example">
            <h2 class="loyalty-bottom__title">{{ __('loyalty.example_title') }}</h2>
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
                    <span class="loyalty-bottom__banner-icon">{!! $icons['star'] !!}</span>
                    <div class="loyalty-bottom__banner-body">
                        <div class="loyalty-bottom__banner-title">{{ __('loyalty.banner_title') }}</div>
                        <div class="loyalty-bottom__banner-text">{{ __('loyalty.banner_text') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="loyalty-bottom__faq">
            <h2 class="loyalty-bottom__title">{{ __('loyalty.faq_title') }}</h2>
            <div class="loyalty-bottom__faq-list" x-data="{ open: 0 }">
                @foreach ($faqs as $i => $faq)
                    <div class="loyalty-bottom__faq-item" :class="{ 'is-open': open === {{ $i }} }">
                        <button type="button" class="loyalty-bottom__faq-question" @click="open = open === {{ $i }} ? null : {{ $i }}">
                            <span>{{ $faq['question'] }}</span>
                            <svg class="loyalty-bottom__faq-icon" width="15" height="20" viewBox="0 0 15 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path class="loyalty-bottom__faq-icon-h" d="M2 10H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path class="loyalty-bottom__faq-icon-v" d="M7.5 4V16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>
                        <div class="loyalty-bottom__faq-answer-wrap" x-show="open === {{ $i }}" x-collapse x-cloak>
                            <p class="loyalty-bottom__faq-answer">{{ $faq['answer'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
