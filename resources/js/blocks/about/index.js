document.addEventListener('alpine:init', () => {
    Alpine.data('about', (count) => ({
        active: 0,
        count: count,

        get markerLeft() {
            if (this.count <= 1) return 0;
            return (this.active / (this.count - 1)) * 100;
        },

        yearColor(i) {
            if (i === this.active) return '#000000';
            const shades = ['#4e4e4e', '#737373', '#909090', '#bebebe'];
            return shades[Math.min(i, shades.length - 1)];
        },

        select(i) {
            this.active = i;
        },
    }));
});
