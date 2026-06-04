@push('block-styles')
    @vite(['resources/blocks/catalog/filters/style.css'])
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
    ];
    $defaultSelectOptions = [
        'mark' => [['value' => '', 'label' => 'Выберите марку'], ['value' => 'BMW', 'label' => 'BMW'], ['value' => 'Audi', 'label' => 'Audi'], ['value' => 'Mercedes', 'label' => 'Mercedes'], ['value' => 'Volkswagen', 'label' => 'Volkswagen']],
        'model' => [['value' => '', 'label' => 'Выберите модель'], ['value' => '5 серия', 'label' => '5 серия'], ['value' => '3 серия', 'label' => '3 серия'], ['value' => '7 серия', 'label' => '7 серия'], ['value' => 'X5', 'label' => 'X5']],
        'generation' => [['value' => '', 'label' => 'Выберите поколение'], ['value' => 'G30', 'label' => 'G30'], ['value' => 'F10', 'label' => 'F10'], ['value' => 'E60', 'label' => 'E60']],
        'engine' => [['value' => '', 'label' => 'Выберите двигатель'], ['value' => '2.0d', 'label' => '2.0d'], ['value' => '3.0i', 'label' => '3.0i'], ['value' => '530d', 'label' => '530d'], ['value' => '540i', 'label' => '540i']],
    ];
    $allBrands = $brands ?: $defaultBrands;
    $allSelectOptions = $selectOptions ?: $defaultSelectOptions;
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

    {{-- Mobile trigger button --}}
    <button type="button" class="catalog-filters__mobile-trigger" @click="openMobile()">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M3 5h14M3 10h14M3 15h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        Фильтры
    </button>

    {{-- Desktop sidebar --}}
    <aside class="catalog-filters">
        @include('catalog.filters._content')
    </aside>
</div>
