@push('block-styles')
    @vite(['resources/css/blocks/loyalty/style.css'])
@endpush

@php
    $benefits = [
        ['icon' => 'coin', 'title' => __('loyalty.benefit_1_title'), 'text' => __('loyalty.benefit_1_text'), 'main' => true],
        ['icon' => 'cart', 'title' => null, 'text' => __('loyalty.benefit_2'), 'main' => false],
        ['icon' => 'infinity', 'title' => null, 'text' => __('loyalty.benefit_3'), 'main' => false],
        ['icon' => 'star', 'title' => null, 'text' => __('loyalty.benefit_4'), 'main' => false],
    ];

    $steps = [
        ['icon' => 'cart', 'title' => __('loyalty.step_1_title'), 'text' => __('loyalty.step_1_text')],
        ['icon' => 'coin', 'title' => __('loyalty.step_2_title'), 'text' => __('loyalty.step_2_text')],
        ['icon' => 'card', 'title' => __('loyalty.step_3_title'), 'text' => __('loyalty.step_3_text')],
    ];

    $faqs = [
        ['question' => __('loyalty.faq_1_question'), 'answer' => __('loyalty.faq_1_answer')],
        ['question' => __('loyalty.faq_2_question'), 'answer' => __('loyalty.faq_2_answer')],
        ['question' => __('loyalty.faq_3_question'), 'answer' => __('loyalty.faq_3_answer')],
        ['question' => __('loyalty.faq_4_question'), 'answer' => __('loyalty.faq_4_answer')],
    ];

    $icons = [
        'coin' => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="16" cy="16" r="14" stroke="#7212bc" stroke-width="2"/><path d="M16 8C12.7 8 10 10.7 10 14s2.7 6 6 6 6-2.7 6-6-2.7-6-6-6zm0 10c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4-1.8 4-4 4z" fill="#7212bc"/></svg>',
        'cart' => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 7h3l3 14h15l2-9H10" stroke="#7212bc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="13" cy="25" r="2" fill="#7212bc"/><circle cx="22" cy="25" r="2" fill="#7212bc"/></svg>',
        'infinity' => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 11c-3.3 0-6 2.2-6 5s2.7 5 6 5c4 0 6-5 6-5s2-5 6-5c3.3 0 6 2.2 6 5s-2.7 5-6 5c-4 0-6-5-6-5" stroke="#7212bc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        'star' => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16 4l3.5 7.5L28 13l-6 5.5L23.5 27 16 23l-7.5 4L10 18.5 4 13l8.5-1.5L16 4z" stroke="#7212bc" stroke-width="2" stroke-linejoin="round"/></svg>',
        'card' => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="4" y="8" width="24" height="16" rx="2" stroke="#7212bc" stroke-width="2"/><line x1="4" y1="13" x2="28" y2="13" stroke="#7212bc" stroke-width="2"/></svg>',
    ];
@endphp

<section class="loyalty">
    <div class="container">

        {{-- Block 1: Hero --}}
        <div class="loyalty__hero">
            <div class="loyalty__hero-content">
                <h1 class="loyalty__title">{{ __('loyalty.title') }}</h1>
                <p class="loyalty__subtitle">{{ __('loyalty.subtitle') }}</p>
                <a href="#" class="btn btn--primary">{{ __('loyalty.register_button') }}</a>
            </div>
            <div class="loyalty__hero-image">
                <x-img path="/images/blog/1" :alt="__('loyalty.title')" class="loyalty__hero-img" />
            </div>
        </div>

        {{-- Block 2: Benefits bar --}}
        <div class="loyalty__benefits">
            @foreach ($benefits as $benefit)
                <div class="loyalty__benefit{{ $benefit['main'] ? ' loyalty__benefit--main' : '' }}">
                    <span class="loyalty__benefit-icon">{!! $icons[$benefit['icon']] !!}</span>
                    @if ($benefit['main'])
                        <div class="loyalty__benefit-body">
                            <div class="loyalty__benefit-title">{{ $benefit['title'] }}</div>
                            <div class="loyalty__benefit-text">{{ $benefit['text'] }}</div>
                        </div>
                    @else
                        <div class="loyalty__benefit-text">{{ $benefit['text'] }}</div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Block 3: How it works --}}
        <h2 class="loyalty__heading">{{ __('loyalty.how_title') }}</h2>
        <div class="loyalty__steps">
            @foreach ($steps as $step)
                <div class="loyalty__step">
                    <div class="loyalty__step-icon">{!! $icons[$step['icon']] !!}</div>
                    <h3 class="loyalty__step-title">{{ $step['title'] }}</h3>
                    <p class="loyalty__step-text">{{ $step['text'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Block 4: Example + FAQ --}}
        <div class="loyalty__bottom">
            <div class="loyalty__example">
                <h2 class="loyalty__heading">{{ __('loyalty.example_title') }}</h2>
                <div class="loyalty__example-card">
                    <div class="loyalty__example-row">
                        <span class="loyalty__example-label">{{ __('loyalty.order_amount') }}</span>
                        <span class="loyalty__example-value">10,000 ₽</span>
                    </div>
                    <div class="loyalty__example-row loyalty__example-row--accent">
                        <span class="loyalty__example-label">{{ __('loyalty.bonus_accrued') }}</span>
                        <span class="loyalty__example-value">100 ₽</span>
                    </div>
                    <div class="loyalty__banner">
                        <span class="loyalty__banner-icon">{!! $icons['star'] !!}</span>
                        <div class="loyalty__banner-body">
                            <div class="loyalty__banner-title">{{ __('loyalty.banner_title') }}</div>
                            <div class="loyalty__banner-text">{{ __('loyalty.banner_text') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="loyalty__faq">
                <h2 class="loyalty__heading">{{ __('loyalty.faq_title') }}</h2>
                <div class="loyalty__faq-list" x-data="{ open: 0 }">
                    @foreach ($faqs as $i => $faq)
                        <div class="loyalty__faq-item" :class="{ 'is-open': open === {{ $i }} }">
                            <button type="button" class="loyalty__faq-question" @click="open = open === {{ $i }} ? null : {{ $i }}">
                                <span>{{ $faq['question'] }}</span>
                                <svg class="loyalty__faq-icon" width="15" height="20" viewBox="0 0 15 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path class="loyalty__faq-icon-h" d="M2 10H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path class="loyalty__faq-icon-v" d="M7.5 4V16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </button>
                            <div class="loyalty__faq-answer-wrap" x-show="open === {{ $i }}" x-collapse x-cloak>
                                <p class="loyalty__faq-answer">{{ $faq['answer'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</section>
