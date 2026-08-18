@props(['title' => 'Рекомендованные товары'])

<section class="home-products section">
    <div class="container">
        <x-slider
            :config="['loop' => true, 'freeMode' => true, 'breakpoints' => [0 => ['grid' => ['cols' => 2, 'rows' => 2]], 1200 => ['perView' => 4]]]"
            class="home-products__root"
            viewport-class="home-products__slider"
            track-class="home-products__track"
            label="{{ $title }}">
            <x-slot:header>
                <div class="section__top home-products__header">
                    <h2 class="home-products__title section__title">{{ $title }}</h2>
                    <div class="home-products__nav">
                        <x-slider-arrows class="home-products__arrows home-products__arrows--pc slider__arrows--pc" />
                    </div>
                </div>
            </x-slot:header>

            <div class="home-products__slide slider__slide">
                <x-product-card :href="route('product')" article="DW-651-1008" title="DeatschWerks 9-651-1008 Насос топливный DW65C 265л/ч для Honda"
                    image="/images/home/products/1/1.png" :rating="4" reviews-count="12" price="1100 ₽"
                    sale="ТОП" />
            </div>
            <div class="home-products__slide slider__slide">
                <x-product-card :href="route('product')" article="DW-651-1008" title="DeatschWerks 9-651-1008 Насос топливный DW65C 265л/ч для Honda"
                    image="/images/home/products/1/2.png" :rating="5" reviews-count="8" price="1100 ₽"
                    old-price="1300 ₽" sale="-15%" />
            </div>
            <div class="home-products__slide slider__slide">
                <x-product-card :href="route('product')" article="DW-651-1008" title="DeatschWerks 9-651-1008 Насос топливный DW65C 265л/ч для Honda"
                    image="/images/home/products/1/3.png" :rating="0" reviews-count="0" price="1100 ₽"
                    old-price="1300 ₽" />
            </div>
            <div class="home-products__slide slider__slide">
                <x-product-card :href="route('product')" article="DW-651-1008" title="DeatschWerks 9-651-1008 Насос топливный DW65C 265л/ч для Honda"
                    image="/images/home/products/1/4.png" :rating="0" reviews-count="0" price="1100 ₽"
                    sale="Распродажа" :in-stock="false" />
            </div>

            <div class="home-products__slide slider__slide">
                <x-product-card :href="route('product')" article="DW-651-1008" title="DeatschWerks 9-651-1008 Насос топливный DW65C 265л/ч для Honda"
                    image="/images/home/products/1/1.png" :rating="4" reviews-count="12" price="1100 ₽"
                    sale="ТОП" />
            </div>
            <div class="home-products__slide slider__slide">
                <x-product-card :href="route('product')" article="DW-651-1008" title="DeatschWerks 9-651-1008 Насос топливный DW65C 265л/ч для Honda"
                    image="/images/home/products/1/2.png" :rating="5" reviews-count="8" price="1100 ₽"
                    old-price="1300 ₽" sale="-15%" />
            </div>
            <div class="home-products__slide slider__slide">
                <x-product-card :href="route('product')" article="DW-651-1008" title="DeatschWerks 9-651-1008 Насос топливный DW65C 265л/ч для Honda"
                    image="/images/home/products/1/3.png" :rating="0" reviews-count="0" price="1100 ₽"
                    old-price="1300 ₽" />
            </div>

            <div class="home-products__slide slider__slide">
                <x-product-card :href="route('product')" article="DW-651-1008" title="DeatschWerks 9-651-1008 Насос топливный DW65C 265л/ч для Honda"
                    image="/images/home/products/1/3.png" :rating="0" reviews-count="0" price="1100 ₽"
                    old-price="1300 ₽" />
            </div>
        </x-slider>
    </div>
</section>
