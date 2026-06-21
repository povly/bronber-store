export default function (Alpine) {
    Alpine.data('qty', (initial = 1) => ({
        qty: initial,

        inc() {
            this.qty++;
        },

        dec() {
            if (this.qty > 1) {
                this.qty--;
            }
        },
    }));
}
