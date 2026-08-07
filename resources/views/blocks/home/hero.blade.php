@push('block-styles')
    @vite(['resources/css/blocks/home/hero/style.css'])
@endpush

<section class="home-hero">
    <div class="container">
        <x-slider
            :config="['perView' => 1, 'pagination' => true]"
            class="home-hero__root"
            viewport-class="home-hero__slider"
            track-class="home-hero__track"
            label="Промо">
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

            <x-slot:nav>
                <x-slider-pagination class="home-hero__pagination" />
            </x-slot:nav>
        </x-slider>

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
