@push('block-styles')
    @vite(['resources/blocks/catalog/filters/style.css'])
@endpush

<div x-data="catalogFilters()" x-init="window.__catalogFilters = $data" @open-filters.window="openMobile()">
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
