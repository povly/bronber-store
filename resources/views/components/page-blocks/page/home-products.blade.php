@push('block-styles')
    @vite(['resources/css/blocks/home/products/style.css'])
@endpush

@php
    $products = \App\Support\CatalogMock::featured(max(1, (int) ($block['count'] ?? 4)));
    $format = static fn (int $price): string => number_format($price, 0, ',', ' ').' ₽';
    $title = $block['title'] ?? 'Рекомендованные товары';
@endphp

<section class="home-products section">
    <div class="container">
        <x-slider
            :config="['loop' => true, 'freeMode' => true, 'breakpoints' => [0 => ['grid' => ['cols' => 2, 'rows' => 2]], 1200 => ['perView' => 4]]]"
            class="home-products__root"
            viewport-class="home-products__slider"
            track-class="home-products__track"
            label="{{ $title }}">
            <x-slot:header>
                <div class="section__top home-products__header">
                    <h2 class="home-products__title section__title">{{ $title }}</h2>
                    <div class="home-products__nav">
                        <x-slider-arrows class="home-products__arrows home-products__arrows--pc slider__arrows--pc" />
                    </div>
                </div>
            </x-slot:header>

            @foreach($products as $p)
                <div class="home-products__slide slider__slide">
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
                </div>
            @endforeach
        </x-slider>
    </div>
</section>
