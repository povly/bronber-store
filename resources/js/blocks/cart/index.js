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

        hasItem(id) {
            return this.items.some((i) => i.id === id);
        },

        itemQty(id) {
            return this.items.find((i) => i.id === id)?.qty ?? 0;
        },

        itemTotal(id) {
            const item = this.items.find((i) => i.id === id);
            return item ? this.formatPrice(item.price * item.qty) : '';
        },

        setQty(id, qty) {
            const item = this.items.find((i) => i.id === id);
            if (item) {
                item.qty = qty;
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
