export default function (Alpine) {
    Alpine.data('slider', (opts = {}) => ({
        index: 0,
        startX: 0,
        deltaX: 0,
        currentOffset: 0,
        isDragging: false,
        track: null,
        pagination: opts.pagination ?? false,

        init() {
            this.track = this.$refs.track;
            this.$watch('index', () => this.snap());
        },

        get perView() {
            if (!this.track) return opts.desktop ?? 4;
            return window.innerWidth >= 1100 ? (opts.desktop ?? 4) : (opts.mobile ?? 2);
        },

        get maxIndex() {
            return Math.max(0, this.track.children.length - this.perView);
        },

        get slideWidth() {
            if (!this.track || !this.track.children.length) return 0;
            return this.track.children[0].offsetWidth;
        },

        get gap() {
            if (!this.track || !this.track.children.length < 2) return 0;
            const raw = getComputedStyle(this.track).getPropertyValue('--slider-gap');
            return parseFloat(raw) || 0;
        },

        get offset() {
            if (!this.track || !this.track.children.length) return 0;
            const slide = this.track.children[this.index];
            return slide ? slide.offsetLeft : 0;
        },

        get maxOffset() {
            if (!this.track || !this.track.children.length) return 0;
            const last = this.track.children[this.maxIndex];
            if (!last) return 0;
            return Math.max(0, last.offsetLeft + last.offsetWidth - this.track.parentElement.offsetWidth);
        },

        get canPrev() { return this.index > 0 },
        get canNext() { return this.index < this.maxIndex },

        get totalPages() {
            if (!this.track) return 1;
            return Math.max(1, Math.ceil(this.track.children.length / this.perView));
        },

        get currentPage() {
            return Math.min(Math.floor(this.index / this.perView), this.totalPages - 1);
        },

        goToPage(page) {
            this.index = Math.min(page * this.perView, this.maxIndex);
        },

        prev() { if (this.canPrev) this.index-- },
        next() { if (this.canNext) this.index++ },

        snap() {
            if (this.isDragging) return;
            this.track.style.setProperty('--slider-offset', `-${this.offset}px`);
        },

        onPointerDown(e) {
            if (e.button && e.button !== 0) return;
            this.isDragging = true;
            this.startX = e.clientX ?? e.touches[0].clientX;
            this.deltaX = 0;
            this.currentOffset = this.offset;
            this.track.style.setProperty('--slider-transition', 'none');
            this.track.style.cursor = 'grabbing';
        },

        onPointerMove(e) {
            if (!this.isDragging) return;
            const clientX = e.clientX ?? e.touches[0].clientX;
            this.deltaX = clientX - this.startX;
            const nextOffset = Math.max(0, Math.min(this.currentOffset - this.deltaX, this.maxOffset));
            this.track.style.setProperty('--slider-offset', `-${nextOffset}px`);
        },

        onPointerUp() {
            if (!this.isDragging) return;
            this.isDragging = false;
            this.track.style.removeProperty('--slider-transition');
            this.track.style.cursor = '';

            const projectedOffset = this.currentOffset - this.deltaX;
            const threshold = this.slideWidth * 0.2;

            let newIndex = this.index;
            if (this.deltaX > threshold && this.canPrev) {
                newIndex = this.index - 1;
            } else if (this.deltaX < -threshold && this.canNext) {
                newIndex = this.index + 1;
            } else {
                let closest = this.index;
                let closestDist = Infinity;
                for (let i = 0; i <= this.maxIndex; i++) {
                    const d = Math.abs(this.track.children[i].offsetLeft - projectedOffset);
                    if (d < closestDist) {
                        closestDist = d;
                        closest = i;
                    }
                }
                newIndex = closest;
            }

            this.index = newIndex;
            this.snap();
        },

        onResize() {
            if (this.index > this.maxIndex) this.index = this.maxIndex;
            this.snap();
        },
    }));
}
