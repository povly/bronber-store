document.addEventListener('alpine:init', () => {
    Alpine.store('catalogMenu', {
        isOpen: false,
        activeCategory: null,
        openMobile: {},
        closeTimer: null,

        cancelClose() {
            if (this.closeTimer) {
                clearTimeout(this.closeTimer);
                this.closeTimer = null;
            }
        },

        scheduleClose() {
            this.cancelClose();
            this.closeTimer = setTimeout(() => {
                this.close();
                this.closeTimer = null;
            }, 200);
        },

        open() {
            this.cancelClose();
            this.isOpen = true;
            console.debug('[catalog-menu] open');
        },

        openMobile() {
            this.cancelClose();
            this.isOpen = true;
            console.debug('[catalog-menu] open (mobile)');
        },

        close() {
            this.cancelClose();
            this.isOpen = false;
            console.debug('[catalog-menu] close');
        },

        toggle() {
            this.isOpen ? this.close() : this.open();
        },

        hoverCat(slug) {
            this.activeCategory = slug;
        },

        toggleMobile(slug) {
            this.openMobile = { ...this.openMobile, [slug]: !this.openMobile[slug] };
            console.debug('[catalog-menu] mobile toggle ' + slug);
        },

        isMobileOpen(slug) {
            return !!this.openMobile[slug];
        },
    });
});
