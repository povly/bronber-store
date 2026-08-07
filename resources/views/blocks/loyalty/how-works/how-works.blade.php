@push('block-styles')
    @vite(['resources/css/blocks/loyalty/how-works/style.css'])
@endpush

@php
    $steps = [
        ['icon' => 'cart', 'title' => __('loyalty.step_1_title'), 'text' => __('loyalty.step_1_text')],
        ['icon' => 'coin', 'title' => __('loyalty.step_2_title'), 'text' => __('loyalty.step_2_text')],
        ['icon' => 'card', 'title' => __('loyalty.step_3_title'), 'text' => __('loyalty.step_3_text')],
    ];

    $icons = [
        'cart' => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 7h3l3 14h15l2-9H10" stroke="#7212bc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="13" cy="25" r="2" fill="#7212bc"/><circle cx="22" cy="25" r="2" fill="#7212bc"/></svg>',
        'coin' => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="16" cy="16" r="14" stroke="#7212bc" stroke-width="2"/><path d="M16 8C12.7 8 10 10.7 10 14s2.7 6 6 6 6-2.7 6-6-2.7-6-6-6zm0 10c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4-1.8 4-4 4z" fill="#7212bc"/></svg>',
        'card' => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="4" y="8" width="24" height="16" rx="2" stroke="#7212bc" stroke-width="2"/><line x1="4" y1="13" x2="28" y2="13" stroke="#7212bc" stroke-width="2"/></svg>',
    ];
@endphp

<section class="loyalty-how">
    <div class="container">
        <h2 class="loyalty-how__title">{{ __('loyalty.how_title') }}</h2>
        <div class="loyalty-how__steps">
            @foreach ($steps as $step)
                <div class="loyalty-how__step">
                    <div class="loyalty-how__step-icon">{!! $icons[$step['icon']] !!}</div>
                    <h3 class="loyalty-how__step-title">{{ $step['title'] }}</h3>
                    <p class="loyalty-how__step-text">{{ $step['text'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
