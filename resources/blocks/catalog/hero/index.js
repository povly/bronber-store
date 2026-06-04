document.addEventListener('alpine:init', () => {
    Alpine.data('catalogHero', (data = {}) => ({
        chips: [],
        sortOpen: false,
        sortDropdownOpen: false,
        currentSort: data.currentSort,
        _ready: false,
        sortOptions: data.sortOptions,

        init() {
            // Read sort from URL
            const params = new URLSearchParams(window.location.search);
            if (params.has('sort') && params.get('sort') !== '') {
                this.currentSort = params.get('sort');
            }

            // Listen for chips from filters
            window.addEventListener('filters-chips', (e) => {
                this.chips = e.detail.chips || [];
            });

            this.$nextTick(() => { this._ready = true; });
        },

        removeChip(index) {
            const chip = this.chips[index];
            if (!chip) return;
            window.dispatchEvent(new CustomEvent('chip-remove', { detail: chip }));
            this.chips.splice(index, 1);
        },

        clearChips() {
            window.dispatchEvent(new CustomEvent('chip-clear'));
            this.chips = [];
        },

        submitForm() {
            if (!this._ready) return;
            const form = this.$el.closest('form');
            if (!form) return;
            form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
        },
    }));
});
