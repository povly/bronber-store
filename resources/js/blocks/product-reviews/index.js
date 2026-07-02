document.addEventListener('alpine:init', () => {
    Alpine.data('productReviews', () => ({
        visibleCount: 5,
        filterPhoto: false,
        filterRating5: false,
        hasMore: false,

        init() {
            this.$watch('filterPhoto', () => { this.visibleCount = 5; this.applyFilters(); });
            this.$watch('filterRating5', () => { this.visibleCount = 5; this.applyFilters(); });
            this.applyFilters();
        },

        applyFilters() {
            const cards = this.$refs.list.querySelectorAll('[data-review]');
            let shown = 0;
            let totalMatching = 0;

            cards.forEach((card) => {
                const hasPhotos = card.dataset.photos === '1';
                const rating = parseInt(card.dataset.rating);
                const matches =
                    (!this.filterPhoto || hasPhotos) &&
                    (!this.filterRating5 || rating === 5);

                if (matches) {
                    totalMatching++;
                    if (shown < this.visibleCount) {
                        card.style.display = '';
                        shown++;
                    } else {
                        card.style.display = 'none';
                    }
                } else {
                    card.style.display = 'none';
                }
            });

            this.hasMore = shown < totalMatching;
        },

        showMore() {
            this.visibleCount += 3;
            this.applyFilters();
        },
    }));
});
