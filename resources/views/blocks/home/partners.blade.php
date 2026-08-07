@push('block-styles')
    @vite(['resources/css/blocks/home/partners/style.css'])
@endpush

<section class="home-partners section">
    <div class="container">
        <x-slider
            :config="['breakpoints' => [0 => ['grid' => ['cols' => 2, 'rows' => 4]], 768 => ['grid' => ['cols' => 5, 'rows' => 2]]]]"
            class="home-partners__root"
            viewport-class="home-partners__slider"
            track-class="home-partners__track"
            label="Наши партнеры">
            <x-slot:header>
                <div class="home-partners__header section__top">
                    <h2 class="home-partners__title section__title">Наши партнеры</h2>
                    <x-slider-arrows class="home-partners__arrows home-partners__arrows--pc slider__arrows--pc" />
                </div>
            </x-slot:header>

            <div class="home-partners__slide slider__slide">
                <div class="home-partners__item">
                    <x-img path="/images/home/partners/brembo.png" width="150" height="38" />
                </div>
            </div>

            <div class="home-partners__slide slider__slide">
                <div class="home-partners__item">
                    <x-img path="/images/home/partners/bosch.png" width="150" height="38" />
                </div>
            </div>

            <div class="home-partners__slide slider__slide">
                <div class="home-partners__item">
                    <x-img path="/images/home/partners/akrapovic.png" width="150" height="38" />
                </div>
            </div>

            <div class="home-partners__slide slider__slide">
                <div class="home-partners__item">
                    <x-img path="/images/home/partners/brembo.png" width="150" height="38" />
                </div>
            </div>

            <div class="home-partners__slide slider__slide">
                <div class="home-partners__item">
                    <x-img path="/images/home/partners/bosch.png" width="150" height="38" />
                </div>
            </div>

            <div class="home-partners__slide slider__slide">
                <div class="home-partners__item">
                    <x-img path="/images/home/partners/akrapovic.png" width="150" height="38" />
                </div>
            </div>

            <div class="home-partners__slide slider__slide">
                <div class="home-partners__item">
                    <x-img path="/images/home/partners/brembo.png" width="150" height="38" />
                </div>
            </div>

            <div class="home-partners__slide slider__slide">
                <div class="home-partners__item">
                    <x-img path="/images/home/partners/bosch.png" width="150" height="38" />
                </div>
            </div>

            <div class="home-partners__slide slider__slide">
                <div class="home-partners__item">
                    <x-img path="/images/home/partners/akrapovic.png" width="150" height="38" />
                </div>
            </div>

            <div class="home-partners__slide slider__slide">
                <div class="home-partners__item">
                    <x-img path="/images/home/partners/brembo.png" width="150" height="38" />
                </div>
            </div>

            <div class="home-partners__slide slider__slide">
                <div class="home-partners__item">
                    <x-img path="/images/home/partners/bosch.png" width="150" height="38" />
                </div>
            </div>

            <div class="home-partners__slide slider__slide">
                <div class="home-partners__item">
                    <x-img path="/images/home/partners/akrapovic.png" width="150" height="38" />
                </div>
            </div>

            <div class="home-partners__slide slider__slide">
                <div class="home-partners__item">
                    <x-img path="/images/home/partners/brembo.png" width="150" height="38" />
                </div>
            </div>

            <div class="home-partners__slide slider__slide">
                <div class="home-partners__item">
                    <x-img path="/images/home/partners/bosch.png" width="150" height="38" />
                </div>
            </div>

            <div class="home-partners__slide slider__slide">
                <div class="home-partners__item">
                    <x-img path="/images/home/partners/akrapovic.png" width="150" height="38" />
                </div>
            </div>

            <div class="home-partners__slide slider__slide">
                <div class="home-partners__item">
                    <x-img path="/images/home/partners/brembo.png" width="150" height="38" />
                </div>
            </div>

            <div class="home-partners__slide slider__slide">
                <div class="home-partners__item">
                    <x-img path="/images/home/partners/bosch.png" width="150" height="38" />
                </div>
            </div>

            <div class="home-partners__slide slider__slide">
                <div class="home-partners__item">
                    <x-img path="/images/home/partners/akrapovic.png" width="150" height="38" />
                </div>
            </div>
        </x-slider>
    </div>
</section>
