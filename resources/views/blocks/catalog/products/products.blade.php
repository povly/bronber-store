@push('block-styles')
    @vite(['resources/css/blocks/catalog/products/style.css'])
@endpush

<section class="product-grid">
    <div class="product-grid__list">
        @for ($i = 0; $i < 15; $i++)
            @php $p = $i % 4; @endphp
        <x-product-card
            :article="'DW-' . $i"
            title="DeatschWerks 9-651-1008 Насос топливный DW65C 265л/ч для Honda"
            :image="'/images/home/products/1/' . ($p + 1)"
            :rating="$p !== 2 ? 4 : 0"
            :reviews-count="$p !== 2 ? ($p < 3 ? 122 : 12) : 0"
            :in-stock="$p !== 2"
            price="1100 ₽"
            :old-price="($p === 1 || $p === 3) ? '1300 ₽' : null"
            :discount="($p === 1 || $p === 3) ? '-15%' : null"
            :sale="$p === 1 ? 'Распродажа' : null"
            :lazy="false"
            :index="$i" 
        />
        @endfor
    </div>
</section>
