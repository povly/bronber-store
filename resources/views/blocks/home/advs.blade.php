@push('block-styles')
    @vite(['resources/css/blocks/home/advs/style.css'])
@endpush

<section class="home-advs section" x-data="slider({ breakpoints: { 0: 1, 768: 2, 1200: 4 }, pagination: true })" @resize.window.debounce.150ms="onResize()">
    <div class="container">
        <div class="home-advs__slider slider">
            <div class="home-advs__track slider__track" x-ref="track"
                 @pointerdown.prevent="onPointerDown($event)"
                 @pointermove.window="onPointerMove($event)"
                 @pointerup.window="onPointerUp()"
                 @pointercancel.window="onPointerUp()">

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

            <x-slider-pagination />
        </div>

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
