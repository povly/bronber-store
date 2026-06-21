export default function (Alpine) {
    Alpine.data('select', (opts = {}) => ({
        open: false,
        flipped: false,
        value: opts.value ?? '',
        label: opts.label ?? '',
        placeholder: opts.placeholder ?? '',

        get displayLabel() {
            return this.label || this.placeholder;
        },

        get hasValue() {
            return this.value !== '';
        },

        toggle() {
            this.open ? this.close() : this.openMenu();
        },

        openMenu() {
            this.open = true;
            this.$nextTick(() => {
                const el = this.$refs.trigger;
                if (!el) return;
                const rect = el.getBoundingClientRect();
                this.flipped = rect.bottom + 200 > window.innerHeight;
            });
        },

        close() {
            this.open = false;
        },

        choose(value, label) {
            this.value = value;
            this.label = label;
            this.close();
        },
    }));
}
