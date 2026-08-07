@push('block-styles')
    @vite(['resources/css/blocks/home/news/style.css'])
@endpush

@php
    $news = [
        [
            'tag' => '#запуск',
            'date' => '25/12/25',
            'title' => 'Запуск нового направления BRONBER Auto Service',
            'desc' =>
                'Открываем новое направление в сфере автосервиса. Современное оборудование, квалифицированные специалисты и широкий спектр услуг для вашего автомобиля.',
            'image' => '/images/blog/1.jpg',
        ],
        [
            'tag' => '#событие',
            'date' => '18/12/25',
            'title' => 'Открытие нового магазина автозапчастей',
            'desc' =>
                'Рады сообщить об открытии нового магазина. Более 50 000 наименований запчастей в наличии и под заказ с быстрой доставкой.',
            'image' => '/images/blog/2.jpg',
        ],
        [
            'tag' => '#партнерство',
            'date' => '10/12/25',
            'title' => 'Новое партнерство с ведущими производителями',
            'desc' =>
                'Заключили соглашения с крупнейшими мировыми производителями автозапчастей. Теперь в ассортименте ещё больше оригинальных деталей.',
            'image' => '/images/blog/3.jpg',
        ],
        [
            'tag' => '#запуск',
            'date' => '25/12/25',
            'title' => 'Запуск нового направления BRONBER Auto Service',
            'desc' =>
                'Открываем новое направление в сфере автосервиса. Современное оборудование, квалифицированные специалисты и широкий спектр услуг для вашего автомобиля.',
            'image' => '/images/blog/1.jpg',
        ],
        [
            'tag' => '#событие',
            'date' => '18/12/25',
            'title' => 'Открытие нового магазина автозапчастей',
            'desc' =>
                'Рады сообщить об открытии нового магазина. Более 50 000 наименований запчастей в наличии и под заказ с быстрой доставкой.',
            'image' => '/images/blog/2.jpg',
        ],
        [
            'tag' => '#партнерство',
            'date' => '10/12/25',
            'title' => 'Новое партнерство с ведущими производителями',
            'desc' =>
                'Заключили соглашения с крупнейшими мировыми производителями автозапчастей. Теперь в ассортименте ещё больше оригинальных деталей.',
            'image' => '/images/blog/3.jpg',
        ],
    ];
@endphp

<section class="home-news section">
    <div class="container">
        <x-slider
            :config="['breakpoints' => [0 => ['perView' => 1], 768 => ['perView' => 2], 1200 => ['grid' => ['cols' => 3]]]]"
            class="home-news__root"
            viewport-class="home-news__slider"
            track-class="home-news__track"
            label="Новости">
            <x-slot:header>
                <div class="home-news__header section__top">
                    <h2 class="home-news__title section__title">Новости</h2>
                    <x-slider-arrows class="home-news__arrows home-news__arrows--top" />
                </div>
            </x-slot:header>

            @foreach ($news as $item)
                <article class="home-news__slide slider__slide">
                    <a href="/blog/sample" class="article">
                        <x-img :path="$item['image']" :alt="$item['title']" class="article__image"
                            width="464" height="650" />
                        <span class="article__shade article__shade--top"></span>
                        <span class="article__shade article__shade--bottom"></span>
                        <div class="article__top">
                            <span class="article__tag">{{ $item['tag'] }}</span>
                            <span class="article__date">{{ $item['date'] }}</span>
                        </div>
                        <div class="article__body">
                            <h3 class="article__title">{{ $item['title'] }}</h3>
                            <p class="article__desc">{{ $item['desc'] }}</p>
                            <div class="article__reveal">
                                <span class="article__more btn btn--white">
                                    Детальнее
                                </span>
                            </div>
                        </div>
                    </a>
                </article>
            @endforeach

            <x-slot:nav>
                <div class="home-news__footer">
                    <x-btn href="/blog" variant="primary" class="home-news__all-btn btn btn--primary" text="Все новости" />
                    <x-slider-arrows class="home-news__arrows home-news__arrows--bottom" />
                </div>
            </x-slot:nav>
        </x-slider>
    </div>
</section>
