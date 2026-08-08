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
        'cart' => '<svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M26.6615 73.3352C28.5024 73.3352 29.9948 71.8429 29.9948 70.0019C29.9948 68.161 28.5024 66.6686 26.6615 66.6686C24.8205 66.6686 23.3281 68.161 23.3281 70.0019C23.3281 71.8429 24.8205 73.3352 26.6615 73.3352Z" stroke="#7212BC" stroke-width="6.3" stroke-linecap="round" stroke-linejoin="round"/> <path d="M63.3333 73.3352C65.1743 73.3352 66.6667 71.8429 66.6667 70.0019C66.6667 68.161 65.1743 66.6686 63.3333 66.6686C61.4924 66.6686 60 68.161 60 70.0019C60 71.8429 61.4924 73.3352 63.3333 73.3352Z" stroke="#7212BC" stroke-width="6.3" stroke-linecap="round" stroke-linejoin="round"/> <path d="M6.82812 6.83667H13.4948L22.3615 48.2367C22.6867 49.7529 23.5304 51.1083 24.7472 52.0695C25.964 53.0308 27.4778 53.5378 29.0281 53.5033H61.6281C63.1454 53.5009 64.6164 52.981 65.7982 52.0295C66.98 51.0779 67.8019 49.7518 68.1281 48.27L73.6281 23.5033H17.0615" stroke="#7212BC" stroke-width="6.3" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
        'coin' => '<svg width="76" height="76" viewBox="0 0 76 76" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M43.5146 56.1645C42.564 59.3077 40.8149 62.1509 38.438 64.4167C36.0611 66.6826 33.1376 68.2937 29.9525 69.0929C26.7674 69.8922 23.4295 69.8522 20.2644 68.9771C17.0993 68.1019 14.2151 66.4213 11.8931 64.0993C9.57107 61.7773 7.8905 58.893 7.01532 55.728C6.14014 52.5629 6.10022 49.225 6.89946 46.0399C7.69871 42.8548 9.30982 39.9313 11.5757 37.5544C13.8415 35.1775 16.6847 33.4284 19.8279 32.4778" stroke="#7212BC" stroke-width="6.33333" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M47.5 19H50.6667V31.6667" stroke="#7212BC" stroke-width="6.33333" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M19.4141 46.7645L22.1564 45.1812L28.4897 56.1505" stroke="#7212BC" stroke-width="6.33333" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M50.6719 44.3326C61.1653 44.3326 69.6719 35.8261 69.6719 25.3326C69.6719 14.8392 61.1653 6.33264 50.6719 6.33264C40.1785 6.33264 31.6719 14.8392 31.6719 25.3326C31.6719 35.8261 40.1785 44.3326 50.6719 44.3326Z" stroke="#7212BC" stroke-width="6.33333" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>',
        'card' => '<svg width="83" height="83" viewBox="0 0 83 83" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M65.7083 24.2083V13.8333C65.7083 12.9161 65.344 12.0365 64.6954 11.3879C64.0469 10.7394 63.1672 10.375 62.25 10.375H17.2917C15.4573 10.375 13.698 11.1037 12.4008 12.4008C11.1037 13.698 10.375 15.4573 10.375 17.2917C10.375 19.1261 11.1037 20.8854 12.4008 22.1825C13.698 23.4796 15.4573 24.2083 17.2917 24.2083H69.1667C70.0839 24.2083 70.9635 24.5727 71.6121 25.2213C72.2606 25.8698 72.625 26.7495 72.625 27.6667V41.5M72.625 41.5H62.25C60.4156 41.5 58.6563 42.2287 57.3592 43.5258C56.0621 44.823 55.3333 46.5823 55.3333 48.4167C55.3333 50.2511 56.0621 52.0104 57.3592 53.3075C58.6563 54.6046 60.4156 55.3333 62.25 55.3333H72.625C73.5422 55.3333 74.4219 54.969 75.0704 54.3204C75.719 53.6718 76.0833 52.7922 76.0833 51.875V44.9583C76.0833 44.0411 75.719 43.1615 75.0704 42.5129C74.4219 41.8644 73.5422 41.5 72.625 41.5Z" stroke="#7212BC" stroke-width="6.3" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M10.375 17.2916V65.7083C10.375 67.5427 11.1037 69.302 12.4008 70.5991C13.698 71.8962 15.4573 72.625 17.2917 72.625H69.1667C70.0839 72.625 70.9635 72.2606 71.6121 71.612C72.2606 70.9635 72.625 70.0838 72.625 69.1666V55.3333" stroke="#7212BC" stroke-width="6.3" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>',
    ];
@endphp

<section class="loyalty-how section">
    <div class="container">
        <h2 class="loyalty-how__title section__title">{{ __('loyalty.how_title') }}</h2>
        <div class="loyalty-how__steps">
            @foreach ($steps as $step)
                <div class="loyalty-how__step">
                    <div class="loyalty-how__step-icon">{!! $icons[$step['icon']] !!}</div>
                    <h3 class="loyalty-how__step-title">{!! $step['title'] !!}</h3>
                    <p class="loyalty-how__step-text">{!! $step['text'] !!}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
