{{-- Card 1: Каталог товаров --}}
<div class="catalog-filters__card">
    <h2 class="catalog-filters__heading">Каталог товаров</h2>

    <ul class="catalog-filters__category-list">
        {{-- Active category: Тормозная система (expanded) --}}
        <li>
            <div
                class="catalog-filters__category-item is-active"
                @click="toggleCategory('brakes')"
            >
                <span>Тормозная система</span>
                <svg class="catalog-filters__category-chevron is-open" :class="{ 'is-open': openCategories.brakes }" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <ul class="catalog-filters__subcategory-list" :class="{ 'is-hidden': !openCategories.brakes }">
                <li class="catalog-filters__subcategory-item">
                    <a href="#">Комплект тормозной системы</a>
                </li>
                <li class="catalog-filters__subcategory-item">
                    <a href="#">Комплекты карбон-керамической тормозной системы</a>
                </li>
                <li class="catalog-filters__subcategory-item">
                    <a href="#">Тормозные суппорты</a>
                </li>
                <li class="catalog-filters__subcategory-item">
                    <a href="#">Тормозные колодки</a>
                </li>
            </ul>
        </li>

        {{-- Collapsed: Чип-тюнинг --}}
        <li>
            <div
                class="catalog-filters__category-item"
                :class="{ 'is-active': activeCategory === 'chiptuning' }"
                @click="toggleCategory('chiptuning')"
            >
                <span class="catalog-filters__category-name">Чип-тюнинг</span>
                <svg class="catalog-filters__category-chevron" :class="{ 'is-open': openCategories.chiptuning }" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <ul class="catalog-filters__subcategory-list is-hidden" :class="{ 'is-hidden': !openCategories.chiptuning }">
                <li class="catalog-filters__subcategory-item">
                    <a href="#">Прошивки</a>
                </li>
                <li class="catalog-filters__subcategory-item">
                    <a href="#">Модули</a>
                </li>
            </ul>
        </li>

        {{-- Collapsed: Диски --}}
        <li>
            <div
                class="catalog-filters__category-item"
                :class="{ 'is-active': activeCategory === 'discs' }"
                @click="toggleCategory('discs')"
            >
                <span class="catalog-filters__category-name">Диски</span>
                <svg class="catalog-filters__category-chevron" :class="{ 'is-open': openCategories.discs }" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <ul class="catalog-filters__subcategory-list is-hidden" :class="{ 'is-hidden': !openCategories.discs }">
                <li class="catalog-filters__subcategory-item">
                    <a href="#">Литые диски</a>
                </li>
                <li class="catalog-filters__subcategory-item">
                    <a href="#">Кованые диски</a>
                </li>
            </ul>
        </li>

        {{-- Collapsed: Оптика --}}
        <li>
            <div
                class="catalog-filters__category-item"
                :class="{ 'is-active': activeCategory === 'optics' }"
                @click="toggleCategory('optics')"
            >
                <span class="catalog-filters__category-name">Оптика</span>
                <svg class="catalog-filters__category-chevron" :class="{ 'is-open': openCategories.optics }" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <ul class="catalog-filters__subcategory-list is-hidden" :class="{ 'is-hidden': !openCategories.optics }">
                <li class="catalog-filters__subcategory-item">
                    <a href="#">Фары</a>
                </li>
                <li class="catalog-filters__subcategory-item">
                    <a href="#">Противотуманки</a>
                </li>
            </ul>
        </li>
    </ul>
</div>

{{-- Card 2: Фильтры --}}
<div class="catalog-filters__card">
    <h2 class="catalog-filters__heading">Фильтры</h2>

    {{-- Section: Цена --}}
    <div class="catalog-filters__section">
        <div class="catalog-filters__section-header" @click="toggleSection('price')">
            <span class="catalog-filters__section-title">Цена, ₽</span>
            <svg class="catalog-filters__section-chevron is-open" :class="{ 'is-open': isSectionOpen('price') }" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div class="catalog-filters__section-body" :class="{ 'is-hidden': !isSectionOpen('price') }">
            <div class="catalog-filters__range">
                <div
                    class="catalog-filters__range-track"
                    x-ref="track"
                    style="--range-left: {{ $initLeftPercent }}%; --range-right: {{ $initRightPercent }}%;"
                    :style="`--range-left: ${leftPercent}%; --range-right: ${rightPercent}%`"
                    @click="onTrackClick($event)"
                >
                    <div class="catalog-filters__range-fill"></div>
                    <div
                        class="catalog-filters__range-thumb catalog-filters__range-thumb--left"
                        @mousedown="startDrag('left', $event)"
                        @touchstart="startDrag('left', $event)"
                        @click.stop
                    ></div>
                    <div
                        class="catalog-filters__range-thumb catalog-filters__range-thumb--right"
                        @mousedown="startDrag('right', $event)"
                        @touchstart="startDrag('right', $event)"
                        @click.stop
                    ></div>
                </div>
                <div class="catalog-filters__range-inputs">
                    <input
                        type="text"
                        class="catalog-filters__range-input"
                        value="{{ $priceMin ?? $rangeMin }}"
                        x-model="priceMin"
                        @change="onPriceChange('min')"
                        @keydown.enter.prevent="$el.blur()"
                        inputmode="numeric"
                    >
                    <span class="catalog-filters__range-separator">—</span>
                    <input
                        type="text"
                        class="catalog-filters__range-input"
                        value="{{ $priceMax ?? $rangeMax }}"
                        x-model="priceMax"
                        @change="onPriceChange('max')"
                        @keydown.enter.prevent="$el.blur()"
                        inputmode="numeric"
                    >
                </div>
            </div>
        </div>
    </div>

    {{-- Section: Бренд --}}
    <div class="catalog-filters__section">
        <div class="catalog-filters__section-header" @click="toggleSection('brand')">
            <span class="catalog-filters__section-title">Бренд</span>
            <svg class="catalog-filters__section-chevron is-open" :class="{ 'is-open': isSectionOpen('brand') }" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div class="catalog-filters__section-body" :class="{ 'is-hidden': !isSectionOpen('brand') }">
            <div class="catalog-filters__search">
                <input
                    type="text"
                    class="catalog-filters__search-input"
                    placeholder="Поиск по названию"
                    x-model="brandSearch"
                >
            </div>
            <div class="catalog-filters__checkbox-list">
                @foreach($allBrands as $brand)
                <label class="catalog-filters__checkbox" @if($loop->index >= $visibleCount) style="display:none;" @endif x-show="isBrandVisible({{ $brand['id'] }})" @click.prevent="toggleBrand({{ $brand['id'] }})">
                    <input type="checkbox" value="{{ $brand['name'] }}" class="catalog-filters__input-hidden" {{ $brand['checked'] ? 'checked' : '' }} x-effect="$el.checked = getBrand({{ $brand['id'] }})?.checked ?? false">
                    <span class="catalog-filters__checkbox-box {{ $brand['checked'] ? 'is-checked' : '' }}" :class="{ 'is-checked': getBrand({{ $brand['id'] }})?.checked }">
                        <svg class="catalog-filters__checkbox-icon" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                            <path d="M2 6l3 3 5-5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="catalog-filters__checkbox-label">{{ $brand['name'] }}</span>
                    <span class="catalog-filters__checkbox-count">({{ $brand['count'] }})</span>
                </label>
                @endforeach
            </div>
            @if(count($allBrands) > $visibleCount)
            <button type="button" class="catalog-filters__show-more" x-show="filteredBrands.length > visibleCount" @click="brandShowAll = !brandShowAll" x-text="brandShowAll ? 'Скрыть' : `Показать еще (${filteredBrands.length - visibleCount})`">Показать еще ({{ count($allBrands) - $visibleCount }})</button>
            @endif
        </div>
    </div>

    {{-- Section: Наличие --}}
    <div class="catalog-filters__section">
        <div class="catalog-filters__section-header" @click="toggleSection('availability')">
            <span class="catalog-filters__section-title">Наличие</span>
            <svg class="catalog-filters__section-chevron {{ $availabilityOpen ? 'is-open' : '' }}" :class="{ 'is-open': isSectionOpen('availability') }" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div class="catalog-filters__section-body {{ !$availabilityOpen ? 'is-hidden' : '' }}" :class="{ 'is-hidden': !isSectionOpen('availability') }">
            <div class="catalog-filters__checkbox-list">
                <label class="catalog-filters__checkbox" @click.prevent="toggleAvailability('in_stock')">
                    <input type="checkbox" value="in_stock" class="catalog-filters__input-hidden" {{ $availability['in_stock'] ? 'checked' : '' }} x-effect="$el.checked = availability.in_stock">
                    <span class="catalog-filters__checkbox-box {{ $availability['in_stock'] ? 'is-checked' : '' }}" :class="{ 'is-checked': availability.in_stock }">
                        <svg class="catalog-filters__checkbox-icon" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                            <path d="M2 6l3 3 5-5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="catalog-filters__checkbox-label">В наличии</span>
                </label>
                <label class="catalog-filters__checkbox" @click.prevent="toggleAvailability('to_order')">
                    <input type="checkbox" value="to_order" class="catalog-filters__input-hidden" {{ $availability['to_order'] ? 'checked' : '' }} x-effect="$el.checked = availability.to_order">
                    <span class="catalog-filters__checkbox-box {{ $availability['to_order'] ? 'is-checked' : '' }}" :class="{ 'is-checked': availability.to_order }">
                        <svg class="catalog-filters__checkbox-icon" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                            <path d="M2 6l3 3 5-5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="catalog-filters__checkbox-label">Под заказ</span>
                </label>
            </div>
        </div>
    </div>

    {{-- Section: Совместимость --}}
    <div class="catalog-filters__section">
        <div class="catalog-filters__section-header" @click="toggleSection('compatibility')">
            <span class="catalog-filters__section-title">Совместимость</span>
            <svg class="catalog-filters__section-chevron is-open" :class="{ 'is-open': isSectionOpen('compatibility') }" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div class="catalog-filters__section-body" :class="{ 'is-hidden': !isSectionOpen('compatibility') }">
            <div class="catalog-filters__selects">
                <div class="catalog-filters__select-group">
                    <div class="catalog-filters__custom-select" x-ref="selectMark" @click.away="closeSelect('mark')">
                        <button type="button" class="catalog-filters__custom-select-trigger" @click="toggleSelect('mark')">
                            <span class="catalog-filters__select-label">Марка</span>
                            <span class="catalog-filters__select-value" x-text="getSelectedLabel('mark')">{{ $selectLabel('mark') }}</span>
                            <svg class="catalog-filters__custom-select-chevron" :class="{ 'is-open': selectOpen.mark }" width="12" height="8" viewBox="0 0 12 8" fill="none"><path d="M1 1.5L6 6.5L11 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <div class="catalog-filters__custom-select-dropdown" style="display:none;" :class="{ 'is-above': selectFlip.mark }" x-show="selectOpen.mark" x-transition>
                            @foreach($allSelectOptions['mark'] as $option)
                            <button type="button" class="catalog-filters__custom-select-option {{ $compatibility['mark'] === $option['value'] ? 'is-active' : '' }}" :class="{ 'is-active': compatibility.mark === '{{ $option['value'] }}' }" @click="selectOption('mark', '{{ $option['value'] }}')">{{ $option['label'] }}</button>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="catalog-filters__select-group">
                    <div class="catalog-filters__custom-select" x-ref="selectModel" @click.away="closeSelect('model')">
                        <button type="button" class="catalog-filters__custom-select-trigger" @click="toggleSelect('model')">
                            <span class="catalog-filters__select-label">Модель</span>
                            <span class="catalog-filters__select-value" x-text="getSelectedLabel('model')">{{ $selectLabel('model') }}</span>
                            <svg class="catalog-filters__custom-select-chevron" :class="{ 'is-open': selectOpen.model }" width="12" height="8" viewBox="0 0 12 8" fill="none"><path d="M1 1.5L6 6.5L11 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <div class="catalog-filters__custom-select-dropdown" style="display:none;" :class="{ 'is-above': selectFlip.model }" x-show="selectOpen.model" x-transition>
                            @foreach($allSelectOptions['model'] as $option)
                            <button type="button" class="catalog-filters__custom-select-option {{ $compatibility['model'] === $option['value'] ? 'is-active' : '' }}" :class="{ 'is-active': compatibility.model === '{{ $option['value'] }}' }" @click="selectOption('model', '{{ $option['value'] }}')">{{ $option['label'] }}</button>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="catalog-filters__select-group">
                    <div class="catalog-filters__custom-select" x-ref="selectGeneration" @click.away="closeSelect('generation')">
                        <button type="button" class="catalog-filters__custom-select-trigger" @click="toggleSelect('generation')">
                            <span class="catalog-filters__select-label">Поколение</span>
                            <span class="catalog-filters__select-value" x-text="getSelectedLabel('generation')">{{ $selectLabel('generation') }}</span>
                            <svg class="catalog-filters__custom-select-chevron" :class="{ 'is-open': selectOpen.generation }" width="12" height="8" viewBox="0 0 12 8" fill="none"><path d="M1 1.5L6 6.5L11 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <div class="catalog-filters__custom-select-dropdown" style="display:none;" :class="{ 'is-above': selectFlip.generation }" x-show="selectOpen.generation" x-transition>
                            @foreach($allSelectOptions['generation'] as $option)
                            <button type="button" class="catalog-filters__custom-select-option {{ $compatibility['generation'] === $option['value'] ? 'is-active' : '' }}" :class="{ 'is-active': compatibility.generation === '{{ $option['value'] }}' }" @click="selectOption('generation', '{{ $option['value'] }}')">{{ $option['label'] }}</button>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="catalog-filters__select-group">
                    <div class="catalog-filters__custom-select" x-ref="selectEngine" @click.away="closeSelect('engine')">
                        <button type="button" class="catalog-filters__custom-select-trigger" @click="toggleSelect('engine')">
                            <span class="catalog-filters__select-label">Двигатель</span>
                            <span class="catalog-filters__select-value" x-text="getSelectedLabel('engine')">{{ $selectLabel('engine') }}</span>
                            <svg class="catalog-filters__custom-select-chevron" :class="{ 'is-open': selectOpen.engine }" width="12" height="8" viewBox="0 0 12 8" fill="none"><path d="M1 1.5L6 6.5L11 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <div class="catalog-filters__custom-select-dropdown" style="display:none;" :class="{ 'is-above': selectFlip.engine }" x-show="selectOpen.engine" x-transition>
                            @foreach($allSelectOptions['engine'] as $option)
                            <button type="button" class="catalog-filters__custom-select-option {{ $compatibility['engine'] === $option['value'] ? 'is-active' : '' }}" :class="{ 'is-active': compatibility.engine === '{{ $option['value'] }}' }" @click="selectOption('engine', '{{ $option['value'] }}')">{{ $option['label'] }}</button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Action buttons --}}
    <div class="catalog-filters__actions">
        <button type="button" class="catalog-filters__btn catalog-filters__btn--reset" @click="resetFilters()">
            Сбросить
        </button>
        <button type="button" class="catalog-filters__btn catalog-filters__btn--apply" @click="applyFilters()">
            Применить
        </button>
    </div>
</div>
