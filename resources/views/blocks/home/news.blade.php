@push('block-styles')
    @vite(['resources/css/blocks/home/news/style.css'])
@endpush

@php
    $news = [
        [
            'tag' => '#запуск',
            'date' => '25/12/25',
            'title' => 'Запуск нового направления BRONBER Auto Service',
            'desc' => 'Открываем новое направление в сфере автосервиса. Современное оборудование, квалифицированные специалисты и широкий спектр услуг для вашего автомобиля.',
            'image' => 'https://placehold.co/464x650/2a2a2a/555555?text=BRONBER',
        ],
        [
            'tag' => '#событие',
            'date' => '18/12/25',
            'title' => 'Открытие нового магазина автозапчастей',
            'desc' => 'Рады сообщить об открытии нового магазина. Более 50 000 наименований запчастей в наличии и под заказ с быстрой доставкой.',
            'image' => 'https://placehold.co/464x650/333333/666666?text=Store',
        ],
        [
            'tag' => '#партнерство',
            'date' => '10/12/25',
            'title' => 'Новое партнерство с ведущими производителями',
            'desc' => 'Заключили соглашения с крупнейшими мировыми производителями автозапчастей. Теперь в ассортименте ещё больше оригинальных деталей.',
            'image' => 'https://placehold.co/464x650/1a1a1a/555555?text=Partners',
        ],
    ];
@endphp

<section class="home-news section" x-data="slider({ grid: { above: 1200, breakpoints: { 1200: 3 } }, breakpoints: { 0: 1, 768: 2 } })" @resize.window.debounce.150ms="onResize()">
    <div class="container">
        <div class="home-news__header section__top">
            <h2 class="home-news__title section__title">Новости</h2>
            <div class="home-news__arrows home-news__arrows--top slider__arrows">
                <button class="slider__arrow slider__arrow--prev home-news__arrow home-news__arrow--prev" type="button" aria-label="Назад" @click="prev()" :disabled="!canPrev">
                    <svg width="22" height="12" viewBox="0 0 22 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20.75 6.27295C21.1642 6.27295 21.5 5.93716 21.5 5.52295C21.5 5.10874 21.1642 4.77295 20.75 4.77295L20.75 5.52295L20.75 6.27295ZM0.219669 4.99262C-0.0732231 5.28551 -0.0732231 5.76038 0.219669 6.05328L4.99264 10.8262C5.28553 11.1191 5.76041 11.1191 6.0533 10.8262C6.34619 10.5334 6.34619 10.0585 6.0533 9.76559L1.81066 5.52295L6.0533 1.28031C6.34619 0.987414 6.34619 0.512541 6.0533 0.219647C5.76041 -0.0732464 5.28553 -0.0732464 4.99264 0.219647L0.219669 4.99262ZM20.75 5.52295L20.75 4.77295L0.75 4.77295L0.75 5.52295L0.75 6.27295L20.75 6.27295L20.75 5.52295Z" fill="#080808" />
                    </svg>
                </button>
                <button class="slider__arrow slider__arrow--next home-news__arrow home-news__arrow--next" type="button" aria-label="Вперёд" @click="next()" :disabled="!canNext">
                    <svg width="22" height="12" viewBox="0 0 22 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.75 4.77295C0.335786 4.77295 0 5.10874 0 5.52295C0 5.93716 0.335786 6.27295 0.75 6.27295V5.52295V4.77295ZM21.2803 6.05328C21.5732 5.76039 21.5732 5.28551 21.2803 4.99262L16.5074 0.219648C16.2145 -0.073245 15.7396 -0.073245 15.4467 0.219648C15.1538 0.512542 15.1538 0.987415 15.4467 1.28031L19.6893 5.52295L15.4467 9.76559C15.1538 10.0585 15.1538 10.5334 15.4467 10.8263C15.7396 11.1191 16.2145 11.1191 16.5074 10.8263L21.2803 6.05328ZM0.75 5.52295V6.27295H20.75V5.52295V4.77295H0.75V5.52295Z" fill="#030303" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="home-news__slider slider">
            <div class="home-news__track slider__track" x-ref="track"
                 @pointerdown.prevent="onPointerDown($event)"
                 @pointermove.window="onPointerMove($event)"
                 @pointerup.window="onPointerUp()"
                 @pointercancel.window="onPointerUp()">

                @foreach($news as $item)
                    <article class="home-news__slide slider__slide">
                        <a href="#" class="home-news__card">
                            <img data-src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="home-news__card-image lazy" width="464" height="650">
                            <span class="home-news__card-shade home-news__card-shade--top"></span>
                            <span class="home-news__card-shade home-news__card-shade--bottom"></span>
                            <div class="home-news__card-top">
                                <span class="home-news__card-date">{{ $item['date'] }}</span>
                                <span class="home-news__card-tag">{{ $item['tag'] }}</span>
                            </div>
                            <div class="home-news__card-body">
                                <h3 class="home-news__card-title">{{ $item['title'] }}</h3>
                                <p class="home-news__card-desc">{{ $item['desc'] }}</p>
                                <span class="home-news__card-more">
                                    Детальнее
                                    <svg class="home-news__card-more-icon" width="14" height="14" viewBox="0 0 16 16" fill="none">
                                        <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>

        <div class="home-news__footer">
            <x-btn href="/news" variant="primary" class="home-news__all-btn" text="Все новости" />
            <div class="home-news__arrows home-news__arrows--bottom slider__arrows">
                <button class="slider__arrow slider__arrow--prev home-news__arrow home-news__arrow--prev" type="button" aria-label="Назад" @click="prev()" :disabled="!canPrev">
                    <svg width="22" height="12" viewBox="0 0 22 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20.75 6.27295C21.1642 6.27295 21.5 5.93716 21.5 5.52295C21.5 5.10874 21.1642 4.77295 20.75 4.77295L20.75 5.52295L20.75 6.27295ZM0.219669 4.99262C-0.0732231 5.28551 -0.0732231 5.76038 0.219669 6.05328L4.99264 10.8262C5.28553 11.1191 5.76041 11.1191 6.0533 10.8262C6.34619 10.5334 6.34619 10.0585 6.0533 9.76559L1.81066 5.52295L6.0533 1.28031C6.34619 0.987414 6.34619 0.512541 6.0533 0.219647C5.76041 -0.0732464 5.28553 -0.0732464 4.99264 0.219647L0.219669 4.99262ZM20.75 5.52295L20.75 4.77295L0.75 4.77295L0.75 5.52295L0.75 6.27295L20.75 6.27295L20.75 5.52295Z" fill="#080808" />
                    </svg>
                </button>
                <button class="slider__arrow slider__arrow--next home-news__arrow home-news__arrow--next" type="button" aria-label="Вперёд" @click="next()" :disabled="!canNext">
                    <svg width="22" height="12" viewBox="0 0 22 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.75 4.77295C0.335786 4.77295 0 5.10874 0 5.52295C0 5.93716 0.335786 6.27295 0.75 6.27295V5.52295V4.77295ZM21.2803 6.05328C21.5732 5.76039 21.5732 5.28551 21.2803 4.99262L16.5074 0.219648C16.2145 -0.073245 15.7396 -0.073245 15.4467 0.219648C15.1538 0.512542 15.1538 0.987415 15.4467 1.28031L19.6893 5.52295L15.4467 9.76559C15.1538 10.0585 15.1538 10.5334 15.4467 10.8263C15.7396 11.1191 16.2145 11.1191 16.5074 10.8263L21.2803 6.05328ZM0.75 5.52295V6.27295H20.75V5.52295V4.77295H0.75V5.52295Z" fill="#030303" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</section>
