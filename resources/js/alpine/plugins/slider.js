export default function (Alpine) {
    const defaults = {
        perView: 1,
        autoHeight: false,
        vertical: false,
        grid: null,
        pagination: false,
        freeMode: false,
        slideWidth: null,
        fade: false,
        loop: false,
    };

    Alpine.data('slider', (opts = {}) => ({
        index: 0,
        startX: 0,
        deltaX: 0,
        currentOffset: 0,
        isDragging: false,
        dragMoved: false,
        activePointerId: undefined,
        rafId: 0,
        pendingOffset: 0,
        resizeTimer: 0,
        resizeObserver: null,
        resizeHandler: null,
        cachedMaxOffset: 0,
        cachedSlideWidth: 0,
        track: null,
        viewport: window.innerWidth,
        breakpointsConfig: opts.breakpoints ?? null,
        resolvedBp: null,
        _cloneBefore: 0,
        rebaseTimer: 0,
        _resolved: {
            perView: 1,
            autoHeight: false,
            vertical: false,
            grid: null,
            pagination: false,
            freeMode: false,
            slideWidth: null,
            fade: false,
            loop: false,
        },

        /**
         * Resolve breakpoint config for the current viewport.
         *
         * Mobile-first convention: each key is a min-width threshold.
         * Picks the largest breakpoint key <= viewport. Below the smallest
         * breakpoint, returns null so the caller falls back to top-level
         * opts — set the top-level default to the mobile value and provide
         * larger keys for tablet/desktop. Values may be a number
         * (= perView, backward compat) or an object of overrides.
         */
        resolveBreakpoint(config) {
            if (!config || typeof config !== 'object') return null;

            const breakpoints = Object.keys(config)
                .map(Number)
                .filter((k) => !isNaN(k))
                .sort((a, b) => b - a);

            for (const bp of breakpoints) {
                if (this.viewport >= bp) return config[bp];
            }

            return null;
        },

        /**
         * Merge: hardcoded defaults <- top-level opts <- resolved breakpoint overrides.
         * Uses !== undefined for grid/slideWidth so that null (disable) is a valid override.
         */
        computeResolved() {
            const bp = this.resolvedBp;
            const bpOpts =
                bp !== null
                    ? typeof bp === 'number'
                        ? { perView: bp }
                        : bp
                    : {};

            this._resolved = {
                perView: bpOpts.perView ?? opts.perView ?? defaults.perView,
                autoHeight:
                    bpOpts.autoHeight ?? opts.autoHeight ?? defaults.autoHeight,
                vertical: bpOpts.vertical ?? opts.vertical ?? defaults.vertical,
                grid:
                    bpOpts.grid !== undefined
                        ? bpOpts.grid
                        : (opts.grid ?? defaults.grid),
                pagination:
                    bpOpts.pagination ??
                    opts.pagination ??
                    defaults.pagination,
                freeMode:
                    bpOpts.freeMode ?? opts.freeMode ?? defaults.freeMode,
                slideWidth:
                    bpOpts.slideWidth !== undefined
                        ? bpOpts.slideWidth
                        : (opts.slideWidth ?? defaults.slideWidth),
                fade: bpOpts.fade ?? opts.fade ?? defaults.fade,
                loop: bpOpts.loop ?? opts.loop ?? defaults.loop,
            };

            const viewport = this.track?.parentElement;
            if (viewport) {
                viewport.style.setProperty(
                    '--per-view',
                    String(this.perView),
                );

                const sw = this._resolved.slideWidth;
                if (sw !== null && sw !== undefined) {
                    viewport.style.setProperty(
                        '--slide-width',
                        typeof sw === 'number' ? `${sw}px` : sw,
                    );
                } else {
                    viewport.style.removeProperty('--slide-width');
                }
            }
        },

        get resolved() {
            return this._resolved;
        },

        get perView() {
            return this.resolved.grid
                ? (this.resolved.grid.cols ?? 2)
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

        get isFreeMode() {
            return this.resolved.freeMode;
        },

        get isFade() {
            return this.resolved.fade;
        },

        get loopActive() {
            return (
                this.resolved.loop &&
                !this.isGrid &&
                this.realCount > (this.isFade ? 1 : this.perView)
            );
        },

        get isLoop() {
            return this.loopActive && !this.isFade;
        },

        get realCount() {
            if (!this.track) return 0;
            let n = 0;
            for (let i = 0; i < this.track.children.length; i++) {
                if (!this.track.children[i].dataset.sliderClone) n++;
            }
            return n;
        },

        childAt(i) {
            if (!this.track) return null;
            return this.track.children[i + (this._cloneBefore || 0)];
        },

        get gridCols() {
            return this.resolved.grid?.cols ?? 2;
        },

        get gridRows() {
            return this.resolved.grid?.rows ?? 1;
        },

        /**
         * Block native HTML5 drag-and-drop inside the slider.
         *
         * <a href="..."> and <img> are draggable=true by default per HTML5
         * spec. When a user starts a pointer drag on a slide that is (or
         * contains) such an element, the browser fires a native `dragstart`
         * event and hijacks the pointer — the slider's pointermove handler
         * stops receiving events and a ghost image of the card appears.
         *
         * Capturing `dragstart` on the viewport and calling preventDefault
         * cancels the native DnD without affecting clicks, pointer events,
         * or keyboard focus. This is the canonical fix recommended by MDN
         * and matches the pattern used by Swiper.js / Embla / Glide.
         */
        blockNativeDragStart(e) {
            e.preventDefault();
        },

        buildClones() {
            this.removeClones();
            if (!this.isLoop || !this.track) return;

            const slides = Array.from(this.track.children).filter(
                (el) => !el.dataset.sliderClone,
            );
            const n = slides.length;
            const count = Math.min(Math.max(1, this.perView), n);

            const head = document.createDocumentFragment();
            const tail = document.createDocumentFragment();
            for (let i = count - 1; i >= 0; i--) {
                tail.append(this.makeClone(slides[n - 1 - i]));
            }
            for (let i = 0; i < count; i++) {
                head.append(this.makeClone(slides[i]));
            }
            this.track.prepend(tail);
            this.track.append(head);
            this._cloneBefore = count;
        },

        removeClones() {
            if (!this.track) return;
            this.track
                .querySelectorAll('[data-slider-clone]')
                .forEach((el) => el.remove());
            this._cloneBefore = 0;
        },

        makeClone(slide) {
            const clone = slide.cloneNode(true);
            clone.dataset.sliderClone = 'true';
            clone.setAttribute('aria-hidden', 'true');
            // vanilla-lazyload знает только исходные <img data-src>;
            // картинки клонов грузим сразу, иначе wrap-зона покажет заглушки
            clone.querySelectorAll('img[data-src]').forEach((img) => {
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                img.classList.remove('lazy');
            });
            return clone;
        },

        transitionDurationMs() {
            const raw = getComputedStyle(this.track).transitionDuration;
            const val = parseFloat(raw) || 0;
            return raw.includes('ms') ? val : val * 1000;
        },

        cancelLoopRebase() {
            if (this.rebaseTimer) {
                clearTimeout(this.rebaseTimer);
                this.rebaseTimer = 0;
            }
        },

        scheduleLoopRebase() {
            this.cancelLoopRebase();
            if (!this.isLoop || !this.track) return;
            if (this.index >= 0 && this.index < this.realCount) return;
            this.rebaseTimer = setTimeout(() => {
                this.rebaseTimer = 0;
                this.rebaseNow();
            }, this.transitionDurationMs() + 50);
        },

        rebaseNow() {
            if (!this.isLoop || !this.track) return;
            const n = this.realCount;
            if (this.index >= 0 && this.index < n) return;
            this.index = ((this.index % n) + n) % n;
            this.track.style.transition = 'none';
            this.applyTransform(this.offset);
            void this.track.offsetWidth;
            this.track.style.transition = '';
        },

        init() {
            this.track = this.$refs.track;
            this.resolvedBp = this.resolveBreakpoint(this.breakpointsConfig);
            this.computeResolved();
            this.updateVerticalMode();
            this.updateGridMode();
            this.updateAutoHeightMode();
            this.updateFreeMode();
            this.updateFadeMode();
            this.buildClones();
            this.snap();
            this.setupResizeObserver();
            this.setupDragGuard();
            this.markReady();
        },

        setupDragGuard() {
            const viewport = this.track?.parentElement;
            if (!viewport) return;
            viewport.addEventListener(
                'dragstart',
                this.blockNativeDragStart,
                { capture: true },
            );
        },

        markReady() {
            const viewport = this.track?.parentElement;
            if (viewport) {
                requestAnimationFrame(() => {
                    viewport.classList.add('is-ready');
                });
            }
        },

        setupResizeObserver() {
            const debounced = () => {
                if (this.resizeTimer) clearTimeout(this.resizeTimer);
                this.resizeTimer = setTimeout(() => {
                    this.resizeTimer = 0;
                    this.onResize();
                }, 150);
            };
            if (
                typeof ResizeObserver !== 'undefined' &&
                this.track?.parentElement
            ) {
                this.resizeObserver = new ResizeObserver(debounced);
                this.resizeObserver.observe(this.track.parentElement);
            } else {
                this.resizeHandler = debounced;
                window.addEventListener('resize', this.resizeHandler);
            }
        },

        destroy() {
            if (this.rafId) cancelAnimationFrame(this.rafId);
            if (this.resizeTimer) clearTimeout(this.resizeTimer);
            this.cancelLoopRebase();
            if (this.resizeObserver) this.resizeObserver.disconnect();
            if (this.resizeHandler)
                window.removeEventListener('resize', this.resizeHandler);
            const viewport = this.track?.parentElement;
            if (viewport) {
                viewport.removeEventListener(
                    'dragstart',
                    this.blockNativeDragStart,
                    { capture: true },
                );
            }
        },

        get maxIndex() {
            if (!this.track) return 0;
            if (this.isGrid) {
                const totalCols = Math.ceil(
                    this.track.children.length / this.gridRows,
                );
                return Math.max(0, totalCols - this.gridCols);
            }
            return Math.max(0, this.realCount - this.perView);
        },

        get slideWidth() {
            if (!this.track || !this.track.children.length) return 0;
            if (this.isGrid) {
                const current = this.track.children[this.index * this.gridRows];
                const next =
                    this.track.children[(this.index + 1) * this.gridRows];
                if (current && next)
                    return next.offsetLeft - current.offsetLeft;
                return current ? current.offsetWidth : 0;
            }
            const child = this.track.children[0];
            return this.isVertical ? child.offsetHeight : child.offsetWidth;
        },

        get gap() {
            if (!this.track || this.track.children.length < 2) return 0;
            const raw = getComputedStyle(this.track).getPropertyValue(
                '--slider-gap',
            );
            return parseFloat(raw) || 0;
        },

        /*
         * Вьюпорт .slider имеет padding (вынос линии обрезки за пределы
         * ховер-теней), поэтому offsetWidth/offsetHeight непригодны для
         * расчёта зоны прокрутки: они включают padding. Считаем content-box.
         */
        get viewportContentSize() {
            const vp = this.track?.parentElement;
            if (!vp) return { width: 0, height: 0 };
            const cs = getComputedStyle(vp);
            return {
                width:
                    vp.clientWidth -
                    parseFloat(cs.paddingLeft) -
                    parseFloat(cs.paddingRight),
                height:
                    vp.clientHeight -
                    parseFloat(cs.paddingTop) -
                    parseFloat(cs.paddingBottom),
            };
        },

        get offset() {
            if (!this.track || !this.track.children.length) return 0;
            if (this.isGrid) {
                const item = this.track.children[this.index * this.gridRows];
                return item ? item.offsetLeft : 0;
            }
            const slide = this.childAt(this.index);
            if (!slide) return 0;
            return this.isVertical ? slide.offsetTop : slide.offsetLeft;
        },

        get maxOffset() {
            if (!this.track || !this.track.children.length) return 0;
            const content = this.viewportContentSize;
            if (this.isGrid) {
                const totalCols = Math.ceil(
                    this.track.children.length / this.gridRows,
                );
                const lastCol = totalCols - 1;
                const item = this.track.children[lastCol * this.gridRows];
                if (!item) return 0;
                return Math.max(
                    0,
                    item.offsetLeft + item.offsetWidth - content.width,
                );
            }
            const last = this.track.children[this.track.children.length - 1];
            if (!last) return 0;
            if (this.isVertical) {
                return Math.max(
                    0,
                    last.offsetTop + last.offsetHeight - content.height,
                );
            }
            return Math.max(
                0,
                last.offsetLeft + last.offsetWidth - content.width,
            );
        },

        get canPrev() {
            return this.loopActive ? this.canScroll : this.index > 0;
        },
        get canNext() {
            return this.loopActive ? this.canScroll : this.index < this.maxIndex;
        },

        get totalPages() {
            if (!this.track) return 1;
            if (this.isGrid) {
                const totalCols = Math.ceil(
                    this.track.children.length / this.gridRows,
                );
                return Math.max(1, Math.ceil(totalCols / this.gridCols));
            }
            return Math.max(1, Math.ceil(this.realCount / this.perView));
        },

        get currentPage() {
            if (this.maxIndex > 0 && this.index >= this.maxIndex) {
                return this.totalPages - 1;
            }
            return Math.min(
                Math.floor(this.index / this.perView),
                this.totalPages - 1,
            );
        },

        goToPage(page) {
            this.index = Math.min(page * this.perView, this.maxIndex);
            this.snap();
        },

        prev() {
            if (this.loopActive) {
                if (!this.canScroll) return;
                if (this.isFade) {
                    this.index =
                        (this.index - 1 + this.realCount) % this.realCount;
                    this.applyFade();
                    return;
                }
                this.rebaseNow();
                this.index--;
                this.snap();
                return;
            }
            if (this.canPrev) {
                this.index--;
                this.snap();
            }
        },

        next() {
            if (this.loopActive) {
                if (!this.canScroll) return;
                if (this.isFade) {
                    this.index = (this.index + 1) % this.realCount;
                    this.applyFade();
                    return;
                }
                this.rebaseNow();
                this.index++;
                this.snap();
                return;
            }
            if (this.canNext) {
                this.index++;
                this.snap();
            }
        },

        snap() {
            if (this.isDragging) return;
            if (this.isFade) {
                this.applyFade();
                return;
            }
            if (this.isLoop) {
                this.applyTransform(this.offset);
                this.scheduleLoopRebase();
                this.updateHeight();
                return;
            }
            this.applyTransform(Math.min(this.offset, this.maxOffset));
            this.updateHeight();
        },

        applyFade() {
            if (!this.track?.children.length) return;
            for (let i = 0; i < this.track.children.length; i++) {
                this.track.children[i].classList.toggle(
                    'is-active',
                    i === this.index,
                );
            }
        },

        applyTransform(offset) {
            if (this.isFade) return;
            this.track._appliedOffset = offset;
            const axis = this.isVertical ? 'Y' : 'X';
            this.track.style.transform = `translate3d(${axis === 'X' ? `-${offset}px` : '0'}, ${axis === 'Y' ? `-${offset}px` : '0'}, 0)`;
        },

        onPointerDown(e) {
            if (e.button && e.button !== 0) return;
            if (this.maxIndex === 0 && this.maxOffset === 0) return;
            this.cancelLoopRebase();
            this.rebaseNow();
            this.isDragging = true;
            this.dragMoved = false;
            const coord = this.isVertical
                ? (e.clientY ?? e.touches?.[0]?.clientY)
                : (e.clientX ?? e.touches?.[0]?.clientX);
            this.startX = coord;
            this.deltaX = 0;
            this.currentOffset = this.track._appliedOffset ?? this.offset;
            this.cachedMaxOffset = this.maxOffset;
            this.cachedSlideWidth = this.slideWidth;
            this.track.style.transition = 'none';
            this.track.style.cursor = 'grabbing';
        },

        onPointerMove(e) {
            if (!this.isDragging) return;
            const coord = this.isVertical
                ? (e.clientY ?? e.touches?.[0]?.clientY)
                : (e.clientX ?? e.touches?.[0]?.clientX);
            this.deltaX = coord - this.startX;
            if (Math.abs(this.deltaX) > 5) {
                this.dragMoved = true;
                if (this.activePointerId === undefined && e.pointerId !== undefined && this.track?.setPointerCapture) {
                    try {
                        this.track.setPointerCapture(e.pointerId);
                    } catch (_) {
                    }
                    this.activePointerId = e.pointerId;
                }
            }
            this.pendingOffset = Math.max(
                0,
                Math.min(
                    this.currentOffset - this.deltaX,
                    this.cachedMaxOffset,
                ),
            );
            if (!this.rafId) {
                this.rafId = requestAnimationFrame(() => {
                    this.applyTransform(this.pendingOffset);
                    this.rafId = 0;
                });
            }
        },

        findNearestIndex(offset) {
            let closest = 0;
            let closestDist = Infinity;
            const lo = this.isLoop ? -1 : 0;
            const hi = this.isLoop ? this.realCount : this.maxIndex;
            for (let i = lo; i <= hi; i++) {
                const itemIndex = this.isGrid ? i * this.gridRows : i;
                const child = this.isGrid
                    ? this.track.children[itemIndex]
                    : this.childAt(i);
                if (!child) continue;
                const itemPos = this.isVertical
                    ? child.offsetTop
                    : child.offsetLeft;
                const d = Math.abs(itemPos - offset);
                if (d < closestDist) {
                    closestDist = d;
                    closest = i;
                }
            }
            return Math.min(Math.max(closest, lo), hi);
        },

        onPointerUp() {
            if (!this.isDragging) return;
            this.isDragging = false;

            if (this.rafId) {
                cancelAnimationFrame(this.rafId);
                this.rafId = 0;
            }

            if (
                this.activePointerId !== undefined &&
                this.track?.releasePointerCapture
            ) {
                try {
                    this.track.releasePointerCapture(this.activePointerId);
                } catch (_) {
                    // pointer already released or element gone
                }
                this.activePointerId = undefined;
            }

            this.track.style.cursor = '';

            if (!this.dragMoved) {
                this.track.style.transition = '';
                return;
            }

            if (this.isFreeMode) {
                const finalOffset = Math.max(
                    0,
                    Math.min(
                        this.currentOffset - this.deltaX,
                        this.cachedMaxOffset,
                    ),
                );

                this.index = this.findNearestIndex(finalOffset);

                this.track.style.transition = '';
                if (this.isFade) {
                    this.applyFade();
                } else if (this.isLoop) {
                    // в loop нельзя оставаться в клон-зоне после release:
                    // дотягиваем до ближайшего слайда и ребейзим
                    this.applyTransform(this.offset);
                    this.scheduleLoopRebase();
                } else {
                    this.applyTransform(finalOffset);
                }
                this.updateHeight();
                return;
            }

            const projectedOffset = this.currentOffset - this.deltaX;
            const threshold = this.cachedSlideWidth * 0.2;

            let newIndex = this.index;
            if (this.deltaX > threshold && this.canPrev) {
                newIndex = this.index - 1;
            } else if (this.deltaX < -threshold && this.canNext) {
                newIndex = this.index + 1;
            } else {
                newIndex = this.findNearestIndex(projectedOffset);
            }

            this.index = newIndex;

            this.track.style.transition = '';
            if (this.isFade) {
                this.applyFade();
            } else {
                this.applyTransform(this.offset);
                this.scheduleLoopRebase();
            }
            this.updateHeight();
        },

        onResize() {
            if (this.isDragging) return;

            const wasGrid = this.isGrid;
            const wasVertical = this.isVertical;
            const wasAutoHeight = this.isAutoHeight;
            this.viewport = window.innerWidth;
            this.resolvedBp = this.resolveBreakpoint(this.breakpointsConfig);
            this.computeResolved();
            this.updateVerticalMode();
            this.updateGridMode();
            this.updateAutoHeightMode();
            this.updateFreeMode();
            this.updateFadeMode();

            if (wasGrid !== this.isGrid || wasVertical !== this.isVertical) {
                this.index = 0;
            }

            if (wasAutoHeight !== this.isAutoHeight && !this.isAutoHeight) {
                const sliderEl = this.track?.parentElement;
                if (sliderEl) sliderEl.style.height = '';
            }

            this.cancelLoopRebase();
            this.rebaseNow();
            this.removeClones();
            this.buildClones();

            if (this.loopActive) {
                if (this.index > this.realCount - 1)
                    this.index = this.realCount - 1;
            } else if (this.index > this.maxIndex) {
                this.index = this.maxIndex;
            }
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
                sliderEl.style.setProperty('--grid-cols', this.gridCols);
                sliderEl.style.setProperty('--grid-rows', this.gridRows);
            } else {
                sliderEl.style.removeProperty('--grid-cols');
                sliderEl.style.removeProperty('--grid-rows');
            }
        },

        updateAutoHeightMode() {
            const sliderEl = this.track?.parentElement;
            if (!sliderEl) return;
            sliderEl.classList.toggle('slider--auto-height', this.isAutoHeight);
        },

        updateFreeMode() {
            const sliderEl = this.track?.parentElement;
            if (!sliderEl) return;
            sliderEl.classList.toggle('slider--free-mode', this.isFreeMode);
        },

        updateFadeMode() {
            const sliderEl = this.track?.parentElement;
            if (!sliderEl) return;
            sliderEl.classList.toggle('slider--fade', this.isFade);
        },

        updateHeight() {
            const sliderEl = this.track?.parentElement;
            if (!sliderEl || !this.track?.children.length) return;

            if (!this.isAutoHeight) {
                sliderEl.style.height = '';
                return;
            }

            const start = this.isGrid ? this.index * this.gridRows : this.index;
            const count = this.isGrid
                ? this.gridCols * this.gridRows
                : this.perView;
            const end = Math.min(start + count, this.realCount);
            let max = 0;
            for (let i = start; i < end; i++) {
                const el = this.childAt(i);
                if (el) max = Math.max(max, el.offsetHeight);
            }
            sliderEl.style.height = max + 'px';
        },

        get canScroll() {
            return this.maxIndex > 0;
        },

        ensureVisible(targetIndex) {
            if (this.loopActive) {
                this.index = Math.min(
                    Math.max(0, targetIndex),
                    this.realCount - 1,
                );
                this.snap();
                return;
            }

            const lastVisible = this.index + this.perView - 1;

            if (targetIndex > lastVisible) {
                this.index = Math.min(
                    targetIndex - this.perView + 1,
                    this.maxIndex,
                );
                this.snap();
            } else if (targetIndex < this.index) {
                this.index = Math.max(0, targetIndex);
                this.snap();
            }
        },

        /**
         * ensureVisible + peek-ahead: advances one step when target is the last visible slide.
         */
        scrollToReveal(targetIndex) {
            if (this.loopActive) {
                this.index = Math.min(
                    Math.max(0, targetIndex),
                    this.realCount - 1,
                );
                this.snap();
                return;
            }

            const lastVisible = this.index + this.perView - 1;

            if (targetIndex === lastVisible && this.canNext) {
                this.next();
            } else if (targetIndex === this.index && this.canPrev) {
                this.prev();
            } else if (targetIndex > lastVisible) {
                this.index = Math.min(
                    targetIndex - this.perView + 1,
                    this.maxIndex,
                );
                this.snap();
            } else if (targetIndex < this.index) {
                this.index = Math.max(0, targetIndex);
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
