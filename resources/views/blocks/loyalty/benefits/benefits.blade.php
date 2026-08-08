@push('block-styles')
    @vite(['resources/css/blocks/loyalty/benefits/style.css'])
@endpush

@php
    $benefits = [
        ['icon' => 'coin', 'title' => '1 бонус = 1 ₽', 'text' => 'Бонусами можно оплатить до 30% от&nbsp;суммы заказа', 'main' => true],
        ['icon' => 'cart', 'title' => null, 'text' => '<strong>Бонусы</strong> начисляются с&nbsp;каждой покупки', 'main' => false],
        ['icon' => 'infinity', 'title' => null, 'text' => 'Бонусы <strong>не сгорают</strong> и&nbsp;действуют всегда', 'main' => false],
        ['icon' => 'star', 'title' => null, 'text' => '<strong>Эксклюзивные</strong> акции и&nbsp;предложения', 'main' => false],
    ];

    $icons = [
        'coin' => '<svg width="111" height="111" viewBox="0 0 111 111" fill="none" xmlns="http://www.w3.org/2000/svg"> <circle cx="56" cy="56" r="48" fill="#F9F7FC" stroke="#F6EAFF" stroke-width="6"/> <path d="M41.625 69H64.75" stroke="#7212BC" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/> <path d="M41.625 55.5H64.75C67.2033 55.5 69.556 54.5254 71.2907 52.7907C73.0254 51.056 74 48.7033 74 46.25C74 43.7967 73.0254 41.444 71.2907 39.7093C69.556 37.9746 67.2033 37 64.75 37H50.875V78.625" stroke="#7212BC" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
        'cart' => '<svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M16.0417 21.8745H18.9583C19.7319 21.8745 20.4737 21.5672 21.0207 21.0202C21.5677 20.4732 21.875 19.7314 21.875 18.9578C21.875 18.1843 21.5677 17.4424 21.0207 16.8954C20.4737 16.3484 19.7319 16.0411 18.9583 16.0411H14.5833C13.7083 16.0411 12.9792 16.3328 12.5417 16.9161L4.375 24.7911" stroke="#7212BC" stroke-width="2.91667" stroke-linecap="round" stroke-linejoin="round"/> <path d="M10.2109 30.6262L12.5443 28.5845C12.9818 28.0012 13.7109 27.7095 14.5859 27.7095H20.4193C22.0234 27.7095 23.4818 27.1262 24.5026 25.9595L31.2109 19.5429C31.7737 19.011 32.1021 18.2775 32.124 17.5035C32.1459 16.7295 31.8594 15.9785 31.3276 15.4158C30.7958 14.853 30.0622 14.5246 29.2882 14.5027C28.5142 14.4808 27.7633 14.7673 27.2005 15.2991L21.0755 20.9866" stroke="#7212BC" stroke-width="2.91667" stroke-linecap="round" stroke-linejoin="round"/> <path d="M2.91406 23.3339L11.6641 32.0839" stroke="#7212BC" stroke-width="2.91667" stroke-linecap="round" stroke-linejoin="round"/> <path d="M23.3307 17.3544C25.6664 17.3544 27.5599 15.4609 27.5599 13.1252C27.5599 10.7895 25.6664 8.89606 23.3307 8.89606C20.995 8.89606 19.1016 10.7895 19.1016 13.1252C19.1016 15.4609 20.995 17.3544 23.3307 17.3544Z" stroke="#7212BC" stroke-width="2.91667" stroke-linecap="round" stroke-linejoin="round"/> <path d="M8.75 11.6661C11.1662 11.6661 13.125 9.70738 13.125 7.29114C13.125 4.87489 11.1662 2.91614 8.75 2.91614C6.33375 2.91614 4.375 4.87489 4.375 7.29114C4.375 9.70738 6.33375 11.6661 8.75 11.6661Z" stroke="#7212BC" stroke-width="2.91667" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
        'infinity' => '<svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M11.6719 2.91736V8.75069" stroke="#7212BC" stroke-width="2.91667" stroke-linecap="round" stroke-linejoin="round"/> <path d="M23.3281 2.91736V8.75069" stroke="#7212BC" stroke-width="2.91667" stroke-linecap="round" stroke-linejoin="round"/> <path d="M27.7083 5.83264H7.29167C5.68084 5.83264 4.375 7.13848 4.375 8.74931V29.166C4.375 30.7768 5.68084 32.0826 7.29167 32.0826H27.7083C29.3192 32.0826 30.625 30.7768 30.625 29.166V8.74931C30.625 7.13848 29.3192 5.83264 27.7083 5.83264Z" stroke="#7212BC" stroke-width="2.91667" stroke-linecap="round" stroke-linejoin="round"/> <path d="M4.375 14.5826H30.625" stroke="#7212BC" stroke-width="2.91667" stroke-linecap="round" stroke-linejoin="round"/> <path d="M11.6719 20.4174H11.6856" stroke="#7212BC" stroke-width="2.91667" stroke-linecap="round" stroke-linejoin="round"/> <path d="M17.5 20.4174H17.5138" stroke="#7212BC" stroke-width="2.91667" stroke-linecap="round" stroke-linejoin="round"/> <path d="M23.3281 20.4174H23.3419" stroke="#7212BC" stroke-width="2.91667" stroke-linecap="round" stroke-linejoin="round"/> <path d="M11.6719 26.25H11.6856" stroke="#7212BC" stroke-width="2.91667" stroke-linecap="round" stroke-linejoin="round"/> <path d="M17.5 26.25H17.5138" stroke="#7212BC" stroke-width="2.91667" stroke-linecap="round" stroke-linejoin="round"/> <path d="M23.3281 26.25H23.3419" stroke="#7212BC" stroke-width="2.91667" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
        'star' => '<svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M5.62011 12.5705C5.40726 11.6117 5.43994 10.6147 5.71513 9.67186C5.99033 8.72904 6.49912 7.87097 7.19434 7.17721C7.88956 6.48344 8.74869 5.97645 9.69208 5.70323C10.6355 5.43001 11.6326 5.39941 12.5909 5.61428C13.1184 4.7893 13.8451 4.11038 14.704 3.64011C15.5629 3.16984 16.5263 2.92334 17.5055 2.92334C18.4847 2.92334 19.4482 3.16984 20.3071 3.64011C21.1659 4.11038 21.8926 4.7893 22.4201 5.61428C23.3799 5.39848 24.3788 5.42894 25.3237 5.70282C26.2685 5.9767 27.1288 6.48511 27.8245 7.18075C28.5201 7.8764 29.0285 8.73668 29.3024 9.68157C29.5763 10.6265 29.6067 11.6253 29.3909 12.5851C30.2159 13.1126 30.8948 13.8393 31.3651 14.6982C31.8354 15.557 32.0819 16.5205 32.0819 17.4997C32.0819 18.4789 31.8354 19.4423 31.3651 20.3012C30.8948 21.1601 30.2159 21.8868 29.3909 22.4143C29.6058 23.3726 29.5752 24.3698 29.302 25.3131C29.0288 26.2565 28.5218 27.1157 27.828 27.8109C27.1343 28.5061 26.2762 29.0149 25.3334 29.2901C24.3906 29.5653 23.3935 29.598 22.4347 29.3851C21.9079 30.2132 21.1806 30.8951 20.3203 31.3675C19.4599 31.8398 18.4943 32.0875 17.5128 32.0875C16.5313 32.0875 15.5657 31.8398 14.7053 31.3675C13.845 30.8951 13.1178 30.2132 12.5909 29.3851C11.6326 29.6 10.6355 29.5694 9.69208 29.2962C8.74869 29.0229 7.88956 28.5159 7.19434 27.8222C6.49912 27.1284 5.99033 26.2704 5.71513 25.3275C5.43994 24.3847 5.40726 23.3877 5.62011 22.4289C4.7888 21.9028 4.10405 21.1749 3.62956 20.3131C3.15507 19.4513 2.90625 18.4835 2.90625 17.4997C2.90625 16.5159 3.15507 15.5481 3.62956 14.6863C4.10405 13.8244 4.7888 13.0966 5.62011 12.5705Z" stroke="#7212BC" stroke-width="2.91667" stroke-linecap="round" stroke-linejoin="round"/> <path d="M21.875 13.125L13.125 21.875" stroke="#7212BC" stroke-width="2.91667" stroke-linecap="round" stroke-linejoin="round"/> <path d="M13.125 13.125H13.1392" stroke="#7212BC" stroke-width="2.91667" stroke-linecap="round" stroke-linejoin="round"/> <path d="M21.875 21.875H21.8892" stroke="#7212BC" stroke-width="2.91667" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
    ];
@endphp

<section class="loyalty-benefits section">
    <div class="container">
        <div class="loyalty-benefits__list">
            @foreach ($benefits as $benefit)
                <div class="loyalty-benefits__item{{ $benefit['main'] ? ' loyalty-benefits__item--main' : '' }}">
                    <span class="loyalty-benefits__icon">{!! $icons[$benefit['icon']] !!}</span>
                    @if ($benefit['main'])
                        <div class="loyalty-benefits__body">
                            <div class="loyalty-benefits__title">{{ $benefit['title'] }}</div>
                            <div class="loyalty-benefits__text">{!! $benefit['text'] !!}</div>
                        </div>
                    @else
                        <div class="loyalty-benefits__text">{!! $benefit['text'] !!}</div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
