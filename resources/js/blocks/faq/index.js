document.addEventListener('alpine:init', () => {
    Alpine.data('faq', () => ({
        open: 0,

        toggle(index) {
            this.open = this.open === index ? null : index;
        },
    }));
});
