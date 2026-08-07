document.addEventListener('alpine:init', () => {
    Alpine.data('about', () => ({
        active: 0,
        accActive: 0,

        yearColor(i) {
            if (i === this.active) return '#000000';
            const shades = ['#4e4e4e', '#737373', '#909090', '#bebebe'];
            return shades[Math.min(i, shades.length - 1)];
        },

        select(i) {
            this.active = i;
        },

        accToggle(i) {
            this.accActive = (this.accActive === i) ? -1 : i;
        },
    }));
});
