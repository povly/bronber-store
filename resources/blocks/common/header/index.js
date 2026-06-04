document.addEventListener('alpine:init', () => {
    Alpine.data('storeHeader', () => ({
        menuOpen: false,

        toggleMenu() {
            this.menuOpen = !this.menuOpen;
            document.body.style.overflow = this.menuOpen ? 'hidden' : '';
        },

        closeMenu() {
            this.menuOpen = false;
            document.body.style.overflow = '';
        },
    }));
});
