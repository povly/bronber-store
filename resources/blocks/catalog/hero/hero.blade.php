@push('block-styles')
    @vite(['resources/blocks/catalog/hero/style.css'])
@endpush

@props([
    'title' => 'Каталог',
    'productCount' => 0,
    'currentSort' => 'popular',
    'sortOptions' => [],
    'activeChips' => [],
])

<div class="catalog-hero" x-data="catalogHero({{ Js::from([
    'currentSort' => $currentSort,
    'sortOptions' => $sortOptions ?: ['popular' => 'По популярности', 'price_asc' => 'Сначала дешёвые', 'price_desc' => 'Сначала дорогие', 'newest' => 'По новизне'],
    'chips' => $activeChips,
]) }})" x-init="init()">
    <div class="container">
        <nav class="catalog-hero__breadcrumb" aria-label="Breadcrumb">
            <ul class="catalog-hero__breadcrumb-list">
                <li class="catalog-hero__breadcrumb-item">
                    <a href="/" class="catalog-hero__breadcrumb-link">Главная</a>
                </li>
                <li class="catalog-hero__breadcrumb-item">
                    <a href="/catalog" class="catalog-hero__breadcrumb-link">Каталог</a>
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
                <div class="catalog-hero__mobile-sort-dropdown" x-show="sortOpen" x-transition>
                    <template x-for="(label, key) in sortOptions" :key="key">
                        <button class="catalog-hero__mobile-sort-option" :class="{ 'is-active': currentSort === key }" type="button" @click="currentSort = key; sortOpen = false; $nextTick(() => submitForm())">
                            <span x-text="label"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <div class="catalog-hero__chips" x-show="chips.length > 0">
            <div class="catalog-hero__chips-scroll">
                <template x-for="(chip, index) in chips" :key="chip.id">
                    <button class="catalog-hero__chip" type="button" @click="removeChip(index)">
                        <span class="catalog-hero__chip-text" x-text="chip.label"></span>
                        <svg class="catalog-hero__chip-close" width="11" height="11" viewBox="0 0 16 16" fill="none"><path d="M4 4L12 12M12 4L4 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </template>
                <button class="catalog-hero__chips-clear" type="button" x-show="chips.length > 0" @click="clearChips()">Сбросить</button>
            </div>
        </div>

        <div class="catalog-hero__desktop-bar">
            <p class="catalog-hero__count">Найдено {{ $productCount }} товаров</p>

            <div class="catalog-hero__desktop-bar-right">
                <template x-if="chips.length > 0">
                    <div class="catalog-hero__desktop-bar-filters">
                        <span class="catalog-hero__desktop-bar-label">Фильтры:</span>
                        <div class="catalog-hero__desktop-bar-chips">
                            <template x-for="(chip, index) in chips" :key="chip.id">
                                <button class="catalog-hero__desktop-chip" type="button" @click="removeChip(index)">
                                    <span class="catalog-hero__desktop-chip-text" x-text="chip.label"></span>
                                    <svg class="catalog-hero__desktop-chip-close" width="11" height="11" viewBox="0 0 16 16" fill="none"><path d="M4 4L12 12M12 4L4 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </template>
                            <button class="catalog-hero__chips-clear" type="button" @click="clearChips()">Сбросить</button>
                        </div>
                    </div>
                </template>

                <div class="catalog-hero__desktop-bar-sort">
                    <input type="hidden" name="sort" :value="currentSort" :disabled="!currentSort">
                    <span class="catalog-hero__desktop-bar-label">Сортировка:</span>
                    <div class="catalog-hero__sort" @click.away="sortDropdownOpen = false">
                        <button class="catalog-hero__sort-trigger" type="button" @click="sortDropdownOpen = !sortDropdownOpen">
                            <span class="catalog-hero__sort-value" x-text="sortOptions[currentSort]"></span>
                            <svg class="catalog-hero__sort-chevron" :class="{ 'is-open': sortDropdownOpen }" width="17" height="13" viewBox="0 0 17 13" fill="none"><path d="M1 1l7.5 11L16 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <div class="catalog-hero__sort-dropdown" x-show="sortDropdownOpen" x-transition>
                            <template x-for="(label, key) in sortOptions" :key="key">
                                <button class="catalog-hero__sort-option" :class="{ 'is-active': currentSort === key }" type="button" @click="currentSort = key; sortDropdownOpen = false; $nextTick(() => submitForm())">
                                    <span x-text="label"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
