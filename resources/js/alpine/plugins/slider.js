export default function (Alpine) {
    const defaults = {
        perView: 1,
        autoHeight: false,
        vertical: false,
        pagination: false,
        grid: null,
    };

    Alpine.data('slider', (opts = {}) => ({
        index: 0,
        startX: 0,
        deltaX: 0,
        currentOffset: 0,
        isDragging: false,
        dragMoved: false,
        track: null,
        viewport: window.innerWidth,
        breakpointsConfig: opts.breakpoints ?? null,
        resolvedBp: null,

        /**
         * Resolve breakpoint config for the current viewport.
         * Picks the largest breakpoint key <= viewport, falling back to the smallest.
         * Values may be a number (= perView, backward compat) or an object of overrides.
         */
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

        /**
         * Merge: hardcoded defaults <- top-level opts <- resolved breakpoint overrides.
         * Uses !== undefined for grid so that null (disable grid) is a valid override.
         */
        get resolved() {
            const bp = this.resolvedBp;
            const bpOpts = bp !== null
                ? (typeof bp === 'number' ? { perView: bp } : bp)
                : {};

            return {
                perView: bpOpts.perView ?? opts.perView ?? defaults.perView,
                autoHeight: bpOpts.autoHeight ?? opts.autoHeight ?? defaults.autoHeight,
                vertical: bpOpts.vertical ?? opts.vertical ?? defaults.vertical,
                pagination: bpOpts.pagination ?? opts.pagination ?? defaults.pagination,
                grid: bpOpts.grid !== undefined ? bpOpts.grid : (opts.grid ?? defaults.grid),
            };
        },

        get perView() {
            return this.resolved.grid
                ? this.resolved.grid.cols ?? 2
                : this.resolved.perView;
        },

        get isGrid() {
            return this.resolved.grid !== null;
        },

        get isVertical() {
            return this.resolved.vertical;
        },

        get isAutoHeight() {
            return this.resolved.autoHeight;
        },

        get pagination() {
            return this.resolved.pagination;
        },

        get gridCols() {
            return this.resolved.grid?.cols ?? 2;
        },

        get gridRows() {
            return this.resolved.grid?.rows ?? 1;
        },

        init() {
            this.track = this.$refs.track;
            this.resolvedBp = this.resolveBreakpoint(this.breakpointsConfig);
            this.updateVerticalMode();
            this.updateGridMode();
            this.updateAutoHeightMode();
            this.snap();
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
            const child = this.track.children[0];
            return this.isVertical ? child.offsetHeight : child.offsetWidth;
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
            if (!slide) return 0;
            return this.isVertical ? slide.offsetTop : slide.offsetLeft;
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
            if (this.isVertical) {
                return Math.max(0, last.offsetTop + last.offsetHeight - this.track.parentElement.offsetHeight);
            }
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
            this.applyTransform(this.offset);
            this.updateHeight();
        },

        applyTransform(offset) {
            const axis = this.isVertical ? 'Y' : 'X';
            this.track.style.transform = `translate3d(${axis === 'X' ? `-${offset}px` : '0'}, ${axis === 'Y' ? `-${offset}px` : '0'}, 0)`;
        },

        onPointerDown(e) {
            if (e.button && e.button !== 0) return;
            if (this.maxIndex === 0) return;
            this.isDragging = true;
            this.dragMoved = false;
            const coord = this.isVertical ? (e.clientY ?? e.touches?.[0]?.clientY) : (e.clientX ?? e.touches?.[0]?.clientX);
            this.startX = coord;
            this.deltaX = 0;
            this.currentOffset = this.offset;
            this.cachedMaxOffset = this.maxOffset;
            this.cachedSlideWidth = this.slideWidth;
            this.track.style.transition = 'none';
            this.track.style.cursor = 'grabbing';
        },

        onPointerMove(e) {
            if (!this.isDragging) return;
            const coord = this.isVertical ? (e.clientY ?? e.touches?.[0]?.clientY) : (e.clientX ?? e.touches?.[0]?.clientX);
            this.deltaX = coord - this.startX;
            if (Math.abs(this.deltaX) > 5) this.dragMoved = true;
            const nextOffset = Math.max(0, Math.min(this.currentOffset - this.deltaX, this.cachedMaxOffset));
            this.applyTransform(nextOffset);
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
                    const itemPos = this.isVertical ? this.track.children[itemIndex].offsetTop : this.track.children[itemIndex].offsetLeft;
                    const d = Math.abs(itemPos - projectedOffset);
                    if (d < closestDist) {
                        closestDist = d;
                        closest = i;
                    }
                }
                newIndex = closest;
            }

            this.index = newIndex;
            const targetOffset = this.offset;

            this.track.style.transition = '';
            this.applyTransform(targetOffset);
            this.updateHeight();
        },

        onResize() {
            if (this.isDragging) return;

            const wasGrid = this.isGrid;
            const wasVertical = this.isVertical;
            const wasAutoHeight = this.isAutoHeight;
            this.viewport = window.innerWidth;
            this.resolvedBp = this.resolveBreakpoint(this.breakpointsConfig);
            this.updateVerticalMode();
            this.updateGridMode();
            this.updateAutoHeightMode();

            if (wasGrid !== this.isGrid || wasVertical !== this.isVertical) {
                this.index = 0;
            }

            if (wasAutoHeight !== this.isAutoHeight && !this.isAutoHeight) {
                const sliderEl = this.track?.parentElement;
                if (sliderEl) sliderEl.style.height = '';
            }

            if (this.index > this.maxIndex) this.index = this.maxIndex;
            this.snap();
        },

        updateVerticalMode() {
            const sliderEl = this.track?.parentElement;
            if (!sliderEl) return;
            sliderEl.classList.toggle('slider--vertical', this.isVertical);
        },

        updateGridMode() {
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

        updateAutoHeightMode() {
            const sliderEl = this.track?.parentElement;
            if (!sliderEl) return;
            sliderEl.classList.toggle('slider--auto-height', this.isAutoHeight);
        },

        updateHeight() {
            const sliderEl = this.track?.parentElement;
            if (!sliderEl || !this.track?.children.length) return;

            if (!this.isAutoHeight) {
                sliderEl.style.height = '';
                return;
            }

            const start = this.index;
            const end = Math.min(start + this.perView, this.track.children.length);
            let max = 0;
            for (let i = start; i < end; i++) {
                max = Math.max(max, this.track.children[i].offsetHeight);
            }
            sliderEl.style.height = max + 'px';
        },

        get canScroll() {
            return this.maxIndex > 0;
        },

        ensureVisible(targetIndex) {
            const lastVisible = this.index + this.perView - 1;
            const firstVisible = this.index;

            if (targetIndex >= lastVisible && this.canNext) {
                this.index++;
                this.snap();
            } else if (targetIndex <= firstVisible && this.canPrev) {
                this.index--;
                this.snap();
            }
        },

        suppressDragClick(e) {
            if (this.dragMoved) {
                this.dragMoved = false;
                e.preventDefault();
                e.stopPropagation();
            }
        },
    }));
}
