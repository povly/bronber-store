document.addEventListener('alpine:init', () => {
    Alpine.data('catalogHero', (data = {}) => ({
        sortOpen: false,
        sortDropdownOpen: false,
        currentSort: data.currentSort,
        _ready: false,
        sortOptions: data.sortOptions,

        init() {
            this.$nextTick(() => { this._ready = true; });
        },

        removeChip(chip) {
            window.dispatchEvent(new CustomEvent('chip-remove', { detail: chip }));
        },

        clearChips() {
            window.dispatchEvent(new CustomEvent('chip-clear'));
        },

        submitForm() {
            if (!this._ready) return;
            const form = this.$el.closest('form');
            if (!form) return;
            form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
        },
    }));
});
