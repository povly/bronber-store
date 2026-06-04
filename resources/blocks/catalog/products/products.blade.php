@push('block-styles')
    @vite(['resources/blocks/catalog/products/style.css'])
@endpush

<section class="product-grid">
    <div class="product-grid__list">
        @for ($i = 0; $i < 6; $i++)
        <article class="product-card">
            <div class="product-card__image-wrap">
                <div class="product-card__image">
                    <img data-src="https://placehold.co/400x400/eaeaea/bfbfbf?text=Product" alt="Product" class="lazy">
                </div>
                <button class="product-card__fav" type="button" aria-label="В избранное">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M9 15.75s-7.5-4.5-7.5-8.25a4.5 4.5 0 0 1 7.5-3.375A4.5 4.5 0 0 1 16.5 7.5c0 3.75-7.5 8.25-7.5 8.25z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="product-card__badges">
                    @if($i === 1 || $i === 4)
                    <span class="product-card__badge product-card__badge--discount">-15%</span>
                    @endif
                    @if($i === 1)
                    <span class="product-card__badge product-card__badge--sale">Распродажа</span>
                    @endif
                </div>
            </div>
            <div class="product-card__body">
                <h3 class="product-card__title">DeatschWerks 9-651-1008 Насос топливный DW65C 265л/ч для Honda</h3>
                @if($i !== 2)
                <div class="product-card__rating">
                    <div class="product-card__stars">
                        @for ($s = 0; $s < 5; $s++)
                        <svg class="product-card__star" width="15" height="15" viewBox="0 0 15 15"><path d="M7.5 1l1.8 4.2H14l-3.7 2.8 1.4 4.5L7.5 10l-4.2 2.5 1.4-4.5L1 5.2h4.7z" fill="{{$s < 4 ? '#FFB800' : '#E1E1E1'}}"/></svg>
                        @endfor
                    </div>
                    <span class="product-card__reviews">{{ $i < 3 ? '122' : '12' }}</span>
                </div>
                @endif
                <div class="product-card__bottom">
                    @if($i === 2)
                    <span class="product-card__out-of-stock">Нет в наличии</span>
                    @else
                    <div class="product-card__prices">
                        <span class="product-card__price">{{ ($i === 1 || $i === 4) ? '1100 ₽' : '1100 ₽' }}</span>
                        @if($i === 1 || $i === 4)
                        <span class="product-card__old-price">1300 ₽</span>
                        @endif
                    </div>
                    <button class="product-card__cart" type="button" aria-label="В корзину">
                        <svg width="23" height="23" viewBox="0 0 24 24" fill="none"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4zM3 6h18M16 10a4 4 0 01-8 0" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    @endif
                </div>
            </div>
        </article>
        @endfor
    </div>
</section>
