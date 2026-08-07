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

        <div class="about__desktop">
            <x-slider
                :config="['breakpoints' => [0 => ['perView' => 2], 768 => ['perView' => 3], 1200 => ['perView' => 5]]]"
                viewport-class="about__years"
                track-class="about__years-track"
                label="История компании">
                @foreach ($timeline as $i => $item)
                    <div class="about__year-slide slider__slide"
                         :class="{ 'is-active': active === {{ $i }} }">
                        <button type="button" class="about__year"
                                :style="{ color: yearColor({{ $i }}) }"
                                @click="select({{ $i }}); $nextTick(() => scrollToReveal({{ $i }}))">
                            {{ $item['year'] }}
                        </button>
                        <span class="about__year-marker">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 0L20 10L10 20L0 10L10 0Z" fill="currentColor"/>
                            </svg>
                        </span>
                    </div>
                @endforeach
            </x-slider>

            <div class="about__separator"></div>

            <div class="about__panels">
                @foreach ($timeline as $i => $item)
                    <div class="about__panel" x-show="active === {{ $i }}" x-collapse>
                        <h2 class="about__subtitle">{{ $item['year'] }} — {{ $item['title'] }}</h2>
                        <div class="about__text">{!! $item['text'] !!}</div>
                        <div class="about__image-wrap">
                            <x-img :path="$item['image']" :alt="$item['title']" class="about__image" />
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="about__accordion">
            <div class="about__acc-separator"></div>
            @foreach ($timeline as $i => $item)
                <div class="about__acc-item" :class="{ 'is-open': accActive === {{ $i }} }">
                    <button type="button" class="about__acc-header"
                            @click="accToggle({{ $i }})"
                            :aria-expanded="accActive === {{ $i }}">
                        <span class="about__acc-title">{{ $item['year'] }} — {{ $item['title'] }}</span>
                        <span class="about__acc-chevron">
                            <svg width="18" height="24" viewBox="0 0 18 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2 12H16" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9 5V19" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </button>
                    <div class="about__acc-content" x-show="accActive === {{ $i }}" x-collapse
                         @if ($i !== 0) x-cloak @endif>
                        <div class="about__text">
                            <p>PASHA Holding invites experienced candidates to apply for the position of Audit Manager within the Group Audit Department. The Audit Manager will play a critical role in formulating and executing the Group Audit Strategy, ensuring the integrity and effectiveness of our internal audit processes.</p>
                            <p>Job description</p>
                            <ul>
                                <li>Participate in the formulation and execution of a three-year Group Audit Strategy, ensuring alignment across financial sector companies.</li>
                                <li>Establish and monitor the implementation of unified audit policies and procedures for Internal Audit Departments (IADs) within Strategic Assets.</li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                            </ul>
                            <p></p>
                            <p></p>
                            <p></p>
                        </div>
                        <div class="about__image-wrap">
                            <x-img :path="$item['image']" :alt="$item['title']" class="about__image" />
                        </div>
                    </div>
                    <div class="about__acc-separator"></div>
                </div>
            @endforeach
        </div>
    </div>
</section>
