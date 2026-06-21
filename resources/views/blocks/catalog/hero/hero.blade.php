@push('block-styles')
    @vite(['resources/css/blocks/catalog/hero/style.css'])
@endpush

@props([
    'title' => 'Каталог',
    'productCount' => 0,
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
        <nav class="catalog-hero__breadcrumb" aria-label="Breadcrumb">
            <ul class="catalog-hero__breadcrumb-list">
                <li class="catalog-hero__breadcrumb-item">
                    <a href="/" class="catalog-hero__breadcrumb-link">Главная</a>
                </li>
                <li class="catalog-hero__breadcrumb-item">
                    <span class="catalog-hero__breadcrumb-current">{{ $title }}</span>
                </li>
            </ul>
        </nav>

        <h1 class="catalog-hero__title">{{ $title }}</h1>
        <p class="catalog-hero__count catalog-hero__count--mobile">Найдено {{ $productCount }} товаров</p>

        <div class="catalog-hero__filter-btns">
            <button class="catalog-hero__filter-btn catalog-hero__filter-btn--primary" type="button" @click="$dispatch('open-filters')">
                <svg class="catalog-hero__filter-icon" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M2.5 5H17.5M5 10H15M7.5 15H12.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Фильтры
            </button>
            <div class="catalog-hero__mobile-sort" @click.away="sortOpen = false">
                <button class="catalog-hero__filter-btn catalog-hero__filter-btn--outline" type="button" @click="sortOpen = !sortOpen">
                    <svg width="17" height="13" viewBox="0 0 17 13" fill="none"><path d="M1 1l7.5 11L16 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Сортировка
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
                @foreach($activeChips as $chip)
                <button class="catalog-hero__chip" type="button" @click="removeChip({{ Js::from($chip) }})">
                    <span class="catalog-hero__chip-text">{{ $chip['label'] }}</span>
                    <svg class="catalog-hero__chip-close" width="11" height="11" viewBox="0 0 16 16" fill="none"><path d="M4 4L12 12M12 4L4 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
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
                            <svg class="catalog-hero__sort-chevron" :class="{ 'is-open': sortDropdownOpen }" width="17" height="13" viewBox="0 0 17 13" fill="none"><path d="M1 1l7.5 11L16 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
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
