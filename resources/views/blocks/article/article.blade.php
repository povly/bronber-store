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
        <x-breadcrumbs class="article-page-block__breadcrumbs" :items="[
            ['label' => __('store.nav_about'), 'url' => route('home')],
            ['label' => __('store.nav_blog'), 'url' => route('blog')],
            ['label' => __('store.article_title')],
        ]" />

        <div class="article-page-block__hero">
            <img data-src="/images/blog/hero.jpg" alt="{{ __('store.article_title') }}"
                class="article-page-block__image lazy" width="1441" height="500">
        </div>

        <div class="article-page-block__body">
            <p class="article-page-block__date">{{ __('store.article_date') }}</p>
            <h1 class="article-page-block__title">{{ __('store.article_title') }}</h1>
            <p class="article-page-block__lead">{{ __('store.article_subtitle') }}</p>
        </div>

        <div class="article-page-block__body">
            <p class="article-page-block__content">{{ __('store.article_lead') }}</p>

            <h2 class="article-page-block__heading">{{ __('store.article_heading') }}</h2>
            <p class="article-page-block__content">{{ __('store.article_body_1') }}</p>

            <div class="article-page-block__images">
                <div class="article-page-block__image-wrap">
                    <img data-src="/images/blog/1.jpg" alt=""
                        class="article-page-block__image lazy" width="710" height="710">
                </div>
                <div class="article-page-block__image-wrap">
                    <img data-src="/images/blog/2.jpg" alt=""
                        class="article-page-block__image lazy" width="710" height="710">
                </div>
            </div>

            <p class="article-page-block__content">{{ __('store.article_body_2') }}</p>

            <a href="#" class="article-page-block__btn btn btn--primary">
                {{ __('store.article_cta') }}
            </a>
        </div>

        <div class="article-page-block__related">
            <h2 class="article-page-block__related-title">{{ __('store.article_related_title') }}</h2>
            <div class="article-page-block__related-list">
                @foreach ($related as $item)
                    <article class="article-page-block__related-item">
                        <a href="{{ route('article', ['slug' => 'sample']) }}" class="article">
                            <img data-src="{{ $item['image'] }}" alt="{{ $item['title'] }}"
                                class="article__image lazy" width="464" height="650">
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
            </div>
        </div>
    </div>
</section>
