@push('block-styles')
    @vite(['resources/css/blocks/home/categories/style.css'])
@endpush

@php
    $categories = [
        ['name' => 'Тормозная система', 'slug' => 'brake-system'],
        ['name' => 'Чип тюнинг', 'slug' => 'chip-tuning'],
        ['name' => 'Диски', 'slug' => 'wheels'],
        ['name' => 'Оптика', 'slug' => 'optics'],
        ['name' => 'Подвеска', 'slug' => 'suspension'],
        ['name' => 'Впускная система', 'slug' => 'intake'],
        ['name' => 'Выхлопные системы', 'slug' => 'exhaust'],
        ['name' => 'Приемные трубы и даунпайпы', 'slug' => 'downpipes'],
        ['name' => 'Карбоновые элементы', 'slug' => 'carbon'],
        ['name' => 'Масла и жидкости', 'slug' => 'oils'],
        ['name' => 'Топливная система', 'slug' => 'fuel-system'],
        ['name' => 'Охлаждение', 'slug' => 'cooling'],
    ];
@endphp

<section class="home-categories">
    <div class="container">
        <div class="home-categories__header">
            <h2 class="home-categories__title">Категории</h2>
            <p class="home-categories__subtitle">Найдите нужные товары по категориям</p>
        </div>

        <div class="home-categories__grid">
            @foreach($categories as $category)
                <a href="{{ route('catalog') }}?category={{ $category['slug'] }}" class="home-categories__card">
                    <div class="home-categories__card-image">
                        @if($loop->first)
                            <span class="home-categories__card-badge">1023 товаров</span>
                        @endif
                        <img data-src="https://placehold.co/342x342/eaeaea/bfbfbf?text={{ urlencode($category['name']) }}" alt="{{ $category['name'] }}" class="lazy" width="342" height="342">
                    </div>
                    <div class="home-categories__card-body">
                        <span class="home-categories__card-name">{{ $category['name'] }}</span>
                        <svg class="home-categories__card-arrow" width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
