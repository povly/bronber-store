@push('block-styles')
    @vite(['resources/blocks/catalog/pagination/style.css'])
@endpush

<div class="catalog-pagination">
    <div class="catalog-pagination__show-more">
        <button type="button">Показать еще</button>
    </div>

    <div class="catalog-pagination__pages">
        <button type="button" class="catalog-pagination__arrow catalog-pagination__arrow--prev" aria-label="Предыдущая страница">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </button>

        <button type="button" class="catalog-pagination__page catalog-pagination__page--active">1</button>
        <button type="button" class="catalog-pagination__page">2</button>
        <button type="button" class="catalog-pagination__page">3</button>
        <span class="catalog-pagination__page catalog-pagination__page--dots">...</span>
        <button type="button" class="catalog-pagination__page">11</button>

        <button type="button" class="catalog-pagination__arrow catalog-pagination__arrow--next" aria-label="Следующая страница">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 6 15 12 9 18"></polyline>
            </svg>
        </button>
    </div>
</div>
