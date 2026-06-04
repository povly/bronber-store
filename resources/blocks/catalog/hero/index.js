document.addEventListener('alpine:init', () => {
    Alpine.data('catalogHero', () => ({
        chips: [],
        sortOpen: false,
        currentSort: 'popular',
        sortOptions: {
            popular: 'По популярности',
            price_asc: 'Сначала дешёвые',
            price_desc: 'Сначала дорогие',
            newest: 'По новизне',
        },

        init() {
            this.chips = [
                { id: 'brand-bmw', label: 'BMW' },
                { id: 'brand-bosch', label: 'Bosch' },
                { id: 'price-range', label: 'От 500 до 5000₽' },
            ];
        },

        removeChip(index) {
            this.chips.splice(index, 1);
        },

        clearChips() {
            this.chips = [];
        },
    }));
});
