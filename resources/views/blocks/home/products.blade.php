@props(['title' => 'Рекомендованные товары'])

<section class="home-products section" x-data="slider({ grid: { below: 1200, breakpoints: { 0: { cols: 2, rows: 2 } } }, breakpoints: { 1200: 4 }, pagination: true })" @resize.window.debounce.150ms="onResize()">
    <div class="container">
        <div class="section__top home-products__header">
            <h2 class="home-products__title section__title">{{ $title }}</h2>
            <div class="home-products__nav">
                <div class="home-products__arrows home-products__arrows--pc slider__arrows--pc slider__arrows">
                    <button class="slider__arrow slider__arrow--prev home-products__arrow home-products__arrow--prev" type="button" aria-label="Назад" @click="prev()" :disabled="!canPrev">
                        <svg width="22" height="12" viewBox="0 0 22 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20.75 6.27295C21.1642 6.27295 21.5 5.93716 21.5 5.52295C21.5 5.10874 21.1642 4.77295 20.75 4.77295L20.75 5.52295L20.75 6.27295ZM0.219669 4.99262C-0.0732231 5.28551 -0.0732231 5.76038 0.219669 6.05328L4.99264 10.8262C5.28553 11.1191 5.76041 11.1191 6.0533 10.8262C6.34619 10.5334 6.34619 10.0585 6.0533 9.76559L1.81066 5.52295L6.0533 1.28031C6.34619 0.987414 6.34619 0.512541 6.0533 0.219647C5.76041 -0.0732464 5.28553 -0.0732464 4.99264 0.219647L0.219669 4.99262ZM20.75 5.52295L20.75 4.77295L0.75 4.77295L0.75 5.52295L0.75 6.27295L20.75 6.27295L20.75 5.52295Z" fill="#080808" />
                        </svg>
                    </button>
                    <button class="slider__arrow slider__arrow--next home-products__arrow home-products__arrow--next" type="button" aria-label="Вперёд" @click="next()" :disabled="!canNext">
                        <svg width="22" height="12" viewBox="0 0 22 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.75 4.77295C0.335786 4.77295 0 5.10874 0 5.52295C0 5.93716 0.335786 6.27295 0.75 6.27295V5.52295V4.77295ZM21.2803 6.05328C21.5732 5.76039 21.5732 5.28551 21.2803 4.99262L16.5074 0.219648C16.2145 -0.073245 15.7396 -0.073245 15.4467 0.219648C15.1538 0.512542 15.1538 0.987415 15.4467 1.28031L19.6893 5.52295L15.4467 9.76559C15.1538 10.0585 15.1538 10.5334 15.4467 10.8263C15.7396 11.1191 16.2145 11.1191 16.5074 10.8263L21.2803 6.05328ZM0.75 5.52295V6.27295H20.75V5.52295V4.77295H0.75V5.52295Z" fill="#030303" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="home-products__slider slider">
            <div class="home-products__track slider__track" x-ref="track"
                 @pointerdown.prevent="onPointerDown($event)"
                 @pointermove.window="onPointerMove($event)"
                 @pointerup.window="onPointerUp()"
                 @pointercancel.window="onPointerUp()">

                <div class="home-products__slide slider__slide">
                    <x-product-card
                        title="DeatschWerks 9-651-1008 Насос топливный DW65C 265л/ч для Honda"
                        image="/images/home/products/1/1.png"
                        :rating="4"
                        reviews-count="12"
                        price="1100 ₽"
                        sale="ТОП"
                    />
                </div>
                <div class="home-products__slide slider__slide">
                    <x-product-card
                        title="DeatschWerks 9-651-1008 Насос топливный DW65C 265л/ч для Honda"
                        image="/images/home/products/1/2.png"
                        :rating="5"
                        reviews-count="8"
                        price="1100 ₽"
                        old-price="1300 ₽"
                        sale="-15%"
                    />
                </div>
                <div class="home-products__slide slider__slide">
                    <x-product-card
                        title="DeatschWerks 9-651-1008 Насос топливный DW65C 265л/ч для Honda"
                        image="/images/home/products/1/3.png"
                        :rating="0"
                        reviews-count="0"
                        price="1100 ₽"
                        old-price="1300 ₽"
                    />
                </div>
                <div class="home-products__slide slider__slide">
                    <x-product-card
                        title="DeatschWerks 9-651-1008 Насос топливный DW65C 265л/ч для Honda"
                        image="/images/home/products/1/4.png"
                        :rating="0"
                        reviews-count="0"
                        price="1100 ₽"
                        sale="Распродажа"
                        :in-stock="false"
                    />
                </div>

                <div class="home-products__slide slider__slide">
                    <x-product-card
                        title="DeatschWerks 9-651-1008 Насос топливный DW65C 265л/ч для Honda"
                        image="/images/home/products/1/1.png"
                        :rating="4"
                        reviews-count="12"
                        price="1100 ₽"
                        sale="ТОП"
                    />
                </div>
                <div class="home-products__slide slider__slide">
                    <x-product-card
                        title="DeatschWerks 9-651-1008 Насос топливный DW65C 265л/ч для Honda"
                        image="/images/home/products/1/2.png"
                        :rating="5"
                        reviews-count="8"
                        price="1100 ₽"
                        old-price="1300 ₽"
                        sale="-15%"
                    />
                </div>
                <div class="home-products__slide slider__slide">
                    <x-product-card
                        title="DeatschWerks 9-651-1008 Насос топливный DW65C 265л/ч для Honda"
                        image="/images/home/products/1/3.png"
                        :rating="0"
                        reviews-count="0"
                        price="1100 ₽"
                        old-price="1300 ₽"
                    />
                </div>

                <div class="home-products__slide slider__slide">
                    <x-product-card
                        title="DeatschWerks 9-651-1008 Насос топливный DW65C 265л/ч для Honda"
                        image="/images/home/products/1/3.png"
                        :rating="0"
                        reviews-count="0"
                        price="1100 ₽"
                        old-price="1300 ₽"
                    />
                </div>
            </div>
        </div>

        <div class="home-products__arrows home-products__arrows--mb slider__arrows--mb slider__arrows">
            <button class="slider__arrow slider__arrow--prev home-products__arrow home-products__arrow--prev" type="button" aria-label="Назад" @click="prev()" :disabled="!canPrev">
                <svg width="22" height="12" viewBox="0 0 22 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20.75 6.27295C21.1642 6.27295 21.5 5.93716 21.5 5.52295C21.5 5.10874 21.1642 4.77295 20.75 4.77295L20.75 5.52295L20.75 6.27295ZM0.219669 4.99262C-0.0732231 5.28551 -0.0732231 5.76038 0.219669 6.05328L4.99264 10.8262C5.28553 11.1191 5.76041 11.1191 6.0533 10.8262C6.34619 10.5334 6.34619 10.0585 6.0533 9.76559L1.81066 5.52295L6.0533 1.28031C6.34619 0.987414 6.34619 0.512541 6.0533 0.219647C5.76041 -0.0732464 5.28553 -0.0732464 4.99264 0.219647L0.219669 4.99262ZM20.75 5.52295L20.75 4.77295L0.75 4.77295L0.75 5.52295L0.75 6.27295L20.75 6.27295L20.75 5.52295Z" fill="#080808" />
                </svg>
            </button>
            <button class="slider__arrow slider__arrow--next home-products__arrow home-products__arrow--next" type="button" aria-label="Вперёд" @click="next()" :disabled="!canNext">
                <svg width="22" height="12" viewBox="0 0 22 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0.75 4.77295C0.335786 4.77295 0 5.10874 0 5.52295C0 5.93716 0.335786 6.27295 0.75 6.27295V5.52295V4.77295ZM21.2803 6.05328C21.5732 5.76039 21.5732 5.28551 21.2803 4.99262L16.5074 0.219648C16.2145 -0.073245 15.7396 -0.073245 15.4467 0.219648C15.1538 0.512542 15.1538 0.987415 15.4467 1.28031L19.6893 5.52295L15.4467 9.76559C15.1538 10.0585 15.1538 10.5334 15.4467 10.8263C15.7396 11.1191 16.2145 11.1191 16.5074 10.8263L21.2803 6.05328ZM0.75 5.52295V6.27295H20.75V5.52295V4.77295H0.75V5.52295Z" fill="#030303" />
                </svg>
            </button>
        </div>
    </div>
</section>
