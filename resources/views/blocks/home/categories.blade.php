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
        ['name' => 'Приемные трубы и даунпайпы', 'slug' => 'downpipes'],
        ['name' => 'Выхлопные системы', 'slug' => 'exhaust'],
        ['name' => 'Карбоновые элементы', 'slug' => 'carbon'],
        ['name' => 'Масла и жидкости', 'slug' => 'oils'],
        ['name' => 'Топливная система', 'slug' => 'fuel-system'],
        ['name' => 'Охлаждение', 'slug' => 'cooling'],
    ];
@endphp

<section class="home-categories section">
    <div class="container">
        <div class="home-categories__header section__top">
            <h2 class="home-categories__title section__title">Категории</h2>
        </div>

        <div class="home-categories__grid">
            @foreach($categories as $category)
                <a href="{{ route('catalog') }}?category={{ $category['slug'] }}" class="home-categories__card">
                    <div class="home-categories__card-image img--full">
                        @if($loop->first)
                            <span class="home-categories__card-tag tag">1023 товаров</span>
                        @endif
                        <x-img path="/images/home/categories/{{ $loop->iteration }}" alt="{{ $category['name'] }}"
                               width="39" height="47"/>
                    </div>
                    <div class="home-categories__card-body">
                        <span class="home-categories__card-name">{{ $category['name'] }}</span>
                        <svg class="home-categories__card-arrow" width="18" height="12" viewBox="0 0 18 12" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M0.75 4.77298C0.335787 4.77298 7.24234e-08 5.10877 0 5.52298C-7.24234e-08 5.93719 0.335786 6.27298 0.75 6.27298L0.75 5.52298L0.75 4.77298ZM17.2803 6.05331C17.5732 5.76042 17.5732 5.28555 17.2803 4.99265L12.5074 0.219681C12.2145 -0.0732125 11.7396 -0.0732126 11.4467 0.219681C11.1538 0.512574 11.1538 0.987448 11.4467 1.28034L15.6893 5.52298L11.4467 9.76562C11.1538 10.0585 11.1538 10.5334 11.4467 10.8263C11.7396 11.1192 12.2145 11.1192 12.5074 10.8263L17.2803 6.05331ZM0.75 5.52298L0.75 6.27298L16.75 6.27298L16.75 5.52298L16.75 4.77298L0.75 4.77298L0.75 5.52298Z"
                                fill="#BFBFBF"/>
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
