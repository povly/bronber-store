@push('block-styles')
    @vite(['resources/css/blocks/article-page/style.css'])
@endpush

@php
    $article = [
        'title' => 'Запуск нового направления Bronber Auto Service',
        'date' => '09/02/2026',
        'lead' => "Bronber объявляет о запуске нового направления — Bronber Auto Service. Это логичный этап развития бренда и важная часть формирования единой экосистемы Bronber, ориентированной на комплексное обслуживание автомобилей премиум-класса.",
        'sections' => [
            [
                'heading' => 'Новый этап развития экосистемы Bronber',
                'body' => "Bronber Auto Service — это профессиональный автосервис, созданный с учетом стандартов качества, сервиса и подхода, которые лежат в основе всех направлений бренда. Мы объединяем техническую экспертизу, современное оборудование и внимание к деталям, чтобы обеспечить высокий уровень обслуживания на каждом этапе работы с автомобилем.\n\nBronber Auto Service — это не стандартный автосервис в привычном понимании. В основе нового направления лежит системный подход, внимание к деталям и стремление создать сервисную среду, соответствующую ожиданиям владельцев автомобилей премиального сегмента.\n\nКаждый этап работы — от диагностики до финальной передачи автомобиля клиенту — выстроен с учетом стандартов качества, прозрачности и комфорта.\nЗапуск Bronber Auto Service является важным шагом в развитии бренда и укрепляет концепцию единой экосистемы. Наша цель — создать не набор отдельных услуг, а целостную систему, в которой клиент получает высокий уровень сервиса, понятную коммуникацию и уверенность в результате.\nBronber Auto Service органично дополняет существующие направления и расширяет возможности бренда, сохраняя единый стиль, ценности и подход.",
            ],
        ],
    ];

    $related = [
        [
            'tag' => '#запуск',
            'date' => '25/12/25',
            'title' => 'Запуск нового направления BRONBER Auto Service',
            'desc' => 'Профессиональный автосервис как часть единой экосистемы бренда',
            'image' => '/images/blog/1.jpg',
        ],
        [
            'tag' => '#событие',
            'date' => '18/12/25',
            'title' => 'Открытие нового магазина автозапчастей',
            'desc' => 'Более 50 000 наименований запчастей в наличии и под заказ',
            'image' => '/images/blog/2.jpg',
        ],
        [
            'tag' => '#партнерство',
            'date' => '10/12/25',
            'title' => 'Новое партнерство с ведущими производителями',
            'desc' => 'Теперь в ассортименте ещё больше оригинальных деталей',
            'image' => '/images/blog/3.jpg',
        ],
    ];
@endphp

<section class="article-page-block">
    <div class="container">
        <x-breadcrumbs class="article-page-block__breadcrumbs" :items="[
            ['label' => 'Главная', 'url' => route('home')],
            ['label' => 'Блог', 'url' => route('blog')],
            ['label' => $article['title']],
        ]" />

        <div class="article-page-block__hero">
            <img src="/images/blog/1.jpg" alt="{{ $article['title'] }}" class="article-page-block__image lazy" loading="lazy">
        </div>

        <div class="article-page-block__body">
            <p class="article-page-block__date">{{ $article['date'] }}</p>
            <h1 class="article-page-block__title">{{ $article['title'] }}</h1>
            <p class="article-page-block__lead">{{ $article['lead'] }}</p>

            @foreach ($article['sections'] as $section)
                <h2 class="article-page-block__heading">{{ $section['heading'] }}</h2>
                <div class="article-page-block__content">{!! nl2br(e($section['body'])) !!}</div>
            @endforeach

            <a href="#" class="article-page-block__btn btn btn--primary">
                Перейти к направлению
            </a>
        </div>

        <div class="article-page-block__related">
            <h2 class="article-page-block__related-title">Другие новости</h2>
            <div class="article-page-block__related-list">
                @foreach ($related as $item)
                    <article class="article-page-block__related-item">
                        <a href="{{ route('article', ['slug' => 'sample']) }}" class="article">
                            <img data-src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="article__image lazy" width="464" height="650">
                            <span class="article__shade article__shade--top"></span>
                            <span class="article__shade article__shade--bottom"></span>
                            <div class="article__top">
                                <span class="article__tag">{{ $item['tag'] }}</span>
                                <span class="article__date">{{ $item['date'] }}</span>
                            </div>
                            <div class="article__body">
                                <h3 class="article__title">{{ $item['title'] }}</h3>
                                <p class="article__desc">{{ $item['desc'] }}</p>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>
