@push('block-styles')
    @vite(['resources/css/blocks/catalog/products/style.css'])
@endpush

<section class="product-grid">
    <div class="product-grid__list">
        @for ($i = 0; $i < 6; $i++)
        <x-product-card
            title="DeatschWerks 9-651-1008 Насос топливный DW65C 265л/ч для Honda"
            image="https://placehold.co/400x400/eaeaea/bfbfbf?text=Product"
            :rating="$i !== 2 ? 4 : 0"
            :reviews-count="$i !== 2 ? ($i < 3 ? 122 : 12) : 0"
            :in-stock="$i !== 2"
            price="1100 ₽"
            :old-price="($i === 1 || $i === 4) ? '1300 ₽' : null"
            :discount="($i === 1 || $i === 4) ? '-15%' : null"
            :sale="$i === 1 ? 'Распродажа' : null"
        />
        @endfor
    </div>
</section>
