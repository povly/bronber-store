@push('block-styles')
    @vite(['resources/css/blocks/about/style.css'])
@endpush

@push('block-scripts')
    @vite(['resources/js/blocks/about/index.js'])
@endpush

@php
    $timeline = [
        [
            'year' => '2017',
            'title' => __('about.year_2017_title'),
            'text' => __('about.year_2017_text'),
            'image' => '/images/blog/1.jpg',
        ],
        [
            'year' => '2018',
            'title' => __('about.year_2018_title'),
            'text' => __('about.year_2018_text'),
            'image' => '/images/blog/2.jpg',
        ],
        [
            'year' => '2019',
            'title' => __('about.year_2019_title'),
            'text' => __('about.year_2019_text'),
            'image' => '/images/blog/3.jpg',
        ],
        [
            'year' => '2020',
            'title' => __('about.year_2020_title'),
            'text' => __('about.year_2020_text'),
            'image' => '/images/blog/1.jpg',
        ],
        [
            'year' => '2021',
            'title' => __('about.year_2021_title'),
            'text' => __('about.year_2021_text'),
            'image' => '/images/blog/2.jpg',
        ],
    ];
@endphp

<section class="about" x-data="about()">
    <div class="container">
        <h1 class="about__title">{{ __('about.title') }}</h1>

        <div class="about__years slider"
             x-data="slider({ breakpoints: { 0: { perView: 2 }, 768: { perView: 3 }, 1200: { perView: 5 } } })"
             x-init="$watch('active', v => ensureVisible(v))"
             @resize.window.debounce.150ms="onResize()">
            <div class="about__years-track slider__track" x-ref="track"
                 @pointerdown.prevent="onPointerDown($event)"
                 @pointermove.window="onPointerMove($event)"
                 @pointerup.window="onPointerUp()"
                 @pointercancel.window="onPointerUp()"
                 @click.capture="suppressDragClick($event)">

                @foreach ($timeline as $i => $item)
                    <div class="about__year-slide slider__slide"
                         :class="{ 'is-active': active === {{ $i }} }">
                        <button type="button" class="about__year"
                                :style="{ color: yearColor({{ $i }}) }"
                                @click="select({{ $i }})">
                            {{ $item['year'] }}
                        </button>
                        <span class="about__year-marker">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 0L20 10L10 20L0 10L10 0Z" fill="currentColor"/>
                            </svg>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="about__separator"></div>

        <div class="about__panels">
            @foreach ($timeline as $i => $item)
                <div class="about__panel" x-show="active === {{ $i }}" x-collapse>
                    <h2 class="about__subtitle">{{ $item['year'] }} — {{ $item['title'] }}</h2>
                    <div class="about__text">{!! nl2br(e($item['text'])) !!}</div>
                    <div class="about__image-wrap">
                        <img data-src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="about__image lazy">
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
