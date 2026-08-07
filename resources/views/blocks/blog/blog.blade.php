@push('block-styles')
    @vite(['resources/css/blocks/blog/style.css'])
@endpush

@push('block-scripts')
    @vite(['resources/js/blocks/blog/index.js'])
@endpush

@php
    $news = [
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

<section class="blog" x-data="blog()">
    <div class="container">
        <h1 class="blog__title">Новости</h1>

        <div class="blog__list" x-ref="list">
            @foreach($news as $item)
                <article class="blog__item" data-article>
                    <a href="#" class="article">
                        <x-img :path="$item['image']" :alt="$item['title']" :lazy="false" class="article__image" width="464" height="650" />
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
        </div>

        <div class="blog__more" x-show="hasMore" x-cloak>
            <button type="button" class="blog__more-btn btn btn--primary" @click="showMore()">
                Показать больше
            </button>
        </div>
    </div>
</section>
