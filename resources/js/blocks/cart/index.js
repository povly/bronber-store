document.addEventListener('alpine:init', () => {
    Alpine.data('cart', (initial = { items: [] }) => ({
        items: initial.items ?? [],

        get itemsCount() {
            return this.items.reduce((sum, item) => sum + item.qty, 0);
        },

        get subtotal() {
            return this.items.reduce((sum, item) => sum + item.price * item.qty, 0);
        },

        get total() {
            return this.subtotal;
        },

        formatPrice(price) {
            return new Intl.NumberFormat('ru-RU', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            }).format(price) + ' ₽';
        },

        inc(id) {
            const item = this.items.find((i) => i.id === id);
            if (item) {
                item.qty++;
            }
        },

        dec(id) {
            const item = this.items.find((i) => i.id === id);
            if (item && item.qty > 1) {
                item.qty--;
            }
        },

        remove(id) {
            this.items = this.items.filter((i) => i.id !== id);
        },

        clear() {
            if (confirm('Очистить корзину?')) {
                this.items = [];
            }
        },
    }));
});
