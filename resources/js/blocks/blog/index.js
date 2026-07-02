document.addEventListener('alpine:init', () => {
    Alpine.data('blog', () => ({
        visibleCount: 6,
        hasMore: false,

        init() {
            this.applyVisibility();
        },

        applyVisibility() {
            const items = this.$refs.list.querySelectorAll('[data-article]');
            let shown = 0;

            items.forEach((item) => {
                if (shown < this.visibleCount) {
                    item.style.display = '';
                    shown++;
                } else {
                    item.style.display = 'none';
                }
            });

            this.hasMore = shown < items.length;
        },

        showMore() {
            this.visibleCount += 3;
            this.applyVisibility();
        },
    }));
});
