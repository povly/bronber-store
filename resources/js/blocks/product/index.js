document.addEventListener('alpine:init', () => {
    Alpine.data('product', () => ({
        qty: 1,
        tab: 'description',
        favorited: false,

        inc() {
            this.qty++;
        },

        dec() {
            if (this.qty > 1) {
                this.qty--;
            }
        },
    }));
});
