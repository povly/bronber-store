@push('block-styles')
    @vite(['resources/css/blocks/article-page/style.css'])
@endpush

@php
    $related = [
        [
            'tag' => '#news',
            'date' => '25/12/25',
            'title' => __('store.article_title'),
            'desc' => 'Профессиональный автосервис как часть единой экосистемы бренда',
            'image' => '/images/blog/1.jpg',
        ],
        [
            'tag' => '#news',
            'date' => '18/12/25',
            'title' => 'Открытие нового магазина автозапчастей',
            'desc' => 'Более 50 000 наименований запчастей в наличии и под заказ',
            'image' => '/images/blog/2.jpg',
        ],
        [
            'tag' => '#news',
            'date' => '10/12/25',
            'title' => 'Новое партнерство с ведущими производителями',
            'desc' => 'Теперь в ассортименте ещё больше оригинальных деталей',
            'image' => '/images/blog/3.jpg',
        ],
    ];
@endphp

<section class="article-page-block">
    <div class="container">
        <div class="article-page-block__hero img--full">
            <x-img path="/images/blog/hero-mb.jpg" alt="{{ __('store.article_title') }}"
                class="article-page-block__image" :lazy="false" width="330" height="225" />
            <x-img path="/images/blog/hero.jpg" alt="{{ __('store.article_title') }}"
                class="article-page-block__image" :lazy="false" width="718" height="308" />
        </div>

        <div class="article-page-block__body"> 
            <p class="article-page-block__date">{{ __('store.article_date') }}</p>
            <h1 class="article-page-block__title">{{ __('store.article_title') }}</h1>
        </div>

        <div class="article-page-block__content">
            <p>{{ __('store.article_lead') }}</p>

            <h2>{{ __('store.article_heading') }}</h2>
            <p>Bronber Auto Service — это профессиональный автосервис, созданный с учетом стандартов качества, сервиса и подхода, которые лежат в основе всех направлений бренда. Мы объединяем техническую экспертизу, современное оборудование и внимание к деталям, чтобы обеспечить высокий уровень обслуживания на каждом этапе работы с автомобилем.</p>
            <p>Bronber Auto Service — это не стандартный автосервис в привычном понимании. В основе нового направления лежит системный подход, внимание к деталям и стремление создать сервисную среду, соответствующую ожиданиям владельцев автомобилей премиального сегмента.</p>
            <p>Каждый этап работы — от диагностики до финальной передачи автомобиля клиенту — выстроен с учетом стандартов качества, прозрачности и комфорта.
            <br>Запуск Bronber Auto Service является важным шагом в развитии бренда и укрепляет концепцию единой экосистемы.<br> Наша цель — создать не набор отдельных услуг, а целостную систему, в которой клиент получает высокий уровень сервиса, понятную коммуникацию и уверенность в результате.
            <br>Bronber Auto Service органично дополняет существующие направления и расширяет возможности бренда, сохраняя единый стиль, ценности и подход.</p>
            <h2>Новый этап развития экосистемы Bronber</h2>
            <p>Bronber Auto Service — это профессиональный автосервис, созданный с учетом стандартов качества, сервиса и подхода, которые лежат в основе всех направлений бренда. Мы объединяем техническую экспертизу, современное оборудование и внимание к деталям, чтобы обеспечить высокий уровень обслуживания на каждом этапе работы с автомобилем.</p>
            <p>Bronber Auto Service — это не стандартный автосервис в привычном понимании. В основе нового направления лежит системный подход, внимание к деталям и стремление создать сервисную среду, соответствующую ожиданиям владельцев автомобилей премиального сегмента.</p>
            <p>Каждый этап работы — от диагностики до финальной передачи автомобиля клиенту — выстроен с учетом стандартов качества, прозрачности и комфорта.
            <br>Запуск Bronber Auto Service является важным шагом в развитии бренда и укрепляет концепцию единой экосистемы.<br> Наша цель — создать не набор отдельных услуг, а целостную систему, в которой клиент получает высокий уровень сервиса, понятную коммуникацию и уверенность в результате.
            <br>Bronber Auto Service органично дополняет существующие направления и расширяет возможности бренда, сохраняя единый стиль, ценности и подход.</p>
        

            <x-btn href="#" variant="primary" class="article-page-block__btn btn btn--primary" :text="__('store.article_cta')" />
            
        </div>

        <x-slider
            :config="['breakpoints' => [0 => ['perView' => 1], 768 => ['perView' => 2]]]"
            class="article-page-block__images"
            viewport-class="article-page-block__images-slider"
            track-class="article-page-block__images-track"
            label="Галерея">
            <div class="article-page-block__image-wrap img--full slider__slide">
                <x-img path="/images/blog/1.jpg" alt=""
                    class="article-page-block__image" width="353" height="353" />
            </div>
            <div class="article-page-block__image-wrap img--full slider__slide">
                <x-img path="/images/blog/2.jpg" alt=""
                    class="article-page-block__image" width="353" height="353" />
            </div>
            <div class="article-page-block__image-wrap img--full slider__slide">
                <x-img path="/images/blog/1.jpg" alt=""
                    class="article-page-block__image" width="353" height="353" />
            </div>
            <div class="article-page-block__image-wrap img--full slider__slide">
                <x-img path="/images/blog/2.jpg" alt=""
                    class="article-page-block__image" width="353" height="353" />
            </div>

            <x-slot:nav>
                <x-slider-arrows class="article-page-block__images-arrows" />
            </x-slot:nav>
        </x-slider>

        <x-slider
            :config="['breakpoints' => [0 => ['perView' => 1], 768 => ['perView' => 2], 1200 => ['perView' => 3]]]"
            class="article-page-block__related section"
            viewport-class="article-page-block__related-slider"
            track-class="article-page-block__related-track"
            label="Похожие статьи">
            <x-slot:header>
                <div class="article-page-block__related-header section__top">
                    <h2 class="article-page-block__related-title section__title">{{ __('store.article_related_title') }}</h2>
                    <x-slider-arrows class="article-page-block__related-arrows article-page-block__related-arrows--top" />
                </div>
            </x-slot:header>

            @foreach (array_slice($related, 0, 3) as $item)
                <article class="article-page-block__related-slide slider__slide">
                    <a href="{{ route('article', ['slug' => 'sample']) }}" class="article">
                        <x-img :path="$item['image']" :alt="$item['title']"
                            class="article__image" width="464" height="650" />
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
                                <span class="article__more btn btn--white">Детальнее</span>
                            </div>
                        </div>
                    </a>
                </article>
            @endforeach

            <x-slot:nav>
                <x-slider-arrows class="article-page-block__related-arrows article-page-block__related-arrows--bottom" />
            </x-slot:nav>
        </x-slider>
    </div>
</section>
