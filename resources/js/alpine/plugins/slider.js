export default function (Alpine) {
    Alpine.data('slider', (opts = {}) => ({
        index: 0,
        startX: 0,
        deltaX: 0,
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
            return this.index * (this.slideWidth + this.gap);
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
            this.track.style.setProperty('--slider-transition', 'none');
            this.track.style.cursor = 'grabbing';
        },

        onPointerMove(e) {
            if (!this.isDragging) return;
            const clientX = e.clientX ?? e.touches[0].clientX;
            this.deltaX = clientX - this.startX;
            this.track.style.setProperty('--slider-offset', `-${this.offset - this.deltaX}px`);
        },

        onPointerUp() {
            if (!this.isDragging) return;
            this.isDragging = false;
            this.track.style.removeProperty('--slider-transition');
            this.track.style.cursor = '';
            const threshold = this.slideWidth * 0.2;
            if (this.deltaX > threshold && this.canPrev) this.index--;
            else if (this.deltaX < -threshold && this.canNext) this.index++;
            this.snap();
        },

        onResize() {
            if (this.index > this.maxIndex) this.index = this.maxIndex;
            this.snap();
        },
    }));
}
