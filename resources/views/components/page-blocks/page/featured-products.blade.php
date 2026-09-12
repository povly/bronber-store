@push('block-styles')
    @vite(['resources/css/blocks/page-blocks/style.css'])
@endpush

@php
    $products = \App\Support\CatalogMock::featured(max(1, (int) ($block['count'] ?? 4)));
    $format = static fn (int $price): string => number_format($price, 0, ',', ' ').' ₽';
@endphp

<section class="pb-featured section">
    <div class="container">
        @if(!empty($block['title']))
            <h2 class="pb-featured__title section__title">{{ $block['title'] }}</h2>
        @endif
        <div class="pb-featured__grid">
            @foreach($products as $p)
                <x-product-card
                    href="{{ route('product') }}"
                    :article="$p['article']"
                    :title="$p['title']"
                    :image="$p['image']"
                    :rating="$p['rating']"
                    :reviews-count="$p['reviewsCount']"
                    :in-stock="$p['inStock']"
                    price="{{ $format((int) $p['price']) }}"
                    :old-price="!empty($p['oldPrice']) ? $format((int) $p['oldPrice']) : null"
                    :sale="$p['sale'] ?? null"
                    :index="$loop->index"
                />
            @endforeach
        </div>
    </div>
</section>
