@push('block-styles')
    @vite(['resources/css/blocks/catalog/filters/style.css', 'resources/js/blocks/catalog/hero/index.js', 'resources/js/blocks/catalog/filters/index.js'])
@endpush

@props([
    'brands' => [],
    'selectOptions' => [],
    'compatibility' => ['mark' => '', 'model' => '', 'generation' => '', 'engine' => ''],
    'priceMin' => null,
    'priceMax' => null,
    'rangeMin' => 0,
    'rangeMax' => 10000,
    'availability' => ['in_stock' => false, 'to_order' => false],
    'visibleCount' => 4,
])

@php
    $defaultBrands = [
        ['id' => 1, 'name' => 'Bosch', 'count' => 24, 'checked' => false],
        ['id' => 2, 'name' => 'DeatschWerks', 'count' => 15, 'checked' => false],
        ['id' => 3, 'name' => 'Denso', 'count' => 21, 'checked' => false],
        ['id' => 4, 'name' => 'Valeo', 'count' => 6, 'checked' => false],
        ['id' => 5, 'name' => 'Continental', 'count' => 8, 'checked' => false],
        ['id' => 6, 'name' => 'Delphi', 'count' => 11, 'checked' => false],
        ['id' => 7, 'name' => 'Pierburg', 'count' => 4, 'checked' => false],
        ['id' => 8, 'name' => 'Mando', 'count' => 6, 'checked' => false],
    ];
    $defaultSelectOptions = [
        'mark' => [['value' => '', 'label' => 'Выберите марку'], ['value' => 'BMW', 'label' => 'BMW'], ['value' => 'Audi', 'label' => 'Audi'], ['value' => 'Mercedes', 'label' => 'Mercedes'], ['value' => 'Volkswagen', 'label' => 'Volkswagen']],
        'model' => [['value' => '', 'label' => 'Выберите модель'], ['value' => '5 серия', 'label' => '5 серия'], ['value' => '3 серия', 'label' => '3 серия'], ['value' => '7 серия', 'label' => '7 серия'], ['value' => 'X5', 'label' => 'X5']],
        'generation' => [['value' => '', 'label' => 'Выберите поколение'], ['value' => 'G30', 'label' => 'G30'], ['value' => 'F10', 'label' => 'F10'], ['value' => 'E60', 'label' => 'E60']],
        'engine' => [['value' => '', 'label' => 'Выберите двигатель'], ['value' => '2.0d', 'label' => '2.0d'], ['value' => '3.0i', 'label' => '3.0i'], ['value' => '530d', 'label' => '530d'], ['value' => '540i', 'label' => '540i']],
    ];
    $allBrands = $brands ?: $defaultBrands;
    $allSelectOptions = $selectOptions ?: $defaultSelectOptions;

    // --- Read ALL filter state from URL for server-side rendering ---

    // Price
    $priceMin = request()->filled('price_min') ? (int) request()->input('price_min') : ($priceMin ?? $rangeMin);
    $priceMax = request()->filled('price_max') ? (int) request()->input('price_max') : ($priceMax ?? $rangeMax);

    // Brands
    $activeBrandNames = request()->filled('brand')
        ? array_filter(array_map('trim', explode(',', request()->input('brand'))))
        : [];
    foreach ($allBrands as &$brand) {
        $brand['checked'] = in_array($brand['name'], $activeBrandNames);
    }
    unset($brand);

    // Availability
    $activeAvailabilityKeys = request()->filled('availability')
        ? array_filter(array_map('trim', explode(',', request()->input('availability'))))
        : [];
    $availability = [
        'in_stock' => in_array('in_stock', $activeAvailabilityKeys),
        'to_order' => in_array('to_order', $activeAvailabilityKeys),
    ];

    // Compatibility
    $compatibility = [
        'mark' => request()->filled('mark') ? request()->input('mark') : ($compatibility['mark'] ?? ''),
        'model' => request()->filled('model') ? request()->input('model') : ($compatibility['model'] ?? ''),
        'generation' => request()->filled('generation') ? request()->input('generation') : ($compatibility['generation'] ?? ''),
        'engine' => request()->filled('engine') ? request()->input('engine') : ($compatibility['engine'] ?? ''),
    ];

    // Helper: find select label by value
    $selectLabel = function (string $field) use ($allSelectOptions, $compatibility): string {
        foreach ($allSelectOptions[$field] ?? [] as $opt) {
            if ($opt['value'] === ($compatibility[$field] ?? '')) {
                return $opt['label'];
            }
        }
        return $allSelectOptions[$field][0]['label'] ?? '';
    };

    // Range slider CSS percentages
    $initLeftPercent = $rangeMax > $rangeMin ? (($priceMin - $rangeMin) / ($rangeMax - $rangeMin)) * 100 : 0;
    $initRightPercent = $rangeMax > $rangeMin ? (($priceMax - $rangeMin) / ($rangeMax - $rangeMin)) * 100 : 100;

    // Whether availability section should start open
    $availabilityOpen = !empty($activeAvailabilityKeys);
@endphp

<div x-data="catalogFilters({{ Js::from([
    'brands' => $allBrands,
    'selectOptions' => $allSelectOptions,
    'compatibility' => $compatibility,
    'priceMin' => $priceMin ?? $rangeMin,
    'priceMax' => $priceMax ?? $rangeMax,
    'rangeMin' => $rangeMin,
    'rangeMax' => $rangeMax,
    'availability' => $availability,
    'visibleCount' => $visibleCount,
    'openSections' => [
        'price' => true,
        'brand' => true,
        'availability' => $availabilityOpen,
        'compatibility' => true,
    ],
    'openCategories' => ['brakes' => true],
    'activeCategory' => 'brakes',
]) }})" x-init="init()" @open-filters.window="openMobile()">
    {{-- Hidden inputs — single instance for form submission --}}
    <input type="hidden" name="price_min" :value="priceMin" :disabled="priceMin <= rangeMin">
    <input type="hidden" name="price_max" :value="priceMax" :disabled="priceMax >= rangeMax">
    <input type="hidden" name="mark" :value="compatibility.mark" :disabled="!compatibility.mark">
    <input type="hidden" name="model" :value="compatibility.model" :disabled="!compatibility.model">
    <input type="hidden" name="generation" :value="compatibility.generation" :disabled="!compatibility.generation">
    <input type="hidden" name="engine" :value="compatibility.engine" :disabled="!compatibility.engine">
    <input type="hidden" name="brand" :value="checkedBrandNames" :disabled="!checkedBrandNames">
    <input type="hidden" name="availability" :value="checkedAvailabilityKeys" :disabled="!checkedAvailabilityKeys">

    {{-- Mobile overlay backdrop --}}
    <div
        class="catalog-filters-overlay"
        :class="{ 'is-open': mobileOpen }"
        @click="closeMobile()"
    ></div>

    {{-- Mobile slide-in panel --}}
    <div
        class="catalog-filters-panel"
        :class="{ 'is-open': mobileOpen }"
        x-show="mobileOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="transform translate-y-full"
        x-transition:enter-end="transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="transform translate-y-0"
        x-transition:leave-end="transform translate-y-full"
    >
        <div class="catalog-filters__mobile-header">
            <span class="catalog-filters__mobile-title">Фильтры</span>
            <button type="button" class="catalog-filters__mobile-close" @click="closeMobile()" aria-label="Закрыть фильтры">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
        <div class="catalog-filters__mobile-body">
            @include('catalog.filters._content')
        </div>
    </div>

    {{-- Desktop sidebar --}}
    <aside class="catalog-filters">
        @include('catalog.filters._content')
    </aside>
</div>
