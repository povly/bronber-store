export default function (Alpine) {
    Alpine.data('slider', (opts = {}) => ({
        index: 0,
        startX: 0,
        deltaX: 0,
        currentOffset: 0,
        isDragging: false,
        track: null,
        viewport: window.innerWidth,
        pagination: opts.pagination ?? false,
        gridConfig: opts.grid?.breakpoints ?? null,
        gridBelow: opts.grid?.below ?? null,
        gridAbove: opts.grid?.above ?? null,
        breakpointsConfig: opts.breakpoints ?? null,

        get isGrid() {
            if (!opts.grid) return false;
            if (this.gridBelow !== null) return this.viewport < this.gridBelow;
            if (this.gridAbove !== null) return this.viewport >= this.gridAbove;
            return true;
        },

        init() {
            this.track = this.$refs.track;
            this.updateGridMode();
        },

        resolveBreakpoint(config) {
            if (!config || typeof config !== 'object') return null;

            const breakpoints = Object.keys(config)
                .map(Number)
                .filter(k => !isNaN(k))
                .sort((a, b) => b - a);

            for (const bp of breakpoints) {
                if (this.viewport >= bp) return config[bp];
            }

            return breakpoints.length ? config[Math.min(...breakpoints)] : null;
        },

        get gridBreakpoint() {
            const resolved = this.resolveBreakpoint(this.gridConfig);
            if (!resolved) return { cols: 2, rows: 1 };
            return typeof resolved === 'number'
                ? { cols: resolved, rows: 1 }
                : { cols: resolved.cols ?? 2, rows: resolved.rows ?? 1 };
        },

        get gridCols() {
            return this.gridBreakpoint.cols;
        },

        get gridRows() {
            return this.gridBreakpoint.rows;
        },

        get perView() {
            if (this.isGrid) return this.gridCols;

            const resolved = this.resolveBreakpoint(this.breakpointsConfig);
            if (resolved !== null && typeof resolved === 'number') return resolved;

            return 1;
        },

        get maxIndex() {
            if (!this.track) return 0;
            if (this.isGrid) {
                const totalCols = Math.ceil(this.track.children.length / this.gridRows);
                return Math.max(0, totalCols - this.gridCols);
            }
            return Math.max(0, this.track.children.length - this.perView);
        },

        get slideWidth() {
            if (!this.track || !this.track.children.length) return 0;
            if (this.isGrid) {
                const current = this.track.children[this.index * this.gridRows];
                const next = this.track.children[(this.index + 1) * this.gridRows];
                if (current && next) return next.offsetLeft - current.offsetLeft;
                return current ? current.offsetWidth : 0;
            }
            return this.track.children[0].offsetWidth;
        },

        get gap() {
            if (!this.track || !this.track.children.length < 2) return 0;
            const raw = getComputedStyle(this.track).getPropertyValue('--slider-gap');
            return parseFloat(raw) || 0;
        },

        get offset() {
            if (!this.track || !this.track.children.length) return 0;
            if (this.isGrid) {
                const item = this.track.children[this.index * this.gridRows];
                return item ? item.offsetLeft : 0;
            }
            const slide = this.track.children[this.index];
            return slide ? slide.offsetLeft : 0;
        },

        get maxOffset() {
            if (!this.track || !this.track.children.length) return 0;
            if (this.isGrid) {
                const totalCols = Math.ceil(this.track.children.length / this.gridRows);
                const lastCol = totalCols - 1;
                const item = this.track.children[lastCol * this.gridRows];
                if (!item) return 0;
                return Math.max(0, item.offsetLeft + item.offsetWidth - this.track.parentElement.offsetWidth);
            }
            const last = this.track.children[this.track.children.length - 1];
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
            this.snap();
        },

        prev() {
            if (this.canPrev) {
                this.index--;
                this.snap();
            }
        },

        next() {
            if (this.canNext) {
                this.index++;
                this.snap();
            }
        },

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
            this.cachedMaxOffset = this.maxOffset;
            this.cachedSlideWidth = this.slideWidth;
            this.track.style.setProperty('--slider-transition', 'none');
            this.track.style.cursor = 'grabbing';

        },

        onPointerMove(e) {
            if (!this.isDragging) return;
            const clientX = e.clientX ?? e.touches[0].clientX;
            this.deltaX = clientX - this.startX;
            const nextOffset = Math.max(0, Math.min(this.currentOffset - this.deltaX, this.cachedMaxOffset));
            this.track.style.setProperty('--slider-offset', `-${nextOffset}px`);
        },

        onPointerUp() {
            if (!this.isDragging) return;
            this.isDragging = false;
            this.track.style.cursor = '';

            const projectedOffset = this.currentOffset - this.deltaX;
            const threshold = this.cachedSlideWidth * 0.2;

            let newIndex = this.index;
            if (this.deltaX > threshold && this.canPrev) {
                newIndex = this.index - 1;
            } else if (this.deltaX < -threshold && this.canNext) {
                newIndex = this.index + 1;
            } else {
                let closest = this.index;
                let closestDist = Infinity;
                for (let i = 0; i <= this.maxIndex; i++) {
                    const itemIndex = this.isGrid ? i * this.gridRows : i;
                    const d = Math.abs(this.track.children[itemIndex].offsetLeft - projectedOffset);
                    if (d < closestDist) {
                        closestDist = d;
                        closest = i;
                    }
                }
                newIndex = closest;
            }

            this.index = newIndex;
            const targetOffset = this.offset;

            requestAnimationFrame(() => {
                this.track.style.setProperty('--slider-transition', '');
                this.track.style.setProperty('--slider-offset', `-${targetOffset}px`);
            });
        },

        onResize() {
            if (this.isDragging) return;

            const wasGrid = this.isGrid;
            this.viewport = window.innerWidth;
            this.updateGridMode();

            if (wasGrid !== this.isGrid) {
                this.index = 0;
            }

            if (this.index > this.maxIndex) this.index = this.maxIndex;
            this.snap();
        },

        updateGridMode() {
            if (!opts.grid) return;
            const sliderEl = this.track?.parentElement;
            if (!sliderEl) return;

            sliderEl.classList.toggle('slider--grid', this.isGrid);

            if (this.isGrid) {
                this.$el.style.setProperty('--grid-cols', this.gridCols);
                this.$el.style.setProperty('--grid-rows', this.gridRows);
            } else {
                this.$el.style.removeProperty('--grid-cols');
                this.$el.style.removeProperty('--grid-rows');
            }
        },
    }));
}
