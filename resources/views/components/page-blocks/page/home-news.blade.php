@push('block-styles')
    @once
        @vite(['resources/css/blocks/home/news/style.css'])
    @endonce
@endpush

@php
    // MediaManagerPicker stores disk-relative paths; absolute/URL paths pass through.
    $items = collect($block['items'] ?? [])
        ->filter(static fn ($item): bool => is_array($item))
        ->map(static function (array $item): array {
            $image = $item['image'] ?? null;

            if (! empty($image)) {
                $item['image'] = str_starts_with((string) $image, '/') || str_starts_with((string) $image, 'http')
                    ? (string) $image
                    : '/storage/'.ltrim((string) $image, '/');
            }

            return $item;
        })
        ->values();
    $title = $block['title'] ?? 'Новости';
@endphp

@if($items->isNotEmpty())
    <section class="home-news section">
        <div class="container">
            <x-slider
                :config="['breakpoints' => [0 => ['perView' => 1], 768 => ['perView' => 2], 1200 => ['grid' => ['cols' => 3]]]]"
                class="home-news__root"
                viewport-class="home-news__slider"
                track-class="home-news__track"
                label="{{ $title }}">
                <x-slot:header>
                    <div class="home-news__header section__top">
                        <h2 class="home-news__title section__title">{{ $title }}</h2>
                        <x-slider-arrows class="home-news__arrows home-news__arrows--top" />
                    </div>
                </x-slot:header>

                @foreach($items as $item)
                    <article class="home-news__slide slider__slide">
                        <a href="{{ route('article', 'sample') }}" class="article">
                            @if(!empty($item['image']))
                                <x-img :path="$item['image']" :alt="$item['title'] ?? ''" class="article__image"
                                    width="464" height="650" />
                            @endif
                            <span class="article__shade article__shade--top"></span>
                            <span class="article__shade article__shade--bottom"></span>
                            <div class="article__top">
                                @if(!empty($item['tag']))
                                    <span class="article__tag">{{ $item['tag'] }}</span>
                                @endif
                                @if(!empty($item['date']))
                                    <span class="article__date">{{ $item['date'] }}</span>
                                @endif
                            </div>
                            <div class="article__body">
                                @if(!empty($item['title']))
                                    <h3 class="article__title">{{ $item['title'] }}</h3>
                                @endif
                                @if(!empty($item['desc']))
                                    <p class="article__desc">{{ $item['desc'] }}</p>
                                @endif
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
                        <x-btn href="{{ route('blog') }}" variant="primary" class="home-news__all-btn btn btn--primary" text="Все новости" />
                        <x-slider-arrows class="home-news__arrows home-news__arrows--bottom" />
                    </div>
                </x-slot:nav>
            </x-slider>
        </div>
    </section>
@endif
