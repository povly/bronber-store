@push('block-styles')
    @vite(['resources/css/blocks/home/advs/style.css'])
@endpush

<section class="home-advs section">
    <div class="container">
        <x-slider
            :config="['pagination' => true, 'breakpoints' => [0 => ['perView' => 1], 768 => ['perView' => 2], 1200 => ['perView' => 4]]]"
            class="home-advs__root"
            viewport-class="home-advs__slider"
            track-class="home-advs__track"
            label="Преимущества">
            <div class="home-advs__slide slider__slide">
                <div class="home-advs__title">Быстрая доставка</div>
                <div class="home-advs__svg">
                    <x-img path="/images/home/advs/1.svg" />
                </div>
                <div class="home-advs__text">Отправка заказов в&nbsp;течение 24 часов
                </div>
            </div>

            <div class="home-advs__slide slider__slide">
                <div class="home-advs__title">Удобная оплата</div>
                <div class="home-advs__svg">
                    <x-img path="/images/home/advs/2.svg" />
                </div>
                <div class="home-advs__text">Оплата картой или при получении
                </div>
            </div>

            <div class="home-advs__slide slider__slide">
                <div class="home-advs__title">Возврат товара</div>
                <div class="home-advs__svg">
                    <x-img path="/images/home/advs/3.svg" />
                </div>
                <div class="home-advs__text">14 дней на возврат без проблем
                </div>
            </div>

            <div class="home-advs__slide slider__slide">
                <div class="home-advs__title">Гарантия качества</div>
                <div class="home-advs__svg">
                    <x-img path="/images/home/advs/4.svg" />
                </div>
                <div class="home-advs__text">Только проверенные автозапчасти
                </div>
            </div>

            <x-slot:nav>
                <x-slider-pagination class="home-advs__pagination" />
            </x-slot:nav>
        </x-slider>

        <div class="home-advs__pc">
            <div class="home-advs__slide slider__slide">
                <div class="home-advs__title">Быстрая доставка</div>
                <div class="home-advs__svg">
                    <x-img path="/images/home/advs/1.svg" />
                </div>
                <div class="home-advs__text">Отправка заказов в&nbsp;течение 24 часов
                </div>
            </div>

            <div class="home-advs__slide slider__slide">
                <div class="home-advs__title">Удобная оплата</div>
                <div class="home-advs__svg">
                    <x-img path="/images/home/advs/2.svg" />
                </div>
                <div class="home-advs__text">Оплата картой или при получении
                </div>
            </div>

            <div class="home-advs__slide slider__slide">
                <div class="home-advs__title">Возврат товара</div>
                <div class="home-advs__svg">
                    <x-img path="/images/home/advs/3.svg" />
                </div>
                <div class="home-advs__text">14 дней на возврат без проблем
                </div>
            </div>

            <div class="home-advs__slide slider__slide">
                <div class="home-advs__title">Гарантия качества</div>
                <div class="home-advs__svg">
                    <x-img path="/images/home/advs/4.svg" />
                </div>
                <div class="home-advs__text">Только проверенные автозапчасти
                </div>
            </div>
        </div>
    </div>
</section>
