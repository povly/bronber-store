@push('block-styles')
    @vite(['resources/css/blocks/catalog/hero/style.css'])
@endpush

@props([
    'title' => 'Каталог',
    'productCount' => 148,
    'currentSort' => 'popular',
    'sortOptions' => [],
    'activeChips' => [],
])

@php
    $defaultSortOptions = [
        'popular' => 'По популярности',
        'price_asc' => 'Сначала дешёвые',
        'price_desc' => 'Сначала дорогие',
        'newest' => 'По новизне',
    ];
    $allSortOptions = $sortOptions ?: $defaultSortOptions;

    // Override sort from URL so it shows correct text immediately
    if (request()->filled('sort')) {
        $currentSort = request()->input('sort');
    }

    // Build active chips from URL parameters (fully server-side)
    $activeChips = [];

    // Brands
    if (request()->filled('brand')) {
        foreach (explode(',', request()->input('brand')) as $brandName) {
            $brandName = trim($brandName);
            if ($brandName !== '') {
                $activeChips[] = [
                    'id' => 'brand-' . $brandName,
                    'label' => $brandName,
                    'type' => 'brand',
                    'value' => $brandName,
                ];
            }
        }
    }

    // Price range
    $rangeMin = 0;
    $rangeMax = 10000;
    $initPriceMin = request()->filled('price_min') ? (int) request()->input('price_min') : $rangeMin;
    $initPriceMax = request()->filled('price_max') ? (int) request()->input('price_max') : $rangeMax;
    if ($initPriceMin > $rangeMin || $initPriceMax < $rangeMax) {
        $activeChips[] = [
            'id' => 'price-range',
            'label' => 'От ' . $initPriceMin . ' до ' . $initPriceMax . '₽',
            'type' => 'price',
        ];
    }

    // Availability
    if (request()->filled('availability')) {
        $availabilityLabels = ['in_stock' => 'В наличии', 'to_order' => 'Под заказ'];
        foreach (explode(',', request()->input('availability')) as $key) {
            $key = trim($key);
            if (isset($availabilityLabels[$key])) {
                $activeChips[] = [
                    'id' => 'avail-' . $key,
                    'label' => $availabilityLabels[$key],
                    'type' => 'availability',
                    'value' => $key,
                ];
            }
        }
    }

    // Compatibility (mark, model, generation, engine)
    $compatLabels = ['mark' => 'Марка', 'model' => 'Модель', 'generation' => 'Поколение', 'engine' => 'Двигатель'];
    foreach ($compatLabels as $field => $label) {
        if (request()->filled($field)) {
            $activeChips[] = [
                'id' => 'compat-' . $field,
                'label' => $label . ': ' . request()->input($field),
                'type' => 'compatibility',
                'field' => $field,
            ];
        }
    }
@endphp

<div class="catalog-hero" x-data="catalogHero({{ Js::from([
    'currentSort' => $currentSort,
    'sortOptions' => $allSortOptions,
]) }})" x-init="init()">
    <div class="container">
        <x-breadcrumbs
            class="catalog-hero__breadcrumb"
            :items="[['label' => 'Главная', 'url' => '/'], ['label' => $title]]"
        />

        <h1 class="catalog-hero__title">{{ $title }}</h1>
        <p class="catalog-hero__count catalog-hero__count--mobile">Найдено {{ $productCount }} товаров</p>

        <div class="catalog-hero__filter-btns">
            <button class="catalog-hero__filter-btn catalog-hero__filter-btn--primary" type="button" @click="$dispatch('open-filters')">
                <span>Фильтры</span>
            </button>
            <div class="catalog-hero__mobile-sort" @click.away="sortOpen = false">
                <button class="catalog-hero__filter-btn catalog-hero__filter-btn--outline" type="button" @click="sortOpen = !sortOpen">
                    <span>Сортировка</span>
                    <svg width="17" height="13" viewBox="0 0 17 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M7.46967 9.53033C7.76256 9.82322 8.23744 9.82322 8.53033 9.53033L13.3033 4.75736C13.5962 4.46447 13.5962 3.98959 13.3033 3.6967C13.0104 3.40381 12.5355 3.40381 12.2426 3.6967L8 7.93934L3.75736 3.6967C3.46447 3.40381 2.98959 3.40381 2.6967 3.6967C2.40381 3.98959 2.40381 4.46447 2.6967 4.75736L7.46967 9.53033ZM8 8L7.25 8L7.25 9L8 9L8.75 9L8.75 8L8 8Z" fill="#BFBFBF" />
                    </svg>
                </button>
                <div class="catalog-hero__mobile-sort-dropdown" style="display:none;" x-show="sortOpen" x-transition>
                    @foreach($allSortOptions as $key => $label)
                    <button class="catalog-hero__mobile-sort-option {{ $currentSort === $key ? 'is-active' : '' }}" :class="{ 'is-active': currentSort === '{{ $key }}' }" type="button" @click="currentSort = '{{ $key }}'; sortOpen = false; $nextTick(() => submitForm())">
                        <span>{{ $label }}</span>
                    </button>
                    @endforeach
                </div>
            </div>
        </div>

        @if(!empty($activeChips))
        <div class="catalog-hero__chips">
            <div class="catalog-hero__chips-scroll">
                @foreach($activeChips as $key => $chip)
                <button class="catalog-hero__chip" type="button" @click="removeChip({{ Js::from($chip) }})">
                    <span class="catalog-hero__chip-text">{{ $chip['label'] }}</span>
                    <svg class="catalog-hero__chip-close" width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_755_2876{{$key}})">
                    <path d="M10.0832 0.916656L0.916504 10.0833" stroke="#7212BC" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M0.916504 0.916656L10.0832 10.0833" stroke="#7212BC" stroke-linecap="round" stroke-linejoin="round"/>
                    </g>
                    <defs>
                    <clipPath id="clip0_755_2876{{$key}}">
                    <rect width="11" height="11" fill="white"/>
                    </clipPath>
                    </defs>
                    </svg>
                </button>
                @endforeach
                <button class="catalog-hero__chips-clear" type="button" @click="clearChips()">Сбросить</button>
            </div>
        </div>
        @endif

        <div class="catalog-hero__desktop-bar">
            <p class="catalog-hero__count">Найдено {{ $productCount }} товаров</p>

            <div class="catalog-hero__desktop-bar-right">
                @if(!empty($activeChips))
                <div class="catalog-hero__desktop-bar-filters">
                    <span class="catalog-hero__desktop-bar-label">Фильтры:</span>
                    <div class="catalog-hero__desktop-bar-chips">
                        @foreach($activeChips as $chip)
                        <button class="catalog-hero__desktop-chip" type="button" @click="removeChip({{ Js::from($chip) }})">
                            <span class="catalog-hero__desktop-chip-text">{{ $chip['label'] }}</span>
                            <svg class="catalog-hero__desktop-chip-close" width="11" height="11" viewBox="0 0 16 16" fill="none"><path d="M4 4L12 12M12 4L4 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        @endforeach
                        <button class="catalog-hero__chips-clear" type="button" @click="clearChips()">Сбросить</button>
                    </div>
                </div>
                @endif

                <div class="catalog-hero__desktop-bar-sort">
                    <input type="hidden" name="sort" :value="currentSort" :disabled="!currentSort">
                    <span class="catalog-hero__desktop-bar-label">Сортировка:</span>
                    <div class="catalog-hero__sort" @click.away="sortDropdownOpen = false">
                        <button class="catalog-hero__sort-trigger" type="button" @click="sortDropdownOpen = !sortDropdownOpen">
                        <span class="catalog-hero__sort-value" x-text="sortOptions[currentSort]">{{ $allSortOptions[$currentSort] ?? '' }}</span>
                            <svg class="catalog-hero__sort-chevron" :class="{ 'is-open': sortDropdownOpen }" width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M4.99311 6.05335C5.286 6.34625 5.76087 6.34625 6.05377 6.05335L10.8267 1.28038C11.1196 0.987488 11.1196 0.512614 10.8267 0.219721C10.5338 -0.0731719 10.059 -0.0731719 9.76608 0.219721L5.52344 4.46236L1.2808 0.219721C0.987904 -0.0731723 0.51303 -0.0731723 0.220137 0.219721C-0.0727566 0.512614 -0.0727566 0.987488 0.220137 1.28038L4.99311 6.05335ZM5.52344 4.74695L4.77344 4.74695L4.77344 5.52302L5.52344 5.52302L6.27344 5.52302L6.27344 4.74695L5.52344 4.74695Z" fill="#BFBFBF" />
                            </svg>
                        </button>
                        <div class="catalog-hero__sort-dropdown" x-show="sortDropdownOpen" x-transition style="display:none;">
                            @foreach($allSortOptions as $key => $label)
                            <button class="catalog-hero__sort-option {{ $currentSort === $key ? 'is-active' : '' }}" :class="{ 'is-active': currentSort === '{{ $key }}' }" type="button" @click="currentSort = '{{ $key }}'; sortDropdownOpen = false; $nextTick(() => submitForm())">
                                <span>{{ $label }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
