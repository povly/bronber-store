@push('block-styles')
    @vite(['resources/css/blocks/home/hero/style.css'])
@endpush

@php
    $slides = collect($block['slides'] ?? [])
        ->filter(static fn ($slide): bool => is_array($slide))
        ->values();
@endphp

@if($slides->isNotEmpty())
    <section class="home-hero">
        <div class="container">
            <x-slider
                :config="['perView' => 1, 'pagination' => true]"
                class="home-hero__root"
                viewport-class="home-hero__slider"
                track-class="home-hero__track"
                label="Промо">
                @foreach($slides as $slide)
                    <div class="home-hero__slide slider__slide">
                        @if(!empty($slide['title']))
                            <div class="home-hero__title">{{ $slide['title'] }}</div>
                        @endif
                        @if(!empty($slide['text']))
                            <div class="home-hero__text">{{ $slide['text'] }}</div>
                        @endif
                        @if(!empty($slide['btn_text']))
                            <x-btn href="{{ $slide['btn_href'] ?? route('catalog') }}" class="home-hero__btn" text="{{ $slide['btn_text'] }}" />
                        @endif
                    </div>
                @endforeach

                <x-slot:nav>
                    <x-slider-pagination class="home-hero__pagination" />
                </x-slot:nav>
            </x-slider>

            <div class="home-hero__pc">
                @foreach($slides as $slide)
                    <div class="home-hero__slide home-hero__slide--{{ $loop->iteration }} slider__slide">
                        @if(!empty($slide['title']))
                            <div class="home-hero__title">{{ $slide['title'] }}</div>
                        @endif
                        @if(!empty($slide['text']))
                            <div class="home-hero__text">{{ $slide['text'] }}</div>
                        @endif
                        @if(!empty($slide['btn_text']))
                            <x-btn href="{{ $slide['btn_href'] ?? route('catalog') }}" class="home-hero__btn" text="{{ $slide['btn_text'] }}" />
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
