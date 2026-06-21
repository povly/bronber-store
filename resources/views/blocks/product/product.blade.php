@push('block-styles')
    @vite(['resources/css/blocks/product/style.css'])
    @vite(['resources/css/blocks/home/products/style.css'])
@endpush

@push('block-scripts')
    @vite(['resources/js/blocks/product/index.js'])
@endpush

<section class="product" x-data="product()">
    <div class="container">
        <x-breadcrumbs
            class="product__breadcrumb"
            :items="[
                ['label' => 'Главная', 'url' => '/'],
                ['label' => 'Каталог', 'url' => route('catalog')],
                ['label' => 'Топливные насосы', 'url' => route('catalog')],
                ['label' => $product['title']],
            ]"
        />
    </div>

    <div class="product__hero">
        <div class="container">
            <div class="product__hero-grid">

                {{-- Gallery: vertical thumbs slider (linked) + horizontal main slider --}}
                <div class="product__gallery" x-data="{ active: 0 }">
                    <div class="product__thumbs slider" x-data="slider({ verticalAbove: 1200, breakpoints: { 0: 4 } })" @resize.window.debounce.150ms="onResize()"
                         x-init="$watch('active', v => ensureVisible(v))">
                        <div class="slider__track product__thumbs-track" x-ref="track"
                             @click.capture="suppressDragClick($event)"
                             @pointerdown.prevent="onPointerDown($event)"
                             @pointermove.window="onPointerMove($event)"
                             @pointerup.window="onPointerUp()"
                             @pointercancel.window="onPointerUp()">
                            @foreach($product['images'] as $i => $img)
                                <button type="button" class="slider__slide product__thumb{{ $i === 0 ? ' is-active' : '' }}"
                                    :class="{ 'is-active': active === {{ $i }} }"
                                    @click="active = {{ $i }}"
                                    aria-label="Фото {{ $i + 1 }}">
                                    <img src="{{ $img }}" alt="" loading="lazy">
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="product__main slider" x-data="slider({ breakpoints: { 0: 1 }, pagination: true })" @resize.window.debounce.150ms="onResize()"
                         x-init="$watch('index', v => active = v); $watch('active', v => { index = v; snap(); })">
                        <div class="slider__track product__track" x-ref="track"
                             @pointerdown.prevent="onPointerDown($event)"
                             @pointermove.window="onPointerMove($event)"
                             @pointerup.window="onPointerUp()"
                             @pointercancel.window="onPointerUp()">
                            @foreach($product['images'] as $img)
                                <div class="slider__slide product__slide">
                                    <img src="{{ $img }}" alt="{{ $product['title'] }}">
                                </div>
                            @endforeach
                        </div>
                        <x-slider-pagination />
                    </div>
                </div>

                {{-- Buy panel --}}
                <div class="product__buy">
                    <h1 class="product__title">{{ $product['title'] }}</h1>

                    <div class="product__meta">
                        <p class="product__article">Артикул: {{ $product['article'] }}</p>

                        <div class="product__rating">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M12 2L14.55 8.63L21.56 9.27L16.27 13.97L17.84 20.82L12 17.27L6.16 20.82L7.73 13.97L2.44 9.27L9.45 8.63L12 2Z"/>
                            </svg>
                            <span>{{ $product['rating'] }}</span>
                        </div>

                        @if($product['isOriginal'])
                            <span class="product__badge">Оригинал</span>
                        @endif
                    </div>

                    <div class="product__availability">
                        <p class="product__stock">{{ $product['inStock'] ? 'В наличии' : 'Нет в наличии' }}</p>
                        <p class="product__delivery-note">Бесплатная доставка при заказе от 5000 ₽</p>
                    </div>

                    <div class="product__price-block">
                        <span class="product__price">{{ $priceFormatted }}</span>
                        <span class="product__old-price">{{ $oldPriceFormatted }}</span>
                        <span class="product__savings">Экономия {{ $savingsFormatted }}</span>
                    </div>

                    <p class="product__bonus">+{{ $product['bonusPoints'] }} баллов</p>

                    <div class="product__buy-row">
                        <div class="product__qty">
                            <button type="button" class="product__qty-btn" @click="dec()" aria-label="Уменьшить количество">
                                <span class="product__qty-icon product__qty-icon--minus"></span>
                            </button>
                            <span class="product__qty-value" x-text="qty"></span>
                            <button type="button" class="product__qty-btn" @click="inc()" aria-label="Увеличить количество">
                                <span class="product__qty-icon product__qty-icon--plus"></span>
                            </button>
                        </div>

                        <button type="button" class="product__add-cart">В корзину</button>

                        <button type="button" class="product__favorite" aria-label="В избранное">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 21S4.5 15.5 2.5 10.5C1 6.5 4 3 7.5 3C9.5 3 11 4 12 5.5C13 4 14.5 3 16.5 3C20 3 23 6.5 21.5 10.5C19.5 15.5 12 21 12 21Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Highlights row --}}
    <div class="container">
        <ul class="product__highlights">
            <li class="product__highlight">
                <span class="product__highlight-icon" aria-hidden="true">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L3 7V12C3 16.97 7.03 21 12 21S21 16.97 21 12V7L12 2Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                        <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="product__highlight-label">Оригинал</span>
            </li>
            <li class="product__highlight">
                <span class="product__highlight-icon" aria-hidden="true">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 22S4 16 4 10C4 6 7 4 10 4C11 4 12 4.5 12 5.5C12 4.5 13 4 14 4C17 4 20 6 20 10C20 16 12 22 12 22Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="product__highlight-label">Гарантия</span>
            </li>
            <li class="product__highlight">
                <span class="product__highlight-icon" aria-hidden="true">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 7H21V17H3V7Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                        <path d="M7 7V5C7 4 8 3 9 3H15C16 3 17 4 17 5V7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M3 12H21" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                </span>
                <span class="product__highlight-label">Быстрая доставка</span>
            </li>
            <li class="product__highlight">
                <span class="product__highlight-icon" aria-hidden="true">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 9H21L19 19H5L3 9Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                        <path d="M8 9V6C8 4 10 3 12 3C14 3 16 4 16 6V9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </span>
                <span class="product__highlight-label">Возврат</span>
            </li>
        </ul>
    </div>

    {{-- Tabs card --}}
    <div class="container">
        <div class="product__tabs-card">
            <nav class="product__tabs" role="tablist">
                <button type="button" class="product__tab" :class="{ 'is-active': tab === 'description' }" @click="tab = 'description'" role="tab">Описание</button>
                <button type="button" class="product__tab" :class="{ 'is-active': tab === 'specs' }" @click="tab = 'specs'" role="tab">Характеристики</button>
                <button type="button" class="product__tab" :class="{ 'is-active': tab === 'compatibility' }" @click="tab = 'compatibility'" role="tab">Совместимость</button>
            </nav>

            <div class="product__tab-content">
                <div class="product__panel" x-show="tab === 'description'">
                    <p class="product__description">{{ $product['description'] }}</p>
                </div>

                <div class="product__panel" x-show="tab === 'specs'" x-cloak>
                    <dl class="product__specs">
                        @foreach($product['specs'] as $spec)
                            <div class="product__spec-row">
                                <dt class="product__spec-label">{{ $spec['label'] }}</dt>
                                <dd class="product__spec-value">{{ $spec['value'] }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>

                <div class="product__panel" x-show="tab === 'compatibility'" x-cloak>
                    <ul class="product__compat">
                        @foreach($product['compatibility'] as $compat)
                            <li class="product__compat-row">
                                <span class="product__compat-brand">{{ $compat['brand'] }}</span>
                                <span class="product__compat-models">{{ $compat['models'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Reviews (slider) --}}
    <div class="container">
        <div class="product__reviews-head">
            <h2 class="product__section-title">Отзывы ({{ $product['reviewCount'] }})</h2>
            <button type="button" class="product__reviews-all">Смотреть все отзывы</button>
        </div>

        <div class="product__reviews" x-data="slider({ breakpoints: { 0: 1, 1200: 2 } })" @resize.window.debounce.150ms="onResize()">
            <div class="slider product__reviews-slider">
                <div class="slider__track product__reviews-track" x-ref="track"
                     @pointerdown.prevent="onPointerDown($event)"
                     @pointermove.window="onPointerMove($event)"
                     @pointerup.window="onPointerUp()"
                     @pointercancel.window="onPointerUp()">
                    @foreach($product['reviews'] as $review)
                        <article class="slider__slide product__review">
                            <div class="product__review-head">
                                <div class="product__avatar" aria-hidden="true">{{ $review['initial'] }}</div>
                                <div class="product__review-meta">
                                    <p class="product__review-name">{{ $review['name'] }}</p>
                                    <p class="product__review-car">{{ $review['car'] }}</p>
                                    <p class="product__review-date">{{ $review['date'] }}</p>
                                </div>
                            </div>
                            <p class="product__review-text">{{ $review['text'] }}</p>

                            @if(!empty($review['photos']))
                                <div class="product__review-photos">
                                    @foreach($review['photos'] as $photo)
                                        <img src="{{ $photo }}" class="product__review-photo" alt="" loading="lazy">
                                    @endforeach
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>

                <div class="product__reviews-arrows slider__arrows slider__arrows--pc">
                    <button class="slider__arrow slider__arrow--prev product__reviews-arrow" type="button" aria-label="Назад" @click="prev()" :disabled="!canPrev">
                        <svg width="22" height="12" viewBox="0 0 22 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20.75 6.27295C21.1642 6.27295 21.5 5.93716 21.5 5.52295C21.5 5.10874 21.1642 4.77295 20.75 4.77295L20.75 5.52295L20.75 6.27295ZM0.219669 4.99262C-0.0732231 5.28551 -0.0732231 5.76038 0.219669 6.05328L4.99264 10.8262C5.28553 11.1191 5.76041 11.1191 6.0533 10.8262C6.34619 10.5334 6.34619 10.0585 6.0533 9.76559L1.81066 5.52295L6.0533 1.28031C6.34619 0.987414 6.34619 0.512541 6.0533 0.219647C5.76041 -0.0732464 5.28553 -0.0732464 4.99264 0.219647L0.219669 4.99262ZM20.75 5.52295L20.75 4.77295L0.75 4.77295L0.75 5.52295L0.75 6.27295L20.75 6.27295L20.75 5.52295Z" fill="#080808"/></svg>
                    </button>
                    <button class="slider__arrow slider__arrow--next product__reviews-arrow" type="button" aria-label="Вперёд" @click="next()" :disabled="!canNext">
                        <svg width="22" height="12" viewBox="0 0 22 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.75 4.77295C0.335786 4.77295 0 5.10874 0 5.52295C0 5.93716 0.335786 6.27295 0.75 6.27295V5.52295V4.77295ZM21.2803 6.05328C21.5732 5.76039 21.5732 5.28551 21.2803 4.99262L16.5074 0.219648C16.2145 -0.073245 15.7396 -0.073245 15.4467 0.219648C15.1538 0.512542 15.1538 0.987415 15.4467 1.28031L19.6893 5.52295L15.4467 9.76559C15.1538 10.0585 15.1538 10.5334 15.4467 10.8263C15.7396 11.1191 16.2145 11.1191 16.5074 10.8263L21.2803 6.05328ZM0.75 5.52295V6.27295H20.75V5.52295V4.77295H0.75V5.52295Z" fill="#030303"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Related products (reuse home-products block) --}}
    @include('blocks.home.products', ['title' => 'С этим товаром покупают'])
</section>
