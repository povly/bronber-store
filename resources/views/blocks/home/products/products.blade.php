@props(['title' => 'Рекомендованные товары'])

@push('block-styles')
    @vite(['resources/css/blocks/home/products/style.css'])
@endpush

<section class="home-products" x-data="slider({ desktop: 4, mobile: 2, pagination: true })" @resize.window.debounce.150ms="onResize()">
    <div class="container">
        <div class="home-products__header">
            <h2 class="home-products__title">{{ $title }}</h2>
            <div class="home-products__nav">
                <a href="{{ route('catalog') }}" class="home-products__link">Смотреть все</a>
                <div class="home-products__arrows">
                    <button class="home-products__arrow home-products__arrow--prev" type="button" aria-label="Назад" @click="prev()" :disabled="!canPrev">
                        <svg width="20" height="20" viewBox="0 0 16 16" fill="none"><path d="M10 4L6 8l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <button class="home-products__arrow home-products__arrow--next" type="button" aria-label="Вперёд" @click="next()" :disabled="!canNext">
                        <svg width="20" height="20" viewBox="0 0 16 16" fill="none"><path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
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
                @for ($i = 0; $i < 8; $i++)
                <div class="home-products__slide slider__slide">
                    <article class="product-card">
                        <div class="product-card__image-wrap">
                            <div class="product-card__image">
                                <img data-src="https://placehold.co/400x400/eaeaea/bfbfbf?text=Product" alt="Product" class="lazy" width="400" height="400">
                            </div>
                            <button class="product-card__fav" type="button" aria-label="В избранное">
                                <svg width="24" height="24" viewBox="0 0 18 18" fill="none"><path d="M9 15.75s-7.5-4.5-7.5-8.25a4.5 4.5 0 0 1 7.5-3.375A4.5 4.5 0 0 1 16.5 7.5c0 3.75-7.5 8.25-7.5 8.25z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <div class="product-card__badges">
                                @if($i === 2)
                                <span class="product-card__badge product-card__badge--discount">-15%</span>
                                <span class="product-card__badge product-card__badge--sale">Распродажа</span>
                                @endif
                            </div>
                        </div>
                        <div class="product-card__body">
                            <h3 class="product-card__title">DeatschWerks 9-651-1008 Насос топливный DW65C 265л/ч для Honda</h3>
                            <div class="product-card__rating">
                                <div class="product-card__stars">
                                    @for ($s = 0; $s < 5; $s++)
                                    <svg class="product-card__star" width="24" height="24" viewBox="0 0 15 15"><path d="M7.5 1l1.8 4.2H14l-3.7 2.8 1.4 4.5L7.5 10l-4.2 2.5 1.4-4.5L1 5.2h4.7z" fill="{{$s < 4 ? '#FFB800' : '#E1E1E1'}}"/></svg>
                                    @endfor
                                </div>
                                <span class="product-card__reviews">{{ $i < 3 ? '122' : '12' }}</span>
                            </div>
                            <div class="product-card__bottom">
                                <div class="product-card__prices">
                                    <span class="product-card__price">{{ $i === 2 ? '1100 ₽' : '1100 ₽' }}</span>
                                    @if($i === 2)
                                    <span class="product-card__old-price">1300 ₽</span>
                                    @endif
                                </div>
                                <button class="product-card__cart" type="button" aria-label="В корзину">
                                    <svg width="23" height="23" viewBox="0 0 24 24" fill="none"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4zM3 6h18M16 10a4 4 0 01-8 0" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                        </div>
                    </article>
                </div>
                @endfor
            </div>
        </div>

        <x-slider-pagination />
    </div>
</section>
