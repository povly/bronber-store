@push('block-styles')
    @vite(['resources/css/blocks/product/style.css'])
    @vite(['resources/css/blocks/home/products/style.css'])
@endpush

@push('block-scripts')
    @vite(['resources/js/blocks/product/index.js'])
@endpush

<section class="product" x-data="product()">
    <div class="product__hero">
        <div class="container">

            <div class="product__info-top product__info-top--mb">
                <h1 class="product__title">{{ $product['title'] }}</h1>

                <div class="product__meta">
                    <p class="product__article">Артикул: {{ $product['article'] }}</p>

                    <div class="product__rating-block">
                        <div class="product__rating">
                            @for ($s = 0; $s < 5; $s++)
                                <svg class="product__rating-star{{ $s < round($product['rating'] / 2) ? ' is-filled' : '' }}"
                                    width="22" height="22" viewBox="0 0 22 22" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M10.5268 1.29489C10.5706 1.20635 10.6383 1.13183 10.7223 1.07972C10.8062 1.02761 10.903 1 11.0018 1C11.1006 1 11.1974 1.02761 11.2813 1.07972C11.3653 1.13183 11.433 1.20635 11.4768 1.29489L13.7868 5.97389C13.939 6.28186 14.1636 6.5483 14.4414 6.75035C14.7192 6.95239 15.0419 7.08401 15.3818 7.13389L20.5478 7.88989C20.6457 7.90408 20.7376 7.94537 20.8133 8.00909C20.8889 8.07282 20.9452 8.15644 20.9758 8.2505C21.0064 8.34456 21.0101 8.4453 20.9864 8.54133C20.9627 8.63736 20.9126 8.72485 20.8418 8.79389L17.1058 12.4319C16.8594 12.672 16.6751 12.9684 16.5686 13.2955C16.4622 13.6227 16.4369 13.9708 16.4948 14.3099L17.3768 19.4499C17.3941 19.5477 17.3835 19.6485 17.3463 19.7406C17.3091 19.8327 17.2467 19.9125 17.1663 19.9709C17.086 20.0293 16.9908 20.0639 16.8917 20.0708C16.7926 20.0777 16.6935 20.0566 16.6058 20.0099L11.9878 17.5819C11.6835 17.4221 11.345 17.3386 11.0013 17.3386C10.6576 17.3386 10.3191 17.4221 10.0148 17.5819L5.3978 20.0099C5.31013 20.0563 5.2112 20.0772 5.11225 20.0701C5.0133 20.0631 4.91832 20.0285 4.83809 19.9701C4.75787 19.9118 4.69563 19.8321 4.65846 19.7401C4.62128 19.6481 4.61066 19.5476 4.6278 19.4499L5.5088 14.3109C5.567 13.9716 5.54178 13.6233 5.43534 13.2959C5.32889 12.9686 5.14441 12.672 4.8978 12.4319L1.1618 8.79489C1.09039 8.72593 1.03979 8.63829 1.01576 8.54197C0.991731 8.44565 0.995237 8.34451 1.02588 8.25008C1.05652 8.15566 1.11307 8.07174 1.18908 8.00788C1.26509 7.94402 1.3575 7.90279 1.4558 7.88889L6.6208 7.13389C6.96106 7.08439 7.28419 6.95295 7.56238 6.75088C7.84058 6.54881 8.0655 6.28216 8.2178 5.97389L10.5268 1.29489Z"
                                        fill="#FFA903" stroke="#FFA903" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            @endfor
                        </div>

                        <div class="product__rating-right">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M22 17C22 17.5304 21.7893 18.0391 21.4142 18.4142C21.0391 18.7893 20.5304 19 20 19H6.828C6.29761 19.0001 5.78899 19.2109 5.414 19.586L3.212 21.788C3.1127 21.8873 2.9862 21.9549 2.84849 21.9823C2.71077 22.0097 2.56803 21.9956 2.43831 21.9419C2.30858 21.8881 2.1977 21.7971 2.11969 21.6804C2.04167 21.5637 2.00002 21.4264 2 21.286V5C2 4.46957 2.21071 3.96086 2.58579 3.58579C2.96086 3.21071 3.46957 3 4 3H20C20.5304 3 21.0391 3.21071 21.4142 3.58579C21.7893 3.96086 22 4.46957 22 5V17Z"
                                    stroke="#797878" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M7 11H17" stroke="#797878" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M7 15H13" stroke="#797878" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M7 7H15" stroke="#797878" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                            <span>8</span>
                        </div>
                    </div>

                    @if ($product['isOriginal'])
                        <div class="product__brand product__brand--pc">
                            <div class="product__brand-item">
                                <x-img path="/images/product/logo" width="158" height="37" :alt="$product['title']" :lazy="false" />
                            </div>
                            <span class="product__badge">
                                <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M17.5 11.375C17.5 15.75 14.4375 17.9375 10.7975 19.2063C10.6069 19.2709 10.3998 19.2678 10.2113 19.1975C6.5625 17.9375 3.5 15.75 3.5 11.375V5.25003C3.5 5.01796 3.59219 4.7954 3.75628 4.63131C3.92038 4.46721 4.14294 4.37503 4.375 4.37503C6.125 4.37503 8.3125 3.32503 9.835 1.99503C10.0204 1.83665 10.2562 1.74963 10.5 1.74963C10.7438 1.74963 10.9796 1.83665 11.165 1.99503C12.6963 3.33378 14.875 4.37503 16.625 4.37503C16.8571 4.37503 17.0796 4.46721 17.2437 4.63131C17.4078 4.7954 17.5 5.01796 17.5 5.25003V11.375Z"
                                        stroke="#7212BC" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M7.875 10.5L9.625 12.25L13.125 8.75" stroke="#7212BC" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span>Оригинал</span>
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="product__hero-grid">

                {{-- Gallery: vertical thumbs slider (linked) + horizontal main slider --}}
                <div class="product__gallery" x-data="{ active: 0 }">
                    <div class="product__thumbs slider" x-data="slider({ breakpoints: { 0: { perView: 4 }, 1200: { perView: 4, vertical: true } } })"
                        @resize.window.debounce.150ms="onResize()" x-init="$watch('active', v => ensureVisible(v))">
                        <div class="slider__track product__thumbs-track" x-ref="track"
                            @click.capture="suppressDragClick($event)" @pointerdown.prevent="onPointerDown($event)"
                            @pointermove.window="onPointerMove($event)" @pointerup.window="onPointerUp()"
                            @pointercancel.window="onPointerUp()">
                            @foreach ($product['images'] as $i => $img)
                                <button type="button"
                                    class="slider__slide img--full product__thumb{{ $i === 0 ? ' is-active' : '' }}"
                                    :class="{ 'is-active': active === {{ $i }} }"
                                    @click="active = {{ $i }}" aria-label="Фото {{ $i + 1 }}">
                                    <x-img path="/images/product/bosch" :lazy="false" width="75" height="75"
                                        alt="" />
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="product__main slider" x-data="slider({ perView: 1, pagination: true })" @resize.window.debounce.150ms="onResize()"
                        x-init="$watch('index', v => active = v);
                        $watch('active', v => {
                            index = v;
                            snap();
                        })">
                        <div class="slider__track product__track" x-ref="track"
                            @pointerdown.prevent="onPointerDown($event)" @pointermove.window="onPointerMove($event)"
                            @pointerup.window="onPointerUp()" @pointercancel.window="onPointerUp()">
                            @foreach ($product['images'] as $img)
                                <div class="slider__slide product__slide img--full">
                                    <x-img path="/images/product/bosch" :lazy="false" width="280" height="280"
                                        :alt="$product['title']" />
                                </div>
                            @endforeach
                        </div>
                        @php($galleryFavorite = in_array($product['article'], $favorites ?? []))
                        <button type="button" class="product__gallery-action{{ $galleryFavorite ? ' is-active' : '' }}"
                            x-data="favorite('{{ $product['article'] }}')" :class="{ 'is-active': active }" @click="toggle()"
                            aria-label="В избранное">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M2 9.50001C2.00002 8.38721 2.33759 7.30059 2.96813 6.38367C3.59867 5.46675 4.49252 4.76267 5.53161 4.36441C6.5707 3.96615 7.70616 3.89245 8.78801 4.15305C9.86987 4.41365 10.8472 4.99629 11.591 5.82401C11.6434 5.88002 11.7067 5.92468 11.7771 5.95521C11.8474 5.98574 11.9233 6.00149 12 6.00149C12.0767 6.00149 12.1526 5.98574 12.2229 5.95521C12.2933 5.92468 12.3566 5.88002 12.409 5.82401C13.1504 4.99091 14.128 4.40338 15.2116 4.13961C16.2952 3.87585 17.4335 3.94836 18.4749 4.34749C19.5163 4.74663 20.4114 5.45346 21.0411 6.37391C21.6708 7.29436 22.0053 8.38477 22 9.50001C22 11.79 20.5 13.5 19 15L13.508 20.313C13.3217 20.527 13.0919 20.6989 12.834 20.8173C12.5762 20.9357 12.296 20.9979 12.0123 20.9997C11.7285 21.0015 11.4476 20.9428 11.1883 20.8277C10.9289 20.7126 10.697 20.5436 10.508 20.332L5 15C3.5 13.5 2 11.8 2 9.50001Z"
                                    fill="{{ $galleryFavorite ? '#7212BC' : 'white' }}"
                                    :fill="active ? '#7212BC' : 'white'" stroke="#7212BC" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <button type="button"
                            class="slider__arrow slider__arrow--prev product__gallery-nav product__gallery-nav--prev"
                            @click="prev()" :disabled="!canPrev" aria-label="Предыдущее фото">
                            <svg width="22" height="12" viewBox="0 0 22 12" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M20.75 6.27295C21.1642 6.27295 21.5 5.93716 21.5 5.52295C21.5 5.10874 21.1642 4.77295 20.75 4.77295L20.75 5.52295L20.75 6.27295ZM0.219669 4.99262C-0.0732231 5.28551 -0.0732231 5.76038 0.219669 6.05328L4.99264 10.8262C5.28553 11.1191 5.76041 11.1191 6.0533 10.8262C6.34619 10.5334 6.34619 10.0585 6.0533 9.76559L1.81066 5.52295L6.0533 1.28031C6.34619 0.987414 6.34619 0.512541 6.0533 0.219647C5.76041 -0.0732464 5.28553 -0.0732464 4.99264 0.219647L0.219669 4.99262ZM20.75 5.52295L20.75 4.77295L0.75 4.77295L0.75 5.52295L0.75 6.27295L20.75 6.27295L20.75 5.52295Z"
                                    fill="#030303" />
                            </svg>
                        </button>
                        <button type="button"
                            class="slider__arrow slider__arrow--next product__gallery-nav product__gallery-nav--next"
                            @click="next()" :disabled="!canNext" aria-label="Следующее фото">
                            <svg width="22" height="12" viewBox="0 0 22 12" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M0.75 4.77295C0.335786 4.77295 0 5.10874 0 5.52295C0 5.93716 0.335786 6.27295 0.75 6.27295V5.52295V4.77295ZM21.2803 6.05328C21.5732 5.76039 21.5732 5.28551 21.2803 4.99262L16.5074 0.219648C16.2145 -0.073245 15.7396 -0.073245 15.4467 0.219648C15.1538 0.512542 15.1538 0.987415 15.4467 1.28031L19.6893 5.52295L15.4467 9.76559C15.1538 10.0585 15.1538 10.5334 15.4467 10.8263C15.7396 11.1191 16.2145 11.1191 16.5074 10.8263L21.2803 6.05328ZM0.75 5.52295V6.27295H20.75V5.52295V4.77295H0.75V5.52295Z"
                                    fill="#030303" />
                            </svg>
                        </button>
                        <x-slider-pagination />
                    </div>
                </div>

                <div class="product__hero-side">
                    {{-- Title + meta (mobile: above gallery, tablet+: right column top) --}}
                    <div class="product__info-top product__info-top--pc">
                        <x-breadcrumbs class="product__breadcrumb" :items="[
                            ['label' => 'Главная', 'url' => '/'],
                            ['label' => 'Каталог', 'url' => route('catalog')],
                            ['label' => 'Топливные насосы', 'url' => route('catalog')],
                            ['label' => $product['title']],
                        ]" />

                        <h1 class="product__title">{{ $product['title'] }}</h1>

                        <div class="product__meta">
                            <p class="product__article">Артикул: {{ $product['article'] }}</p>

                            <div class="product__rating-block">
                                <div class="product__rating">
                                    @for ($s = 0; $s < 5; $s++)
                                        <svg class="product__rating-star{{ $s < round($product['rating'] / 2) ? ' is-filled' : '' }}"
                                            width="22" height="22" viewBox="0 0 22 22" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M10.5268 1.29489C10.5706 1.20635 10.6383 1.13183 10.7223 1.07972C10.8062 1.02761 10.903 1 11.0018 1C11.1006 1 11.1974 1.02761 11.2813 1.07972C11.3653 1.13183 11.433 1.20635 11.4768 1.29489L13.7868 5.97389C13.939 6.28186 14.1636 6.5483 14.4414 6.75035C14.7192 6.95239 15.0419 7.08401 15.3818 7.13389L20.5478 7.88989C20.6457 7.90408 20.7376 7.94537 20.8133 8.00909C20.8889 8.07282 20.9452 8.15644 20.9758 8.2505C21.0064 8.34456 21.0101 8.4453 20.9864 8.54133C20.9627 8.63736 20.9126 8.72485 20.8418 8.79389L17.1058 12.4319C16.8594 12.672 16.6751 12.9684 16.5686 13.2955C16.4622 13.6227 16.4369 13.9708 16.4948 14.3099L17.3768 19.4499C17.3941 19.5477 17.3835 19.6485 17.3463 19.7406C17.3091 19.8327 17.2467 19.9125 17.1663 19.9709C17.086 20.0293 16.9908 20.0639 16.8917 20.0708C16.7926 20.0777 16.6935 20.0566 16.6058 20.0099L11.9878 17.5819C11.6835 17.4221 11.345 17.3386 11.0013 17.3386C10.6576 17.3386 10.3191 17.4221 10.0148 17.5819L5.3978 20.0099C5.31013 20.0563 5.2112 20.0772 5.11225 20.0701C5.0133 20.0631 4.91832 20.0285 4.83809 19.9701C4.75787 19.9118 4.69563 19.8321 4.65846 19.7401C4.62128 19.6481 4.61066 19.5476 4.6278 19.4499L5.5088 14.3109C5.567 13.9716 5.54178 13.6233 5.43534 13.2959C5.32889 12.9686 5.14441 12.672 4.8978 12.4319L1.1618 8.79489C1.09039 8.72593 1.03979 8.63829 1.01576 8.54197C0.991731 8.44565 0.995237 8.34451 1.02588 8.25008C1.05652 8.15566 1.11307 8.07174 1.18908 8.00788C1.26509 7.94402 1.3575 7.90279 1.4558 7.88889L6.6208 7.13389C6.96106 7.08439 7.28419 6.95295 7.56238 6.75088C7.84058 6.54881 8.0655 6.28216 8.2178 5.97389L10.5268 1.29489Z"
                                                fill="#FFA903" stroke="#FFA903" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    @endfor
                                </div>

                                <div class="product__rating-right">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M22 17C22 17.5304 21.7893 18.0391 21.4142 18.4142C21.0391 18.7893 20.5304 19 20 19H6.828C6.29761 19.0001 5.78899 19.2109 5.414 19.586L3.212 21.788C3.1127 21.8873 2.9862 21.9549 2.84849 21.9823C2.71077 22.0097 2.56803 21.9956 2.43831 21.9419C2.30858 21.8881 2.1977 21.7971 2.11969 21.6804C2.04167 21.5637 2.00002 21.4264 2 21.286V5C2 4.46957 2.21071 3.96086 2.58579 3.58579C2.96086 3.21071 3.46957 3 4 3H20C20.5304 3 21.0391 3.21071 21.4142 3.58579C21.7893 3.96086 22 4.46957 22 5V17Z"
                                            stroke="#797878" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M7 11H17" stroke="#797878" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M7 15H13" stroke="#797878" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M7 7H15" stroke="#797878" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                    <span>8</span>
                                </div>
                            </div>

                            @if ($product['isOriginal'])
                                <div class="product__brand product__brand--pc">
                                    <div class="product__brand-item">
                                        <x-img path="/images/product/logo" width="158" height="37"
                                            :alt="$product['title']" :lazy="false" />
                                    </div>
                                    <span class="product__badge">
                                        <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M17.5 11.375C17.5 15.75 14.4375 17.9375 10.7975 19.2063C10.6069 19.2709 10.3998 19.2678 10.2113 19.1975C6.5625 17.9375 3.5 15.75 3.5 11.375V5.25003C3.5 5.01796 3.59219 4.7954 3.75628 4.63131C3.92038 4.46721 4.14294 4.37503 4.375 4.37503C6.125 4.37503 8.3125 3.32503 9.835 1.99503C10.0204 1.83665 10.2562 1.74963 10.5 1.74963C10.7438 1.74963 10.9796 1.83665 11.165 1.99503C12.6963 3.33378 14.875 4.37503 16.625 4.37503C16.8571 4.37503 17.0796 4.46721 17.2437 4.63131C17.4078 4.7954 17.5 5.01796 17.5 5.25003V11.375Z"
                                                stroke="#7212BC" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M7.875 10.5L9.625 12.25L13.125 8.75" stroke="#7212BC"
                                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <span>Оригинал</span>
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Availability (mobile: after buy-row, tablet+: right column bottom) --}}
                    <div class="product__availability">
                        <p class="product__stock">
                            <span class="product__stock-dot" aria-hidden="true"></span>
                            {{ $product['inStock'] ? 'В наличии' : 'Нет в наличии' }}
                        </p>
                        <p class="product__delivery-note">Бесплатная доставка при заказе от 5000 ₽</p>
                    </div>

                    {{-- Buy info: price, bonus, buy-row (mobile: after gallery, tablet+: right column) --}}
                    <div class="product__buy-info">

                        @if ($product['isOriginal'])
                            <div class="product__brand product__brand--mb">
                                <div class="product__brand-item">
                                    <x-img path="/images/product/logo" width="158" height="37"
                                        :alt="$product['title']" :lazy="false" />
                                </div>
                                <span class="product__badge">
                                    <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M17.5 11.375C17.5 15.75 14.4375 17.9375 10.7975 19.2063C10.6069 19.2709 10.3998 19.2678 10.2113 19.1975C6.5625 17.9375 3.5 15.75 3.5 11.375V5.25003C3.5 5.01796 3.59219 4.7954 3.75628 4.63131C3.92038 4.46721 4.14294 4.37503 4.375 4.37503C6.125 4.37503 8.3125 3.32503 9.835 1.99503C10.0204 1.83665 10.2562 1.74963 10.5 1.74963C10.7438 1.74963 10.9796 1.83665 11.165 1.99503C12.6963 3.33378 14.875 4.37503 16.625 4.37503C16.8571 4.37503 17.0796 4.46721 17.2437 4.63131C17.4078 4.7954 17.5 5.01796 17.5 5.25003V11.375Z"
                                            stroke="#7212BC" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M7.875 10.5L9.625 12.25L13.125 8.75" stroke="#7212BC"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <span>Оригинал</span>
                                </span>
                            </div>
                        @endif

                        <div class="product__price-block">
                            <span class="product__price">{{ $priceFormatted }}</span>
                            <span class="product__old-price">{{ $oldPriceFormatted }}</span>
                            <span class="product__savings">Экономия {{ $savingsFormatted }}</span>
                        </div>

                        <div class="product__bonus">
                            <div class="product__bonus-svg">
                                <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M10.5649 2.10382C10.6051 2.02265 10.6672 1.95434 10.7441 1.90657C10.821 1.85881 10.9098 1.8335 11.0003 1.8335C11.0909 1.8335 11.1797 1.85881 11.2566 1.90657C11.3335 1.95434 11.3956 2.02265 11.4358 2.10382L13.5533 6.3929C13.6928 6.6752 13.8987 6.91944 14.1533 7.10465C14.408 7.28986 14.7038 7.4105 15.0153 7.45623L19.7508 8.14923C19.8406 8.16223 19.9249 8.20008 19.9942 8.2585C20.0636 8.31691 20.1152 8.39357 20.1432 8.47979C20.1712 8.56601 20.1746 8.65835 20.1529 8.74638C20.1312 8.83441 20.0853 8.9146 20.0203 8.9779L16.5957 12.3127C16.3698 12.5328 16.2008 12.8045 16.1033 13.1044C16.0057 13.4043 15.9825 13.7234 16.0356 14.0342L16.8441 18.7459C16.8599 18.8356 16.8502 18.9279 16.8161 19.0124C16.782 19.0968 16.7249 19.17 16.6512 19.2235C16.5775 19.277 16.4902 19.3087 16.3994 19.3151C16.3085 19.3214 16.2177 19.302 16.1373 19.2592L11.9042 17.0336C11.6253 16.8871 11.3149 16.8106 10.9999 16.8106C10.6848 16.8106 10.3745 16.8871 10.0956 17.0336L5.86335 19.2592C5.78298 19.3018 5.6923 19.3209 5.60159 19.3145C5.51089 19.308 5.42382 19.2762 5.35028 19.2228C5.27675 19.1693 5.21969 19.0962 5.18562 19.0119C5.15154 18.9276 5.1418 18.8355 5.15751 18.7459L5.9651 14.0351C6.01844 13.7242 5.99533 13.4048 5.89776 13.1048C5.80018 12.8047 5.63107 12.5329 5.40501 12.3127L1.98035 8.97882C1.91489 8.9156 1.86851 8.83526 1.84648 8.74697C1.82445 8.65867 1.82766 8.56596 1.85575 8.47941C1.88384 8.39285 1.93568 8.31592 2.00535 8.25738C2.07503 8.19885 2.15974 8.16106 2.24985 8.14832L6.98443 7.45623C7.29633 7.41086 7.59254 7.29037 7.84755 7.10514C8.10256 6.91991 8.30874 6.67548 8.44835 6.3929L10.5649 2.10382Z"
                                        stroke="#7212BC" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div class="product__bonus-text">
                                <span>+{{ $product['bonusPoints'] }}</span> баллов
                            </div>
                            <div class="product__bonus-info">
                                <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M10.5 19.25C15.3325 19.25 19.25 15.3325 19.25 10.5C19.25 5.66751 15.3325 1.75 10.5 1.75C5.66751 1.75 1.75 5.66751 1.75 10.5C1.75 15.3325 5.66751 19.25 10.5 19.25Z"
                                        stroke="#CACACA" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M10.5 7V10.5" stroke="#CACACA" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M10.5 14H10.5088" stroke="#CACACA" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>

                        <div class="product__buy-row">
                            <x-qty />

                            <button type="button" class="product__add-cart btn btn--primary">
                                <span class="product__add-cart-text">В корзину</span>
                                <svg class="product__add-cart-icon" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M8 22C8.55228 22 9 21.5523 9 21C9 20.4477 8.55228 20 8 20C7.44772 20 7 20.4477 7 21C7 21.5523 7.44772 22 8 22Z"
                                        stroke="white" stroke-width="1.8" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path
                                        d="M19 22C19.5523 22 20 21.5523 20 21C20 20.4477 19.5523 20 19 20C18.4477 20 18 20.4477 18 21C18 21.5523 18.4477 22 19 22Z"
                                        stroke="white" stroke-width="1.8" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path
                                        d="M2.05078 2.0498H4.05078L6.71078 14.4698C6.80836 14.9247 7.06145 15.3313 7.42649 15.6197C7.79153 15.908 8.24569 16.0602 8.71078 16.0498H18.4908C18.946 16.0491 19.3873 15.8931 19.7418 15.6076C20.0964 15.3222 20.3429 14.9243 20.4408 14.4798L22.0908 7.0498H5.12078"
                                        stroke="white" stroke-width="1.8" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </button>

                            @php($productFavorite = in_array($product['article'], $favorites ?? []))
                            <button type="button"
                                class="product__favorite{{ $productFavorite ? ' is-active' : '' }}"
                                x-data="favorite('{{ $product['article'] }}')" :class="{ 'is-active': active }" @click="toggle()"
                                aria-label="В избранное">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M2 9.49998C2.00002 8.38718 2.33759 7.30056 2.96813 6.38364C3.59867 5.46672 4.49252 4.76264 5.53161 4.36438C6.5707 3.96612 7.70616 3.89242 8.78801 4.15302C9.86987 4.41362 10.8472 4.99626 11.591 5.82398C11.6434 5.87999 11.7067 5.92465 11.7771 5.95518C11.8474 5.98571 11.9233 6.00146 12 6.00146C12.0767 6.00146 12.1526 5.98571 12.2229 5.95518C12.2933 5.92465 12.3566 5.87999 12.409 5.82398C13.1504 4.99088 14.128 4.40335 15.2116 4.13958C16.2952 3.87581 17.4335 3.94833 18.4749 4.34746C19.5163 4.7466 20.4114 5.45343 21.0411 6.37388C21.6708 7.29433 22.0053 8.38474 22 9.49998C22 11.79 20.5 13.5 19 15L13.508 20.313C13.3217 20.527 13.0919 20.6989 12.834 20.8173C12.5762 20.9357 12.296 20.9978 12.0123 20.9996C11.7285 21.0014 11.4476 20.9428 11.1883 20.8277C10.9289 20.7126 10.697 20.5436 10.508 20.332L5 15C3.5 13.5 2 11.8 2 9.49998Z"
                                        fill="{{ $productFavorite ? '#7212BC' : 'white' }}"
                                        :fill="active ? '#7212BC' : 'white'" stroke="#7212BC" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Features strip (desktop only) --}}
    <div class="container">
        <div class="product__features">
            <div class="product__feature">
                <div class="product__feature-icon">
                    <svg width="49" height="41" viewBox="0 0 49 41" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M32.0963 28.5504H20.3497C20.0119 28.5504 19.6878 28.4162 19.4489 28.1772C19.21 27.9383 19.0757 27.6143 19.0757 27.2764L14.6395 11.0329C14.6395 10.8643 14.6729 10.6974 14.7379 10.5418C14.8029 10.3862 14.8981 10.2451 15.018 10.1266C15.1379 10.0081 15.2801 9.91459 15.4365 9.85147C15.5928 9.78834 15.7601 9.75687 15.9287 9.75888H35.1827C35.5213 9.75887 35.8462 9.89285 36.0863 10.1316C36.3264 10.3703 36.4623 10.6943 36.4643 11.0329L40.256 27.2764C40.256 27.6143 40.1218 27.9383 39.8828 28.1772C39.6439 28.4162 39.3199 28.5504 38.982 28.5504H37.3288"
                            stroke="black" stroke-width="2.275" />
                        <path
                            d="M16.9449 28.5501H19.5763L15.7846 15.4385H9.55113C9.45737 15.4383 9.36469 15.4585 9.27956 15.4978C9.19442 15.5371 9.11889 15.5945 9.05822 15.666L6.34338 20.4435C6.2461 20.5548 6.19225 20.6975 6.19172 20.8454L8.71697 28.5501H11.447"
                            stroke="black" stroke-width="2.275" />
                        <path
                            d="M34.6496 32.2281C33.0958 32.2281 31.8362 30.9515 31.8362 29.3767C31.8362 27.802 33.0958 26.5254 34.6496 26.5254C36.2034 26.5254 37.463 27.802 37.463 29.3767C37.463 30.9515 36.2034 32.2281 34.6496 32.2281Z"
                            stroke="black" stroke-width="2.275" />
                        <path
                            d="M14.1457 32.2281C12.5919 32.2281 11.3323 30.9515 11.3323 29.3767C11.3323 27.802 12.5919 26.5254 14.1457 26.5254C15.6995 26.5254 16.9591 27.802 16.9591 29.3767C16.9591 30.9515 15.6995 32.2281 14.1457 32.2281Z"
                            stroke="black" stroke-width="2.275" />
                        <path d="M30.9048 13.4897H43.3263" stroke="black" stroke-width="2.275"
                            stroke-linecap="round" />
                        <path d="M33.3493 17.4634H42.1232" stroke="black" stroke-width="2.275"
                            stroke-linecap="round" />
                        <path d="M32.4649 21.9376H46.0997" stroke="black" stroke-width="2.275"
                            stroke-linecap="round" />
                    </svg>
                </div>
                <div class="product__feature-info">
                    <p class="product__feature-title">Быстрая доставка</p>
                    <p class="product__feature-subtitle">Отправка заказов в&nbsp;течение 24 часов</p>
                </div>
            </div>
            <div class="product__feature">
                <div class="product__feature-icon">
                    <svg width="35" height="35" viewBox="0 0 35 35" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M5.55911 12.4508C5.34828 11.5011 5.38065 10.5136 5.65323 9.57975C5.9258 8.64591 6.42975 7.79602 7.11834 7.10886C7.80694 6.4217 8.65789 5.91954 9.5923 5.64892C10.5267 5.37831 11.5143 5.348 12.4636 5.56082C12.986 4.7437 13.7058 4.07125 14.5565 3.60545C15.4072 3.13966 16.3615 2.89551 17.3313 2.89551C18.3012 2.89551 19.2555 3.13966 20.1062 3.60545C20.9569 4.07125 21.6766 4.7437 22.1991 5.56082C23.1498 5.34707 24.1391 5.37724 25.075 5.64852C26.0109 5.91979 26.863 6.42335 27.552 7.11237C28.241 7.80139 28.7446 8.65348 29.0159 9.58938C29.2871 10.5253 29.3173 11.5146 29.1036 12.4653C29.9207 12.9877 30.5931 13.7075 31.0589 14.5582C31.5247 15.4089 31.7689 16.3632 31.7689 17.333C31.7689 18.3029 31.5247 19.2572 31.0589 20.1079C30.5931 20.9586 29.9207 21.6784 29.1036 22.2008C29.3164 23.1501 29.2861 24.1377 29.0155 25.0721C28.7448 26.0065 28.2427 26.8574 27.5555 27.546C26.8684 28.2346 26.0185 28.7386 25.0846 29.0111C24.1508 29.2837 23.1632 29.3161 22.2136 29.1053C21.6918 29.9255 20.9714 30.6008 20.1193 31.0687C19.2671 31.5366 18.3107 31.7819 17.3386 31.7819C16.3664 31.7819 15.41 31.5366 14.5578 31.0687C13.7057 30.6008 12.9853 29.9255 12.4636 29.1053C11.5143 29.3181 10.5267 29.2878 9.5923 29.0172C8.65789 28.7465 7.80694 28.2444 7.11834 27.5572C6.42975 26.8701 5.9258 26.0202 5.65323 25.0863C5.38065 24.1525 5.34828 23.1649 5.55911 22.2153C4.73572 21.6942 4.05749 20.9733 3.58751 20.1197C3.11754 19.2661 2.87109 18.3075 2.87109 17.333C2.87109 16.3586 3.11754 15.4 3.58751 14.5464C4.05749 13.6928 4.73572 12.9719 5.55911 12.4508Z"
                            stroke="black" stroke-width="2.27933" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M13 17.3332L15.8889 20.2221L21.6667 14.4443" stroke="black" stroke-width="2.27933"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <div class="product__feature-info">
                    <p class="product__feature-title">Гарантия качества</p>
                    <p class="product__feature-subtitle">Только оригинальные автозапчасти</p>
                </div>
            </div>
            <div class="product__feature">
                <div class="product__feature-icon">
                    <svg width="42" height="38" viewBox="0 0 42 38" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M26.1845 14.1009L21.1484 9.06484L26.1845 4.02881" stroke="black"
                            stroke-width="2.27933" stroke-linecap="round" stroke-linejoin="round" />
                        <path
                            d="M21.1484 9.06494H31.7241C32.4516 9.06494 33.1719 9.20823 33.844 9.48662C34.5161 9.76501 35.1268 10.1731 35.6412 10.6875C36.1556 11.2019 36.5637 11.8126 36.8421 12.4847C37.1205 13.1568 37.2638 13.8771 37.2638 14.6046C37.2638 15.3321 37.1205 16.0524 36.8421 16.7245C36.5637 17.3966 36.1556 18.0073 35.6412 18.5217C35.1268 19.0361 34.5161 19.4441 33.844 19.7225C33.1719 20.0009 32.4516 20.1442 31.7241 20.1442H28.1989"
                            stroke="black" stroke-width="2.27933" stroke-linecap="round" stroke-linejoin="round" />
                        <g clip-path="url(#clip0_792_414)">
                            <path d="M10.0703 35.252V27.1943" stroke="black" stroke-width="2.27933"
                                stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M2.35156 22.0476L10.0708 27.1944L21.8702 20.1147" stroke="black"
                                stroke-width="2.27933" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M22.1598 27.1946C22.1598 27.5424 22.0697 27.8844 21.8983 28.1871C21.7269 28.4898 21.4801 28.743 21.1818 28.9219L11.1097 34.9652C10.7828 35.1615 10.4069 35.2609 10.0257 35.252C9.6445 35.2431 9.27366 35.1262 8.95629 34.9148L2.91305 30.886C2.63691 30.702 2.41051 30.4527 2.25395 30.1601C2.09739 29.8675 2.01552 29.5408 2.01563 29.209V23.1658C2.01562 22.8179 2.1057 22.476 2.27709 22.1733C2.44848 21.8705 2.69534 21.6174 2.99362 21.4384L13.0657 15.3952C13.3926 15.1989 13.7685 15.0994 14.1497 15.1083C14.5309 15.1173 14.9017 15.2342 15.2191 15.4455L21.2623 19.4743C21.5385 19.6583 21.7649 19.9077 21.9214 20.2003C22.078 20.4928 22.1599 20.8195 22.1598 21.1513V27.1946Z"
                                stroke="black" stroke-width="2.27933" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </g>
                        <defs>
                            <clipPath id="clip0_792_414">
                                <rect width="24.173" height="24.173" fill="white"
                                    transform="translate(0 13.0938)" />
                            </clipPath>
                        </defs>
                    </svg>

                </div>
                <div class="product__feature-info">
                    <p class="product__feature-title">Возврат товара</p>
                    <p class="product__feature-subtitle">14 дней на возврат без проблем</p>
                </div>
            </div>
            <div class="product__feature">
                <div class="product__feature-icon">
                    <svg width="53" height="53" viewBox="0 0 53 53" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_792_406)">
                            <path
                                d="M41.8194 24.1452L19.8031 11.4341C18.2832 10.5566 16.3397 11.0773 15.4622 12.5973L9.1066 23.6054C8.22908 25.1253 8.74984 27.0688 10.2697 27.9463L32.286 40.6574C33.8059 41.535 35.7494 41.0142 36.627 39.4943L42.9825 28.4861C43.86 26.9662 43.3393 25.0227 41.8194 24.1452Z"
                                stroke="black" stroke-width="2.27933" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path
                                d="M24.4573 28.7976C25.9772 29.6751 27.9207 29.1544 28.7983 27.6345C29.6758 26.1146 29.155 24.1711 27.6351 23.2935C26.1152 22.416 24.1717 22.9368 23.2942 24.4567C22.4167 25.9766 22.9374 27.9201 24.4573 28.7976Z"
                                stroke="black" stroke-width="2.27933" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M17.7893 21.2789L17.8031 21.2868M34.3015 30.8122L34.3153 30.8201" stroke="black"
                                stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" />
                        </g>
                        <defs>
                            <clipPath id="clip0_792_406">
                                <rect width="38.1333" height="38.1333" fill="white"
                                    transform="translate(19.0667) rotate(30)" />
                            </clipPath>
                        </defs>
                    </svg>
                </div>
                <div class="product__feature-info">
                    <p class="product__feature-title">Удобная оплата</p>
                    <p class="product__feature-subtitle">Оплата картой или при получении</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs card --}}
    <div class="container">
        <div class="product__tabs-card">
            <nav class="product__tabs" role="tablist">
                <button type="button" class="product__tab" :class="{ 'is-active': tab === 'description' }"
                    @click="tab = 'description'" role="tab">Описание</button>
                <button type="button" class="product__tab" :class="{ 'is-active': tab === 'specs' }"
                    @click="tab = 'specs'" role="tab">Характеристики</button>
                <button type="button" class="product__tab" :class="{ 'is-active': tab === 'compatibility' }"
                    @click="tab = 'compatibility'" role="tab">Совместимость</button>
            </nav>

            <div class="product__tab-content">
                <div class="product__panel" x-show="tab === 'description'">
                    <div class="product__description">
                        <p>Топливный насос Bosch 0 580 464 070 обеспечивает стабильную подачу топлива и надежную работу
                            двигателя в различных условиях эксплуатации. Оригинальная продукция Bosch отличается высоким
                            качеством изготовления, долговечностью и соответствием заводским стандартам.
                        </p>
                        <p>Преимущества:<br>
                            ✔ Стабильная подача топлива при любых режимах работы двигателя<br>
                            ✔ Низкий уровень шума и вибраций<br>
                            ✔ Устойчивость к износу и коррозии<br>омобилей<br>
                            ✔ Соответствие оригинальным техническим требованиям производителя</p>

                        <p>Насос изготовлен из качественных материалов и рассчитан на длительный срок службы, что делает
                            его надежным решением для замены штатного топливного оборудования.</p>
                    </div>
                </div>

                <div class="product__panel" x-show="tab === 'specs'" x-cloak>
                    <div class="product__description">
                        <p><strong>Бренд</strong> — Bosch<br>
                            <strong>Страна производства</strong> — Германия<br>
                            <strong>Тип</strong> — Электрический топливный насос<br>
                            <strong>Рабочее напряжение</strong> — 12 В<br>
                            <strong>Рабочее давление</strong> — 3.0 бар<br>
                            <strong>OEM номер</strong> — 0 580 464 070<br>
                            <strong>Тип топлива</strong> — Бензин<br>
                            <strong>Материал корпуса</strong> — Металл<br>
                            <strong>Состояние</strong> — Новый<br>
                            <strong>Гарантия</strong> — 12 месяцев
                        </p>
                    </div>
                </div>

                <div class="product__panel" x-show="tab === 'compatibility'" x-cloak>
                    <div class="product__description">
                        <p><strong>BWM</strong> — BMW 3 Series (E46), BMW 5 Series (E39), BMW X5 (E53)<br>
                            <strong>Audi</strong> — Audi A4 B6, Audi A6 C5, Audi TT 8N<br>
                            <strong>Volkswagen</strong> — Volkswagen Golf IV, Volkswagen Passat B5, Volkswagen Bora<br>
                            <strong>Mercedes-Benz</strong> — Mercedes-Benz C-Class (W203), Mercedes-Benz E-Class
                            (W210)<br>
                            <strong>Ford</strong> — Ford Focus Mk1, Ford Mondeo Mk3
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Reviews (slider) --}}
    <div class="container">
        <div class="product__reviews-section section" x-data="slider({ breakpoints: { 0: { perView: 1, autoHeight: true }, 1200: { perView: 2 } } })"
            @resize.window.debounce.150ms="onResize()">
            <div class="product__reviews-head">
                <h2 class="product__section-title">Отзывы ({{ $product['reviewCount'] }})</h2>
                <div class="product__reviews-arrows slider__arrows slider__arrows--pc">
                    <button class="slider__arrow slider__arrow--prev product__reviews-arrow" type="button"
                        aria-label="Назад" @click="prev()" :disabled="!canPrev">
                        <svg width="22" height="12" viewBox="0 0 22 12" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M20.75 6.27295C21.1642 6.27295 21.5 5.93716 21.5 5.52295C21.5 5.10874 21.1642 4.77295 20.75 4.77295L20.75 5.52295L20.75 6.27295ZM0.219669 4.99262C-0.0732231 5.28551 -0.0732231 5.76038 0.219669 6.05328L4.99264 10.8262C5.28553 11.1191 5.76041 11.1191 6.0533 10.8262C6.34619 10.5334 6.34619 10.0585 6.0533 9.76559L1.81066 5.52295L6.0533 1.28031C6.34619 0.987414 6.34619 0.512541 6.0533 0.219647C5.76041 -0.0732464 5.28553 -0.0732464 4.99264 0.219647L0.219669 4.99262ZM20.75 5.52295L20.75 4.77295L0.75 4.77295L0.75 5.52295L0.75 6.27295L20.75 6.27295L20.75 5.52295Z"
                                fill="#080808" />
                        </svg>
                    </button>
                    <button class="slider__arrow slider__arrow--next product__reviews-arrow" type="button"
                        aria-label="Вперёд" @click="next()" :disabled="!canNext">
                        <svg width="22" height="12" viewBox="0 0 22 12" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M0.75 4.77295C0.335786 4.77295 0 5.10874 0 5.52295C0 5.93716 0.335786 6.27295 0.75 6.27295V5.52295V4.77295ZM21.2803 6.05328C21.5732 5.76039 21.5732 5.28551 21.2803 4.99262L16.5074 0.219648C16.2145 -0.073245 15.7396 -0.073245 15.4467 0.219648C15.1538 0.512542 15.1538 0.987415 15.4467 1.28031L19.6893 5.52295L15.4467 9.76559C15.1538 10.0585 15.1538 10.5334 15.4467 10.8263C15.7396 11.1191 16.2145 11.1191 16.5074 10.8263L21.2803 6.05328ZM0.75 5.52295V6.27295H20.75V5.52295V4.77295H0.75V5.52295Z"
                                fill="#030303" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="slider product__reviews-slider">
                <div class="slider__track product__reviews-track" x-ref="track"
                    @pointerdown.prevent="onPointerDown($event)" @pointermove.window="onPointerMove($event)"
                    @pointerup.window="onPointerUp()" @pointercancel.window="onPointerUp()">
                    @foreach ($product['reviews'] as $review)
                        <article class="slider__slide product__review">
                            <div class="product__review-head">
                                <div class="product__avatar" aria-hidden="true">{{ $review['initial'] }}</div>
                                <div class="product__review-meta">
                                    <p class="product__review-name">{{ $review['name'] }}</p>
                                    <p class="product__review-car">{{ $review['car'] }}</p>
                                </div>
                                <p class="product__review-date">{{ $review['date'] }}</p>
                            </div>
                            <div class="product__review-stars" aria-hidden="true">
                                @for ($s = 0; $s < 5; $s++)
                                    <svg width="24" height="24" viewBox="0 0 22 22" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M10.5268 1.29489C10.5706 1.20635 10.6383 1.13183 10.7223 1.07972C10.8062 1.02761 10.903 1 11.0018 1C11.1006 1 11.1974 1.02761 11.2813 1.07972C11.3653 1.13183 11.433 1.20635 11.4768 1.29489L13.7868 5.97389C13.939 6.28186 14.1636 6.5483 14.4414 6.75035C14.7192 6.95239 15.0419 7.08401 15.3818 7.13389L20.5478 7.88989C20.6457 7.90408 20.7376 7.94537 20.8133 8.00909C20.8889 8.07282 20.9452 8.15644 20.9758 8.2505C21.0064 8.34456 21.0101 8.4453 20.9864 8.54133C20.9627 8.63736 20.9126 8.72485 20.8418 8.79389L17.1058 12.4319C16.8594 12.672 16.6751 12.9684 16.5686 13.2955C16.4622 13.6227 16.4369 13.9708 16.4948 14.3099L17.3768 19.4499C17.3941 19.5477 17.3835 19.6485 17.3463 19.7406C17.3091 19.8327 17.2467 19.9125 17.1663 19.9709C17.086 20.0293 16.9908 20.0639 16.8917 20.0708C16.7926 20.0777 16.6935 20.0566 16.6058 20.0099L11.9878 17.5819C11.6835 17.4221 11.345 17.3386 11.0013 17.3386C10.6576 17.3386 10.3191 17.4221 10.0148 17.5819L5.3978 20.0099C5.31013 20.0563 5.2112 20.0772 5.11225 20.0701C5.0133 20.0631 4.91832 20.0285 4.83809 19.9701C4.75787 19.9118 4.69563 19.8321 4.65846 19.7401C4.62128 19.6481 4.61066 19.5476 4.6278 19.4499L5.5088 14.3109C5.567 13.9716 5.54178 13.6233 5.43534 13.2959C5.32889 12.9686 5.14441 12.672 4.8978 12.4319L1.1618 8.79489C1.09039 8.72593 1.03979 8.63829 1.01576 8.54197C0.991731 8.44565 0.995237 8.34451 1.02588 8.25008C1.05652 8.15566 1.11307 8.07174 1.18908 8.00788C1.26509 7.94402 1.3575 7.90279 1.4558 7.88889L6.6208 7.13389C6.96106 7.08439 7.28419 6.95295 7.56238 6.75088C7.84058 6.54881 8.0655 6.28216 8.2178 5.97389L10.5268 1.29489Z"
                                            fill="#FFA903" stroke="#FFA903" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                @endfor
                            </div>
                            <p class="product__review-text">{{ $review['text'] }}</p>

                            @if (!empty($review['photos']))
                                <div class="product__review-photos">
                                    @foreach ($review['photos'] as $photo)
                                        <x-img path="/images/product/review" class="product__review-photo"
                                            alt="" />
                                    @endforeach
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>
            </div>

            <div class="product__reviews-bottom">
                <a href="{{ route('product.reviews') }}" class="product__reviews-all btn btn--primary">
                    <span class="product__reviews-all-short">Смотреть все</span>
                    <span class="product__reviews-all-full">Смотреть все отзывы</span>
                </a>

                <div class="product__reviews-arrows slider__arrows slider__arrows--mb">
                    <button class="slider__arrow slider__arrow--prev product__reviews-arrow" type="button"
                        aria-label="Назад" @click="prev()" :disabled="!canPrev">
                        <svg width="22" height="12" viewBox="0 0 22 12" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M20.75 6.27295C21.1642 6.27295 21.5 5.93716 21.5 5.52295C21.5 5.10874 21.1642 4.77295 20.75 4.77295L20.75 5.52295L20.75 6.27295ZM0.219669 4.99262C-0.0732231 5.28551 -0.0732231 5.76038 0.219669 6.05328L4.99264 10.8262C5.28553 11.1191 5.76041 11.1191 6.0533 10.8262C6.34619 10.5334 6.34619 10.0585 6.0533 9.76559L1.81066 5.52295L6.0533 1.28031C6.34619 0.987414 6.34619 0.512541 6.0533 0.219647C5.76041 -0.0732464 5.28553 -0.0732464 4.99264 0.219647L0.219669 4.99262ZM20.75 5.52295L20.75 4.77295L0.75 4.77295L0.75 5.52295L0.75 6.27295L20.75 6.27295L20.75 5.52295Z"
                                fill="#080808" />
                        </svg>
                    </button>
                    <button class="slider__arrow slider__arrow--next product__reviews-arrow" type="button"
                        aria-label="Вперёд" @click="next()" :disabled="!canNext">
                        <svg width="22" height="12" viewBox="0 0 22 12" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M0.75 4.77295C0.335786 4.77295 0 5.10874 0 5.52295C0 5.93716 0.335786 6.27295 0.75 6.27295V5.52295V4.77295ZM21.2803 6.05328C21.5732 5.76039 21.5732 5.28551 21.2803 4.99262L16.5074 0.219648C16.2145 -0.073245 15.7396 -0.073245 15.4467 0.219648C15.1538 0.512542 15.1538 0.987415 15.4467 1.28031L19.6893 5.52295L15.4467 9.76559C15.1538 10.0585 15.1538 10.5334 15.4467 10.8263C15.7396 11.1191 16.2145 11.1191 16.5074 10.8263L21.2803 6.05328ZM0.75 5.52295V6.27295H20.75V5.52295V4.77295H0.75V5.52295Z"
                                fill="#030303" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Related products (reuse home-products block) --}}
    @include('blocks.home.products', ['title' => 'С этим товаром покупают'])
</section>
