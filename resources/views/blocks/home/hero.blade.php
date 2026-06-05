@push('block-styles')
    @vite(['resources/css/blocks/home/hero/style.css'])
@endpush

<section class="home-hero" x-data="slider({ breakpoints: { 0: 1 }, pagination: true })" @resize.window.debounce.150ms="onResize()">
    <div class="container">
        <div class="home-hero__slider slider">
            <div class="home-hero__track slider__track" x-ref="track"
                 @pointerdown.prevent="onPointerDown($event)"
                 @pointermove.window="onPointerMove($event)"
                 @pointerup.window="onPointerUp()"
                 @pointercancel.window="onPointerUp()">
                <div class="home-hero__slide slider__slide">
                    <div class="home-hero__title">Найдите нужные товары по категориям</div>
                    <div class="home-hero__text">Удобная структура каталога поможет быстро перейти к&nbsp;нужному разделу и&nbsp;выбрать подходящий товар</div>
                    <x-btn href="/catalog" class="home-hero__btn" text="В каталог" />
                </div>
                <div class="home-hero__slide slider__slide">
                    <div class="home-hero__title">Найдите нужные товары</div>
                    <div class="home-hero__text">Удобная структура каталога поможет быстро перейти к&nbsp;нужному разделу</div>
                    <x-btn href="/catalog" class="home-hero__btn" text="В каталог" />
                </div>
                <div class="home-hero__slide slider__slide">
                    <div class="home-hero__title">Найдите нужные товары</div>
                    <div class="home-hero__text">Удобная структура каталога поможет быстро перейти к&nbsp;нужному разделу</div>
                    <x-btn href="/catalog" class="home-hero__btn" text="В каталог" />
                </div>
            </div>

            <x-slider-pagination />
        </div>

        <div class="home-hero__pc">
            <div class="home-hero__slide home-hero__slide--1 slider__slide">
                <div class="home-hero__title">Найдите нужные товары по категориям</div>
                <div class="home-hero__text">Удобная структура каталога поможет быстро перейти к&nbsp;нужному разделу и&nbsp;выбрать подходящий товар</div>
                <x-btn href="/catalog" class="home-hero__btn" text="В каталог" />
            </div>
            <div class="home-hero__slide slider__slide home-hero__slide--2">
                <div class="home-hero__title">Найдите нужные товары</div>
                <div class="home-hero__text">Удобная структура каталога поможет быстро перейти к&nbsp;нужному разделу</div>
                <x-btn href="/catalog" class="home-hero__btn" text="В каталог" />
            </div>
            <div class="home-hero__slide slider__slide home-hero__slide--3">
                <div class="home-hero__title">Найдите нужные товары</div>
                <div class="home-hero__text">Удобная структура каталога поможет быстро перейти к&nbsp;нужному разделу</div>
                <x-btn href="/catalog" class="home-hero__btn" text="В каталог" />
            </div>
        </div>
    </div>
</section>
